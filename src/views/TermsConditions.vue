<template>
  <!-- 📜 TÉRMINOS Y CONDICIONES - 105 POS PRO -->
  <div class="min-h-screen bg-slate-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
      
      <!-- Header -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-6">
        <div class="flex items-center justify-between mb-4">
          <h1 class="text-4xl font-bold text-slate-900">Términos y Condiciones</h1>
          <button 
            @click="$router.push('/register')" 
            class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-all"
          >
            ← Volver al Registro
          </button>
        </div>
        <p class="text-slate-600">
          <strong>105 POS Pro</strong> - Sistema de Punto de Venta en la Nube
        </p>
        <p class="text-sm text-slate-500 mt-2">
          Última actualización: <strong>11 de diciembre de 2025</strong>
        </p>
      </div>

      <!-- Contenido -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 space-y-8">
        
        <!-- 1. Aceptación de los Términos -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg text-lg font-bold">1</span>
            Aceptación de los Términos
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>
              Al acceder y utilizar <strong>105 POS Pro</strong> ("el Servicio"), usted acepta cumplir con estos Términos y Condiciones. 
              Si no está de acuerdo con alguna parte de estos términos, no podrá acceder al servicio.
            </p>
            <p>
              Nos reservamos el derecho de actualizar estos términos en cualquier momento. 
              Los cambios entrarán en vigor inmediatamente después de su publicación en el sitio web.
            </p>
          </div>
        </section>

        <!-- 2. Descripción del Servicio -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg text-lg font-bold">2</span>
            Descripción del Servicio
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>
              <strong>105 POS Pro</strong> es un sistema de punto de venta (POS) basado en la nube que permite a los comerciantes gestionar:
            </p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Inventario de productos</li>
              <li>Ventas y facturación</li>
              <li>Gestión de clientes</li>
              <li>Reportes y análisis de negocio</li>
              <li>Control de cajas y usuarios</li>
              <li>Facturación electrónica DIAN (opcional)</li>
            </ul>
          </div>
        </section>

        <!-- 3. Registro y Cuenta -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg text-lg font-bold">3</span>
            Registro y Cuenta de Usuario
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>Para utilizar el Servicio, debe:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Proporcionar información precisa y completa durante el registro</li>
              <li>Mantener la seguridad de su contraseña</li>
              <li>Notificarnos inmediatamente sobre cualquier uso no autorizado de su cuenta</li>
              <li>Ser mayor de 18 años o tener autorización legal para contratar</li>
            </ul>
            <p class="mt-3">
              Usted es responsable de todas las actividades que ocurran bajo su cuenta.
            </p>
          </div>
        </section>

        <!-- 4. Planes y Pagos -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg text-lg font-bold">4</span>
            Planes y Métodos de Pago
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>
              <strong>105 POS Pro</strong> ofrece diferentes planes de suscripción:
            </p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li><strong>Trial (3 días)</strong>: Acceso completo sin costo</li>
              <li><strong>Basic</strong>: Funcionalidades esenciales</li>
              <li><strong>Premium</strong>: Automatización completa con IA</li>
              <li><strong>Enterprise</strong>: Solución corporativa ilimitada</li>
            </ul>
            <p class="mt-3">
              <strong>Facturación:</strong> Los pagos se procesan de forma segura a través de <strong>ePayco</strong> (Colombia). 
              Las suscripciones se renuevan automáticamente según el período elegido (mensual, anual, 24 meses).
            </p>
            <p>
              <strong>Reembolsos:</strong> Ofrecemos garantía de 3 días. Si no está satisfecho, puede solicitar un reembolso completo 
              dentro de este período.
            </p>
          </div>
        </section>

        <!-- 5. Uso Aceptable -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg text-lg font-bold">5</span>
            Uso Aceptable
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>Al utilizar el Servicio, usted acepta NO:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Violar leyes locales, nacionales o internacionales</li>
              <li>Distribuir malware, virus o código malicioso</li>
              <li>Intentar acceder de forma no autorizada al sistema</li>
              <li>Realizar ingeniería inversa del software</li>
              <li>Revender o redistribuir el servicio sin autorización</li>
              <li>Usar el servicio para actividades fraudulentas</li>
            </ul>
          </div>
        </section>

        <!-- 6. Propiedad Intelectual -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg text-lg font-bold">6</span>
            Propiedad Intelectual
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>
              Todos los derechos de propiedad intelectual sobre el software, diseño, logotipos y contenido del servicio 
              pertenecen a <strong>105 POS Pro</strong>.
            </p>
            <p>
              Los datos que usted ingrese (productos, clientes, ventas) son de su propiedad. 
              Nosotros solo los almacenamos de forma segura y no los compartimos con terceros.
            </p>
          </div>
        </section>

        <!-- 7. Privacidad y Seguridad -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg text-lg font-bold">7</span>
            Privacidad y Seguridad de Datos
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>
              Nos tomamos muy en serio la seguridad de sus datos:
            </p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Cifrado SSL/TLS en todas las comunicaciones</li>
              <li>Copias de seguridad diarias automáticas</li>
              <li>Acceso restringido con autenticación de dos factores (opcional)</li>
              <li>Cumplimiento con leyes de protección de datos de Colombia</li>
            </ul>
            <p class="mt-3">
              Para más detalles, consulte nuestra 
              <a href="/politica-privacidad" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-semibold underline">
                Política de Privacidad
              </a>.
            </p>
          </div>
        </section>

        <!-- 8. Cancelación -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg text-lg font-bold">8</span>
            Cancelación de Suscripción
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>
              Puede cancelar su suscripción en cualquier momento desde el panel de administración. 
              No hay penalizaciones ni cargos adicionales.
            </p>
            <p>
              Al cancelar, mantendrá acceso hasta el final del período ya pagado. 
              Después de eso, su cuenta será desactivada pero sus datos se conservarán por <strong>30 días</strong> 
              por si desea reactivar el servicio.
            </p>
          </div>
        </section>

        <!-- 9. Limitación de Responsabilidad -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg text-lg font-bold">9</span>
            Limitación de Responsabilidad
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>
              <strong>105 POS Pro</strong> se proporciona "tal cual" sin garantías de ningún tipo. 
              No nos hacemos responsables de:
            </p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Pérdidas económicas derivadas del uso del servicio</li>
              <li>Interrupciones temporales del servicio por mantenimiento</li>
              <li>Errores en la información ingresada por el usuario</li>
              <li>Problemas causados por terceros (proveedores de internet, bancos, etc.)</li>
            </ul>
            <p class="mt-3">
              Nuestra responsabilidad máxima se limita al monto pagado en los últimos 12 meses.
            </p>
          </div>
        </section>

        <!-- 10. Contacto -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg text-lg font-bold">10</span>
            Contacto y Soporte
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>
              Si tiene preguntas sobre estos Términos y Condiciones, puede contactarnos:
            </p>
            <ul class="space-y-2">
              <li class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                  <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                <strong>Email:</strong> soporte@105pos.pro
              </li>
              <li class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                </svg>
                <strong>WhatsApp:</strong> +57 300 123 4567
              </li>
            </ul>
          </div>
        </section>

      </div>

      <!-- Footer -->
      <div class="mt-6 text-center text-sm text-slate-500">
        <p>© 2024-2025 <strong>105 POS Pro</strong>. Todos los derechos reservados.</p>
      </div>

    </div>
  </div>
</template>

<script setup>
// No necesita imports adicionales - página estática
</script>

