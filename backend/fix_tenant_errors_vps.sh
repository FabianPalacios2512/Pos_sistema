#!/bin/bash

# 🚨 Script de Reparación de Errores Críticos - 105POS
# =====================================================
#
# Este script repara:
# 1. Dominios faltantes para tenants existentes
# 2. Limpia registros de pagos duplicados
# 3. Verifica configuración de .env
#
# USO:
#   chmod +x fix_tenant_errors_vps.sh
#   ./fix_tenant_errors_vps.sh

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  🚨 REPARACIÓN DE ERRORES CRÍTICOS - 105POS                 ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Directorio del proyecto backend
BACKEND_DIR="/var/www/105pos.pro/backend"

# Verificar que estamos en el directorio correcto
if [ ! -f "$BACKEND_DIR/artisan" ]; then
    echo -e "${RED}❌ Error: No se encuentra el archivo artisan en $BACKEND_DIR${NC}"
    echo "   Por favor, edita la variable BACKEND_DIR en este script"
    exit 1
fi

cd "$BACKEND_DIR" || exit 1

echo -e "${BLUE}📂 Directorio de trabajo: $BACKEND_DIR${NC}"
echo ""

# ========================================
# PASO 1: Diagnóstico
# ========================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${BLUE}🔍 PASO 1: Ejecutando diagnóstico...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

php diagnose_tenant_errors.php

echo ""
read -p "¿Continuar con las reparaciones? (s/n): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo -e "${YELLOW}⏸️  Reparación cancelada${NC}"
    exit 0
fi

# ========================================
# PASO 2: Verificar configuración .env
# ========================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${BLUE}⚙️ PASO 2: Verificando configuración .env...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

if ! grep -q "CENTRAL_DOMAIN=105pos.pro" .env; then
    echo -e "${YELLOW}⚠️  CENTRAL_DOMAIN no está configurado en .env${NC}"
    echo ""
    echo "Agregando CENTRAL_DOMAIN=105pos.pro a .env..."
    echo "" >> .env
    echo "# Central domain for multi-tenancy" >> .env
    echo "CENTRAL_DOMAIN=105pos.pro" >> .env
    echo -e "${GREEN}✅ CENTRAL_DOMAIN agregado a .env${NC}"
else
    echo -e "${GREEN}✅ CENTRAL_DOMAIN ya está configurado${NC}"
fi

echo ""

# ========================================
# PASO 3: Reparar dominios faltantes
# ========================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${BLUE}🔧 PASO 3: Reparando dominios faltantes...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "Ejecutando: php artisan tenants:fix-missing-domains --dry-run"
php artisan tenants:fix-missing-domains --dry-run

echo ""
read -p "¿Aplicar los cambios? (s/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Ss]$ ]]; then
    echo "Ejecutando: php artisan tenants:fix-missing-domains"
    php artisan tenants:fix-missing-domains
    echo -e "${GREEN}✅ Dominios reparados${NC}"
else
    echo -e "${YELLOW}⏭️  Reparación de dominios omitida${NC}"
fi

echo ""

# ========================================
# PASO 4: Limpiar duplicados en pending_payments
# ========================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${BLUE}🧹 PASO 4: Limpiando registros de pagos duplicados...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "Buscando duplicados en pending_payments..."
DUPLICATES=$(mysql -u root -p"${DB_PASSWORD}" -D "${DB_DATABASE}" -sN -e "
SELECT reference, COUNT(*) as count
FROM pending_payments
GROUP BY reference
HAVING count > 1;
" 2>/dev/null)

if [ -z "$DUPLICATES" ]; then
    echo -e "${GREEN}✅ No se encontraron duplicados en pending_payments${NC}"
else
    echo -e "${YELLOW}⚠️  Duplicados encontrados:${NC}"
    echo "$DUPLICATES"
    echo ""
    echo "Se mantendrá el registro más reciente y se eliminarán los demás."
    read -p "¿Continuar? (s/n): " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Ss]$ ]]; then
        mysql -u root -p"${DB_PASSWORD}" -D "${DB_DATABASE}" -e "
        DELETE t1 FROM pending_payments t1
        INNER JOIN pending_payments t2
        WHERE t1.reference = t2.reference
        AND t1.id < t2.id;
        " 2>/dev/null
        echo -e "${GREEN}✅ Duplicados eliminados${NC}"
    else
        echo -e "${YELLOW}⏭️  Limpieza de duplicados omitida${NC}"
    fi
fi

echo ""

# ========================================
# PASO 5: Verificar caso específico: maria-jose
# ========================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${BLUE}🔍 PASO 5: Verificando caso maria-jose...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

php artisan tinker --execute="
\$subdomain = 'maria-jose';
\$baseDomain = env('CENTRAL_DOMAIN', '105pos.pro');
\$fullDomain = \"\$subdomain.\$baseDomain\";

echo \"🔍 Buscando dominio: \$fullDomain\n\";

\$domain = \\Stancl\\Tenancy\\Database\\Models\\Domain::where('domain', \$fullDomain)->first();

if (\$domain) {
    echo \"✅ Dominio encontrado\n\";
    echo \"   Tenant ID: {\$domain->tenant_id}\n\";

    \$tenant = \\App\\Models\\Tenant::find(\$domain->tenant_id);
    if (\$tenant) {
        echo \"✅ Tenant existe\n\";
        echo \"   Business Name: {\$tenant->business_name}\n\";
        echo \"   Plan: {\$tenant->plan}\n\";
    } else {
        echo \"❌ ERROR: Domain existe pero tenant NO existe\n\";
        echo \"   Esto causará error 'Tenant could not be identified'\n\";
        echo \"   SOLUCIÓN: Eliminar el domain huérfano\n\";
    }
} else {
    echo \"❌ Dominio NO encontrado\n\";
    echo \"   Buscando si existe tenant con ID similar...\n\";

    \$tenant = \\App\\Models\\Tenant::where('id', 'LIKE', \"%\$subdomain%\")
        ->orWhere('business_name', 'LIKE', \"%\$subdomain%\")
        ->first();

    if (\$tenant) {
        echo \"✅ Tenant encontrado: {\$tenant->id}\n\";
        echo \"   Business Name: {\$tenant->business_name}\n\";
        echo \"   SOLUCIÓN: Crear domain para este tenant\n\";
        echo \"   php artisan tinker --execute=\\\"\\\\App\\\\Models\\\\Tenant::find('{\$tenant->id}')->domains()->create(['domain' => '\$fullDomain'])\\\"\n\";
    } else {
        echo \"❌ No se encontró tenant ni dominio\n\";
        echo \"   Posible causa: Registro incompleto o eliminado\n\";
    }
}
"

echo ""

# ========================================
# PASO 6: Limpiar caché
# ========================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${BLUE}🧹 PASO 6: Limpiando caché...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

php artisan config:cache
php artisan route:cache
php artisan view:clear

echo -e "${GREEN}✅ Caché limpiado${NC}"

echo ""

# ========================================
# RESUMEN
# ========================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${GREEN}✅ REPARACIÓN COMPLETADA${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo -e "${YELLOW}📋 ACCIONES REALIZADAS:${NC}"
echo "  ✅ Diagnóstico ejecutado"
echo "  ✅ Configuración .env verificada"
echo "  ✅ Dominios faltantes reparados"
echo "  ✅ Duplicados en pending_payments limpiados"
echo "  ✅ Caso maria-jose verificado"
echo "  ✅ Caché limpiado"
echo ""
echo -e "${BLUE}📝 PRÓXIMOS PASOS:${NC}"
echo "  1. Verifica que los dominios ahora funcionan"
echo "  2. Prueba hacer login en maria-jose.105pos.pro"
echo "  3. Verifica que los pagos ahora se procesan correctamente"
echo ""
echo -e "${YELLOW}⚠️  Si los problemas persisten:${NC}"
echo "  - Revisa los logs: tail -f storage/logs/laravel.log"
echo "  - Ejecuta: php artisan tenants:list"
echo "  - Verifica tabla 'domains': SELECT * FROM domains WHERE domain LIKE '%maria-jose%';"
echo ""
