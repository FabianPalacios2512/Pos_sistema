# 📻 Radio 105 FM - API Pública

## Descripción

**Radio 105 FM** es un servicio gratuito de streaming de radio colombiana que puedes integrar en cualquier aplicación web o móvil. Ofrecemos más de 20 estaciones de radio de las principales ciudades de Colombia.

**Base URL:** `https://105pos.pro/api/public/radio`

---

## 🚀 Integración Rápida (Widget)

La forma más fácil de integrar el reproductor de radio es usando nuestro widget embebible:

```html
<!-- 1. Agregar el contenedor donde quieres el reproductor -->
<div id="radio-105"></div>

<!-- 2. Incluir el script del widget -->
<script src="https://105pos.pro/api/public/radio/widget"></script>

<!-- 3. Inicializar el widget -->
<script>
  Radio105.init({
    container: '#radio-105',
    theme: 'dark',           // 'dark', 'light', o 'auto'
    primaryColor: '#10b981', // Color principal (emerald por defecto)
    height: '500px',         // Alto del widget
    defaultCity: null        // Filtrar por ciudad: 'Medellín', 'Bogotá', 'Cali'
  });
</script>
```

### Opciones del Widget

| Opción | Tipo | Default | Descripción |
|--------|------|---------|-------------|
| `container` | string | `'#radio-105'` | Selector CSS del contenedor |
| `theme` | string | `'dark'` | Tema: `'dark'`, `'light'`, `'auto'` |
| `primaryColor` | string | `'#10b981'` | Color principal en formato hex |
| `height` | string | `'500px'` | Alto del widget |
| `defaultCity` | string | `null` | Filtrar estaciones por ciudad |
| `showFavorites` | boolean | `true` | Mostrar sección de favoritos |
| `autoplay` | boolean | `false` | Reproducir automáticamente |

### Métodos del Widget

```javascript
// Reproducir una estación por ID
Radio105.play('co-medellin-mega');

// Pausar reproducción
Radio105.pause();

// Toggle play/pause
Radio105.toggle();

// Obtener estación actual
const station = Radio105.currentStation;

// Verificar si está reproduciendo
const isPlaying = Radio105.isPlaying;

// Destruir widget
Radio105.destroy();
```

---

## 📡 API REST

Si prefieres construir tu propia interfaz, puedes usar nuestra API REST directamente.

### Autenticación

No se requiere autenticación. La API es pública y gratuita.

### Rate Limiting

- **100 requests por minuto** por IP
- Headers de respuesta incluyen: `X-RateLimit-Remaining`

### CORS

CORS está habilitado para todos los orígenes (`Access-Control-Allow-Origin: *`).

---

## 📋 Endpoints

### 1. Información del Servicio

```http
GET /api/public/radio/info
```

**Respuesta:**
```json
{
  "service": "Radio 105 FM",
  "version": "1.0.0",
  "description": "Servicio gratuito de streaming de radio colombiana",
  "provider": "105POS",
  "endpoints": {
    "info": "/api/public/radio/info",
    "stations": "/api/public/radio/stations",
    "station": "/api/public/radio/stations/{id}",
    "search": "/api/public/radio/search",
    "categories": "/api/public/radio/categories",
    "cities": "/api/public/radio/cities"
  },
  "cors_enabled": true,
  "rate_limit": "100 requests per minute"
}
```

---

### 2. Listar Estaciones

```http
GET /api/public/radio/stations
```

**Parámetros Query:**

| Parámetro | Tipo | Default | Descripción |
|-----------|------|---------|-------------|
| `limit` | int | 50 | Máximo de resultados (max: 100) |
| `offset` | int | 0 | Offset para paginación |
| `state` | string | - | Filtrar por estado/ciudad |
| `category` | string | - | Filtrar por categoría (tags) |

**Ejemplo:**
```bash
curl "https://105pos.pro/api/public/radio/stations?limit=10&state=Antioquia"
```

**Respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "id": "co-medellin-mega",
      "name": "La Mega 92.9 FM",
      "stream_url": "https://us-b4-p-e-qg12-audio.cdn.mdstrm.com/live-audio-aw/632cb48f613bac0856b931ab",
      "logo": "https://cdn-profiles.tunein.com/s34144/images/logog.png",
      "tags": "pop,rock,hits",
      "country": "Colombia",
      "state": "Antioquia",
      "city": "Medellín",
      "frequency": "92.9 FM",
      "bitrate": 128,
      "votes": 1200
    }
  ],
  "meta": {
    "total": 20,
    "limit": 10,
    "offset": 0,
    "has_more": true
  }
}
```

---

### 3. Obtener Estación por ID

```http
GET /api/public/radio/stations/{id}
```

**Ejemplo:**
```bash
curl "https://105pos.pro/api/public/radio/stations/co-medellin-mega"
```

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "id": "co-medellin-mega",
    "name": "La Mega 92.9 FM",
    "stream_url": "https://us-b4-p-e-qg12-audio.cdn.mdstrm.com/live-audio-aw/632cb48f613bac0856b931ab",
    "logo": "https://cdn-profiles.tunein.com/s34144/images/logog.png",
    "tags": "pop,rock,hits",
    "country": "Colombia",
    "state": "Antioquia",
    "city": "Medellín",
    "frequency": "92.9 FM",
    "bitrate": 128,
    "votes": 1200
  }
}
```

---

### 4. Buscar Estaciones

```http
GET /api/public/radio/search?q={query}
```

**Parámetros Query:**

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `q` | string | Sí | Término de búsqueda (mín. 2 caracteres) |
| `limit` | int | No | Máximo de resultados (default: 20, max: 50) |

**Ejemplo:**
```bash
curl "https://105pos.pro/api/public/radio/search?q=olimpica"
```

**Respuesta:**
```json
{
  "success": true,
  "query": "olimpica",
  "count": 4,
  "data": [
    {
      "id": "co-medellin-olimpica",
      "name": "Olímpica Stereo 104.9 FM",
      "city": "Medellín",
      ...
    },
    {
      "id": "co-bogota-olimpica",
      "name": "Olímpica Stereo 105.9 FM",
      "city": "Bogotá",
      ...
    }
  ]
}
```

---

### 5. Listar Categorías

```http
GET /api/public/radio/categories
```

**Respuesta:**
```json
{
  "success": true,
  "data": [
    { "id": "pop", "name": "Pop & Hits", "icon": "🎵" },
    { "id": "rock", "name": "Rock", "icon": "🎸" },
    { "id": "tropical", "name": "Tropical & Salsa", "icon": "🌴" },
    { "id": "vallenato", "name": "Vallenato", "icon": "🪗" },
    { "id": "romantica", "name": "Romántica", "icon": "❤️" },
    { "id": "noticias", "name": "Noticias", "icon": "📰" },
    { "id": "urbana", "name": "Urbana & Reggaetón", "icon": "🔥" },
    { "id": "cristiana", "name": "Cristiana", "icon": "✝️" },
    { "id": "clasica", "name": "Clásica", "icon": "🎻" }
  ]
}
```

---

### 6. Listar Ciudades

```http
GET /api/public/radio/cities
```

**Respuesta:**
```json
{
  "success": true,
  "data": [
    { "id": "medellin", "name": "Medellín", "state": "Antioquia" },
    { "id": "bogota", "name": "Bogotá", "state": "Bogota" },
    { "id": "cali", "name": "Cali", "state": "Valle" },
    { "id": "barranquilla", "name": "Barranquilla", "state": "Atlantico" },
    { "id": "cartagena", "name": "Cartagena", "state": "Bolivar" }
  ]
}
```

---

## 🎧 Reproducir Audio

Para reproducir una estación, usa la URL del campo `stream_url` directamente en un elemento `<audio>` de HTML5:

```html
<audio id="radio-player" controls>
  <source src="https://us-b4-p-e-qg12-audio.cdn.mdstrm.com/live-audio-aw/632cb48f613bac0856b931ab" type="audio/aac">
</audio>
```

### JavaScript Example

```javascript
async function playStation(stationId) {
  const response = await fetch(`https://105pos.pro/api/public/radio/stations/${stationId}`);
  const { data } = await response.json();
  
  const audio = document.getElementById('radio-player');
  audio.src = data.stream_url;
  audio.play();
}

// Reproducir La Mega Medellín
playStation('co-medellin-mega');
```

---

## 📱 Ejemplo Completo: React

```jsx
import { useState, useEffect, useRef } from 'react';

function RadioPlayer() {
  const [stations, setStations] = useState([]);
  const [current, setCurrent] = useState(null);
  const [isPlaying, setIsPlaying] = useState(false);
  const audioRef = useRef(null);

  useEffect(() => {
    fetch('https://105pos.pro/api/public/radio/stations?limit=10')
      .then(res => res.json())
      .then(data => setStations(data.data));
  }, []);

  const play = (station) => {
    setCurrent(station);
    audioRef.current.src = station.stream_url;
    audioRef.current.play();
    setIsPlaying(true);
  };

  const toggle = () => {
    if (isPlaying) {
      audioRef.current.pause();
    } else {
      audioRef.current.play();
    }
    setIsPlaying(!isPlaying);
  };

  return (
    <div>
      <audio ref={audioRef} />
      
      {current && (
        <div className="now-playing">
          <img src={current.logo} alt={current.name} width="60" />
          <div>
            <h3>{current.name}</h3>
            <p>{current.city} • {current.frequency}</p>
          </div>
          <button onClick={toggle}>
            {isPlaying ? '⏸️ Pausar' : '▶️ Reproducir'}
          </button>
        </div>
      )}
      
      <ul className="stations-list">
        {stations.map(station => (
          <li key={station.id} onClick={() => play(station)}>
            <img src={station.logo} alt={station.name} width="40" />
            <span>{station.name}</span>
          </li>
        ))}
      </ul>
    </div>
  );
}
```

---

## 📱 Ejemplo Completo: Vue.js

```vue
<template>
  <div class="radio-player">
    <audio ref="audioPlayer"></audio>
    
    <!-- Now Playing -->
    <div v-if="currentStation" class="now-playing">
      <img :src="currentStation.logo" :alt="currentStation.name" width="60">
      <div>
        <h3>{{ currentStation.name }}</h3>
        <p>{{ currentStation.city }} • {{ currentStation.frequency }}</p>
      </div>
      <button @click="toggle">{{ isPlaying ? '⏸️' : '▶️' }}</button>
    </div>
    
    <!-- Stations List -->
    <ul class="stations">
      <li v-for="station in stations" :key="station.id" @click="play(station)">
        <img :src="station.logo" width="40">
        <span>{{ station.name }}</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const stations = ref([])
const currentStation = ref(null)
const isPlaying = ref(false)
const audioPlayer = ref(null)

onMounted(async () => {
  const res = await fetch('https://105pos.pro/api/public/radio/stations?limit=10')
  const data = await res.json()
  stations.value = data.data
})

const play = (station) => {
  currentStation.value = station
  audioPlayer.value.src = station.stream_url
  audioPlayer.value.play()
  isPlaying.value = true
}

const toggle = () => {
  if (isPlaying.value) {
    audioPlayer.value.pause()
  } else {
    audioPlayer.value.play()
  }
  isPlaying.value = !isPlaying.value
}
</script>
```

---

## 🔧 Errores Comunes

### Error 404: Station not found
```json
{
  "success": false,
  "error": "Station not found",
  "message": "No station found with ID: invalid-id"
}
```

### Error 400: Query too short
```json
{
  "success": false,
  "error": "Query too short",
  "message": "Search query must be at least 2 characters"
}
```

---

## 📊 Estaciones Disponibles

### Medellín
- La Mega 92.9 FM (`co-medellin-mega`)
- Olímpica Stereo 104.9 FM (`co-medellin-olimpica`)
- El Sol 107.9 FM (`co-medellin-elsol`)
- Mix 89.9 FM (`co-medellin-mix`)
- Tropicana 98.9 FM (`co-medellin-tropicana`)
- Bésame 94.9 FM (`co-medellin-besame`)
- Radioacktiva 102.3 FM (`co-medellin-radioacktiva`)

### Bogotá
- Blu Radio 89.9 FM (`co-bogota-blu`)
- Caracol Radio 100.9 FM (`co-bogota-caracol`)
- W Radio 99.9 FM (`co-bogota-wradio`)
- Olímpica Stereo 105.9 FM (`co-bogota-olimpica`)
- LOS40 Colombia (`co-bogota-los40`)
- Tropicana 102.9 FM (`co-bogota-tropicana`)

### Cali
- Olímpica Stereo 104.5 FM (`co-cali-olimpica`)
- Tropicana 93.1 FM (`co-cali-tropicana`)

### Barranquilla
- Olímpica Stereo 92.1 FM (`co-barranquilla-olimpica`)

---

## 📞 Soporte

- **Email:** soporte@105pos.pro
- **Website:** https://105pos.pro
- **GitHub Issues:** [Reportar un problema](https://github.com/105pos/radio-api/issues)

---

## 📜 Licencia

Este servicio es **gratuito para uso comercial y personal**. Al usar la API aceptas:

1. No abusar del servicio con requests excesivos
2. Dar crédito a "Radio 105 FM by 105POS" cuando sea posible
3. No redistribuir los streams como propios

---

**Powered by 105POS** | © 2026 Todos los derechos reservados
