#!/bin/bash

# Script para detener el sistema POS
# Autor: Sistema POS
# Fecha: $(date)

echo "🛑 Deteniendo Sistema POS..."
echo "=============================="

# Directorios
BACKEND_DIR="/home/martin/Escritorio/pos/01_POS_BASICO (2)/01_POS_BASICO/backend"
FRONTEND_DIR="/home/martin/Escritorio/pos/01_POS_BASICO (2)/01_POS_BASICO"

# Función para detener proceso por PID
stop_by_pid() {
    local service_name=$1
    local pid_file=$2
    
    if [ -f "$pid_file" ]; then
        local pid=$(cat "$pid_file")
        if kill -0 "$pid" 2>/dev/null; then
            echo "🛑 Deteniendo $service_name (PID: $pid)..."
            kill "$pid"
            sleep 2
            
            # Verificar si el proceso sigue corriendo
            if kill -0 "$pid" 2>/dev/null; then
                echo "⚠️  Forzando cierre de $service_name..."
                kill -9 "$pid"
            fi
            
            echo "✅ $service_name detenido"
        else
            echo "⚠️  $service_name ya estaba detenido"
        fi
        rm -f "$pid_file"
    else
        echo "📝 No se encontró archivo PID para $service_name"
    fi
}

# Función para detener por puerto
stop_by_port() {
    local service_name=$1
    local port=$2
    
    local pid=$(lsof -t -i:$port 2>/dev/null)
    if [ -n "$pid" ]; then
        echo "🛑 Deteniendo $service_name en puerto $port (PID: $pid)..."
        kill "$pid"
        sleep 2
        
        # Verificar si el proceso sigue corriendo
        local check_pid=$(lsof -t -i:$port 2>/dev/null)
        if [ -n "$check_pid" ]; then
            echo "⚠️  Forzando cierre de $service_name..."
            kill -9 "$check_pid"
        fi
        
        echo "✅ $service_name detenido"
    else
        echo "✅ $service_name ya estaba detenido"
    fi
}

# Función principal
main() {
    # Cambiar al directorio del backend
    cd "$BACKEND_DIR"
    
    echo "🔍 Buscando procesos activos..."
    echo ""
    
    # Detener WhatsApp
    echo "📱 Deteniendo WhatsApp..."
    stop_by_pid "WhatsApp Server" "whatsapp.pid"
    stop_by_port "WhatsApp Server" "3002"
    echo ""
    
    # Detener Laravel
    echo "🐘 Deteniendo Laravel..."
    stop_by_pid "Laravel Server" "laravel.pid"
    stop_by_port "Laravel Server" "8000"
    echo ""
    
    # Detener Vite (si está corriendo)
    echo "⚡ Deteniendo Vite..."
    cd "$FRONTEND_DIR"
    stop_by_pid "Vite Server" "vite.pid"
    stop_by_port "Vite Server" "5173"
    echo ""
    
    # Limpiar procesos node que puedan haber quedado
    echo "🧹 Limpiando procesos residuales..."
    
    # Buscar procesos node relacionados con nuestros servicios
    local whatsapp_processes=$(ps aux | grep "whatsapp-server.js" | grep -v grep | awk '{print $2}')
    local vite_processes=$(ps aux | grep "vite" | grep -v grep | awk '{print $2}')
    
    if [ -n "$whatsapp_processes" ]; then
        echo "🔧 Eliminando procesos WhatsApp residuales..."
        echo "$whatsapp_processes" | xargs kill -9 2>/dev/null || true
    fi
    
    if [ -n "$vite_processes" ]; then
        echo "🔧 Eliminando procesos Vite residuales..."
        echo "$vite_processes" | xargs kill -9 2>/dev/null || true
    fi
    
    echo "=============================="
    echo "✅ Sistema POS detenido completamente!"
    echo ""
    echo "📊 Puertos liberados:"
    echo "   - 3002 (WhatsApp API)"
    echo "   - 8000 (Laravel API)"
    echo "   - 5173 (Vite Frontend)"
    echo ""
    echo "🚀 Para reiniciar: ./start-pos.sh"
}

# Ejecutar función principal
main