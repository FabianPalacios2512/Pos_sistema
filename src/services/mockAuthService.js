// Mock de usuarios para desarrollo y testing
const mockUsers = [
  {
    id: 1,
    name: 'Administrador del Sistema',
    cc: '12345678',
    email: 'admin@105code.com',
    role: 'admin',
    permissions: ['all', 'users', 'products_manage', 'settings', 'reports_advanced'], // Admin tiene todos los permisos
    password: 'admin123', // En producción esto estaría hasheado
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 2,
    name: 'María Rodríguez',
    cc: '87654321',
    email: 'maria@tienda.com',
    role: 'cajero',
    permissions: ['pos', 'customers', 'reports_basic'], // Cajero: POS, clientes, reportes básicos
    password: 'cajero123',
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 3,
    name: 'Carlos Pérez',
    cc: '11223344',
    email: 'carlos@tienda.com',
    role: 'vendedor',
    permissions: ['pos', 'products_view', 'customers'], // Vendedor: POS, ver productos, clientes
    password: 'vendedor123',
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 4,
    name: 'Ana García',
    cc: '44332211',
    email: 'ana@tienda.com',
    role: 'cajero',
    permissions: ['pos', 'customers', 'reports_basic'], // Cajero: POS, clientes, reportes básicos
    password: 'cajero456',
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  }
];

// Simular delay de red
const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

// Generar token mock
const generateMockToken = (userId) => {
  return `mock_token_${userId}_${Date.now()}`;
};

// Mock del servicio de autenticación
const mockAuthService = {
  // Login
  async login(credentials) {
    await delay(1000); // Simular latencia de red
    
    const { cc, password } = credentials;
    
    // Validar campos requeridos
    if (!cc || !password) {
      throw {
        message: 'Cédula y contraseña son requeridos',
        errors: {
          cc: !cc ? ['La cédula es requerida'] : [],
          password: !password ? ['La contraseña es requerida'] : []
        }
      };
    }

    // Buscar usuario por cédula
    const user = mockUsers.find(u => u.cc === cc.toString());
    
    if (!user) {
      throw {
        message: 'Usuario no encontrado',
        errors: {
          cc: ['No existe un usuario con esta cédula']
        }
      };
    }

    // Verificar contraseña
    if (user.password !== password) {
      throw {
        message: 'Contraseña incorrecta',
        errors: {
          password: ['La contraseña es incorrecta']
        }
      };
    }

    // Generar token
    const token = generateMockToken(user.id);
    
    // Remover password del response
    const { password: _, ...userWithoutPassword } = user;
    
    return {
      message: 'Login exitoso',
      token,
      user: userWithoutPassword
    };
  },

  // Obtener usuario actual
  async getCurrentUser(token) {
    await delay(500);
    
    if (!token || !token.startsWith('mock_token_')) {
      throw {
        message: 'Token inválido',
        status: 401
      };
    }

    // Extraer ID del token mock
    const tokenParts = token.split('_');
    const userId = parseInt(tokenParts[2]);
    
    const user = mockUsers.find(u => u.id === userId);
    
    if (!user) {
      throw {
        message: 'Usuario no encontrado',
        status: 404
      };
    }

    const { password: _, ...userWithoutPassword } = user;
    return userWithoutPassword;
  },

  // Logout
  async logout() {
    await delay(300);
    return { message: 'Logout exitoso' };
  },

  // Cambiar contraseña
  async changePassword(token, passwords) {
    await delay(800);
    
    const { current_password, new_password, new_password_confirmation } = passwords;
    
    // Validaciones
    if (!current_password || !new_password || !new_password_confirmation) {
      throw {
        message: 'Todos los campos son requeridos',
        errors: {
          current_password: !current_password ? ['La contraseña actual es requerida'] : [],
          new_password: !new_password ? ['La nueva contraseña es requerida'] : [],
          new_password_confirmation: !new_password_confirmation ? ['Confirme la nueva contraseña'] : []
        }
      };
    }

    if (new_password !== new_password_confirmation) {
      throw {
        message: 'Las contraseñas no coinciden',
        errors: {
          new_password_confirmation: ['Las contraseñas no coinciden']
        }
      };
    }

    if (new_password.length < 6) {
      throw {
        message: 'La contraseña debe tener al menos 6 caracteres',
        errors: {
          new_password: ['La contraseña debe tener al menos 6 caracteres']
        }
      };
    }

    // Obtener usuario actual
    const currentUser = await this.getCurrentUser(token);
    const user = mockUsers.find(u => u.id === currentUser.id);
    
    if (user.password !== current_password) {
      throw {
        message: 'La contraseña actual es incorrecta',
        errors: {
          current_password: ['La contraseña actual es incorrecta']
        }
      };
    }

    // Actualizar contraseña (en un sistema real esto sería hasheado)
    user.password = new_password;
    user.updated_at = new Date().toISOString();

    return {
      message: 'Contraseña actualizada exitosamente'
    };
  },

  // Registrar usuario (solo admin)
  async register(userData) {
    await delay(1000);
    
    const { name, cc, email, role, password } = userData;
    
    // Validaciones
    if (!name || !cc || !email || !role || !password) {
      throw {
        message: 'Todos los campos son requeridos',
        errors: {
          name: !name ? ['El nombre es requerido'] : [],
          cc: !cc ? ['La cédula es requerida'] : [],
          email: !email ? ['El email es requerido'] : [],
          role: !role ? ['El rol es requerido'] : [],
          password: !password ? ['La contraseña es requerida'] : []
        }
      };
    }

    // Verificar que no exista usuario con misma cédula
    if (mockUsers.find(u => u.cc === cc.toString())) {
      throw {
        message: 'Ya existe un usuario con esta cédula',
        errors: {
          cc: ['Ya existe un usuario con esta cédula']
        }
      };
    }

    // Verificar que no exista usuario con mismo email
    if (mockUsers.find(u => u.email === email)) {
      throw {
        message: 'Ya existe un usuario con este email',
        errors: {
          email: ['Ya existe un usuario con este email']
        }
      };
    }

    // Crear nuevo usuario
    const newUser = {
      id: Math.max(...mockUsers.map(u => u.id)) + 1,
      name,
      cc: cc.toString(),
      email,
      role,
      password,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString()
    };

    mockUsers.push(newUser);

    const { password: _, ...userWithoutPassword } = newUser;
    return {
      message: 'Usuario creado exitosamente',
      user: userWithoutPassword
    };
  },

  // Obtener todos los usuarios (solo admin)
  async getUsers() {
    await delay(600);
    
    return mockUsers.map(({ password: _, ...user }) => user);
  },

  // Validar credenciales de administrador
  async validateAdminCredentials(email, password) {
    await delay(300);
    
    const admin = mockUsers.find(user => 
      user.email === email && 
      user.password === password && 
      user.role === 'admin'
    );
    
    return !!admin;
  }
};

export default mockAuthService;