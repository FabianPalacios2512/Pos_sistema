import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'
import { VitePWA } from 'vite-plugin-pwa'

export default defineConfig({
  base: '/',
  plugins: [
    vue(),
    VitePWA({
      registerType: 'autoUpdate',
      injectRegister: 'auto',
      includeAssets: ['logo.png', 'logo2.png', 'robots.txt'],
      manifest: {
        name: '105 POS Pro - Sistema de Gestión Empresarial',
        short_name: '105 POS Pro',
        description: 'Sistema POS completo para gestión empresarial profesional',
        theme_color: '#1e293b',
        background_color: '#0a0a0c',
        display: 'standalone',
        orientation: 'portrait-primary',
        scope: '/',
        start_url: '/',
        icons: [
          {
            src: '/pwa-192x192.png',
            sizes: '192x192',
            type: 'image/png',
            purpose: 'any maskable'
          },
          {
            src: '/pwa-512x512.png',
            sizes: '512x512',
            type: 'image/png',
            purpose: 'any maskable'
          }
        ],
        categories: ['business', 'productivity', 'finance'],
        shortcuts: [
          {
            name: 'Nueva Venta',
            short_name: 'Vender',
            description: 'Iniciar una nueva venta',
            url: '/pos?action=new-sale',
            icons: [{ src: '/pwa-192x192.png', sizes: '192x192' }]
          },
          {
            name: 'Inventario',
            short_name: 'Inventario',
            description: 'Ver inventario de productos',
            url: '/pos?module=inventory',
            icons: [{ src: '/pwa-192x192.png', sizes: '192x192' }]
          },
          {
            name: 'Reportes',
            short_name: 'Reportes',
            description: 'Ver reportes de ventas',
            url: '/pos?module=reports',
            icons: [{ src: '/pwa-192x192.png', sizes: '192x192' }]
          }
        ]
      },
      workbox: {
        cleanupOutdatedCaches: true,
        skipWaiting: true,
        clientsClaim: true,
        // 🔇 Silenciar logs de Workbox en consola
        disableDevLogs: true,
        maximumFileSizeToCacheInBytes: 10 * 1024 * 1024, // 10MB (para login.png)
        globPatterns: ['**/*.{js,css,html,ico,png,jpg,svg,woff,woff2}'],
        // Inyectar código para silenciar logs
        additionalManifestEntries: [],
        // Configuración silenciosa
        mode: 'production',
        // Importar archivo de configuración personalizado
        importScripts: ['sw-config.js'],
        // Excluir rutas de API del Service Worker navigation
        navigateFallback: '/index.html',
        navigateFallbackDenylist: [
          /^\/@vite/,
          /^\/src/,
          /^\/node_modules/,
          /^\/@id/,
          /^\/@fs/,
          /^\/@vite-plugin-pwa/,
          /^\/api\//,  // 🔥 Excluir TODAS las rutas de API
        ],
        runtimeCaching: [
          {
            urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
            handler: 'CacheFirst',
            options: {
              cacheName: 'google-fonts-cache',
              expiration: {
                maxEntries: 10,
                maxAgeSeconds: 60 * 60 * 24 * 365 // 1 año
              },
              cacheableResponse: {
                statuses: [0, 200]
              }
            }
          },
          {
            urlPattern: /^https:\/\/fonts\.gstatic\.com\/.*/i,
            handler: 'CacheFirst',
            options: {
              cacheName: 'gstatic-fonts-cache',
              expiration: {
                maxEntries: 10,
                maxAgeSeconds: 60 * 60 * 24 * 365
              },
              cacheableResponse: {
                statuses: [0, 200]
              }
            }
          },
          {
            // OAuth endpoints - SOLO red, sin cache - NUNCA interceptar
            urlPattern: /\/api\/auth\/google\/.*/,
            handler: 'NetworkOnly'
          },
          {
            // Todas las rutas de API - NetworkOnly para evitar problemas
            urlPattern: /\/api\/.*/,
            handler: 'NetworkOnly'
          }
        ]
      },
      devOptions: {
        enabled: true,
        type: 'module',
        suppressWarnings: true  // 🔇 Suprimir warnings de Workbox
      }
    })
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src')
    }
  },
  build: {
    // Eliminar console.log y debugger en producción
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true
      }
    }
  },
  server: {
    port: 3000,
    open: true,
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: false, // Important: Keep the original host header (e.g. tenant.localhost)
        secure: false,
      },
      '/admin/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: false,
        secure: false,
        rewrite: (path) => path.replace(/^\/admin\/api/, '/api/admin/api'),
      },
      '/storage': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: false,
        secure: false,
      }
    },
    // Configurar para que las rutas de Vue Router funcionen al refrescar
    historyApiFallback: true
  }
})