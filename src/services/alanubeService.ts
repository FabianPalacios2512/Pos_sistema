/**
 * ═══════════════════════════════════════════════════════════════════
 * 🧾 SERVICIO DE FACTURACIÓN ELECTRÓNICA - ALANUBE
 * ═══════════════════════════════════════════════════════════════════
 * 
 * Servicio para gestión de facturación electrónica DIAN
 * Soporta: Alanube (rápido ~2s) y Factus (legacy ~12s)
 */

import api from './api';

export interface FiscalData {
  company_name: string;
  company_document: string;  // NIT sin DV
  company_dv: string;        // Dígito verificación (1 carácter)
  company_address: string;
  company_city_code: string;
  company_department_code: string;
  company_email: string;
  company_phone?: string;
  tax_regime?: string;       // R-99-PN (Persona Natural), R-49-PN (Responsable IVA), etc.
}

export interface ResolutionData {
  resolution_number: string;
  prefix: string;
  min_number: number;
  max_number: number;
  start_date: string;
  end_date: string;
  technical_key?: string;
}

export interface AlanubeStatus {
  provider: 'none' | 'factus' | 'alanube';
  alanube: {
    company_id: string | null;
    status: 'pending' | 'testing' | 'active';
    test_set_id: string | null;
  };
  company: {
    name: string | null;
    nit: string | null;
    dv: string | null;
    address: string | null;
    city_code: string | null;
    department_code: string | null;
    email: string | null;
    phone: string | null;
    tax_regime: string | null;
  };
  dian_resolution: {
    number: string | null;
    prefix: string | null;
    min_number: number | null;
    max_number: number | null;
    current_number: number | null;
    start_date: string | null;
    end_date: string | null;
  };
}

export interface City {
  code: string;
  name: string;
  department: string;
}

const alanubeService = {
  /**
   * Obtener estado actual de facturación electrónica
   */
  async getStatus(): Promise<{ success: boolean; data: AlanubeStatus }> {
    const response = await api.get('/alanube/status');
    return response.data;
  },

  /**
   * Guardar datos fiscales del comercio (NIT, razón social, etc.)
   */
  async saveFiscalData(data: FiscalData): Promise<{ success: boolean; message: string }> {
    const response = await api.post('/alanube/fiscal-data', data);
    return response.data;
  },

  /**
   * Registrar empresa en Alanube para facturación electrónica
   */
  async registerCompany(): Promise<{ success: boolean; message: string; data?: { company_id: string } }> {
    const response = await api.post('/alanube/register-company');
    return response.data;
  },

  /**
   * Ejecutar set de pruebas DIAN (habilitación)
   */
  async runTestSet(): Promise<{ 
    success: boolean; 
    message: string; 
    data?: { 
      test_set_id: string; 
      status: string;
      errors?: string[];
    } 
  }> {
    const response = await api.post('/alanube/run-test-set');
    return response.data;
  },

  /**
   * Cambiar proveedor de facturación electrónica
   */
  async setProvider(provider: 'none' | 'factus' | 'alanube'): Promise<{ success: boolean; message: string }> {
    const response = await api.post('/alanube/set-provider', { provider });
    return response.data;
  },

  /**
   * Guardar datos de resolución DIAN
   */
  async saveResolution(data: ResolutionData): Promise<{ success: boolean; message: string }> {
    const response = await api.post('/alanube/resolution', data);
    return response.data;
  },

  /**
   * Obtener lista de ciudades de Colombia
   */
  async getCities(): Promise<{ success: boolean; data: City[] }> {
    const response = await api.get('/alanube/cities');
    return response.data;
  },

  /**
   * Calcular dígito de verificación del NIT
   * Algoritmo oficial DIAN Colombia
   */
  calculateDV(nit: string): string {
    const cleanNit = nit.replace(/[^0-9]/g, '');
    if (cleanNit.length < 8 || cleanNit.length > 15) return '';
    
    const weights = [71, 67, 59, 53, 47, 43, 41, 37, 29, 23, 19, 17, 13, 7, 3];
    const nitArray = cleanNit.padStart(15, '0').split('').map(Number);
    
    let sum = 0;
    for (let i = 0; i < 15; i++) {
      sum += nitArray[i] * weights[i];
    }
    
    const remainder = sum % 11;
    if (remainder === 0 || remainder === 1) return String(remainder);
    return String(11 - remainder);
  },

  /**
   * Formatear NIT con dígito de verificación
   */
  formatNIT(nit: string, dv: string): string {
    const cleanNit = nit.replace(/[^0-9]/g, '');
    // Formatear con puntos de miles
    const formatted = cleanNit.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return `${formatted}-${dv}`;
  },

  /**
   * Validar formato de NIT
   */
  validateNIT(nit: string): boolean {
    const cleanNit = nit.replace(/[^0-9]/g, '');
    return cleanNit.length >= 8 && cleanNit.length <= 15;
  }
};

export default alanubeService;
