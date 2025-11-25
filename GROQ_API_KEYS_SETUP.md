# 🔑 Sistema de Múltiples API Keys de Groq

## 📊 Capacidad Total del Sistema
- **10 cuentas de Gmail** × **100,000 tokens/día por cuenta**
- **= 1,000,000 tokens/día en total** 🚀
- **Rotación automática** cuando una key alcanza el límite

---

## ✅ Estado Actual de API Keys

| # | Gmail Account | API Key Status | Creada | Configurada |
|---|---------------|----------------|---------|-------------|
| 1 | prueba1 | `gsk_...zeDc` | ✅ | ✅ |
| 2 | - | - | ❌ | ❌ |
| 3 | - | - | ❌ | ❌ |
| 4 | - | - | ❌ | ❌ |
| 5 | - | - | ❌ | ❌ |
| 6 | - | - | ❌ | ❌ |
| 7 | - | - | ❌ | ❌ |
| 8 | - | - | ❌ | ❌ |
| 9 | - | - | ❌ | ❌ |
| 10 | - | - | ❌ | ❌ |

---

## 🚀 Pasos para Crear las API Keys Restantes

### 1. Abre una ventana de incógnito/privada en el navegador

### 2. Para cada cuenta de Gmail (2-10):

1. **Ve a:** https://console.groq.com/
2. **Haz clic en:** "Sign in"
3. **Selecciona:** "Continue with Google"
4. **Inicia sesión** con una de tus 10 cuentas de Gmail
5. **Ve a:** API Keys (menú lateral izquierdo) o https://console.groq.com/keys
6. **Haz clic en:** "+ Create API Key"
7. **Nombre sugerido:** `pos-sistema-key-2` (ajusta el número)
8. **Copia la API Key** que empieza con `gsk_...`
9. **⚠️ IMPORTANTE:** Guarda la key inmediatamente (solo se muestra una vez)

### 3. Agrega cada API Key al archivo `.env`

Abre el archivo `/backend/.env` y reemplaza las líneas vacías:

```env
# Sistema de múltiples API Keys de Groq (Rotación automática)
GROQ_API_KEY_1=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
GROQ_API_KEY_2=gsk_TU_SEGUNDA_KEY_AQUI
GROQ_API_KEY_3=gsk_TU_TERCERA_KEY_AQUI
GROQ_API_KEY_4=gsk_TU_CUARTA_KEY_AQUI
GROQ_API_KEY_5=gsk_TU_QUINTA_KEY_AQUI
GROQ_API_KEY_6=gsk_TU_SEXTA_KEY_AQUI
GROQ_API_KEY_7=gsk_TU_SEPTIMA_KEY_AQUI
GROQ_API_KEY_8=gsk_TU_OCTAVA_KEY_AQUI
GROQ_API_KEY_9=gsk_TU_NOVENA_KEY_AQUI
GROQ_API_KEY_10=gsk_TU_DECIMA_KEY_AQUI
```

### 4. ¡Listo! El sistema funcionará automáticamente

No necesitas reiniciar nada. El sistema PHP leerá las nuevas keys automáticamente.

---

## 🔄 Cómo Funciona la Rotación Automática

1. **Primera llamada:** Intenta con `GROQ_API_KEY_1`
2. **Si devuelve error 429 (rate limit):** Automáticamente prueba con `GROQ_API_KEY_2`
3. **Si también falla:** Prueba con `GROQ_API_KEY_3`
4. **Y así sucesivamente** hasta encontrar una key disponible
5. **Si todas fallan:** Muestra mensaje "servicio temporalmente saturado"

---

## 📊 Monitoreo de Uso

Los logs del sistema mostrarán qué key se está usando:

```
[Groq API] Intentando con API Key #1
[Groq API] ⚠️ Rate limit alcanzado en API Key #1, probando siguiente...
[Groq API] Intentando con API Key #2
[Groq API] ✅ Respuesta exitosa con API Key #2
```

Para ver los logs en tiempo real:
```bash
cd backend
tail -f storage/logs/laravel.log
```

---

## 💡 Consejos

- **Crea las 10 keys HOY** para tener la capacidad completa
- **Usa navegación privada** para cambiar entre cuentas rápidamente
- **Cierra la sesión** entre cada cuenta para evitar confusiones
- **Guarda las keys** en un lugar seguro (solo se muestran una vez)
- **No compartas las keys** - son privadas de tu sistema

---

## ⚡ Próximos Pasos (Opcional)

Para optimizar aún más el sistema:

1. **Rate Limiting por Usuario:**
   - Limitar a 20-30 mensajes por usuario/día
   - Cooldown de 3 segundos entre mensajes

2. **Caché de Respuestas:**
   - Guardar respuestas a preguntas frecuentes
   - Respuesta instantánea sin consumir tokens

3. **Dashboard de Uso:**
   - Ver qué keys están más saturadas
   - Estadísticas de uso por día/semana

---

## 🆘 Solución de Problemas

### ❌ "Error: No se han configurado API Keys"
**Solución:** Verifica que al menos `GROQ_API_KEY_1` tenga un valor en `.env`

### ❌ "Servicio temporalmente saturado"
**Solución:** 
- Todas las 10 keys alcanzaron el límite diario
- Espera que se reinicie el contador (medianoche UTC)
- O crea más cuentas de Gmail y agrega más keys

### ❌ "Invalid API Key"
**Solución:**
- Verifica que copiaste la key completa (empieza con `gsk_`)
- Asegúrate de no tener espacios antes/después de la key
- La key debe estar activa en console.groq.com

---

## 📈 Estadísticas Estimadas

Con **10 API Keys activas**:
- **Tokens disponibles:** 1,000,000/día
- **Llamadas aproximadas:** ~650 llamadas/día (1,500 tokens promedio por llamada)
- **Usuarios simultáneos:** ~20-30 usuarios activos
- **Conversaciones/día:** ~200-300 conversaciones completas

---

**Última actualización:** 25 de noviembre de 2025  
**Sistema:** POS 105 - AI Chat Multi-Account  
**Versión:** 1.0
