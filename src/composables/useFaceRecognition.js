import { ref, onUnmounted } from 'vue'
import * as faceapi from 'face-api.js'

const MODELS_URL = '/models/face-api'
const MATCH_THRESHOLD = 0.4
let modelsLoaded = false

/**
 * Composable para el motor de reconocimiento facial con face-api.js
 * Toda la detección y cómputo de descriptores ocurre en el navegador.
 */
export function useFaceRecognition() {
  const isModelLoading = ref(false)
  const modelError = ref(null)
  const isCameraActive = ref(false)
  const faceDetected = ref(false)
  const matchResult = ref(null) // { match: boolean, distance: number }

  let videoStream = null
  let detectionInterval = null

  /**
   * Cargar modelos de face-api.js (solo una vez)
   */
  const loadModels = async () => {
    if (modelsLoaded) return true

    isModelLoading.value = true
    modelError.value = null

    try {
      await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(MODELS_URL),
        faceapi.nets.faceLandmark68Net.loadFromUri(MODELS_URL),
        faceapi.nets.faceRecognitionNet.loadFromUri(MODELS_URL),
      ])
      modelsLoaded = true
      return true
    } catch (error) {
      modelError.value = 'No se pudieron cargar los modelos de reconocimiento facial. Verifique la conexión.'
      return false
    } finally {
      isModelLoading.value = false
    }
  }

  /**
   * Iniciar la cámara y asignarla a un elemento <video>
   */
  const startCamera = async (videoElement) => {
    try {
      videoStream = await navigator.mediaDevices.getUserMedia({
        video: {
          width: { ideal: 640 },
          height: { ideal: 480 },
          facingMode: 'user',
        },
        audio: false,
      })

      videoElement.srcObject = videoStream
      await videoElement.play()
      isCameraActive.value = true
      return true
    } catch (error) {
      if (error.name === 'NotAllowedError') {
        modelError.value = 'Permiso de cámara denegado. Por favor, permita el acceso a la cámara en su navegador.'
      } else if (error.name === 'NotFoundError') {
        modelError.value = 'No se encontró una cámara disponible en este dispositivo.'
      } else {
        modelError.value = 'Error al acceder a la cámara: ' + error.message
      }
      return false
    }
  }

  /**
   * Detener la cámara
   */
  const stopCamera = () => {
    if (detectionInterval) {
      clearInterval(detectionInterval)
      detectionInterval = null
    }
    if (videoStream) {
      videoStream.getTracks().forEach(track => track.stop())
      videoStream = null
    }
    isCameraActive.value = false
    faceDetected.value = false
    matchResult.value = null
  }

  /**
   * Detectar un rostro y extraer su descriptor (Float32Array de 128 valores)
   * Retorna null si no se detecta rostro claro
   */
  const detectFace = async (videoElement) => {
    const detection = await faceapi
      .detectSingleFace(videoElement, new faceapi.TinyFaceDetectorOptions({
        inputSize: 416,
        scoreThreshold: 0.5,
      }))
      .withFaceLandmarks()
      .withFaceDescriptor()

    if (detection) {
      faceDetected.value = true
      return detection
    }

    faceDetected.value = false
    return null
  }

  /**
   * Extraer solo el descriptor facial (para enrolamiento)
   */
  const extractDescriptor = async (videoElement) => {
    const detection = await detectFace(videoElement)
    return detection ? detection.descriptor : null
  }

  /**
   * Capturar imagen del video como Base64
   */
  const captureImage = (videoElement) => {
    const canvas = document.createElement('canvas')
    canvas.width = videoElement.videoWidth
    canvas.height = videoElement.videoHeight
    const ctx = canvas.getContext('2d')
    ctx.drawImage(videoElement, 0, 0)
    return canvas.toDataURL('image/jpeg', 0.8)
  }

  /**
   * Comparar el rostro actual con un descriptor base
   * @param {HTMLVideoElement} videoElement
   * @param {number[]} baseDescriptor - Array de 128 floats
   * @returns {{ match: boolean, distance: number } | null}
   */
  const compareFace = async (videoElement, baseDescriptor) => {
    const detection = await detectFace(videoElement)

    if (!detection) {
      matchResult.value = null
      return null
    }

    const currentDescriptor = detection.descriptor
    const baseFloat32 = new Float32Array(baseDescriptor)
    const distance = faceapi.euclideanDistance(currentDescriptor, baseFloat32)

    const result = {
      match: distance < MATCH_THRESHOLD,
      distance: Math.round(distance * 10000) / 10000,
    }

    matchResult.value = result
    return result
  }

  /**
   * Iniciar detección continua con comparación
   * @param {HTMLVideoElement} videoElement
   * @param {number[]} baseDescriptor
   * @param {Function} onResult - Callback (result) => void
   * @param {number} intervalMs - Intervalo de detección en ms
   */
  const startContinuousDetection = (videoElement, baseDescriptor, onResult, intervalMs = 500) => {
    if (detectionInterval) clearInterval(detectionInterval)

    detectionInterval = setInterval(async () => {
      if (!isCameraActive.value) return

      try {
        const result = await compareFace(videoElement, baseDescriptor)
        if (onResult) onResult(result)
      } catch {
        // Silenciar errores transitorios de detección
      }
    }, intervalMs)
  }

  /**
   * Iniciar detección de rostro simple (sin comparación, para enrolamiento)
   * @param {HTMLVideoElement} videoElement
   * @param {HTMLCanvasElement} overlayCanvas - Canvas superpuesto para dibujar recuadro
   * @param {number} intervalMs
   */
  const startFaceGuide = (videoElement, overlayCanvas, intervalMs = 300) => {
    if (detectionInterval) clearInterval(detectionInterval)

    const ctx = overlayCanvas.getContext('2d')

    detectionInterval = setInterval(async () => {
      if (!isCameraActive.value) return

      try {
        const detection = await faceapi.detectSingleFace(
          videoElement,
          new faceapi.TinyFaceDetectorOptions({ inputSize: 416, scoreThreshold: 0.5 })
        ).withFaceLandmarks()

        // Limpiar canvas
        ctx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height)

        if (detection) {
          faceDetected.value = true
          const box = detection.detection.box

          // Escalar coordenadas al tamaño del canvas
          const scaleX = overlayCanvas.width / videoElement.videoWidth
          const scaleY = overlayCanvas.height / videoElement.videoHeight

          const x = box.x * scaleX
          const y = box.y * scaleY
          const w = box.width * scaleX
          const h = box.height * scaleY

          // Dibujar recuadro guía
          ctx.strokeStyle = '#10b981' // emerald-500
          ctx.lineWidth = 3
          ctx.setLineDash([])

          // Esquinas redondeadas
          const r = 12
          ctx.beginPath()
          ctx.moveTo(x + r, y)
          ctx.lineTo(x + w - r, y)
          ctx.arcTo(x + w, y, x + w, y + r, r)
          ctx.lineTo(x + w, y + h - r)
          ctx.arcTo(x + w, y + h, x + w - r, y + h, r)
          ctx.lineTo(x + r, y + h)
          ctx.arcTo(x, y + h, x, y + h - r, r)
          ctx.lineTo(x, y + r)
          ctx.arcTo(x, y, x + r, y, r)
          ctx.closePath()
          ctx.stroke()

          // Label
          ctx.fillStyle = 'rgba(16, 185, 129, 0.9)'
          ctx.fillRect(x, y - 24, 120, 22)
          ctx.fillStyle = '#ffffff'
          ctx.font = '12px Inter, system-ui, sans-serif'
          ctx.fillText('Rostro detectado', x + 6, y - 8)
        } else {
          faceDetected.value = false

          // Dibujar guía oval centrada (gris punteado)
          const cx = overlayCanvas.width / 2
          const cy = overlayCanvas.height / 2
          const rx = overlayCanvas.width * 0.2
          const ry = overlayCanvas.height * 0.32

          ctx.strokeStyle = 'rgba(161, 161, 170, 0.5)' // zinc-400
          ctx.lineWidth = 2
          ctx.setLineDash([8, 6])
          ctx.beginPath()
          ctx.ellipse(cx, cy, rx, ry, 0, 0, 2 * Math.PI)
          ctx.stroke()
          ctx.setLineDash([])

          // Texto guía
          ctx.fillStyle = 'rgba(161, 161, 170, 0.8)'
          ctx.font = '13px Inter, system-ui, sans-serif'
          ctx.textAlign = 'center'
          ctx.fillText('Centre su rostro aquí', cx, cy + ry + 28)
          ctx.textAlign = 'start'
        }
      } catch {
        // Silenciar errores transitorios
      }
    }, intervalMs)
  }

  /**
   * Dibujar indicador de match/no-match en el canvas overlay
   */
  const drawMatchIndicator = (overlayCanvas, result) => {
    if (!overlayCanvas) return
    const ctx = overlayCanvas.getContext('2d')
    ctx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height)

    if (!result) return

    const cx = overlayCanvas.width / 2
    const cy = overlayCanvas.height / 2
    const radius = Math.min(overlayCanvas.width, overlayCanvas.height) * 0.35

    ctx.beginPath()
    ctx.arc(cx, cy, radius, 0, 2 * Math.PI)
    ctx.strokeStyle = result.match ? 'rgba(16, 185, 129, 0.7)' : 'rgba(239, 68, 68, 0.7)'
    ctx.lineWidth = 4
    ctx.stroke()

    // Icon
    ctx.fillStyle = result.match ? 'rgba(16, 185, 129, 0.9)' : 'rgba(239, 68, 68, 0.9)'
    ctx.font = 'bold 14px Inter, system-ui, sans-serif'
    ctx.textAlign = 'center'
    ctx.fillText(
      result.match ? `Verificado (${(result.distance * 100).toFixed(1)}%)` : 'No reconocido',
      cx,
      cy + radius + 30
    )
    ctx.textAlign = 'start'
  }

  onUnmounted(() => {
    stopCamera()
  })

  return {
    // State
    isModelLoading,
    modelError,
    isCameraActive,
    faceDetected,
    matchResult,
    MATCH_THRESHOLD,

    // Methods
    loadModels,
    startCamera,
    stopCamera,
    detectFace,
    extractDescriptor,
    captureImage,
    compareFace,
    startContinuousDetection,
    startFaceGuide,
    drawMatchIndicator,
  }
}
