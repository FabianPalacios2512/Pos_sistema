"""
Fix all U+FFFD replacement characters in CatalogTemplateA.vue
These are Spanish accented characters that got corrupted.
"""

FILE = 'src/components/catalog/CatalogTemplateA.vue'
R = '\uFFFD'  # replacement character

# Ordered from LONGEST to SHORTEST to avoid partial matches
fixes = [
    # Phrases
    (f'\u00bfQu{R} est{R}s buscando?', '¿Qué estás buscando?'),
    (f'Lun{R}S{R}b 8am{R}6pm', 'Lun–Sáb 8am–6pm'),
    (f'dise{R}o, calidad y car{R}cter', 'diseño, calidad y carácter'),
    (f'Toda la colecci{R}n', 'Toda la colección'),
    (f'Colecci{R}n seleccionada', 'Colección seleccionada'),
    (f'Sin categor{R}a', 'Sin categoría'),
    (f'Nueva Colecci{R}n', 'Nueva Colección'),
    (f'Ver Colecci{R}n', 'Ver Colección'),
    (f'Colecci{R}n 2026', 'Colección 2026'),
    (f'Colecci{R}n principal', 'Colección principal'),
    (f'pre-cat{R}logo', 'pre-catálogo'),
    (f'Ferreter{R}a/cat{R}logo', 'Ferretería/catálogo'),
    (f'barra b{R}squeda', 'barra búsqueda'),
    (f'b{R}squeda m{R}vil', 'búsqueda móvil'),
    (f'Slider Simple (Solo M{R}ximo)', 'Slider Simple (Solo Máximo)'),
    (f'Precio m{R}ximo', 'Precio máximo'),
    (f'precio m{R}ximo', 'precio máximo'),
    (f'm{R}nimo y m{R}ximo', 'mínimo y máximo'),
    (f'm{R}vil, m{R}ximo 4', 'móvil, máximo 4'),
    (f'Env{R}o Gratis', 'Envío Gratis'),
    (f'sin inter{R}s', 'sin interés'),
    (f'en 30 d{R}as', 'en 30 días'),
    (f'pa{R}s', 'país'),
    (f'R{R}pido y seguro', 'Rápido y seguro'),
    (f'r{R}pido', 'rápido'),
    (f'obsesi{R}n', 'obsesión'),
    (f'Lencer{R}a', 'Lencería'),
    (f'Alta Costura', 'Alta Costura'),  # no fix needed
    (f'asim{R}trico', 'asimétrico'),
    (f'fotograf{R}a', 'fotografía'),
    (f'Tipograf{R}a Serif Elegante', 'Tipografía Serif Elegante'),
    (f'Tipograf{R}a Serif', 'Tipografía Serif'),
    (f'tipograf{R}a', 'tipografía'),
    (f'Tipograf{R}a', 'Tipografía'),
    (f'Navegaci{R}n', 'Navegación'),
    (f'Lupa M{R}vil', 'Lupa Móvil'),
    (f'L{R}neas finas', 'Líneas finas'),
    (f'L{R}nea decorativa', 'Línea decorativa'),
    (f'l{R}nea', 'línea'),
    (f'L{R}nea', 'Línea'),
    (f'Men{R} Hamburguesa', 'Menú Hamburguesa'),
    (f'Men{R} Lateral', 'Menú Lateral'),
    (f'Tecnolog{R}a por', 'Tecnología por'),
    (f'Tecnolog{R}a', 'Tecnología'),
    (f'tecnolog{R}a', 'tecnología'),
    (f'Animaci{R}n del ticker', 'Animación del ticker'),
    (f'Animaci{R}n Carrusel', 'Animación Carrusel'),
    (f'Animaci{R}n Fade', 'Animación Fade'),
    (f'animaci{R}n', 'animación'),
    (f'Animaci{R}n', 'Animación'),
    (f'Selecci{R}n de Variantes', 'Selección de Variantes'),
    (f'selecci{R}n', 'selección'),
    (f'Selecci{R}n', 'Selección'),
    (f'conversi{R}n', 'conversión'),
    (f'Conversi{R}n', 'Conversión'),
    (f'Categor{R}as', 'Categorías'),
    (f'categor{R}as', 'categorías'),
    (f'categor{R}a seleccionada', 'categoría seleccionada'),
    (f'Categor{R}a - Minimalista', 'Categoría - Minimalista'),
    (f'Categor{R}a', 'Categoría'),
    (f'categor{R}a', 'categoría'),
    (f'Im{R}genes subidas', 'Imágenes subidas'),
    (f'Im{R}genes del carrusel', 'Imágenes del carrusel'),
    (f'im{R}genes', 'imágenes'),
    (f'Im{R}genes', 'Imágenes'),
    (f'colecci{R}n', 'colección'),
    (f'Colecci{R}n', 'Colección'),
    (f'Bot{R}n Agregar', 'Botón Agregar'),
    (f'Bot{R}n FILTRAR', 'Botón FILTRAR'),
    (f'Bot{R}n ORDENAR', 'Botón ORDENAR'),
    (f'Bot{R}n Limpiar', 'Botón Limpiar'),
    (f'bot{R}n', 'botón'),
    (f'Bot{R}n', 'Botón'),
    (f'{R}REA PRINCIPAL', 'ÁREA PRINCIPAL'),
    (f'Header de Cat{R}logo', 'Header de Catálogo'),
    (f'Cat{R}logo Curado', 'Catálogo Curado'),
    (f'cat{R}logo', 'catálogo'),
    (f'Cat{R}logo', 'Catálogo'),
    (f'seg{R}n', 'según'),
    (f's{R}lido', 'sólido'),
    (f'S{R}lida', 'Sólida'),
    (f'M{R}ximo', 'Máximo'),
    (f'm{R}ximo', 'máximo'),
    (f'm{R}nimo', 'mínimo'),
    (f'M{R}vil', 'Móvil'),
    (f'm{R}vil', 'móvil'),
    (f'V{R}lez', 'Vélez'),
    (f'Men{R}', 'Menú'),
    (f'men{R}', 'menú'),
    (f'din{R}mico', 'dinámico'),
    (f'Env{R}o', 'Envío'),
    (f'env{R}o', 'envío'),
    (f'dise{R}o', 'diseño'),
    (f'car{R}cter', 'carácter'),
    (f'M{R}todos', 'Métodos'),
    (f'Ferreter{R}a', 'Ferretería'),
    (f'ferreter{R}a', 'ferretería'),
    (f'obsesi{R}n', 'obsesión'),
    (f'B{R}squeda', 'Búsqueda'),
    (f'b{R}squeda', 'búsqueda'),
    (f'est{R}s', 'estás'),
    (f'\u00bfQu{R}', '¿Qué'),
    (f'inter{R}s', 'interés'),
    (f'd{R}as', 'días'),
    (f'R{R}pido', 'Rápido'),
    (f'pa{R}s', 'país'),
    (f'filtrados', 'filtrados'),  # check if '? filtrados' needs fix
    # Dash replacements (? used as em-dash in comments)
    (f' {R} ', ' — '),
    # Remaining single replacements
    (f'{R}', '?'),  # catch-all: unknown replacements become ?
]

with open(FILE, 'r', encoding='utf-8', errors='replace') as f:
    content = f.read()

original_count = content.count(R)
print(f'Found {original_count} replacement characters before fix')

for broken, correct in fixes:
    if broken in content:
        count = content.count(broken)
        content = content.replace(broken, correct)
        print(f'  Fixed {count}x: {repr(broken[:40])} → {repr(correct[:40])}')

remaining = content.count(R)
print(f'\nRemaining replacement characters: {remaining}')
if remaining > 0:
    lines = content.split('\n')
    for i, line in enumerate(lines, 1):
        if R in line:
            print(f'  L{i}: {line.strip()}')

with open(FILE, 'w', encoding='utf-8') as f:
    f.write(content)

print('\nDone. File saved as UTF-8.')
