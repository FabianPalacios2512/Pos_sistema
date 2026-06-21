$ErrorActionPreference = "Stop"

$SSH_KEY = "$env:USERPROFILE\.ssh\id_rsa"
$VPS = "root@72.61.73.245"
$REMOTE_PATH = "/var/www/105pos"
$SSH_OPTS = @("-i", $SSH_KEY, "-o", "StrictHostKeyChecking=no", "-o", "BatchMode=yes")

Write-Host "Agregando credenciales de Cloudflare R2 al .env del VPS..." -ForegroundColor Cyan

# Las credenciales exactas que tienes en tu .env local
$ENV_VARS = @"

# ==========================================================
# CLOUDFLARE R2 STORAGE
# ==========================================================
AWS_ACCESS_KEY_ID=93b67c96990f79779027b798ccafe476
AWS_SECRET_ACCESS_KEY=754ed9489f626a80b7a54103ee866eb055fa35ecda91319eca239a44a47255bf
AWS_DEFAULT_REGION=auto
AWS_BUCKET=105pos-media
AWS_ENDPOINT=https://4c93dafd89a32c19dadf690f61dce2c9.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true
"@

# Comando remoto para inyectar si no existe y limpiar caché
$REMOTE_CMD = @"
if ! grep -q 'AWS_BUCKET' $REMOTE_PATH/backend/.env; then
    cat << 'EOF' >> $REMOTE_PATH/backend/.env
$ENV_VARS
EOF
    echo "Credenciales añadidas al .env"
else
    # Reemplazar usando sed si ya existe alguna variable para actualizarla
    sed -i '/AWS_/d' $REMOTE_PATH/backend/.env
    cat << 'EOF' >> $REMOTE_PATH/backend/.env
$ENV_VARS
EOF
    echo "Credenciales actualizadas en el .env"
fi

cd $REMOTE_PATH/backend
php artisan config:clear
php artisan cache:clear
php artisan config:cache
sudo systemctl restart php8.3-fpm
echo "✅ CACHE Y SERVIDOR REINICIADOS"
"@

# Ejecutar por SSH
& ssh @SSH_OPTS $VPS $REMOTE_CMD

Write-Host "¡Listo! Ya puedes subir imágenes desde el sistema." -ForegroundColor Green
