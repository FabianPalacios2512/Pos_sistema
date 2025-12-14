#!/bin/bash
# 🚀 Deploy Rápido Incremental - Solo archivos modificados
# Uso: ./deploy-fast.sh

set -e  # Exit on error

echo "🔨 Compilando frontend..."
npm run build

echo ""
echo "🔍 Detectando archivos modificados..."

# Crear directorio temporal para archivos modificados
TEMP_DIR="/tmp/pos_deploy_$(date +%s)"
mkdir -p "$TEMP_DIR"

# Comparar con el último deploy (usando checksums)
LAST_DEPLOY_CHECKSUMS="deploy_checksums.txt"
CURRENT_CHECKSUMS="deploy_checksums_new.txt"

# Generar checksums actuales
find dist -type f -exec md5sum {} \; | sort > "$CURRENT_CHECKSUMS"

if [ -f "$LAST_DEPLOY_CHECKSUMS" ]; then
    # Encontrar archivos modificados/nuevos
    echo "📊 Comparando con deploy anterior..."
    
    MODIFIED_FILES=$(comm -13 <(cut -d' ' -f1 "$LAST_DEPLOY_CHECKSUMS" | sort) <(cut -d' ' -f1 "$CURRENT_CHECKSUMS" | sort) | wc -l)
    
    if [ "$MODIFIED_FILES" -eq 0 ]; then
        echo "✅ No hay cambios para deployar"
        rm -rf "$TEMP_DIR"
        exit 0
    fi
    
    echo "📦 Archivos modificados/nuevos: $MODIFIED_FILES"
    
    # Copiar solo archivos modificados
    while IFS= read -r line; do
        checksum=$(echo "$line" | cut -d' ' -f1)
        filepath=$(echo "$line" | cut -d' ' -f3-)
        
        # Verificar si el checksum cambió o es nuevo
        if ! grep -q "^$checksum " "$LAST_DEPLOY_CHECKSUMS" 2>/dev/null; then
            rel_path="${filepath#dist/}"
            target_dir="$TEMP_DIR/$(dirname "$rel_path")"
            mkdir -p "$target_dir"
            cp "$filepath" "$target_dir/"
            echo "  ✓ $rel_path"
        fi
    done < "$CURRENT_CHECKSUMS"
else
    echo "⚠️  Primer deploy - subiendo todos los archivos"
    cp -r dist/* "$TEMP_DIR/"
fi

# Contar archivos a subir
FILE_COUNT=$(find "$TEMP_DIR" -type f | wc -l)

if [ "$FILE_COUNT" -eq 0 ]; then
    echo "✅ No hay archivos para subir"
    rm -rf "$TEMP_DIR"
    exit 0
fi

echo ""
echo "📤 Subiendo $FILE_COUNT archivos al servidor..."

# Subir solo los archivos modificados
rsync -avz --progress "$TEMP_DIR/" root@72.61.73.245:/tmp/dist_incremental/

echo ""
echo "🐋 Copiando archivos al contenedor Docker..."

ssh root@72.61.73.245 "docker cp /tmp/dist_incremental/. pos_frontend:/usr/share/nginx/html/"

# Limpiar temporal en servidor
ssh root@72.61.73.245 "rm -rf /tmp/dist_incremental"

# Guardar checksums para próximo deploy
mv "$CURRENT_CHECKSUMS" "$LAST_DEPLOY_CHECKSUMS"

# Limpiar temporal local
rm -rf "$TEMP_DIR"

echo ""
echo "✅ Deploy completado exitosamente!"
echo "🌐 https://105pos.pro"
echo ""
echo "📊 Estadísticas:"
echo "   - Archivos subidos: $FILE_COUNT"
echo "   - Tiempo ahorrado: ~$(($FILE_COUNT * 2))s vs deploy completo"
