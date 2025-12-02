#!/bin/bash

# Script de inicio automático para WhatsApp Multi-tenant
# Este script inicia el servidor de WhatsApp en segundo plano

# Colores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Directorio del proyecto
PROJECT_DIR="/home/kali/Escritorio/definitivo/01_POS_BASICO (2)/backend"
LOG_DIR="$PROJECT_DIR/storage/logs"
PID_FILE="$PROJECT_DIR/whatsapp.pid"

# Crear directorio de logs si no existe
mkdir -p "$LOG_DIR"

# Función para verificar si el servicio está corriendo
is_running() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        if ps -p $PID > /dev/null 2>&1; then
            return 0
        fi
    fi
    return 1
}

# Función para detener el servicio
stop_service() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        if ps -p $PID > /dev/null 2>&1; then
            echo -e "${YELLOW}⏹️  Deteniendo servicio WhatsApp (PID: $PID)...${NC}"
            kill $PID
            sleep 2
            if ps -p $PID > /dev/null 2>&1; then
                echo -e "${RED}⚠️  Forzando detención...${NC}"
                kill -9 $PID
            fi
            rm -f "$PID_FILE"
            echo -e "${GREEN}✅ Servicio detenido${NC}"
        else
            rm -f "$PID_FILE"
        fi
    fi
}

# Función para iniciar el servicio
start_service() {
    cd "$PROJECT_DIR"
    
    echo -e "${GREEN}🚀 Iniciando servicio WhatsApp Multi-tenant...${NC}"
    
    # Iniciar el servidor de WhatsApp en segundo plano
    nohup node whatsapp-server-multitenant.js > "$LOG_DIR/whatsapp.log" 2>&1 &
    
    # Guardar el PID
    echo $! > "$PID_FILE"
    
    sleep 3
    
    # Verificar que se inició correctamente
    if is_running; then
        PID=$(cat "$PID_FILE")
        echo -e "${GREEN}✅ Servicio WhatsApp iniciado correctamente (PID: $PID)${NC}"
        echo -e "${GREEN}📋 Logs: $LOG_DIR/whatsapp.log${NC}"
        return 0
    else
        echo -e "${RED}❌ Error al iniciar el servicio${NC}"
        echo -e "${YELLOW}📋 Revisa los logs: tail -f $LOG_DIR/whatsapp.log${NC}"
        return 1
    fi
}

# Comando principal
case "$1" in
    start)
        if is_running; then
            echo -e "${YELLOW}⚠️  El servicio ya está corriendo${NC}"
            PID=$(cat "$PID_FILE")
            echo -e "${GREEN}PID: $PID${NC}"
        else
            start_service
        fi
        ;;
    stop)
        stop_service
        ;;
    restart)
        echo -e "${YELLOW}🔄 Reiniciando servicio...${NC}"
        stop_service
        sleep 2
        start_service
        ;;
    status)
        if is_running; then
            PID=$(cat "$PID_FILE")
            echo -e "${GREEN}✅ Servicio corriendo (PID: $PID)${NC}"
        else
            echo -e "${RED}❌ Servicio detenido${NC}"
        fi
        ;;
    *)
        # Si no se especifica comando, iniciar el servicio
        if is_running; then
            echo -e "${GREEN}✅ Servicio WhatsApp ya está corriendo${NC}"
        else
            start_service
        fi
        ;;
esac

exit 0
