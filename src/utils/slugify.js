/**
 * Convert a product name to a URL-safe slug.
 * "Mocho Bermuda" → "mocho-bermuda"
 */
export function slugify(text) {
  if (!text) return ''
  return text
    .toString()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '') // Remove accents
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9\s-]/g, '')   // Remove non-alphanumeric
    .replace(/[\s_]+/g, '-')         // Spaces/underscores to hyphens
    .replace(/-+/g, '-')             // Collapse multiple hyphens
    .replace(/^-|-$/g, '')           // Trim hyphens
}

/**
 * Build the catalog product URL using a pure slug (no ID exposed).
 * e.g. "Camisa Polo" → /catalog/producto/camisa-polo
 */
export function productUrl(product) {
  if (!product) return '/catalog'
  return `/catalog/producto/${slugify(product.name)}`
}

/**
 * Find a product in a list by matching its slugified name against the URL slug.
 * Returns the matched product object or null.
 */
export function findProductBySlug(slug, products) {
  if (!slug || !products || products.length === 0) return null
  return products.find(p => slugify(p.name) === slug) || null
}

/**
 * @deprecated — kept for backward-compat with old slug-id URLs.
 * Extracts a numeric ID from a slug like "mocho-bermuda-42".
 */
export function extractIdFromSlug(slug) {
  if (!slug) return null
  const parts = slug.split('-')
  const last = parts[parts.length - 1]
  const id = parseInt(last, 10)
  return isNaN(id) ? null : id
}
