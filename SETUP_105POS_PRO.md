# 🚀 Configuración Final para 105pos.pro

## ✅ Estado Actual

**Backend y Frontend desplegados en**: `/var/www/105pos/`
**Nginx configurado para**: `105pos.pro` y `www.105pos.pro`
**SSL**: Pendiente (requiere que DNS apunte correctamente)

---

## 📋 PASOS PARA COMPLETAR EL DEPLOYMENT

### **1️⃣ CONFIGURAR DNS (URGENTE)**

Ve al panel de tu proveedor de dominios y configura:

| Tipo | Nombre | Valor        | TTL  |
|------|--------|--------------|------|
| A    | @      | 72.61.73.245 | 3600 |
| A    | www    | 72.61.73.245 | 3600 |

**Actualmente apunta a**: `84.32.84.32` ❌  
**Debe apuntar a**: `72.61.73.245` ✅

---

### **2️⃣ VERIFICAR PROPAGACIÓN DNS**

Espera 5-10 minutos después de cambiar el DNS, luego verifica:

```bash
dig +short 105pos.pro
```

**Debe mostrar**: `72.61.73.245`

---

### **3️⃣ INSTALAR CERTIFICADO SSL**

Una vez que el DNS esté propagado, ejecuta:

```bash
ssh root@72.61.73.245 "certbot --nginx -d 105pos.pro -d www.105pos.pro --non-interactive --agree-tos --email tu@email.com"
```

Reemplaza `tu@email.com` con tu email real.

Certbot automáticamente:
- ✅ Generará certificado SSL gratuito
- ✅ Configurará HTTPS
- ✅ Habilitará redirección HTTP → HTTPS
- ✅ Configurará renovación automática

---

### **4️⃣ VERIFICAR FUNCIONAMIENTO**

Accede a:
- ✅ https://105pos.pro
- ✅ https://www.105pos.pro

---

## 🔧 CONFIGURACIÓN BACKEND (.env en VPS)

Debes actualizar el archivo `.env` en el VPS con las credenciales correctas:

```bash
ssh root@72.61.73.245 "nano /var/www/105pos/backend/.env"
```

**Cambios necesarios**:

```env
APP_URL=https://105pos.pro
APP_ENV=production
APP_DEBUG=false

# Tus credenciales de Mercado Pago (ya las tienes)
MERCADOPAGO_PUBLIC_KEY=APP_USR-d1af5791-fa70-4707-a8b5-61b7bf81a978
MERCADOPAGO_ACCESS_TOKEN=APP_USR-4051583343447871-120914-d1f45ea3071d39c9fdab5e3ba88985f6-3052668646

# Tus API Keys de Groq (cópialas desde tu .env local)
GROQ_API_KEY_1=your_groq_api_key_1_here
GROQ_API_KEY_2=your_groq_api_key_2_here
# ... (copiar todas las que tienes desde tu .env local)
```

**Para actualizar .env completo**:

```bash
# Desde tu máquina local
scp backend/.env root@72.61.73.245:/var/www/105pos/backend/.env

# Luego en el VPS, ajusta solo estas líneas:
ssh root@72.61.73.245 "cd /var/www/105pos/backend && \
  sed -i 's|APP_URL=.*|APP_URL=https://105pos.pro|' .env && \
  sed -i 's|APP_ENV=.*|APP_ENV=production|' .env && \
  sed -i 's|APP_DEBUG=.*|APP_DEBUG=false|' .env && \
  php artisan config:cache"
```

---

## 🎯 RESUMEN DE ARQUITECTURA

```
DNS: 105pos.pro → 72.61.73.245

VPS (72.61.73.245)
├── Nginx (puerto 80/443)
│   └── 105pos.pro
│       ├── Frontend: /var/www/105pos/dist/
│       └── API: /var/www/105pos/backend/public/
│
├── MySQL
│   └── Database: pos_105
│
└── PHP-FPM
    └── Laravel Backend
```

---

## ✅ CHECKLIST FINAL

Antes de considerar el deployment completo:

- [ ] DNS apunta a 72.61.73.245
- [ ] SSL instalado con Certbot
- [ ] https://105pos.pro carga correctamente
- [ ] Backend .env configurado con credenciales de producción
- [ ] Primer tenant creado exitosamente
- [ ] Login funciona
- [ ] API responde correctamente
- [ ] Mercado Pago configurado (si aplica)
- [ ] Groq AI configurado (si aplica)

---

## 🆘 COMANDOS ÚTILES

### Ver logs en tiempo real:
```bash
ssh root@72.61.73.245 "tail -f /var/www/105pos/backend/storage/logs/laravel.log"
```

### Reiniciar servicios:
```bash
ssh root@72.61.73.245 "systemctl restart nginx php8.3-fpm"
```

### Limpiar cache de Laravel:
```bash
ssh root@72.61.73.245 "cd /var/www/105pos/backend && php artisan cache:clear && php artisan config:clear"
```

### Ver estado del certificado SSL:
```bash
ssh root@72.61.73.245 "certbot certificates"
```

---

## 📞 SIGUIENTE PASO INMEDIATO

**🎯 Ve a tu proveedor de DNS y cambia los registros A para que apunten a `72.61.73.245`**

Una vez hecho esto, avísame y continuamos con el SSL.

---

**Fecha**: 9 de Diciembre, 2025  
**Estado**: ⏳ Esperando configuración DNS
