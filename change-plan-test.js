// Script temporal para cambiar el plan del tenant a Premium
// Ejecuta esto en la consola del navegador para ver la opción "Tienda Online"

// Cambiar a Premium
appStore.tenantPlan = 'premium'

// O cambiar a Enterprise
// appStore.tenantPlan = 'enterprise'

// Para volver a Basic:
// appStore.tenantPlan = 'basic'

console.log('✅ Plan actual:', appStore.tenantPlan)
console.log('💡 Ahora deberías ver "TIENDA ONLINE" en el sidebar')
