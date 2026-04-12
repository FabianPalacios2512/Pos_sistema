import { defineStore } from 'pinia'
import axios from 'axios'

export const useRadioStore = defineStore('radio', {
    state: () => ({
        audio: null,
        currentStation: null,
        isPlaying: false,
        volume: 70,
        isMuted: false,
        isLoading: false,

        // Data Lists
        topStations: [],
        newsStations: [],
        musicStations: [],
        searchResults: [],
        favorites: [], // Lista de favoritos

        // Cache for city filters
        cityStations: {
            medellin: [],
            bogota: [],
            cali: [],
            costa: []
        },

        currentView: 'home', // 'home', 'search', 'city', 'favorites'
        currentCityTitle: '',
        activeCityStations: []
    }),

    actions: {
        initAudio() {
            if (!this.audio) {
                this.audio = new Audio()
                this.audio.volume = this.volume / 100

                // Event Listeners
                this.audio.addEventListener('play', () => { this.isPlaying = true })
                this.audio.addEventListener('pause', () => { this.isPlaying = false })
                this.audio.addEventListener('error', (e) => {
                    console.error('Audio Error:', e)
                    this.isPlaying = false
                })
                this.audio.addEventListener('ended', () => { this.isPlaying = false })
            }
            
            // Cargar favoritos desde localStorage
            this.loadFavorites()
        },

        // Sistema de Favoritos
        loadFavorites() {
            try {
                const saved = localStorage.getItem('radio-favorites')
                if (saved) {
                    const parsed = JSON.parse(saved)
                    // Validar que sea un array
                    if (Array.isArray(parsed)) {
                        this.favorites = parsed
                    } else {
                        this.favorites = []
                    }
                }
            } catch (e) {
                this.favorites = []
            }
        },

        saveFavorites() {
            try {
                localStorage.setItem('radio-favorites', JSON.stringify(this.favorites))
            } catch (e) {
                console.error('Error saving favorites:', e)
            }
        },

        toggleFavorite(station) {
            const index = this.favorites.findIndex(f => f.id === station.id)
            if (index >= 0) {
                this.favorites.splice(index, 1)
            } else {
                this.favorites.push({
                    id: station.id,
                    name: station.name,
                    url: station.url,
                    logo: station.logo,
                    favicon: station.favicon || station.logo, // Para compatibilidad
                    state: station.state,
                    country: station.country
                })
            }
            this.saveFavorites()
        },

        isFavorite(stationId) {
            return this.favorites.some(f => f.id === stationId)
        },

        // Navegación entre emisoras
        getAllStations() {
            // Combinar todas las emisoras disponibles según la vista actual
            let stations = []
            
            if (this.currentView === 'favorites') {
                stations = [...this.favorites]
            } else if (this.currentView === 'search') {
                stations = [...this.searchResults]
            } else if (this.currentView === 'city') {
                stations = [...this.activeCityStations]
            } else {
                // Home: combinar todas
                stations = [...this.topStations, ...this.newsStations, ...this.musicStations]
            }
            
            // Eliminar duplicados por id
            const uniqueStations = []
            const seenIds = new Set()
            for (const station of stations) {
                if (!seenIds.has(station.id)) {
                    seenIds.add(station.id)
                    uniqueStations.push(station)
                }
            }
            return uniqueStations
        },

        playNext() {
            const stations = this.getAllStations()
            if (stations.length === 0) return
            
            const currentIndex = stations.findIndex(s => s.id === this.currentStation?.id)
            const nextIndex = (currentIndex + 1) % stations.length
            this.playStation(stations[nextIndex])
        },

        playPrevious() {
            const stations = this.getAllStations()
            if (stations.length === 0) return
            
            const currentIndex = stations.findIndex(s => s.id === this.currentStation?.id)
            const prevIndex = currentIndex <= 0 ? stations.length - 1 : currentIndex - 1
            this.playStation(stations[prevIndex])
        },

        playRandom() {
            const stations = this.getAllStations()
            if (stations.length === 0) return
            
            const randomIndex = Math.floor(Math.random() * stations.length)
            this.playStation(stations[randomIndex])
        },

        async fetchHomeData() {
            this.isLoading = true
            try {
                // 1. Top Colombia (Sorted by votes)
                // Usar nuestro proxy local en lugar de la API externa
                const topRes = await axios.get('/api/radio/search', {
                    params: {
                        countrycode: 'CO',
                        limit: 10,
                        order: 'votes',
                        reverse: true,
                        hidebroken: true
                    }
                })
                this.topStations = this.processStations(topRes.data)

                // 2. Noticias (Tag: news)
                // Usar nuestro proxy local
                const newsRes = await axios.get('/api/radio/search', {
                    params: {
                        countrycode: 'CO',
                        tag: 'news',
                        limit: 10,
                        order: 'clickcount',
                        reverse: true,
                        hidebroken: true
                    }
                })
                this.newsStations = this.processStations(newsRes.data)

                // 3. Música Popular/Vallenato
                // Usar nuestro proxy local
                const musicRes = await axios.get('/api/radio/search', {
                    params: {
                        countrycode: 'CO',
                        tag: 'vallenato',
                        limit: 10,
                        order: 'clickcount',
                        reverse: true,
                        hidebroken: true
                    }
                })
                this.musicStations = this.processStations(musicRes.data)

            } catch (error) {
                console.error('Error fetching radio data:', error)
            } finally {
                this.isLoading = false
            }
        },

        async searchStations(query) {
            if (!query) return
            this.isLoading = true
            this.currentView = 'search'

            try {
                // Usar nuestro proxy local
                const res = await axios.get('/api/radio/search', {
                    params: {
                        countrycode: 'CO',
                        name: query,
                        limit: 20,
                        order: 'votes',
                        reverse: true,
                        hidebroken: true
                    }
                })
                this.searchResults = this.processStations(res.data)
            } catch (error) {
                console.error('Error searching stations:', error)
            } finally {
                this.isLoading = false
            }
        },

        async fetchByCity(city, stateFilter) {
            this.isLoading = true
            this.currentView = 'city'
            this.currentCityTitle = city
            
            // Map city names to state if needed
            if (!stateFilter) {
                // Default mapping if not provided
                const cityMap = {
                    'Bogotá': 'Bogota',
                    'Medellín': 'Antioquia',
                    'Cali': 'Valle del Cauca',
                    'Barranquilla': 'Atlantico',
                    'Cartagena': 'Bolivar',
                    'Bucaramanga': 'Santander',
                    'Pereira': 'Risaralda',
                    'Manizales': 'Caldas',
                    'Cúcuta': 'Norte de Santander',
                    'Santa Marta': 'Magdalena',
                    'Ibagué': 'Tolima',
                    'Villavicencio': 'Meta',
                    'Neiva': 'Huila',
                    'Montería': 'Cordoba',
                    'Pasto': 'Narino',
                    'Armenia': 'Quindio'
                }
                stateFilter = cityMap[city] || ''
            }

            try {
                // Usar nuestro proxy local
                const res = await axios.get('/api/radio/search', {
                    params: {
                        countrycode: 'CO',
                        state: stateFilter,
                        limit: 20,
                        order: 'votes',
                        reverse: true,
                        hidebroken: true
                    }
                })
                this.activeCityStations = this.processStations(res.data)
            } catch (error) {
                console.error(`Error fetching stations for ${city}:`, error)
            } finally {
                this.isLoading = false
            }
        },



        processStations(data) {
            return data.map(station => ({
                id: station.stationuuid,
                name: station.name.trim(),
                url: station.url_resolved || station.url,
                logo: station.favicon || null,
                favicon: station.favicon || null, // Para compatibilidad
                tags: station.tags,
                country: station.country,
                state: station.state,
                votes: station.votes
            })).filter(s => s.url) // Ensure URL exists
        },

        async playStation(station) {
            if (!this.audio) this.initAudio()

            if (this.currentStation?.id === station.id && this.isPlaying) {
                this.togglePlay()
                return
            }

            this.currentStation = station
            this.audio.src = station.url
            try {
                await this.audio.play()
                this.isPlaying = true
            } catch (error) {
                console.error('Playback error:', error)
                this.isPlaying = false
            }
        },

        togglePlay() {
            if (!this.audio) return

            if (this.isPlaying) {
                this.audio.pause()
            } else {
                this.audio.play()
            }
        },

        setVolume(val) {
            this.volume = val
            if (this.audio) {
                this.audio.volume = val / 100
                if (this.isMuted && val > 0) this.isMuted = false
            }
        },

        toggleMute() {
            if (!this.audio) return
            this.isMuted = !this.isMuted
            this.audio.muted = this.isMuted
        }
    }
})
