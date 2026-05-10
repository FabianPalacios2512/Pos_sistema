<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class GroqBrandService
{
    private string $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private string $model = 'llama-3.3-70b-versatile';

    /**
     * Google Fonts pre-approved for pairing
     */
    private array $fontLibrary = [
        'serif' => [
            'Playfair Display', 'Cormorant Garamond', 'Lora', 'Merriweather',
            'Libre Baskerville', 'Crimson Text', 'EB Garamond', 'Spectral'
        ],
        'sans' => [
            'Inter', 'Montserrat', 'Poppins', 'Raleway', 'Work Sans',
            'DM Sans', 'Nunito Sans', 'Source Sans 3', 'Outfit', 'Manrope'
        ],
        'display' => [
            'Oswald', 'Bebas Neue', 'Anton', 'Josefin Sans', 'Archivo Black'
        ]
    ];

    /**
     * Estilos de header disponibles en Template A
     * El AI elige el más adecuado según el tipo de negocio
     */
    private array $headerStyles = [
        'editorial-center' => 'Logo centrado con tipografía serif elegante — ideal para boutiques de moda, marcas de lujo, alta costura',
        'retail-left'      => 'Logo a la izquierda estilo retail masivo — supermercados, tiendas de conveniencia, marcas de consumo',
        'transparent-glass'=> 'Header transparente sobre el hero, efecto glass — cosmética, beauty, marcas aspiracionales con hero fuerte',
        'floating-pill'    => 'Header flotante tipo píldora, urbano y moderno — streetwear, tech, marcas juveniles y atrevidas',
        'centered-serif'   => 'Logo centrado serif estilo distribuidor/boutique — distribuidoras, ropa premium, marcas clásicas',
        'dark-premium'     => 'Fondo negro sólido con efecto glass oscuro al hacer scroll, íconos y texto siempre en blanco — USAR cuando la paleta tenga background oscuro/negro, alta costura noir, marcas de lujo oscuras, tecnología premium',
    ];

    /**
     * Estilos de bloque Hook/Spotlight (sección pre-catálogo)
     */
    private array $hookStyles = [
        'editorial-story' => 'Bloque editorial storytelling — boutique, lencería, alta costura, marcas con historia emocional',
        'urban-lookbook'  => 'Lookbook urbano asimétrico — streetwear, sneakers, drops, marcas juveniles y bold',
        'dynamic-bento'   => 'Cuadrícula bento dinámica — deportivo, tech, consumo rápido, marcas con muchos productos',
        'dark-noir'       => 'Brand story noir: fondo negro puro, imagen asimétrica 60/40, texto blanco + gris plata, CTA outline sutil — USAR únicamente con diseños oscuros (background negro/muy oscuro), alta costura noir, lujo minimalista dark, tech premium oscuro',
    ];

    /**
     * Estilos de Trust Strip (banda de confianza/valores)
     */
    private array $trustStripStyles = [
        'dark-contrast'  => 'Fondo negro, texto blanco, scroll horizontal — marcas premium oscuras, streetwear, Kharis-style',
        'minimal-border' => 'Blanco con borde fino, íconos lineales — boutiques minimalistas, Zara-style, moda editorial',
        'divided'        => 'Gris sutil con divisores verticales — cosmética, corporativo, marcas ordenadas y limpias',
        'marquee'        => 'Texto en loop infinito animado — streetwear urbano, marcas energéticas, drops exclusivos',
        'soft-pills'     => 'Pastillas blancas flotantes sobre fondo claro — tech, digital, marcas modernas y frescas',
    ];

    /**
     * Design directions — pool unificado con énfasis en la combinación de piezas
     */
    private array $designDirections = [
        [
            'mood' => 'luxury-editorial',
            'hint' => 'Paleta oscura o crema/beige/dorada. Serif elegante. Header: editorial-center o centered-serif. Hero: split-portrait o portrait. Hook: editorial-story. Trust: minimal-border o dark-contrast. CTA single-outline. Ticker muted-light.',
        ],
        [
            'mood' => 'bold-energetic',
            'hint' => 'Colores saturados y contrastantes. Tipografía display o grotesca grande. Header: floating-pill o retail-left. Hero: full-bleed. Hook: dynamic-bento. Trust: marquee o soft-pills. CTA bold-filled. Ticker soft-primary o contrast-dark.',
        ],
        [
            'mood' => 'fresh-modern',
            'hint' => 'Colores pastel o neones suaves. Sans moderna (DM Sans, Outfit, Manrope). Header: editorial-center o retail-left. Hero: full-bleed o centered-minimal. Hook: editorial-story o dynamic-bento. Trust: soft-pills o divided. CTA double-solid.',
        ],
        [
            'mood' => 'warm-artisan',
            'hint' => 'Tonos tierra, ocre, terracota, verde salvia. Header: centered-serif o editorial-center. Hero: split-portrait. Hook: editorial-story. Trust: minimal-border o divided. CTA single-outline. Ticker muted-light.',
        ],
        [
            'mood' => 'urban-street',
            'hint' => 'Negro, blanco y un acento neón o vibrante. Display (Oswald, Bebas Neue, Anton). Header: floating-pill. Hero: streetwear. Hook: urban-lookbook. Trust: dark-contrast o marquee. CTA bold-filled o double-solid. Ticker contrast-dark.',
        ],
        [
            'mood' => 'clean-minimal',
            'hint' => 'Paleta casi monocromática con un acento suave. Ultra-limpia (Inter, Work Sans). Header: editorial-center o transparent-glass. Hero: centered-minimal. Hook: editorial-story. Trust: minimal-border. CTA single-outline.',
        ],
        [
            'mood' => 'vibrant-tropical',
            'hint' => 'Colores vivos tropicales (coral, turquesa, amarillo, verde). Rounded (Nunito Sans, Poppins). Header: retail-left o floating-pill. Hero: full-bleed. Hook: dynamic-bento. Trust: soft-pills o marquee. CTA double-solid. Ticker soft-primary.',
        ],
        [
            'mood' => 'portrait-beauty',
            'hint' => 'Hero portrait para rostros/productos close-up. Colores suaves, rosados, nude. Header: transparent-glass. Hero: portrait. Hook: editorial-story. Trust: divided o soft-pills. Tipografía (Cormorant Garamond, Lora). CTA double-solid.',
        ],
        [
            'mood' => 'glass-premium',
            'hint' => 'Transparencia y efecto glass sobre imagen de fondo. Header: transparent-glass. Hero: full-bleed o portrait. Hook: urban-lookbook o editorial-story. Trust: minimal-border. Colores neutros con acento metálico. CTA single-outline.',
        ],
        [
            'mood' => 'dark-luxury',
            'hint' => 'Fondo negro o gris abismal (#0a0a0a, #111111). Acento dorado, plata o color vibrante único sobre negro. Header: dark-premium. Hero: dark-cinematic. Hook: dark-noir. Trust: dark-contrast o marquee. Tipografía serif liviana (Playfair Display, Cormorant Garamond) o display bold (Bebas Neue, Oswald). CTA: single-outline con borde blanco o bg-white text-black. Ticker contrast-dark. PALETA: background=#0a0a0a o #111111, text_dark=#000000, text_light=#ffffff, primary=color acento vivo (dorado, esmeralda, rojo, blanco). IMPORTANTE: NO uses bg-white en tarjetas; usa grises profundos y transparencias.',
        ],
    ];

    /**
     * Hero styles for layout_config
     */
    private array $heroStyles = [
        'full-bleed'       => 'Full-screen image with text and CTAs overlaying the photo (best for fashion, beauty, lifestyle)',
        'split-portrait'   => 'Half image + half text panel side by side (best for boutiques, artisan brands, cosmetics)',
        'centered-minimal' => 'Clean centered layout, product-focused (best for premium, minimalist, direct-conversion brands)',
        'streetwear'       => 'Asymmetric bold layout, huge typography overlapping image (best for urban brands, youth, bold/edgy aesthetics)',
        'portrait'         => 'Full-bleed close-up portrait photo, mixed sans+serif typography headline, two solid rectangular CTAs, integrated trust strip below (best for beauty, hair, cosmetics distributors, brands where face/product close-up photography is the hero)',
        'dark-cinematic'   => 'Full-screen noir: overlay dramático de abajo hacia arriba (rgba(0,0,0,0.92)→transparente), título serif masivo en blanco, CTA blanco sólido con texto negro — USAR únicamente cuando background sea negro/muy oscuro; ideal para alta costura noir, lujo oscuro, tech premium, marcas de moda masculina de alto impacto',
    ];

    /**
     * CTA styles for layout_config
     */
    private array $ctaStyles = [
        'double-solid'   => 'Two solid buttons: one light, one dark (high-conversion, like Kharis/luxury stores)',
        'single-outline' => 'One outlined button (minimal, elegant, editorial stores)',
        'bold-filled'    => 'One large filled button, bold color (energetic brands, discount stores)'
    ];

    /**
     * Category display styles
     */
    private array $categoryStyles = [
        'horizontal-pills' => 'Scrollable horizontal pill buttons (mobile-first, recommended)',
        'image-cards'      => 'Grid cards with product image background (visual-heavy stores)'
    ];

    /**
     * Ticker styles for top announcement bar
     */
    private array $tickerStyles = [
        'muted-light'   => 'Soft, light and premium top bar (recommended default)',
        'soft-primary'  => 'Top bar tinted with primary color for energetic brands',
        'contrast-dark' => 'Dark contrast top bar for bold/high-contrast brands'
    ];

    /**
     * Hero content density
     */
    private array $heroContentDensity = [
        'compact'  => 'Very short copy for fast-scanning ecommerce users',
        'balanced' => 'Balanced headline/subheadline lengths (recommended)',
        'rich'     => 'Slightly longer narrative copy'
    ];

    /**
     * Get rotating Groq API keys
     */
    private function getApiKeys(): array
    {
        return array_filter([
            config('services.groq.api_key_1'),
            config('services.groq.api_key_2'),
            config('services.groq.api_key_3'),
            config('services.groq.api_key_4'),
            config('services.groq.api_key_5'),
            config('services.groq.api_key_6'),
            config('services.groq.api_key_7'),
            config('services.groq.api_key_8'),
            config('services.groq.api_key_9'),
            config('services.groq.api_key_10'),
            config('services.groq.api_key_11'),
            config('services.groq.api_key_12'),
            config('services.groq.api_key_13'),
            config('services.groq.api_key_14'),
        ]);
    }

    /**
     * Generate complete brand identity from business description
     * @param int $slot 0-4 for parallel generation (ensures distinct directions), -1 for random
     */
    public function generateBrandIdentity(string $businessDescription, string $storeName, ?string $currentTemplate = null, string $storeType = 'general', int $slot = -1): array
    {
        // Pick design direction: if slot provided, use it to guarantee variety across parallel calls
        if ($slot >= 0) {
            // Shuffle pool deterministically using description hash so each session is unique
            // but the 5 parallel calls always get different directions
            $seed = abs(crc32($businessDescription . $storeName));
            mt_srand($seed);
            $shuffled = $this->designDirections;
            shuffle($shuffled);
            mt_srand();
            $direction = $shuffled[$slot % count($shuffled)];
        } else {
            $direction = $this->designDirections[array_rand($this->designDirections)];
        }

        $prompt = $this->buildBrandPrompt($businessDescription, $storeName, $direction);

        $apiKeys = $this->getApiKeys();
        if (empty($apiKeys)) {
            Log::error('[GroqBrand] No Groq API keys configured');
            return ['success' => false, 'error' => 'No hay claves de API configuradas'];
        }

        foreach ($apiKeys as $index => $apiKey) {
            try {
                $response = Http::timeout(60)->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->getSystemPrompt()
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.95,
                    'max_tokens' => 4000,
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $content = $responseData['choices'][0]['message']['content'] ?? '';

                    $parsed = $this->parseResponse($content);
                    if ($parsed) {
                        return ['success' => true, 'data' => $parsed];
                    }
                }

                Log::warning("[GroqBrand] Key {$index} responded but parsing failed");
            } catch (\Exception $e) {
                Log::error("[GroqBrand] Key {$index} error: " . $e->getMessage());
                continue;
            }
        }

        return ['success' => false, 'error' => 'No se pudo generar la identidad de marca. Intenta de nuevo.'];
    }

    /**
     * System prompt for the AI — Template A component-based architecture
     */
    private function getSystemPrompt(): string
    {
        $fontsJson         = json_encode($this->fontLibrary,      JSON_UNESCAPED_UNICODE);
        $heroStylesJson    = json_encode($this->heroStyles,        JSON_UNESCAPED_UNICODE);
        $ctaStylesJson     = json_encode($this->ctaStyles,         JSON_UNESCAPED_UNICODE);
        $categoryStylesJson= json_encode($this->categoryStyles,    JSON_UNESCAPED_UNICODE);
        $tickerStylesJson  = json_encode($this->tickerStyles,      JSON_UNESCAPED_UNICODE);
        $heroDensityJson   = json_encode($this->heroContentDensity,JSON_UNESCAPED_UNICODE);
        $headerStylesJson  = json_encode($this->headerStyles,      JSON_UNESCAPED_UNICODE);
        $hookStylesJson    = json_encode($this->hookStyles,        JSON_UNESCAPED_UNICODE);
        $trustStripJson    = json_encode($this->trustStripStyles,  JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
Eres un Director Creativo Senior especializado en e-commerce y branding digital para tiendas latinoamericanas.
Tu nivel es el de un arquitecto UI/UX de agencia con 15 años de experiencia en retail de alta gama.
Tu trabajo es crear identidades de marca profesionales, visualmente cohesivas, de nivel internacional, SIEMPRE apropiadas para el público objetivo del negocio.

ARQUITECTURA MODULAR: La tienda usa bloques intercambiables (Headers, Heroes, Hooks, Trust Strips).
Tu trabajo es combinar estas piezas creando SINERGIA visual — como un arquitecto que elige materiales complementarios.

REGLAS ESTRICTAS:
- Responde ÚNICAMENTE con un JSON válido, sin texto adicional, sin markdown, sin explicaciones
- Los textos deben ser en español latinoamericano profesional
- NUNCA uses emojis en los textos generados
- Los colores deben ser en formato hexadecimal (#RRGGBB)
- Los colores deben tener buen contraste y ser accesibles (WCAG AA)
- Las fuentes SOLO pueden ser de esta librería: {$fontsJson}
- Los textos de banner deben ser cortos, impactantes y persuasivos (estilo Zara, H&M, Nike)
- La sección "Sobre Nosotros" debe generar confianza y conexión emocional
- Los mensajes de valor deben ser profesionales sin ser genéricos
- Los anuncios del ticker deben ser breves y atractivos (max 50 caracteres c/u)
- En layout_config, prioriza experiencia mobile-first ecommerce

DIRECTRIZ CRÍTICA — INTELIGENCIA DEMOGRÁFICA:
Antes de elegir cualquier color o componente, identifica el público objetivo real del negocio:

- MUJERES ADULTAS (35-80 años), madres, amas de casa, profesionales mayores:
  → NUNCA Dark Mode. NUNCA fondos negros o grises oscuros.
  → Paleta obligatoria: fondos blancos (#ffffff), cremas (#fafaf8, #f5f0ea), rosados suaves, malvas, beige.
  → Colores primarios: suaves y femeninos (rosado, lila, verde salvia, coral, burdeos). NUNCA neones ni colores agresivos.
  → Tipografía: serif elegante (Playfair Display, Cormorant Garamond, Lora) para headings, sans limpia para body.
  → Componentes: editorial-center o centered-serif (header), portrait o split-portrait (hero), editorial-story (hook), minimal-border o divided (trust).

- PÚBLICO JUVENIL (15-30 años), streetwear, tendencias:
  → Puede usar Dark Mode si la marca lo requiere. Colores vivos y bold permitidos.
  → Componentes: floating-pill (header), streetwear o full-bleed (hero), urban-lookbook (hook).

- PÚBLICO GENERAL o MIXTO:
  → Fondo claro por defecto. Solo usar Dark si la descripción lo pide explícitamente (marca de tecnología, lujo masculino noir, alta costura oscura).

- NEGOCIOS DE SALUD, FARMACIA, ALIMENTOS, MASCOTAS:
  → SIEMPRE fondos claros, paletas limpias y confiables (blancos, verdes suaves, azules).

DIRECTRIZ DE CONTRASTE Y LEGIBILIDAD (OBLIGATORIA):
- Si el background es oscuro (#0a0a0a, #111111, #1a1a1a, #2a2a2a o cualquier hex con luminancia < 50%):
  → text_dark debe ser negro (#0a0a0a o #000000) — es el color del fondo, NO del texto
  → text_light debe ser blanco puro (#ffffff) o casi blanco (#f8f8f8) — es el color del TEXTO sobre fondos oscuros
  → header_style DEBE ser dark-premium (fondo negro, texto siempre blanco)
  → Los componentes de la página usarán text_light automáticamente para garantizar legibilidad
- Si el background es claro (#ffffff, cremas, pasteles):
  → text_dark debe ser el color oscuro del texto (#0a0a0a, #1a1a1a, #2c1a1a, etc.)
  → text_light debe ser el color claro para fondos de color (#f9fafb, #ffffff)
  → El header usará text_dark y se verá perfectamente legible

DIRECTRICES DE DISEÑO AVANZADAS:
1. COMBINACIONES NATURALES POR TIPO DE NEGOCIO:
   - Boutique / Moda Femenina / Alta Costura → Header: editorial-center. Hero: split-portrait o portrait. Hook: editorial-story. Trust: minimal-border.
   - Streetwear / Urbano / Sneakers → Header: floating-pill. Hero: streetwear. Hook: urban-lookbook. Trust: dark-contrast o marquee.
   - Deportivo / Tech / Consumo masivo → Header: retail-left. Hero: full-bleed. Hook: dynamic-bento. Trust: soft-pills.
   - Cosmética / Beauty / Hair → Header: transparent-glass. Hero: portrait. Hook: editorial-story. Trust: divided.
   - Lujo Noir (SOLO si negocio lo exige explícitamente) → Header: dark-premium. Hero: dark-cinematic. Hook: dark-noir. Trust: dark-contrast.

2. TEORÍA DEL COLOR MADURA (nivel agencia):
   - background: SIEMPRE neutro — blanco puro, crema sutil, o negro profundo. NUNCA fondo de color saturado.
   - primary: el color de acento principal para botones CTA y elementos destacados.
   - secondary: derivado o complementario del primary, más apagado. NUNCA un color completamente diferente y gritón.
   - accent: detalle de contraste. En paletas claras: tono oscuro del primario. En paletas oscuras: dorado o el primario brillante.

PIEZAS DISPONIBLES (elige la combinación más coherente con la marca):

HEADER (header_style): {$headerStylesJson}
HERO (hero_style): {$heroStylesJson}
BLOQUE PRE-CATÁLOGO / HOOK (hook_style): {$hookStylesJson}
BANDA DE CONFIANZA (trust_strip_style): {$trustStripJson}
CTA del hero (hero_cta_style): {$ctaStylesJson}
CATEGORÍAS (category_style): {$categoryStylesJson}
TICKER superior (ticker_style): {$tickerStylesJson}
DENSIDAD COPY HERO (hero_content_density): {$heroDensityJson}

FORMATO DE RESPUESTA JSON:
{
  "color_palette": {
    "primary": "#hex — color de acento para botones y elementos destacados",
    "secondary": "#hex — tono derivado del primary, más apagado",
    "accent": "#hex — detalle de contraste para bordes y detalles",
    "background": "#hex — SIEMPRE neutro: blanco, crema o negro profundo",
    "text_dark": "#hex — oscuro para texto sobre fondos claros (ej. #0a0a0a); o el mismo background oscuro si Dark Mode",
    "text_light": "#hex — claro para texto sobre fondos oscuros (ej. #ffffff o #f8f8f8)"
  },
  "fonts": {
    "heading": "NombreFuente",
    "body": "NombreFuente",
    "style_rationale": "breve explicación"
  },
  "banner_texts": {
    "headline": "Texto principal del hero banner",
    "subheadline": "Subtexto del banner",
    "cta_text": "Texto del botón CTA",
    "cta_secondary": "Texto del segundo botón (solo para hero_cta_style double-solid)"
  },
  "layout_config": {
    "header_style": "editorial-center|retail-left|transparent-glass|floating-pill|centered-serif|dark-premium",
    "hero_style": "full-bleed|split-portrait|centered-minimal|streetwear|portrait|dark-cinematic",
    "hero_cta_style": "double-solid|single-outline|bold-filled",
    "hero_text_position": "bottom-left|center|bottom-center",
    "hero_content_density": "compact|balanced|rich",
    "ticker_style": "muted-light|soft-primary|contrast-dark",
    "category_style": "horizontal-pills|image-cards",
    "editorial_mood": "luxury|fresh|bold|minimal",
    "hook_style": "editorial-story|urban-lookbook|dynamic-bento|dark-noir",
    "trust_strip_style": "dark-contrast|minimal-border|divided|marquee|soft-pills"
  },
  "about_us": "Texto completo de Nuestra Historia (2-3 párrafos, profesional y emotivo)",
  "value_messages": [
    "Mensaje de valor 1",
    "Mensaje de valor 2",
    "Mensaje de valor 3"
  ],
  "announcements": [
    "Anuncio ticker 1",
    "Anuncio ticker 2",
    "Anuncio ticker 3",
    "Anuncio ticker 4"
  ],
  "cross_sell_messages": [
    "Mensaje venta cruzada 1",
    "Mensaje venta cruzada 2",
    "Mensaje venta cruzada 3"
  ]
}
PROMPT;
    }

    /**
     * Build the user prompt
     */
    private function buildBrandPrompt(string $description, string $storeName, array $direction = []): string
    {
        $moodName = $direction['mood'] ?? 'custom';
        $moodHint = $direction['hint'] ?? '';

        return <<<PROMPT
NOMBRE DE LA TIENDA: {$storeName}

DESCRIPCIÓN DEL NEGOCIO (dada por el dueño):
"{$description}"

DIRECCIÓN CREATIVA ASIGNADA PARA ESTA GENERACIÓN: [{$moodName}]
{$moodHint}
Esta dirección es una guía de estilo para forzar variedad creativa — adáptala inteligentemente al tipo de negocio real. Si el negocio contradice completamente esta dirección (ej: dirección "urban-street" para una farmacia), prioriza el negocio pero mantén la energía del mood.

INSTRUCCIONES:
1. PRIMERO identifica el público objetivo real: edad, género, nivel socioeconómico, estilo de vida
2. Con base en el público, determina el estilo visual apropiado ANTES de elegir colores o componentes
   - Mujeres maduras (35-80): paleta clara, suave, elegante. NUNCA dark mode.
   - Público joven / urbano: puede ser bold, oscuro o vibrante según la marca
   - Adultos generales / familia: paleta neutra, limpia, confiable
3. Genera una paleta de colores que refleje al público y el negocio:
   - background: neutro (blanco, crema o negro profundo). NUNCA fondo saturado de color
   - text_dark: oscuro legible sobre fondos claros (#0a0a0a, #1a1a1a, etc.)
   - text_light: blanco o casi blanco (#ffffff, #f8f8f8) — OBLIGATORIO si el background es oscuro
4. Selecciona fuentes que coincidan con la personalidad de marca y el público
5. Escribe textos de banner originales y persuasivos para este negocio y su público específico
6. Redacta una historia "Sobre Nosotros" que genere confianza y conexión emocional con el público objetivo
7. Crea mensajes de valor diferenciadores y específicos
8. Genera anuncios cortos para la barra superior (ticker)
9. Escribe mensajes de venta cruzada profesionales
10. En layout_config elige la COMBINACIÓN DE PIEZAS más coherente con el público y la marca:
    - Si public objetivo = mujeres adultas/maduras → editorial-center + portrait/split-portrait + editorial-story + minimal-border
    - Si public objetivo = jóvenes urbanos → floating-pill + streetwear + urban-lookbook + marquee
    - Si background es oscuro → dark-premium + dark-cinematic + dark-noir + dark-contrast (SIEMPRE como trio)
    - Si background es claro → NUNCA usar componentes dark-*
    - Los demás campos de layout_config según el mood asignado y el público
11. IMPORTANTE: Sé creativo y específico. Evita soluciones genéricas. Cada generación debe sentirse única.

Recuerda: La tienda se llama "{$storeName}". Personaliza TODO para este negocio.
Genera SOLO el JSON, nada más.
PROMPT;
    }

    /**
     * Parse and validate the AI response
     */
    private function parseResponse(string $content): ?array
    {
        // Clean markdown formatting if present
        $content = trim($content);
        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('[GroqBrand] Invalid JSON: ' . json_last_error_msg());
            return null;
        }

        // Validate required sections (recommended_template removed — always Template A)
        $requiredKeys = ['color_palette', 'fonts', 'banner_texts', 'about_us', 'value_messages', 'announcements', 'cross_sell_messages'];
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                Log::error("[GroqBrand] Missing required key: {$key}");
                return null;
            }
        }

        // Default layout_config if AI didn't include it
        $validHeroStyles     = ['full-bleed', 'split-portrait', 'centered-minimal', 'streetwear', 'portrait', 'dark-cinematic'];
        $validCtaStyles      = ['double-solid', 'single-outline', 'bold-filled'];
        $validCategoryStyles = ['horizontal-pills', 'image-cards'];
        $validTextPositions  = ['bottom-left', 'center', 'bottom-center'];
        $validTickerStyles   = ['muted-light', 'soft-primary', 'contrast-dark'];
        $validHeroDensity    = ['compact', 'balanced', 'rich'];
        $validHeaderStyles   = ['editorial-center', 'retail-left', 'transparent-glass', 'floating-pill', 'centered-serif', 'dark-premium'];
        $validHookStyles     = ['editorial-story', 'urban-lookbook', 'dynamic-bento', 'dark-noir'];
        $validTrustStyles    = ['dark-contrast', 'minimal-border', 'divided', 'marquee', 'soft-pills'];

        if (!isset($data['layout_config'])) {
            $data['layout_config'] = [
                'header_style'         => $validHeaderStyles[array_rand($validHeaderStyles)],
                'hero_style'           => $validHeroStyles[array_rand($validHeroStyles)],
                'hero_cta_style'       => $validCtaStyles[array_rand($validCtaStyles)],
                'hero_text_position'   => 'bottom-left',
                'hero_content_density' => 'balanced',
                'ticker_style'         => $validTickerStyles[array_rand($validTickerStyles)],
                'category_style'       => 'horizontal-pills',
                'editorial_mood'       => 'luxury',
                'hook_style'           => $validHookStyles[array_rand($validHookStyles)],
                'trust_strip_style'    => $validTrustStyles[array_rand($validTrustStyles)],
            ];
        } else {
            // Validate each component — fallback to random valid value if invalid
            if (!in_array($data['layout_config']['header_style'] ?? '', $validHeaderStyles)) {
                $data['layout_config']['header_style'] = $validHeaderStyles[array_rand($validHeaderStyles)];
            }
            if (!in_array($data['layout_config']['hook_style'] ?? '', $validHookStyles)) {
                $data['layout_config']['hook_style'] = $validHookStyles[array_rand($validHookStyles)];
            }
            if (!in_array($data['layout_config']['trust_strip_style'] ?? '', $validTrustStyles)) {
                $data['layout_config']['trust_strip_style'] = $validTrustStyles[array_rand($validTrustStyles)];
            }
            if (!in_array($data['layout_config']['hero_style'] ?? '', $validHeroStyles)) {
                $data['layout_config']['hero_style'] = $validHeroStyles[array_rand($validHeroStyles)];
            }
            if (!in_array($data['layout_config']['hero_cta_style'] ?? '', $validCtaStyles)) {
                $data['layout_config']['hero_cta_style'] = $validCtaStyles[array_rand($validCtaStyles)];
            }
            if (!in_array($data['layout_config']['category_style'] ?? '', $validCategoryStyles)) {
                $data['layout_config']['category_style'] = 'horizontal-pills';
            }
            if (!in_array($data['layout_config']['hero_text_position'] ?? '', $validTextPositions)) {
                $data['layout_config']['hero_text_position'] = 'bottom-left';
            }
            if (!in_array($data['layout_config']['ticker_style'] ?? '', $validTickerStyles)) {
                $data['layout_config']['ticker_style'] = $validTickerStyles[array_rand($validTickerStyles)];
            }
            if (!in_array($data['layout_config']['hero_content_density'] ?? '', $validHeroDensity)) {
                $data['layout_config']['hero_content_density'] = 'balanced';
            }
        }

        // Validate color format
        if (isset($data['color_palette'])) {
            foreach ($data['color_palette'] as $key => $color) {
                if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                    Log::warning("[GroqBrand] Invalid color for {$key}: {$color}");
                    $data['color_palette'][$key] = '#1a1a1a';
                }
            }
        }

        // Validate fonts exist in our library
        $allFonts = array_merge(
            $this->fontLibrary['serif'],
            $this->fontLibrary['sans'],
            $this->fontLibrary['display']
        );

        if (isset($data['fonts']['heading']) && !in_array($data['fonts']['heading'], $allFonts)) {
            $data['fonts']['heading'] = 'Playfair Display';
        }
        if (isset($data['fonts']['body']) && !in_array($data['fonts']['body'], $allFonts)) {
            $data['fonts']['body'] = 'Inter';
        }

        // Always use Template A (visual-story) — components within it vary via layout_config
        $data['recommended_template'] = 'visual-story';

        return $data;
    }

    /**
     * Save generated brand identity to database
     */
    public function saveBrandIdentity(string $tenantId, array $brandData, string $businessDescription): bool
    {
        try {
            $updateData = [
                'business_description' => $businessDescription,
                'ai_color_palette' => json_encode($brandData['color_palette']),
                'ai_fonts' => json_encode($brandData['fonts']),
                'ai_recommended_template' => $brandData['recommended_template'],
                'ai_banner_texts' => json_encode($brandData['banner_texts']),
                'ai_about_us' => $brandData['about_us'],
                'ai_value_messages' => json_encode($brandData['value_messages']),
                'ai_announcements' => json_encode($brandData['announcements']),
                'ai_cross_sell_messages' => json_encode($brandData['cross_sell_messages']),
                'ai_layout_config' => json_encode($brandData['layout_config'] ?? []),
                'ai_generated_at' => now(),
                'updated_at' => now(),
            ];

            $exists = DB::table('web_catalog_configs')->where('tenant_id', $tenantId)->exists();

            if ($exists) {
                DB::table('web_catalog_configs')
                    ->where('tenant_id', $tenantId)
                    ->update($updateData);
            } else {
                $updateData['tenant_id'] = $tenantId;
                $updateData['created_at'] = now();
                DB::table('web_catalog_configs')->insert($updateData);
            }

            // Clear cache
            Cache::forget("web_catalog_config_{$tenantId}");

            return true;
        } catch (\Exception $e) {
            Log::error('[GroqBrand] Error saving brand: ' . $e->getMessage());
            return false;
        }
    }
}
