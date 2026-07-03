/**
 * Central registry of all blocks in the 105POS Catalog System.
 * Contains metadata, category, and aesthetic pairing rules for every component.
 */
export const blocks = {
  headers: {
    'editorial-center': {
      name: 'Editorial Centered',
      description: 'Centered logo with serif typeface. Best for high-end fashion and boutiques.',
      moods: ['luxury-editorial', 'clean-minimal', 'warm-artisan', 'portrait-beauty'],
      bgCompatibility: 'light',
      requiresDark: false
    },
    'retail-left': {
      name: 'Retail Left',
      description: 'Left-aligned logo with solid retail look. Best for mass consumer shops and supermarkets.',
      moods: ['bold-energetic', 'fresh-modern', 'vibrant-tropical', 'industrial-raw', 'candy-pop'],
      bgCompatibility: 'any',
      requiresDark: false
    },
    'transparent-glass': {
      name: 'Transparent Glassmorphism',
      description: 'Floating glass overlay that becomes solid on scroll. Aesthetically strong.',
      moods: ['glass-premium', 'portrait-beauty', 'clean-minimal', 'botanical-wellness', 'scandinavian-neutral'],
      bgCompatibility: 'any',
      requiresDark: false
    },
    'floating-pill': {
      name: 'Floating Pill',
      description: 'Floating rounded capsule header. Urban, trendy, and youth-oriented.',
      moods: ['urban-street', 'bold-energetic', 'vibrant-tropical', 'candy-pop'],
      bgCompatibility: 'any',
      requiresDark: false
    },
    'centered-serif': {
      name: 'Centered Serif',
      description: 'Serif logo with subtitle/tagline layout. Traditional, premium.',
      moods: ['luxury-editorial', 'warm-artisan', 'retro-vintage'],
      bgCompatibility: 'light',
      requiresDark: false
    },
    'dark-premium': {
      name: 'Dark Premium',
      description: 'Solid black background with white icons. Required for dark layouts.',
      moods: ['dark-luxury', 'urban-street', 'industrial-raw'],
      bgCompatibility: 'dark',
      requiresDark: true
    },
    'split-action': {
      name: 'Split Action Search',
      description: 'Logo left, search bar center, icons right. Best for large inventories.',
      moods: ['industrial-raw', 'retail-left', 'bold-energetic'],
      bgCompatibility: 'any',
      requiresDark: false
    },
    'minimal-float': {
      name: 'Minimal Float',
      description: 'Apple-style minimal pill that reveals on scroll-up only.',
      moods: ['scandinavian-neutral', 'clean-minimal', 'candy-pop'],
      bgCompatibility: 'any',
      requiresDark: false
    },
    'general-header-search': {
      name: 'General Search Header',
      description: 'Header with large central search bar, typical for general retail/marketplaces.',
      moods: ['retail-conversion', 'app-native'],
      bgCompatibility: 'light',
      requiresDark: false
    }
  },

  heroes: {
    'full-bleed': {
      name: 'Full Bleed Image',
      description: 'Full viewport image with text overlay.',
      moods: ['bold-energetic', 'fresh-modern', 'vibrant-tropical', 'glass-premium'],
      bgCompatibility: 'any'
    },
    'split-portrait': {
      name: 'Split Portrait 50/50',
      description: 'Clean side-by-side splits of text and image.',
      moods: ['luxury-editorial', 'warm-artisan', 'retro-vintage'],
      bgCompatibility: 'light'
    },
    'centered-minimal': {
      name: 'Centered Minimal Conversion',
      description: 'Clean centered typography focusing on direct conversions.',
      moods: ['fresh-modern', 'clean-minimal'],
      bgCompatibility: 'light'
    },
    'streetwear': {
      name: 'Streetwear Graphic',
      description: 'Bold text overlays with street-style typography.',
      moods: ['urban-street', 'bold-energetic'],
      bgCompatibility: 'any'
    },
    'portrait': {
      name: 'Close-Up Portrait',
      description: 'Face or vertical product shot with details.',
      moods: ['portrait-beauty', 'luxury-editorial', 'glass-premium'],
      bgCompatibility: 'any'
    },
    'dark-cinematic': {
      name: 'Dark Cinematic Noir',
      description: 'Moody overlay with bold text over dark graphics.',
      moods: ['dark-luxury'],
      bgCompatibility: 'dark',
      requiresDark: true
    },
    'carousel': {
      name: 'Campaign Carousel',
      description: 'Multi-slide rotating carousel with progress indicators.',
      moods: ['candy-pop', 'bold-energetic', 'fresh-modern'],
      bgCompatibility: 'any'
    },
    'centered': {
      name: 'Typography Pure',
      description: 'Pure stylized typography, no image, solid background.',
      moods: ['scandinavian-neutral', 'clean-minimal'],
      bgCompatibility: 'any'
    },
    'image-grid': {
      name: 'Bento Grid Mosaics',
      description: 'Asymmetric grid layout featuring multiple items.',
      moods: ['retro-vintage', 'luxury-editorial'],
      bgCompatibility: 'any'
    },
    'minimal': {
      name: 'Ultra Clean Minimalist',
      description: 'Lots of whitespace, tiny indicators, premium feel.',
      moods: ['scandinavian-neutral', 'clean-minimal'],
      bgCompatibility: 'light'
    },
    'overlay': {
      name: 'Color Overlay Campaign',
      description: 'High-contrast text on color tinted backgrounds.',
      moods: ['industrial-raw', 'bold-energetic'],
      bgCompatibility: 'any'
    },
    'video-loop': {
      name: 'Autoplay Video Loop',
      description: 'Cinematic looping video background.',
      moods: ['urban-street', 'industrial-raw', 'bold-energetic'],
      bgCompatibility: 'any'
    },
    'parallax': {
      name: 'Parallax Layer Scroll',
      description: 'Slow-scrolling background image relative to view.',
      moods: ['botanical-wellness', 'glass-premium', 'luxury-editorial'],
      bgCompatibility: 'any'
    },
    'gradient-wave': {
      name: 'Animated Gradient Mesh',
      description: 'Smooth slow-changing animated colors without graphics.',
      moods: ['botanical-wellness', 'fresh-modern'],
      bgCompatibility: 'any'
    },
    'general-hero-promo': {
      name: 'General Retail Promo Hero',
      description: 'Banner dedicated to general mass-market promotions.',
      moods: ['retail-conversion', 'app-native'],
      bgCompatibility: 'light'
    }
  },

  hooks: {
    'editorial-story': {
      name: 'Editorial Story',
      description: 'Storytelling segment with large typography. Warm and authentic.',
      moods: ['luxury-editorial', 'warm-artisan', 'fresh-modern', 'clean-minimal', 'glass-premium'],
      bgCompatibility: 'light'
    },
    'urban-lookbook': {
      name: 'Urban Lookbook',
      description: 'Visual drop layout for sneakers and street campaigns.',
      moods: ['urban-street', 'bold-energetic', 'glass-premium'],
      bgCompatibility: 'any'
    },
    'dynamic-bento': {
      name: 'Dynamic Bento Grid',
      description: 'Multi-grid cells of details, text, and promo.',
      moods: ['bold-energetic', 'fresh-modern', 'vibrant-tropical', 'industrial-raw'],
      bgCompatibility: 'any'
    },
    'dark-noir': {
      name: 'Noir Manifesto Story',
      description: 'Pure black backdrop with silver outlines and clean narrative text.',
      moods: ['dark-luxury'],
      bgCompatibility: 'dark',
      requiresDark: true
    },
    'testimonials': {
      name: 'Client Testimonials',
      description: 'Horizontal scroll reviews to establish strong social proof.',
      moods: ['botanical-wellness', 'candy-pop', 'clean-minimal', 'vibrant-tropical'],
      bgCompatibility: 'any'
    },
    'collection-grid': {
      name: 'Collection Showcase Grid',
      description: 'Clean visual category index to navigate campaigns.',
      moods: ['retro-vintage', 'candy-pop', 'vibrant-tropical'],
      bgCompatibility: 'any'
    },
    'brand-manifesto': {
      name: 'Brand Typography Statement',
      description: 'Philosophy quote block with fine author signature.',
      moods: ['botanical-wellness', 'scandinavian-neutral', 'luxury-editorial'],
      bgCompatibility: 'any'
    },
    'general-bento-departments': {
      name: 'General Departments Bento',
      description: 'Grid layout for browsing multiple different retail departments.',
      moods: ['retail-conversion', 'app-native'],
      bgCompatibility: 'light'
    },
    'general-trust-benefits': {
      name: 'General Trust & Benefits',
      description: 'Functional large blocks highlighting free shipping, support, etc.',
      moods: ['retail-conversion', 'app-native'],
      bgCompatibility: 'light'
    }
  },

  trustStrips: {
    'dark-contrast': {
      name: 'Dark Contrast Row',
      description: 'Dark background ribbon with fine contrast metrics.',
      moods: ['dark-luxury', 'urban-street', 'industrial-raw']
    },
    'minimal-border': {
      name: 'Minimal Border Line',
      description: 'Ultra-thin borders, clean text items. Ideal for boutiques.',
      moods: ['luxury-editorial', 'clean-minimal', 'botanical-wellness', 'scandinavian-neutral', 'retro-vintage']
    },
    'divided': {
      name: 'Corporate Dividers',
      description: 'Segmented light gray bars. Feels clean and organized.',
      moods: ['divided', 'warm-artisan', 'scandinavian-neutral']
    },
    'marquee': {
      name: 'Infinite Marquee Loop',
      description: 'High energy endless text ticker.',
      moods: ['urban-street', 'bold-energetic', 'candy-pop', 'vibrant-tropical']
    },
    'soft-pills': {
      name: 'Floating Soft Pills',
      description: 'Rounded capsules floating over light background.',
      moods: ['fresh-modern', 'candy-pop', 'vibrant-tropical']
    },
    'icon-grid': {
      name: 'Beneficial Icon Grid',
      description: 'Four grid blocks highlighting store policies.',
      moods: ['retro-vintage', 'botanical-wellness']
    },
    'countdown': {
      name: 'Flash Sale Urgency Timer',
      description: 'Dynamic countdown display ticking for promo campaigns.',
      moods: ['industrial-raw', 'bold-energetic']
    }
  }
}
