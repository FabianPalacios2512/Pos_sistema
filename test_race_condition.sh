#!/bin/bash

# ═══════════════════════════════════════════════════════════════
# 🧪 TEST DE RACE CONDITION - FIXES IMPLEMENTADOS
# ═══════════════════════════════════════════════════════════════
# Este script valida que los locks implementados previenen
# race conditions en ventas simultáneas
# ═══════════════════════════════════════════════════════════════

echo "🧪 INICIANDO TEST DE RACE CONDITION"
echo "══════════════════════════════════════════════════════════"

# Configuración
API_URL="http://las-nanas.localhost:3000/api/invoices/pos"
PRODUCT_ID=1
PRODUCT_NAME="CAMISA POLO"
QUANTITY=1
PRICE=10000
NUM_REQUESTS=10

# Obtener stock inicial
echo ""
echo "📊 Stock ANTES del test:"
mysql -u root tenantlas_nanas -e "
SELECT 
    p.id, 
    p.name, 
    pw.warehouse_id, 
    w.name as warehouse, 
    pw.stock 
FROM products p 
JOIN product_warehouse pw ON p.id = pw.product_id 
JOIN warehouses w ON pw.warehouse_id = w.id 
WHERE p.id = $PRODUCT_ID;
"

STOCK_INICIAL=$(mysql -u root tenantlas_nanas -sN -e "SELECT stock FROM product_warehouse WHERE product_id=$PRODUCT_ID AND warehouse_id=1;")
echo "Stock inicial: $STOCK_INICIAL unidades"
echo ""

# Ejecutar ventas simultáneas
echo "🚀 Ejecutando $NUM_REQUESTS ventas simultáneas..."
echo "══════════════════════════════════════════════════════════"

for i in $(seq 1 $NUM_REQUESTS); do
  echo "Venta #$i iniciada..."
  curl -s -X POST "$API_URL" \
    -H "Content-Type: application/json" \
    -d '{
      "type":"invoice",
      "customer_id":1,
      "items":[{
        "product_id":'$PRODUCT_ID',
        "product_name":"'$PRODUCT_NAME'",
        "product_sku":"SKU-TEST",
        "quantity":'$QUANTITY',
        "unit_price":'$PRICE',
        "cost_price":5000,
        "discount_amount":0,
        "tax_amount":0,
        "notes":null
      }],
      "subtotal":'$PRICE',
      "tax_amount":0,
      "total":'$PRICE',
      "date":"2025-12-24",
      "payment_method":"cash"
    }' > /tmp/race_test_$i.json 2>&1 &
done

echo ""
echo "⏳ Esperando que terminen todas las requests..."
wait
echo "✅ Todas las requests completadas"
echo ""

# Analizar resultados
echo "📊 RESULTADOS:"
echo "══════════════════════════════════════════════════════════"

SUCCESS_COUNT=0
ERROR_COUNT=0

for i in $(seq 1 $NUM_REQUESTS); do
  if grep -q '"success":true' /tmp/race_test_$i.json; then
    SUCCESS_COUNT=$((SUCCESS_COUNT + 1))
    echo "✅ Venta #$i: EXITOSA"
  else
    ERROR_COUNT=$((ERROR_COUNT + 1))
    ERROR_MSG=$(grep -o '"message":"[^"]*"' /tmp/race_test_$i.json | head -1)
    echo "❌ Venta #$i: ERROR - $ERROR_MSG"
  fi
done

echo ""
echo "📊 Stock DESPUÉS del test:"
mysql -u root tenantlas_nanas -e "
SELECT 
    p.id, 
    p.name, 
    pw.warehouse_id, 
    w.name as warehouse, 
    pw.stock 
FROM products p 
JOIN product_warehouse pw ON p.id = pw.product_id 
JOIN warehouses w ON pw.warehouse_id = w.id 
WHERE p.id = $PRODUCT_ID;
"

STOCK_FINAL=$(mysql -u root tenantlas_nanas -sN -e "SELECT stock FROM product_warehouse WHERE product_id=$PRODUCT_ID AND warehouse_id=1;")
echo "Stock final: $STOCK_FINAL unidades"
echo ""

# Validación matemática
echo "══════════════════════════════════════════════════════════"
echo "🧮 VALIDACIÓN MATEMÁTICA:"
echo "══════════════════════════════════════════════════════════"
echo "Stock inicial:       $STOCK_INICIAL unidades"
echo "Ventas exitosas:     $SUCCESS_COUNT unidades"
echo "Stock esperado:      $((STOCK_INICIAL - SUCCESS_COUNT)) unidades"
echo "Stock real final:    $STOCK_FINAL unidades"
echo "Ventas rechazadas:   $ERROR_COUNT"
echo ""

# Verificar integridad
STOCK_ESPERADO=$((STOCK_INICIAL - SUCCESS_COUNT))

if [ $STOCK_FINAL -eq $STOCK_ESPERADO ]; then
  echo "✅ ¡INTEGRIDAD CORRECTA! El stock coincide con lo esperado."
  EXIT_CODE=0
elif [ $STOCK_FINAL -lt 0 ]; then
  echo "❌ ¡FALLO CRÍTICO! Stock negativo detectado ($STOCK_FINAL)"
  echo "🐛 BUG-001 NO ESTÁ CORREGIDO CORRECTAMENTE"
  EXIT_CODE=1
else
  echo "⚠️  Stock final ($STOCK_FINAL) NO coincide con esperado ($STOCK_ESPERADO)"
  echo "Diferencia: $((STOCK_FINAL - STOCK_ESPERADO)) unidades"
  EXIT_CODE=1
fi

# Verificar facturas creadas
echo ""
echo "📝 Facturas creadas en los últimos 5 minutos:"
mysql -u root tenantlas_nanas -e "
SELECT 
    id, 
    number, 
    total, 
    DATE_FORMAT(created_at, '%H:%i:%s') as hora 
FROM invoices 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) 
ORDER BY id DESC 
LIMIT $NUM_REQUESTS;
"

# Limpiar archivos temporales
rm -f /tmp/race_test_*.json

echo ""
echo "══════════════════════════════════════════════════════════"
if [ $EXIT_CODE -eq 0 ]; then
  echo "✅ TEST COMPLETADO EXITOSAMENTE"
  echo "🔒 Los locks están funcionando correctamente"
else
  echo "❌ TEST FALLÓ - REVISAR IMPLEMENTACIÓN"
fi
echo "══════════════════════════════════════════════════════════"

exit $EXIT_CODE
