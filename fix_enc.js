const fs = require('fs');
const file = 'src/views/SaasRegister.vue';
let c = fs.readFileSync(file, 'utf8');

const FD = '\uFFFD';
const before = (c.match(/\uFFFD/g) || []).length;
console.log('FFFD before:', before);

const fixes = [
  // Double-FFFD (2-byte UTF-8 seq, both bytes invalid individually)
  ['Content Din' + FD + FD + 'mico',   'Content Dinámica'],
  ['Header Din' + FD + FD + 'mico',    'Header Dinámico'],
  ['L' + FD + FD + 'nea conectora',    'Línea conectora'],
  ['L' + FD + FD + 'nea',              'Línea'],
  ['d' + FD + FD + 'as',              'días'],
  ['actualizar' + FD + FD + ' cuando', 'actualizará cuando'],
  ['actualizar' + FD + FD,             'actualizará'],
  ['Pol' + FD + FD + 'tica',           'Política'],
  ['pol' + FD + FD + 'tica',           'política'],
  ['M' + FD + FD + 'nimo 8',           'Mínimo 8'],
  ['m' + FD + FD + 'nimo',             'mínimo'],
  ['T' + FD + FD + 'tulo',             'Título'],
  ['n' + FD + FD + 'meros',            'números'],
  ['n' + FD + FD + 'mero',             'número'],
  ['N' + FD + FD + 'mero',             'Número'],
  ['min' + FD + FD + 'sculas',         'minúsculas'],
  ['pesta' + FD + FD + 'a',            'pestaña'],
  ['Mar' + FD + FD + 'a',              'María'],
  ['enlace ' + FD + FD + 'nico',       'enlace único'],
  // Single-FFFD
  ['Bot' + FD + 'n',                   'Botón'],
  ['Gesti' + FD + 'n',                 'Gestión'],
  ['gesti' + FD + 'n',                 'gestión'],
  ['expansi' + FD + 'n',               'expansión'],
  ['dise' + FD + 'ado',                'diseñado'],
  ['Optimizaci' + FD + 'n',            'Optimización'],
  ['precisi' + FD + 'n',               'precisión'],
  ['Jos' + FD + ' G',                  'José G'],
  ['Jos' + FD + '<',                   'José<'],
  ['Patr' + FD + 'n',                  'Patrón'],
  ['m' + FD + 'vil',                   'móvil'],
  ['DISE' + FD + 'O',                  'DISEÑO'],
  ['reci' + FD + 'n',                  'recién'],
  ['C' + FD + 'digo',                  'Código'],
  ['Aceptaci' + FD + 'n',              'Aceptación'],
  ['facturaci' + FD + 'n',             'facturación'],
  ['Informaci' + FD + 'n',             'Información'],
  ['informaci' + FD + 'n',             'información'],
  ['Soluci' + FD + 'n',                'Solución'],
  ['soluci' + FD + 'n',                'solución'],
  ['Operaci' + FD + 'n',               'Operación'],
  ['operaci' + FD + 'n',               'operación'],
  ['Integraci' + FD + 'n',             'Integración'],
  ['integraci' + FD + 'n',             'integración'],
  ['Versi' + FD + 'n',                 'Versión'],
  ['versi' + FD + 'n',                 'versión'],
  ['Sesi' + FD + 'n',                  'Sesión'],
  ['sesi' + FD + 'n',                  'sesión'],
  ['generaci' + FD + 'n',              'generación'],
  ['configuraci' + FD + 'n',           'configuración'],
  ['recuperaci' + FD + 'n',            'recuperación'],
  ['validaci' + FD + 'n',              'validación'],
  ['actualizaci' + FD + 'n',           'actualización'],
  ['creaci' + FD + 'n',                'creación'],
  ['eliminaci' + FD + 'n',             'eliminación'],
  ['selecci' + FD + 'n',               'selección'],
  ['notificaci' + FD + 'n',            'notificación'],
  ['autenticaci' + FD + 'n',           'autenticación'],
  ['autorizaci' + FD + 'n',            'autorización'],
  ['conexi' + FD + 'n',                'conexión'],
  ['Direcci' + FD + 'n',               'Dirección'],
  ['direcci' + FD + 'n',               'dirección'],
  ['condici' + FD + 'n',               'condición'],
  ['Opci' + FD + 'n',                  'Opción'],
  ['opci' + FD + 'n',                  'opción'],
  ['descripci' + FD + 'n',             'descripción'],
  ['comunicaci' + FD + 'n',            'comunicación'],
  ['transacci' + FD + 'n',             'transacción'],
  ['publicaci' + FD + 'n',             'publicación'],
  ['colecci' + FD + 'n',               'colección'],
  ['administraci' + FD + 'n',          'administración'],
  ['verificaci' + FD + 'n',            'verificación'],
  ['separaci' + FD + 'n',              'separación'],
  ['ubicaci' + FD + 'n',               'ubicación'],
  ['visualizaci' + FD + 'n',           'visualización'],
  ['paginaci' + FD + 'n',              'paginación'],
  ['implementaci' + FD + 'n',          'implementación'],
  ['Acci' + FD + 'n',                  'Acción'],
  ['acci' + FD + 'n',                  'acción'],
  ['Contrase' + FD + 'a',              'Contraseña'],
  ['contrase' + FD + 'a',              'contraseña'],
  ['enumeraci' + FD + 'n',             'enumeración'],
  ['C' + FD + 'dula',                  'Cédula'],
  ['Electr' + FD + 'nico',             'Electrónico'],
  ['electr' + FD + 'nico',             'electrónico'],
  ['T' + FD + 'rminos',                'Términos'],
  ['t' + FD + 'rminos',                'términos'],
  ['Atr' + FD + 's',                   'Atrás'],
  ['est' + FD + ' ',                   'está '],
  ['est' + FD + 'n',                   'están'],
  [FD + 'xito',                        'éxito'],
  ['m' + FD + 'dulo',                  'módulo'],
  ['M' + FD + 'dulo',                  'Módulo'],
  ['tel' + FD + 'fono',                'teléfono'],
  ['a' + FD + 'n',                     'aún'],
  ['Si dice que S' + FD + ' ',         'Si dice que Sí '],
  [FD + 'Deseas',                      '¿Deseas'],
  [FD + 'Ya tienes',                   '¿Ya tienes'],
  [FD + 'Olvidaste',                   '¡Olvidaste'],
  ['da' + FD + 'o',                    'daño'],
  ['se' + FD + 'al',                   'señal'],
  ['compa' + FD + 'i',                 'compañi'],
  // Regex char class issue (replace the whole broken range)
  ['a-zA-Z0-9' + FD + FD + FD + FD + FD + FD + FD + FD + FD + FD + FD + FD + FD + FD + FD + FD + FD + FD + FD + FD,
   'a-zA-Z0-9áéíóúñÁÉÍÓÚÑüÜ'],
];

let count = 0;
for (const [from, to] of fixes) {
  if (c.includes(from)) {
    c = c.split(from).join(to);
    count++;
    console.log('Fixed:', JSON.stringify(from.substring(0, 20)));
  }
}

const after = (c.match(/\uFFFD/g) || []).length;
console.log('Fixes applied:', count);
console.log('FFFD after:', after);

fs.writeFileSync(file, c, 'utf8');
console.log('Saved.');
