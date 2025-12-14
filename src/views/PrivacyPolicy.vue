<template>
  <!-- 🔒 POLÍTICA DE PRIVACIDAD - 105 POS PRO -->
  <div class="min-h-screen bg-slate-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
      
      <!-- Header -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-6">
        <div class="flex items-center justify-between mb-4">
          <h1 class="text-4xl font-bold text-slate-900">Política de Privacidad</h1>
          <button 
            @click="$router.push('/register')" 
            class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-all"
          >
            ← Volver al Registro
          </button>
        </div>
        <p class="text-slate-600">
          <strong>105 POS Pro</strong> - Protección de Datos Personales
        </p>
        <p class="text-sm text-slate-500 mt-2">
          Última actualización: <strong>11 de diciembre de 2025</strong>
        </p>
      </div>

      <!-- Contenido -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 space-y-8">
        
        <!-- 1. Información que Recopilamos -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-lg text-lg font-bold">1</span>
            Información que Recopilamos
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p><strong>Información de Registro:</strong></p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Nombre completo del propietario</li>
              <li>Cédula o documento de identidad</li>
              <li>Nombre de la empresa</li>
              <li>Correo electrónico</li>
              <li>Contraseña (cifrada)</li>
              <li>Subdominio elegido</li>
            </ul>
            <p class="mt-3"><strong>Información de Uso:</strong></p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Productos y servicios registrados</li>
              <li>Transacciones de venta</li>
              <li>Datos de clientes y proveedores</li>
              <li>Reportes y análisis generados</li>
            </ul>
          </div>
        </section>

        <!-- 2. Cómo Usamos su Información -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-lg text-lg font-bold">2</span>
            Cómo Usamos su Información
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>Utilizamos su información para:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Proporcionar y mantener el servicio</li>
              <li>Procesar pagos y suscripciones</li>
              <li>Enviar notificaciones importantes del servicio</li>
              <li>Ofrecer soporte técnico</li>
              <li>Mejorar nuestras funcionalidades</li>
              <li>Cumplir con obligaciones legales (ej: facturación DIAN)</li>
            </ul>
            <p class="mt-3 font-semibold text-blue-700">
              ⚠️ NO vendemos ni compartimos sus datos con terceros para marketing.
            </p>
          </div>
        </section>

        <!-- 3. Seguridad de Datos -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-lg text-lg font-bold">3</span>
            Seguridad de Datos
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>Implementamos medidas de seguridad robustas:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li><strong>Cifrado SSL/TLS</strong>: Todas las comunicaciones están encriptadas</li>
              <li><strong>Contraseñas hasheadas</strong>: No almacenamos contraseñas en texto plano</li>
              <li><strong>Copias de seguridad diarias</strong>: Protección contra pérdida de datos</li>
              <li><strong>Acceso restringido</strong>: Solo personal autorizado accede a servidores</li>
              <li><strong>Monitoreo 24/7</strong>: Detección de actividades sospechosas</li>
            </ul>
          </div>
        </section>

        <!-- 4. Compartir Información con Terceros -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-lg text-lg font-bold">4</span>
            Compartir Información con Terceros
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>Solo compartimos datos con:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li><strong>Wompi</strong>: Procesador de pagos (solo información necesaria para transacciones)</li>
              <li><strong>DIAN</strong>: Para facturación electrónica (si activa esta opción)</li>
              <li><strong>Autoridades</strong>: Si es requerido por ley</li>
            </ul>
            <p class="mt-3">
              Estos terceros están obligados contractualmente a proteger su información.
            </p>
          </div>
        </section>

        <!-- 5. Sus Derechos -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-lg text-lg font-bold">5</span>
            Sus Derechos
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>Usted tiene derecho a:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li><strong>Acceder</strong>: Consultar qué datos tenemos sobre usted</li>
              <li><strong>Rectificar</strong>: Corregir información incorrecta</li>
              <li><strong>Eliminar</strong>: Solicitar la eliminación de sus datos (sujeto a obligaciones legales)</li>
              <li><strong>Portabilidad</strong>: Exportar sus datos en formato CSV/Excel</li>
              <li><strong>Oponerse</strong>: Rechazar ciertos usos de su información</li>
            </ul>
            <p class="mt-3">
              Para ejercer estos derechos, contáctenos en: <strong class="text-blue-600">privacidad@105pos.pro</strong>
            </p>
          </div>
        </section>

        <!-- 6. Cookies y Tecnologías Similares -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-lg text-lg font-bold">6</span>
            Cookies y Tecnologías Similares
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>Utilizamos cookies para:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Mantener su sesión activa</li>
              <li>Recordar preferencias de usuario</li>
              <li>Analizar uso del servicio (Google Analytics - anónimo)</li>
            </ul>
            <p class="mt-3">
              Puede desactivar cookies en su navegador, pero esto puede afectar la funcionalidad del servicio.
            </p>
          </div>
        </section>

        <!-- 7. Retención de Datos -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-lg text-lg font-bold">7</span>
            Retención de Datos
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>
              Conservamos sus datos mientras su cuenta esté activa. 
              Al cancelar su suscripción:
            </p>
            <ul class="list-disc list-inside space-y-2 ml-4">
              <li>Sus datos se mantienen por <strong>30 días</strong> por si desea reactivar</li>
              <li>Después de 30 días, puede solicitar eliminación permanente</li>
              <li>Datos de facturación se conservan <strong>5 años</strong> (obligación fiscal DIAN)</li>
            </ul>
          </div>
        </section>

        <!-- 8. Cambios en esta Política -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-lg text-lg font-bold">8</span>
            Cambios en esta Política
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>
              Podemos actualizar esta Política de Privacidad ocasionalmente. 
              Le notificaremos cambios importantes por correo electrónico.
            </p>
            <p>
              El uso continuado del servicio después de cambios constituye aceptación de la nueva política.
            </p>
          </div>
        </section>

        <!-- 9. Contacto -->
        <section>
          <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-lg text-lg font-bold">9</span>
            Contacto
          </h2>
          <div class="text-slate-700 leading-relaxed space-y-3">
            <p>
              Si tiene preguntas sobre esta Política de Privacidad:
            </p>
            <ul class="space-y-2">
              <li class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                  <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                <strong>Email:</strong> privacidad@105pos.pro
              </li>
              <li class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                </svg>
                <strong>Dirección:</strong> Bogotá, Colombia
              </li>
            </ul>
          </div>
        </section>

      </div>

      <!-- Footer -->
      <div class="mt-6 text-center text-sm text-slate-500">
        <p>© 2024-2025 <strong>105 POS Pro</strong>. Todos los derechos reservados.</p>
        <p class="mt-1">
          Cumplimos con la Ley 1581 de 2012 (Colombia) sobre Protección de Datos Personales.
        </p>
      </div>

    </div>
  </div>
</template>

<script setup>
const goBack = () => {
  window.history.back()
}
</script>
