#!/bin/bash

# Script para iniciar el sistema POS con WhatsApp
# Autor: Sistema POS
# Fecha: $(date)

echo "🚀 Iniciando Sistema POS con WhatsApp..."
echo "======================================"

# Directorios
BACKEND_DIR="/home/kali/Escritorio/definitivo/01_POS_BASICO (2)/backend"
FRONTEND_DIR="/home/kali/Escritorio/definitivo/01_POS_BASICO (2)"

# Función para verificar si un puerto está ocupado
check_port() {
    local port=$1
    if lsof -i:$port >/dev/null 2>&1; then
        echo "✅ Puerto $port ya está en uso"
        return 0
    else
        echo "❌ Puerto $port disponible"
        return 1
    fi
}

# Función para iniciar WhatsApp si no está corriendo
start_whatsapp() {
    echo "📱 Verificando servidor WhatsApp..."
    
    if check_port 3002; then
        echo "✅ Servidor WhatsApp ya está ejecutándose en puerto 3002"
    else
        echo "🚀 Iniciando servidor WhatsApp..."
        cd "$BACKEND_DIR"
        
        # Verificar si node_modules existe
        if [ ! -d "node_modules" ]; then
            echo "📦 Instalando dependencias de Node.js..."
            npm install
        fi
        
        # Iniciar servidor multi-tenant en segundo plano
        nohup node whatsapp-server-multitenant.js > whatsapp-multitenant.log 2>&1 &
        WHATSAPP_PID=$!
        echo "📱 Servidor WhatsApp iniciado con PID: $WHATSAPP_PID"
        
        # Guardar PID para poder cerrarlo después
        echo $WHATSAPP_PID > whatsapp.pid
        
        # Esperar un momento para que se inicie
        sleep 3
        
        # Verificar que se inició correctamente
        if check_port 3002; then
            echo "✅ Servidor WhatsApp iniciado exitosamente"
        else
            echo "❌ Error iniciando servidor WhatsApp"
        fi
    fi
}

# Función para iniciar servidor PHP
start_php() {
    echo "🐘 Verificando servidor PHP..."
    
    if check_port 8000; then
        echo "✅ Servidor PHP ya está ejecutándose en puerto 8000"
    else
        echo "🚀 Iniciando servidor PHP..."
        cd "$BACKEND_DIR"
        
        # Iniciar servidor PHP en segundo plano
        nohup php artisan serve --host=0.0.0.0 --port=8000 > laravel.log 2>&1 &
        PHP_PID=$!
        echo "🐘 Servidor PHP iniciado con PID: $PHP_PID"
        
        # Guardar PID
        echo $PHP_PID > laravel.pid
        
        # Esperar un momento para que se inicie
        sleep 3
        
        # Verificar que se inició correctamente
        if check_port 8000; then
            echo "✅ Servidor PHP iniciado exitosamente"
        else
            echo "❌ Error iniciando servidor PHP"
        fi
    fi
}

# Función para iniciar servidor de desarrollo (Vite)
start_vite() {
    echo "⚡ Verificando servidor Vite..."
    
    if check_port 3000; then
        echo "✅ Servidor Vite ya está ejecutándose en puerto 3000"
    else
        echo "🚀 Iniciando servidor Vite..."
        cd "$FRONTEND_DIR"
        
        # Verificar si node_modules existe
        if [ ! -d "node_modules" ]; then
            echo "📦 Instalando dependencias del frontend..."
            npm install
        fi
        
        # 🔧 CRÍTICO: Limpiar caché de Vite para evitar errores 504 (Outdated Optimize Dep)
        echo "🧹 Limpiando caché de Vite para evitar errores..."
        rm -rf "$FRONTEND_DIR/node_modules/.vite"
        echo "✅ Caché de Vite limpiado"
        
        # Iniciar servidor en segundo plano
        nohup npm run dev > vite.log 2>&1 &
        VITE_PID=$!
        echo "⚡ Servidor Vite iniciado con PID: $VITE_PID"
        
        # Guardar PID
        echo $VITE_PID > vite.pid
        
        # Esperar un momento para que se inicie
        sleep 5
        
        # Verificar que se inició correctamente
        if check_port 3000; then
            echo "✅ Servidor Vite iniciado exitosamente"
        else
            echo "❌ Error iniciando servidor Vite"
        fi
    fi
}

# Función principal
main() {
    echo "🔧 Verificando servicios del Sistema POS..."
    echo ""
    
    # Cambiar al directorio del backend
    cd "$BACKEND_DIR"
    
    # Iniciar servicios en orden
    start_whatsapp
    echo ""
    start_php
    echo ""
    
    # Si se pasa el argumento --with-frontend, iniciar Vite también
    if [ "$1" = "--with-frontend" ]; then
        start_vite
        echo ""
    fi
    
    echo "======================================"
    echo "✅ Sistema POS iniciado correctamente!"
    echo ""
    echo "📊 URLs del sistema:"
    echo "   Backend (API): http://localhost:8000"
    echo "   WhatsApp API:  http://localhost:3002"
    if [ "$1" = "--with-frontend" ]; then
        echo "   Frontend:      http://localhost:3000"
    fi
    echo ""
    echo "📱 Estado de WhatsApp:"
    echo "   - Si es la primera vez, escanea el QR desde los logs"
    echo "   - Si ya está conectado, debería conectar automáticamente"
    echo ""
    echo "📝 Logs disponibles:"
    echo "   - WhatsApp: $BACKEND_DIR/whatsapp-multitenant.log"
    echo "   - Laravel:  $BACKEND_DIR/laravel.log"
    if [ "$1" = "--with-frontend" ]; then
        echo "   - Vite:     $FRONTEND_DIR/vite.log"
    fi
    echo ""
    echo "🛑 Para detener todos los servicios: ./stop-pos.sh"
}

# Ejecutar función principal con argumentos
main "$@"