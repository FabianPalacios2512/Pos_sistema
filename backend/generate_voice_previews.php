<?php
/**
 * Script para generar los audios de preview de voces UNA VEZ
 * y guardarlos como archivos estáticos en storage/app/public/voice-previews/
 * 
 * Ejecutar: php generate_voice_previews.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Cargar .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['GEMINI_API_KEY'] ?? null;

if (!$apiKey) {
    die("❌ Error: GEMINI_API_KEY no configurada en .env\n");
}

// Crear directorio si no existe
$outputDir = __DIR__ . '/storage/app/public/voice-previews';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "📁 Creado directorio: $outputDir\n";
}

// Voces y sus textos de preview
$voices = [
    'Kore' => [
        'preview' => 'Hola, soy Kore. Te ayudaré a gestionar tu negocio con claridad.',
        'confirm_female' => 'Perfecto, sabía que me ibas a elegir. Estoy lista para ayudarte.',
        'welcome' => 'Bienvenido a 105 POS. Soy Kore, tu asistente de voz. Desliza para conocer las diferentes voces disponibles.'
    ],
    'Puck' => [
        'preview' => 'Hola, soy Puck. Cuenta conmigo para lo que necesites.',
        'confirm_male' => 'Perfecto, sabía que me ibas a elegir. Estoy listo para ayudarte.',
        'welcome' => 'Bienvenido a 105 POS. Soy Puck, tu asistente de voz. Desliza para conocer las diferentes voces disponibles.'
    ],
    'Aoede' => [
        'preview' => 'Hola, soy Aoede. Estoy aquí para asistirte de forma natural.',
        'confirm_female' => 'Perfecto, sabía que me ibas a elegir. Estoy lista para ayudarte.',
        'welcome' => 'Bienvenido a 105 POS. Soy Aoede, tu asistente de voz. Desliza para conocer las diferentes voces disponibles.'
    ],
    'Charon' => [
        'preview' => 'Hola, soy Charon. Te guiaré con precisión en cada paso.',
        'confirm_male' => 'Perfecto, sabía que me ibas a elegir. Estoy listo para ayudarte.',
        'welcome' => 'Bienvenido a 105 POS. Soy Charon, tu asistente de voz. Desliza para conocer las diferentes voces disponibles.'
    ],
    'Leda' => [
        'preview' => 'Hola, soy Leda. Será un gusto ayudarte hoy.',
        'confirm_female' => 'Perfecto, sabía que me ibas a elegir. Estoy lista para ayudarte.',
        'welcome' => 'Bienvenido a 105 POS. Soy Leda, tu asistente de voz. Desliza para conocer las diferentes voces disponibles.'
    ],
    'Fenrir' => [
        'preview' => 'Hola, soy Fenrir. Estoy listo para ayudarte con energía.',
        'confirm_male' => 'Perfecto, sabía que me ibas a elegir. Estoy listo para ayudarte.',
        'welcome' => 'Bienvenido a 105 POS. Soy Fenrir, tu asistente de voz. Desliza para conocer las diferentes voces disponibles.'
    ],
    'Orus' => [
        'preview' => 'Hola, soy Orus. Puedes confiar en mí para asistirte.',
        'confirm_male' => 'Perfecto, sabía que me ibas a elegir. Estoy listo para ayudarte.',
        'welcome' => 'Bienvenido a 105 POS. Soy Orus, tu asistente de voz. Desliza para conocer las diferentes voces disponibles.'
    ],
    'Achird' => [
        'preview' => 'Hola, soy Achird. Estaré aquí como un amigo para ayudarte.',
        'confirm_male' => 'Perfecto, sabía que me ibas a elegir. Estoy listo para ayudarte.',
        'welcome' => 'Bienvenido a 105 POS. Soy Achird, tu asistente de voz. Desliza para conocer las diferentes voces disponibles.'
    ]
];

// Función para convertir PCM a WAV
function pcmToWav(string $pcmData, int $sampleRate = 24000, int $bitsPerSample = 16, int $channels = 1): string {
    $byteRate = $sampleRate * $channels * ($bitsPerSample / 8);
    $blockAlign = $channels * ($bitsPerSample / 8);
    $dataSize = strlen($pcmData);
    $chunkSize = 36 + $dataSize;
    
    $header = 'RIFF';
    $header .= pack('V', $chunkSize);
    $header .= 'WAVE';
    $header .= 'fmt ';
    $header .= pack('V', 16);
    $header .= pack('v', 1);
    $header .= pack('v', $channels);
    $header .= pack('V', $sampleRate);
    $header .= pack('V', $byteRate);
    $header .= pack('v', $blockAlign);
    $header .= pack('v', $bitsPerSample);
    $header .= 'data';
    $header .= pack('V', $dataSize);
    
    return $header . $pcmData;
}

// Función para generar audio con Gemini TTS
function generateAudio(string $text, string $voiceName, string $apiKey): ?string {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-tts:generateContent?key=" . $apiKey;
    
    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $text]
                ]
            ]
        ],
        'generationConfig' => [
            'response_modalities' => ['AUDIO'],
            'speech_config' => [
                'voiceConfig' => [
                    'prebuiltVoiceConfig' => [
                        'voiceName' => $voiceName
                    ]
                ]
            ]
        ]
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        echo "  ❌ Error HTTP $httpCode\n";
        return null;
    }
    
    $data = json_decode($response, true);
    
    // Extraer audio base64
    $audioBase64 = $data['candidates'][0]['content']['parts'][0]['inlineData']['data'] ?? null;
    
    if (!$audioBase64) {
        echo "  ❌ No se encontró audio en la respuesta\n";
        return null;
    }
    
    // Decodificar y convertir PCM a WAV
    $pcmData = base64_decode($audioBase64);
    return pcmToWav($pcmData);
}

echo "\n🎙️ Generando audios de preview para 8 voces...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$totalFiles = 0;
$errors = 0;

foreach ($voices as $voiceName => $texts) {
    echo "🔊 Procesando voz: $voiceName\n";
    
    foreach ($texts as $type => $text) {
        $filename = strtolower($voiceName) . "_" . $type . ".wav";
        $filepath = $outputDir . "/" . $filename;
        
        // Si ya existe, saltar
        if (file_exists($filepath)) {
            echo "  ⏭️ $filename ya existe, saltando...\n";
            continue;
        }
        
        echo "  📝 Generando $type...\n";
        
        $wavData = generateAudio($text, $voiceName, $apiKey);
        
        if ($wavData) {
            file_put_contents($filepath, $wavData);
            $size = round(strlen($wavData) / 1024, 1);
            echo "  ✅ $filename ({$size}KB)\n";
            $totalFiles++;
        } else {
            echo "  ❌ Error generando $filename\n";
            $errors++;
        }
        
        // Esperar un poco para no saturar la API
        usleep(500000); // 0.5 segundos
    }
    
    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Generados: $totalFiles archivos\n";
echo "❌ Errores: $errors\n";

if ($totalFiles > 0) {
    echo "\n📋 Archivos guardados en: $outputDir\n";
    echo "🔗 Accesibles via: /storage/voice-previews/\n";
    
    // Crear symlink si no existe
    $publicLink = __DIR__ . '/public/storage';
    if (!file_exists($publicLink)) {
        echo "\n⚠️ Ejecuta 'php artisan storage:link' para crear el symlink\n";
    }
}

echo "\n🎉 ¡Listo! Los audios están pre-generados.\n";
