# =============================================================================
# Dockerfile para Frontend - Build Vue + Nginx
# =============================================================================

# ETAPA 1: Build de Vue.js
FROM node:20-alpine AS builder

WORKDIR /app

# Copiar package.json y package-lock.json
COPY package*.json ./

# Instalar dependencias
RUN npm ci --silent

# Copiar código fuente
COPY . .

# Build de producción
RUN npm run build

# ETAPA 2: Servir con Nginx
FROM nginx:alpine

# Copiar build de Vue desde etapa anterior
COPY --from=builder /app/dist /usr/share/nginx/html

# Copiar configuración personalizada de Nginx
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Exponer puerto 80
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
