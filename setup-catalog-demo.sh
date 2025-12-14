#!/bin/bash

# Script para configurar productos públicos de prueba en el catálogo online

echo "🛒 Configurando Catálogo Online - Datos de Prueba"
echo "=================================================="

# Base de datos del tenant
DB_NAME="tenantventa-de-gorras"

# 1. Marcar productos existentes como públicos
echo ""
echo "✅ Paso 1: Activando productos en el catálogo..."
mysql -u root $DB_NAME << 'SQL'
UPDATE products 
SET 
  is_public = TRUE,
  public_description = CONCAT('¡Oferta especial! ', description),
  public_image = image_url
WHERE 
  active = TRUE 
  AND current_stock > 0
LIMIT 20;
SQL

if [ $? -eq 0 ]; then
    echo "   ✔ Productos activados correctamente"
else
    echo "   ✖ Error al activar productos"
    exit 1
fi

# 2. Verificar productos públicos
echo ""
echo "✅ Paso 2: Verificando productos públicos..."
PRODUCT_COUNT=$(mysql -u root $DB_NAME -sN -e "SELECT COUNT(*) FROM products WHERE is_public = TRUE AND active = TRUE AND current_stock > 0;")
echo "   ✔ $PRODUCT_COUNT productos disponibles en el catálogo"

# 3. Mostrar algunos productos de ejemplo
echo ""
echo "✅ Paso 3: Productos de ejemplo en el catálogo:"
mysql -u root $DB_NAME << 'SQL'
SELECT 
  name AS 'Nombre',
  sale_price AS 'Precio',
  current_stock AS 'Stock',
  is_public AS 'Público'
FROM products 
WHERE is_public = TRUE AND active = TRUE 
LIMIT 5;
SQL

# 4. Verificar tablas del catálogo
echo ""
echo "✅ Paso 4: Verificando tablas del catálogo online..."
ORDERS_TABLE=$(mysql -u root $DB_NAME -sN -e "SHOW TABLES LIKE 'online_orders';")
ITEMS_TABLE=$(mysql -u root $DB_NAME -sN -e "SHOW TABLES LIKE 'online_order_items';")

if [ -n "$ORDERS_TABLE" ]; then
    echo "   ✔ Tabla 'online_orders' existe"
else
    echo "   ✖ Tabla 'online_orders' NO existe"
fi

if [ -n "$ITEMS_TABLE" ]; then
    echo "   ✔ Tabla 'online_order_items' existe"
else
    echo "   ✖ Tabla 'online_order_items' NO existe"
fi

# 5. Mostrar información de configuración
echo ""
echo "=================================================="
echo "🎉 Configuración Completada"
echo "=================================================="
echo ""
echo "📋 Información importante:"
echo "   • Productos públicos: $PRODUCT_COUNT"
echo "   • Base de datos: $DB_NAME"
echo "   • URL del catálogo: http://localhost:5173/catalog"
echo ""
echo "🚀 Próximos pasos:"
echo "   1. Iniciar el frontend: npm run dev"
echo "   2. Abrir el navegador en la URL del catálogo"
echo "   3. Probar agregar productos al carrito"
echo "   4. Completar un pedido de prueba"
echo ""
echo "📝 Nota: El enlace de WhatsApp usará un número de prueba"
echo "   Actualiza 'whatsapp_business_phone' en PublicCatalogController.php"
echo ""
