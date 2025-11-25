#!/bin/bash

# Script de prueba para validar las mejoras de 105 IA
# Fecha: 25 de noviembre de 2025

echo "🤖 ====================================="
echo "   PRUEBAS DE 105 IA MEJORADA"
echo "====================================="
echo ""

# Colores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}📋 CASOS DE PRUEBA SUGERIDOS:${NC}"
echo ""

echo "1️⃣  NAVEGACIÓN AUTOMÁTICA:"
echo -e "${YELLOW}   Pregunta: 'Créame un producto por favor'${NC}"
echo "   ✅ Debe navegar automáticamente a creación de productos"
echo ""

echo "2️⃣  CONTEXTO Y CONFIRMACIÓN:"
echo -e "${YELLOW}   Pregunta: '¿Puedo vender sin caja abierta?'${NC}"
echo "   ✅ Debe responder que no y ofrecer abrir caja"
echo -e "${YELLOW}   Sigue con: 'Sí'${NC}"
echo "   ✅ Debe navegar al POS automáticamente"
echo ""

echo "3️⃣  LISTA DE CLIENTES:"
echo -e "${YELLOW}   Pregunta: '¿Cuántos clientes tengo?'${NC}"
echo "   ✅ Debe mostrar número total Y lista de clientes recientes"
echo ""

echo "4️⃣  EJEMPLOS HIPOTÉTICOS:"
echo -e "${YELLOW}   Pregunta: 'Si vendo 1 millón y me devuelven 500 mil, ¿cuánto queda?'${NC}"
echo "   ✅ Debe responder el cálculo: $500,000 como balance"
echo ""

echo "5️⃣  VENTAS PASADAS:"
echo -e "${YELLOW}   Pregunta: 'Hace tres días las ventas fueron de cuánto?'${NC}"
echo "   ✅ Debe explicar qué datos tiene disponibles y ofrecer alternativas"
echo ""

echo "6️⃣  BÚSQUEDA DE CLIENTE:"
echo -e "${YELLOW}   Pregunta: '¿Existe un cliente con teléfono 3134540533?'${NC}"
echo "   ✅ Debe buscar en la lista y ofrecer ir a clientes si no lo encuentra"
echo ""

echo "7️⃣  ANÁLISIS DE VENTAS:"
echo -e "${YELLOW}   Pregunta: '¿Cómo van las ventas hoy?'${NC}"
echo "   ✅ Debe mostrar total, comparar con ayer y top productos"
echo ""

echo "8️⃣  CÓDIGOS PROMOCIONALES:"
echo -e "${YELLOW}   Pregunta: 'Créame un código promocional de 20%'${NC}"
echo "   ✅ Debe explicar que no puede pero ofrecer ir a configuración"
echo ""

echo ""
echo -e "${GREEN}✅ PARA PROBAR:${NC}"
echo "1. Abre el sistema POS en tu navegador"
echo "2. Haz clic en el botón de IA (botón morado flotante)"
echo "3. Prueba cada uno de los casos de arriba"
echo "4. Verifica que las respuestas sean inteligentes y navegue cuando deba"
echo ""

echo -e "${BLUE}🔧 VERIFICAR CONFIGURACIÓN:${NC}"
echo "1. El archivo .env debe tener GROQ_API_KEY configurada"
echo "2. El backend debe estar corriendo (php artisan serve)"
echo "3. El frontend debe estar corriendo (npm run dev)"
echo ""

echo -e "${GREEN}📊 DATOS ADICIONALES QUE AHORA TIENE:${NC}"
echo "✅ Lista de 10 clientes recientes (nombre, teléfono, email, documento)"
echo "✅ Lista de categorías activas"
echo "✅ Ventas de últimos 7 días con nombres de días"
echo "✅ Estado de cajas abiertas/cerradas"
echo "✅ Productos agotados y stock bajo"
echo "✅ Top 5 productos más vendidos hoy con ingresos"
echo "✅ Total de proveedores activos"
echo "✅ Fecha y hora actual del sistema"
echo ""

echo "====================================="
echo "🚀 ¡LA IA ESTÁ LISTA PARA PROBAR!"
echo "====================================="
