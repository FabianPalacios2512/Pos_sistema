# ============================================================
#  Deploy 105 POS Pro — PowerShell (Windows nativo, sin rsync)
#  Uso: .\deploy-win.ps1
#        .\deploy-win.ps1 -SkipBuild        # sube sin compilar
#        .\deploy-win.ps1 -OnlyFrontend     # solo frontend
#        .\deploy-win.ps1 -OnlyBackend      # solo backend
# ============================================================
param(
    [switch]$SkipBuild,
    [switch]$OnlyFrontend,
    [switch]$OnlyBackend
)

$ErrorActionPreference = "Stop"

# ── CONFIG ──────────────────────────────────────────────────
$SSH_KEY      = "$env:USERPROFILE\.ssh\id_rsa"
$VPS          = "root@72.61.73.245"
$REMOTE_PATH  = "/var/www/105pos"
$SSH_OPTS     = @("-i", $SSH_KEY, "-o", "StrictHostKeyChecking=no", "-o", "BatchMode=yes")
$ROOT         = $PSScriptRoot
$TEMP_DIR     = "$ROOT\.deploy-tmp2"

Set-Location $ROOT

# ── HELPERS ─────────────────────────────────────────────────
function Write-Step($msg) {
    Write-Host ""
    Write-Host ">> $msg" -ForegroundColor Cyan
}

function Write-Ok($msg) {
    Write-Host "   $msg" -ForegroundColor Green
}

function Write-Fail($msg) {
    Write-Host "   ERROR: $msg" -ForegroundColor Red
    exit 1
}

function Test-SSH {
    Write-Step "Verificando conexion SSH al VPS..."
    $result = & ssh @SSH_OPTS $VPS "echo OK" 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Fail "No se pudo conectar al VPS. Verifica tu llave SSH en $SSH_KEY"
    }
    Write-Ok "Conexion OK"
}

function Invoke-RemoteCmd($cmd) {
    & ssh @SSH_OPTS $VPS $cmd
    if ($LASTEXITCODE -ne 0) {
        Write-Fail "Comando remoto fallo: $cmd"
    }
}

# ── VALIDACIONES ────────────────────────────────────────────
if (-not (Test-Path $SSH_KEY)) {
    Write-Fail "Llave SSH no encontrada: $SSH_KEY"
}

Test-SSH

# Crear directorio temporal
if (Test-Path $TEMP_DIR) { Remove-Item $TEMP_DIR -Recurse -Force }
New-Item -ItemType Directory -Path $TEMP_DIR -Force | Out-Null

$stopwatch = [System.Diagnostics.Stopwatch]::StartNew()

try {
    # ── 1. BUILD FRONTEND ───────────────────────────────────
    if (-not $SkipBuild -and -not $OnlyBackend) {
        Write-Step "Compilando frontend..."
        $prevPref = $ErrorActionPreference
        $ErrorActionPreference = "Continue"
        & npm run build 2>&1 | ForEach-Object { "$_" } | Select-Object -Last 5
        $ErrorActionPreference = $prevPref
        if ($LASTEXITCODE -ne 0) { Write-Fail "Error en npm run build" }
        Write-Ok "Build completado"
    }

    # ── 2. COPIAR dist -> backend/public ────────────────────
    if (-not $OnlyBackend) {
        Write-Step "Copiando dist -> backend/public..."
        if (-not (Test-Path "$ROOT\backend\public")) {
            Write-Fail "backend/public no existe"
        }
        Copy-Item -Path "$ROOT\dist\*" -Destination "$ROOT\backend\public\" -Recurse -Force
        Write-Ok "Copiado"
    }

    # ── 3. SUBIR FRONTEND ──────────────────────────────────
    if (-not $OnlyBackend) {
        Write-Step "Empaquetando y subiendo frontend..."

        # Empaquetar dist con tar
        $frontendTar = "$TEMP_DIR\frontend.tar.gz"
        Push-Location "$ROOT"
        & tar -czf $frontendTar -C dist .
        Pop-Location
        if ($LASTEXITCODE -ne 0) { Write-Fail "Error empaquetando frontend (dist)" }

        # Empaquetar backend/public
        $publicTar = "$TEMP_DIR\public.tar.gz"
        & tar -czf $publicTar -C "$ROOT\backend\public" .
        if ($LASTEXITCODE -ne 0) { Write-Fail "Error empaquetando backend/public" }

        # Subir y desempaquetar dist
        & scp @SSH_OPTS $frontendTar "${VPS}:/tmp/frontend.tar.gz"
        if ($LASTEXITCODE -ne 0) { Write-Fail "Error subiendo frontend.tar.gz" }
        Invoke-RemoteCmd "mkdir -p $REMOTE_PATH/dist && tar -xzf /tmp/frontend.tar.gz -C $REMOTE_PATH/dist && rm /tmp/frontend.tar.gz"

        # Subir y desempaquetar backend/public
        & scp @SSH_OPTS $publicTar "${VPS}:/tmp/public.tar.gz"
        if ($LASTEXITCODE -ne 0) { Write-Fail "Error subiendo public.tar.gz" }
        Invoke-RemoteCmd "tar -xzf /tmp/public.tar.gz -C $REMOTE_PATH/backend/public && rm /tmp/public.tar.gz"

        Write-Ok "Frontend subido"
    }

    # ── 4. SUBIR BACKEND ───────────────────────────────────
    if (-not $OnlyFrontend) {
        Write-Step "Empaquetando y subiendo backend..."

        $backendTar = "$TEMP_DIR\backend.tar.gz"

        # Empaquetar backend excluyendo carpetas pesadas/sensibles
        Push-Location "$ROOT"
        & tar -czf $backendTar `
            --exclude="node_modules" `
            --exclude="vendor" `
            --exclude="dist" `
            --exclude=".git" `
            --exclude="storage/logs/*" `
            --exclude="storage/framework/cache/*" `
            --exclude="storage/framework/views/*" `
            --exclude="storage/framework/sessions/*" `
            --exclude="public/storage" `
            --exclude=".env" `
            -C backend .
        Pop-Location
        if ($LASTEXITCODE -ne 0) { Write-Fail "Error empaquetando backend" }

        $sizeMB = [math]::Round((Get-Item $backendTar).Length / 1MB, 1)
        Write-Ok "Backend empaquetado: ${sizeMB}MB"

        # Subir y desempaquetar
        & scp @SSH_OPTS $backendTar "${VPS}:/tmp/backend.tar.gz"
        if ($LASTEXITCODE -ne 0) { Write-Fail "Error subiendo backend.tar.gz" }

        # Desempaquetar sin sobreescribir .env, vendor, storage
        Invoke-RemoteCmd @"
cd $REMOTE_PATH/backend && tar -xzf /tmp/backend.tar.gz --exclude='.env' && rm /tmp/backend.tar.gz
"@

        Write-Ok "Backend subido"
    }

    Write-Step "Actualizando dependencias, limpiando cache, migrando DB en VPS..."
    Invoke-RemoteCmd @"
cd $REMOTE_PATH/backend && composer update --no-dev --optimize-autoloader && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear && php artisan config:cache && php artisan route:cache && php artisan migrate --force && php artisan storage:link && sudo systemctl restart php8.3-fpm && echo 'DEPENDENCIAS, CACHE Y MIGRATE OK'
"@
    Write-Ok "Dependencias actualizadas, cache limpiado, DB migrada y PHP reiniciado"

} finally {
    # ── LIMPIAR ARCHIVOS TEMPORALES ────────────────────────
    if (Test-Path $TEMP_DIR) {
        Remove-Item $TEMP_DIR -Recurse -Force -ErrorAction SilentlyContinue
    }
}

$stopwatch.Stop()
$elapsed = $stopwatch.Elapsed.ToString("mm\:ss")

# ── RESUMEN ────────────────────────────────────────────────
Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "  Deploy completado en $elapsed" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
Write-Host "  https://maria.105pos.pro/pos" -ForegroundColor Yellow
Write-Host "  Ctrl+Shift+R en el navegador para limpiar cache" -ForegroundColor Gray
Write-Host ""
