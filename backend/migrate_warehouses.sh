#!/bin/bash

# Script para ejecutar migración de warehouses en todos los tenants

echo "🏪 Ejecutando migración de warehouses en todos los tenants..."
echo ""

# Obtener lista de bases de datos tenant
TENANTS=$(mysql -u root -N -e "SHOW DATABASES LIKE 'tenant%'")

SUCCESS_COUNT=0
ERROR_COUNT=0

for TENANT_DB in $TENANTS; do
    echo "📦 Procesando: $TENANT_DB"

    # Verificar si ya existe la tabla warehouses
    HAS_WAREHOUSES=$(mysql -u root "$TENANT_DB" -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$TENANT_DB' AND table_name = 'warehouses'")

    if [ "$HAS_WAREHOUSES" -gt 0 ]; then
        echo "  ⏭️  Ya tiene tabla warehouses, omitiendo..."
        ((SUCCESS_COUNT++))
        echo ""
        continue
    fi

    # Ejecutar migración
    mysql -u root "$TENANT_DB" << 'EOF'

-- 1. Crear tabla warehouses
CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` text,
  `phone` varchar(255),
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouses_is_default_index` (`is_default`),
  KEY `warehouses_active_index` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear bodega principal
INSERT INTO `warehouses` (`name`, `is_default`, `active`, `created_at`, `updated_at`)
VALUES ('Sede Principal', 1, 1, NOW(), NOW());

-- 2. Crear tabla product_warehouse
CREATE TABLE IF NOT EXISTS `product_warehouse` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_warehouse_product_id_warehouse_id_unique` (`product_id`,`warehouse_id`),
  KEY `product_warehouse_warehouse_id_index` (`warehouse_id`),
  KEY `product_warehouse_stock_index` (`stock`),
  CONSTRAINT `product_warehouse_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_warehouse_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Migrar stock existente (intentar con ambos nombres de columna)
INSERT INTO `product_warehouse` (`product_id`, `warehouse_id`, `stock`, `created_at`, `updated_at`)
SELECT p.id, w.id, COALESCE(IFNULL(p.stock, p.current_stock), 0), NOW(), NOW()
FROM `products` p
CROSS JOIN `warehouses` w
WHERE w.is_default = 1
ON DUPLICATE KEY UPDATE stock = VALUES(stock);

-- 4. Agregar warehouse_id a cash_sessions si existe la tabla
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables
                     WHERE table_schema = DATABASE() AND table_name = 'cash_sessions');

SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns
                      WHERE table_schema = DATABASE()
                      AND table_name = 'cash_sessions'
                      AND column_name = 'warehouse_id');

SET @sql = IF(@table_exists > 0 AND @column_exists = 0,
    'ALTER TABLE `cash_sessions`
     ADD COLUMN `warehouse_id` bigint unsigned NULL AFTER `user_id`,
     ADD KEY `cash_sessions_warehouse_id_index` (`warehouse_id`),
     ADD CONSTRAINT `cash_sessions_warehouse_id_foreign`
     FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL',
    'SELECT "Column already exists or table does not exist" AS status');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5. Actualizar sesiones existentes con bodega por defecto
UPDATE `cash_sessions` cs
CROSS JOIN `warehouses` w
SET cs.warehouse_id = w.id
WHERE cs.warehouse_id IS NULL AND w.is_default = 1;

EOF

    if [ $? -eq 0 ]; then
        echo "  ✅ $TENANT_DB completado exitosamente"
        ((SUCCESS_COUNT++))
    else
        echo "  ❌ Error en $TENANT_DB"
        ((ERROR_COUNT++))
    fi

    echo ""
done

echo "=============================================================="
echo "  RESUMEN"
echo "=============================================================="
echo "✅ Exitosos: $SUCCESS_COUNT"
echo "❌ Errores: $ERROR_COUNT"
echo ""
echo "✨ Proceso completado!"
