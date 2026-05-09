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
     * Available templates
     */
    private array $templates = [
        'visual-story' => 'Optimal for fashion boutiques, luxury brands, visual storytelling',
        'speed-market' => 'Optimal for supermarkets, convenience stores, fast browsing',
        'modern-grid' => 'Optimal for general retail, electronics, variety stores'
    ];

    /**
     * Hero styles for layout_config
     */
    private array $heroStyles = [
        'full-bleed'       => 'Full-screen image with text and CTAs overlaying the photo (best for fashion, beauty, lifestyle)',
        'split-portrait'   => 'Half image + half text panel side by side (best for boutiques, artisan brands, cosmetics)',
        'centered-minimal' => 'Clean centered layout, product-focused (best for premium, minimalist, direct-conversion brands)',
        'streetwear'       => 'Asymmetric bold layout, huge typography overlapping image (best for urban brands, youth, bold/edgy aesthetics)',
        'portrait'         => 'Full-bleed close-up portrait photo, mixed sans+serif typography headline, two solid rectangular CTAs, integrated trust strip below (best for beauty, hair, cosmetics distributors, brands where face/product close-up photography is the hero)'
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
     */
    public function generateBrandIdentity(string $businessDescription, string $storeName, ?string $currentTemplate = null): array
    {
        $prompt = $this->buildBrandPrompt($businessDescription, $storeName, $currentTemplate);

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
                    'temperature' => 0.7,
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
     * System prompt for the AI
     */
    private function getSystemPrompt(): string
    {
        $fontsJson = json_encode($this->fontLibrary, JSON_UNESCAPED_UNICODE);
        $templatesJson = json_encode($this->templates, JSON_UNESCAPED_UNICODE);
        $heroStylesJson = json_encode($this->heroStyles, JSON_UNESCAPED_UNICODE);
        $ctaStylesJson = json_encode($this->ctaStyles, JSON_UNESCAPED_UNICODE);
        $categoryStylesJson = json_encode($this->categoryStyles, JSON_UNESCAPED_UNICODE);
        $tickerStylesJson = json_encode($this->tickerStyles, JSON_UNESCAPED_UNICODE);
        $heroDensityJson = json_encode($this->heroContentDensity, JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
Eres un director creativo de marca experto en e-commerce y branding digital para tiendas online latinoamericanas.
Tu trabajo es crear identidades de marca profesionales, persuasivas y coherentes.

REGLAS ESTRICTAS:
- Responde ÚNICAMENTE con un JSON válido, sin texto adicional, sin markdown, sin explicaciones
- Los textos deben ser en español latinoamericano profesional
- NUNCA uses emojis en los textos generados
- Los colores deben ser en formato hexadecimal (#RRGGBB)
- Los colores deben tener buen contraste y ser accesibles (WCAG AA)
- Las fuentes SOLO pueden ser de esta librería: {$fontsJson}
- Las plantillas disponibles son: {$templatesJson}
- Los textos de banner deben ser cortos, impactantes y persuasivos (estilo Zara, H&M, Nike)
- La sección "Sobre Nosotros" debe generar confianza y conexión emocional
- Los mensajes de valor deben ser profesionales sin ser genéricos
- Los mensajes de venta cruzada deben incitar a completar el "look" o la compra
- Los anuncios del ticker deben ser breves y atractivos (max 50 caracteres c/u)
- En layout_config, prioriza experiencia mobile-first ecommerce
- Evita barras superiores negras por defecto; usa negro solo cuando realmente encaje con la marca

ESTILOS DE HERO disponibles: {$heroStylesJson}
ESTILOS DE CTA para hero: {$ctaStylesJson}
ESTILOS DE CATEGORÍAS: {$categoryStylesJson}
ESTILOS DE TICKER: {$tickerStylesJson}
DENSIDAD DE CONTENIDO HERO: {$heroDensityJson}

FORMATO DE RESPUESTA JSON:
{
  "color_palette": {
    "primary": "#hex",
    "secondary": "#hex",
    "accent": "#hex",
    "background": "#hex",
    "text_dark": "#hex",
    "text_light": "#hex"
  },
  "fonts": {
    "heading": "NombreFuente",
    "body": "NombreFuente",
    "style_rationale": "breve explicación"
  },
  "recommended_template": "visual-story|speed-market|modern-grid",
  "banner_texts": {
    "headline": "Texto principal del hero banner",
    "subheadline": "Subtexto del banner",
        "cta_text": "Texto del botón CTA",
        "cta_secondary": "Texto del segundo botón CTA (solo para double-solid)"
    },
    "layout_config": {
        "hero_style": "full-bleed|split-portrait|centered-minimal|streetwear|portrait",
        "hero_cta_style": "double-solid|single-outline|bold-filled",
        "hero_text_position": "bottom-left|center|bottom-center",
        "hero_content_density": "compact|balanced|rich",
        "ticker_style": "muted-light|soft-primary|contrast-dark",
        "category_style": "horizontal-pills|image-cards",
        "editorial_mood": "luxury|fresh|bold|minimal"
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
    private function buildBrandPrompt(string $description, string $storeName, ?string $currentTemplate): string
    {
        $templateNote = $currentTemplate
            ? "La plantilla actual es '{$currentTemplate}'. Puedes mantenerla o recomendar otra si es más adecuada."
            : "Recomienda la plantilla más adecuada.";

        return <<<PROMPT
NOMBRE DE LA TIENDA: {$storeName}

DESCRIPCIÓN DEL NEGOCIO (dada por el dueño):
"{$description}"

INSTRUCCIONES:
1. Analiza el tipo de negocio, el estilo, el público objetivo y la personalidad de marca
2. Genera una paleta de colores que refleje la identidad del negocio
3. Selecciona fuentes que coincidan con la personalidad (serif para elegancia, sans para modernidad)
4. {$templateNote}
5. Escribe textos de banner que capten la atención del cliente ideal
6. Redacta una historia "Sobre Nosotros" que genere confianza y conexión emocional con el comprador
7. Crea mensajes de valor que diferencien la tienda
8. Genera anuncios cortos para la barra superior del sitio (ticker)
9. Escribe mensajes profesionales de venta cruzada que complementen la compra
10. Define layout_config coherente con el negocio (hero, CTA, ticker, densidad de texto, estilo de categorías)
11. Para hero_style: usa "portrait" para marcas de belleza, cabello, cosméticos o distribuidoras donde las fotos tipo retrato o close-up de producto/persona son el centro. Usa "streetwear" solo si la marca es explícitamente urbana, juvenil, bold o edgy. Usa "split-portrait" para marcas equilibradas y minimalistas. Usa "full-bleed" para moda editorial y lifestyle. Usa "centered-minimal" para marcas premium de conversión directa.

Recuerda: La tienda se llama "{$storeName}". Personaliza todo para este negocio específico.
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

        // Validate required sections
        $requiredKeys = ['color_palette', 'fonts', 'recommended_template', 'banner_texts', 'about_us', 'value_messages', 'announcements', 'cross_sell_messages'];
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                Log::error("[GroqBrand] Missing required key: {$key}");
                return null;
            }
        }

        // Default layout_config if AI didn't include it
        if (!isset($data['layout_config'])) {
            $data['layout_config'] = [
                'hero_style' => 'full-bleed',
                'hero_cta_style' => 'single-outline',
                'hero_text_position' => 'bottom-left',
                'hero_content_density' => 'balanced',
                'ticker_style' => 'muted-light',
                'category_style' => 'horizontal-pills',
                'editorial_mood' => 'luxury'
            ];
        } else {
            $validHeroStyles = ['full-bleed', 'split-portrait', 'centered-minimal', 'streetwear', 'portrait'];
            $validCtaStyles = ['double-solid', 'single-outline', 'bold-filled'];
            $validCategoryStyles = ['horizontal-pills', 'image-cards'];
            $validTextPositions = ['bottom-left', 'center', 'bottom-center'];
            $validTickerStyles = ['muted-light', 'soft-primary', 'contrast-dark'];
            $validHeroDensity = ['compact', 'balanced', 'rich'];

            if (!in_array($data['layout_config']['hero_style'] ?? '', $validHeroStyles)) {
                $data['layout_config']['hero_style'] = 'full-bleed';
            }
            if (!in_array($data['layout_config']['hero_cta_style'] ?? '', $validCtaStyles)) {
                $data['layout_config']['hero_cta_style'] = 'single-outline';
            }
            if (!in_array($data['layout_config']['category_style'] ?? '', $validCategoryStyles)) {
                $data['layout_config']['category_style'] = 'horizontal-pills';
            }
            if (!in_array($data['layout_config']['hero_text_position'] ?? '', $validTextPositions)) {
                $data['layout_config']['hero_text_position'] = 'bottom-left';
            }
            if (!in_array($data['layout_config']['ticker_style'] ?? '', $validTickerStyles)) {
                $data['layout_config']['ticker_style'] = 'muted-light';
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

        // Validate template
        $validTemplates = array_keys($this->templates);
        if (!in_array($data['recommended_template'], $validTemplates)) {
            $data['recommended_template'] = 'visual-story';
        }

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
