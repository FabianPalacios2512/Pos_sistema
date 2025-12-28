#!/bin/bash

# Script para arreglar la conexión de base de datos en el VPS
# Ejecutar este script dentro de la carpeta 'backend' del proyecto en el VPS

# 1. Configuración de credenciales seguras
DB_USER="pos_admin"
DB_PASS="PosSistema_2025_Secure"

echo "🛠️  Iniciando reparación de base de datos..."

# 2. Crear usuario MySQL y dar permisos
# Usamos sudo mysql para entrar como root del sistema (auth_socket)
echo "👤 Creando usuario de base de datos '${DB_USER}'..."
sudo mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON *.* TO '${DB_USER}'@'localhost' WITH GRANT OPTION;"
sudo mysql -e "FLUSH PRIVILEGES;"

if [ $? -eq 0 ]; then
    echo "✅ Usuario MySQL creado/actualizado correctamente."
else
    echo "❌ Error al crear usuario MySQL. Verifica que MySQL esté corriendo."
    exit 1
fi

# 3. Actualizar archivo .env
if [ -f ".env" ]; then
    echo "📝 Actualizando archivo .env..."
    
    # Backup por seguridad
    cp .env .env.backup_$(date +%s)
    
    # Reemplazar DB_USERNAME
    # Usamos sed con un delimitador diferente (#) para evitar problemas
    sed -i "s/^DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" .env
    
    # Reemplazar DB_PASSWORD
    sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${DB_PASS}/" .env
    
    echo "✅ Archivo .env actualizado."
else
    echo "❌ No se encontró el archivo .env. Asegúrate de estar en la carpeta 'backend'."
    exit 1
fi

# 4. Limpiar caché de Laravel
echo "🧹 Limpiando caché de Laravel..."
php artisan config:clear
php artisan cache:clear

echo "🎉 ¡Listo! La conexión a la base de datos debería funcionar ahora."
echo "👉 Prueba nuevamente la página o el endpoint."
