#!/bin/bash

# Script para solucionar problemas de WhatsApp

echo "🔧 =========================================="
echo "🔧  SOLUCIONADOR DE PROBLEMAS WHATSAPP"
echo "🔧 =========================================="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # Sin color

# 1. Detener procesos de WhatsApp
echo -e "${YELLOW}1️⃣  Deteniendo procesos de WhatsApp...${NC}"
pkill -f "whatsapp-server.js" 2>/dev/null
pkill -f "whatsapp-service.js" 2>/dev/null
sleep 2
echo -e "${GREEN}✅ Procesos detenidos${NC}"
echo ""

# 2. Limpiar sesión de WhatsApp
echo -e "${YELLOW}2️⃣  Limpiando sesión de WhatsApp...${NC}"
if [ -d "whatsapp_session" ]; then
    rm -rf whatsapp_session
    echo -e "${GREEN}✅ Sesión eliminada${NC}"
else
    echo -e "${BLUE}ℹ️  No había sesión previa${NC}"
fi

if [ -f "whatsapp_qr.txt" ]; then
    rm -f whatsapp_qr.txt
    echo -e "${GREEN}✅ QR eliminado${NC}"
fi
echo ""

# 3. Verificar y limpiar duplicados en base de datos
echo -e "${YELLOW}3️⃣  Limpiando números duplicados en base de datos...${NC}"

mysql -u root pos_sistema <<EOF
-- Mostrar duplicados antes de limpiar
SELECT phone, COUNT(*) as count, GROUP_CONCAT(id) as ids, GROUP_CONCAT(name SEPARATOR ' | ') as names
FROM customers
GROUP BY REPLACE(REPLACE(REPLACE(phone, '+', ''), '-', ''), ' ', '')
HAVING COUNT(*) > 1;

-- Actualizar números con formato correcto
UPDATE customers
SET phone = CONCAT('+57', phone)
WHERE phone REGEXP '^3[0-9]{9}$'
  AND phone NOT LIKE '+%';

-- Marcar duplicados con números inválidos
UPDATE customers
SET phone = CONCAT('DUPLICADO_', phone)
WHERE id IN (
    SELECT id FROM (
        SELECT c1.id
        FROM customers c1
        INNER JOIN customers c2 ON REPLACE(REPLACE(REPLACE(c1.phone, '+', ''), '-', ''), ' ', '') = REPLACE(REPLACE(REPLACE(c2.phone, '+', ''), '-', ''), ' ', '')
        WHERE c1.id > c2.id
        AND c1.phone REGEXP '^[+]?57?3[0-9]{9}$'
    ) AS dups
);

-- Mostrar resultado
SELECT id, name, phone FROM customers WHERE phone LIKE '%DUPLICADO%' OR phone NOT REGEXP '^[+]?57[0-9]{10}$';
EOF

echo -e "${GREEN}✅ Base de datos limpiada${NC}"
echo ""

# 4. Mostrar clientes válidos
echo -e "${YELLOW}4️⃣  Clientes con números válidos:${NC}"
mysql -u root pos_sistema -e "SELECT id, name, phone FROM customers WHERE phone REGEXP '^[+]57[0-9]{10}$' OR phone REGEXP '^57[0-9]{10}$' ORDER BY id;"
echo ""

# 5. Iniciar servidor WhatsApp
echo -e "${YELLOW}5️⃣  Iniciando servidor WhatsApp...${NC}"
cd /home/kali/Escritorio/definitivo/01_POS_BASICO\ \(2\)/backend

# Iniciar en background
nohup node whatsapp-server.js > whatsapp-server.log 2>&1 &
WHATSAPP_PID=$!

echo -e "${GREEN}✅ Servidor iniciado (PID: $WHATSAPP_PID)${NC}"
echo ""

# 6. Esperar a que el servidor esté listo
echo -e "${YELLOW}6️⃣  Esperando conexión de WhatsApp...${NC}"
sleep 3

# 7. Verificar estado
echo -e "${YELLOW}7️⃣  Verificando estado...${NC}"
STATUS=$(curl -s http://localhost:3002/status 2>/dev/null || echo "error")

if [[ "$STATUS" == "error" ]]; then
    echo -e "${RED}❌ El servidor no responde${NC}"
    echo -e "${YELLOW}⚠️  Verifica el log: tail -f whatsapp-server.log${NC}"
else
    echo -e "${GREEN}✅ Servidor funcionando${NC}"
    echo ""
    echo -e "${BLUE}📱 IMPORTANTE: Debes escanear el código QR de WhatsApp${NC}"
    echo ""
    echo -e "${YELLOW}Ejecuta uno de estos comandos:${NC}"
    echo -e "  ${GREEN}1)${NC} tail -f whatsapp-server.log    ${BLUE}# Ver código QR en el log${NC}"
    echo -e "  ${GREEN}2)${NC} cat whatsapp_qr.txt            ${BLUE}# Ver código QR en texto${NC}"
    echo ""
fi

echo -e "${BLUE}ℹ️  Verificar estado: curl http://localhost:3002/status${NC}"
echo ""
echo -e "${GREEN}✅ Proceso completado${NC}"
echo ""
