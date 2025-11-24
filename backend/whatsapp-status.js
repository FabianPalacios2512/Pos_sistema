import whatsappService from './whatsapp-service.js'

const status = whatsappService.getStatus()
console.log(JSON.stringify(status))
