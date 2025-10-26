# FactuX - Sistema de Facturación Empresarial

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.31.1-red" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.4.12-blue" alt="PHP Version">
  <img src="https://img.shields.io/badge/PostgreSQL-17.5-blue" alt="PostgreSQL Version">
  <img src="https://img.shields.io/badge/Bootstrap-5.3.2-purple" alt="Bootstrap Version">
  <img src="https://img.shields.io/badge/Estado-Producción-green" alt="Estado">
</p>

**FactuX** es un sistema completo de facturación empresarial desarrollado con Laravel que incluye gestión de inventario, punto de venta (POS), caja registradora y funcionalidades avanzadas para negocios. Diseñado para ser fácil de usar, robusto y cumplir con los requisitos fiscales.

## 🚀 Características Principales

### 📊 **Gestión de Inventario**
- ✅ Registro completo de productos con códigos de barras
- ✅ Control de stock con ajustes automáticos
- ✅ Gestión de precios de costo y venta
- ✅ Cálculo automático de IVA por producto
- ✅ Carga de imágenes para productos
- ✅ Búsqueda avanzada y filtros

### 🛒 **Sistema de Facturación POS**
- ✅ Escáner de código de barras integrado
- ✅ Carrito de compras dinámico con AJAX
- ✅ Búsqueda rápida de productos
- ✅ Cálculo automático de totales e IVA
- ✅ Gestión completa de clientes
- ✅ Facturas con numeración consecutiva

### 🧾 **Impresión de Tirillas**
- ✅ Tirillas optimizadas para impresoras térmicas (58mm/80mm)
- ✅ Formato POS profesional con información fiscal
- ✅ Desglose detallado de IVA por producto
- ✅ Impresión automática post-venta
- ✅ CSS separado para fácil personalización

### 💰 **Gestión de Caja**
- ✅ Apertura y cierre de caja con control de efectivo
- ✅ Registro detallado de movimientos
- ✅ Reportes PDF de cierre con estadísticas
- ✅ Control de diferencias y arqueos
- ✅ Historial completo de operaciones

### 👥 **Gestión de Usuarios**
- ✅ Sistema de autenticación personalizado
- ✅ Roles y permisos granulares
- ✅ Gestión de trabajadores/vendedores
- ✅ Middleware de seguridad

### 📈 **Reportes y Consultas**
- ✅ Listado completo de facturas con paginación
- ✅ Búsqueda de facturas por múltiples criterios
- ✅ Reportes de caja con estadísticas detalladas
- ✅ Reimpresión de tirillas
- ✅ Consultas de inventario

## 💼 **Funcionalidades de Negocio**

### 🧮 **Sistema de IVA Completo**
- **Cálculo Automático**: IVA calculado en tiempo real basado en precio de venta
- **Múltiples Tasas**: Soporte para diferentes porcentajes de IVA por producto
- **Desglose Fiscal**: Visualización clara del IVA en carrito y tirillas
- **Precisión Decimal**: Sin redondeos indebidos para cumplimiento contable

### 🎯 **Punto de Venta Avanzado**
- **Código de Barras**: Escaneo directo con búsqueda automática de productos
- **Carrito Inteligente**: Actualización AJAX sin recargar página
- **Cliente por Defecto**: Sistema automático para ventas rápidas
- **Validación en Tiempo Real**: Control de stock y precios

### 📱 **Interfaz Responsive**
- **Bootstrap 5.3.2**: Diseño moderno y adaptable
- **FontAwesome 6.4.0**: Iconografía profesional
- **UX Optimizada**: Flujo de trabajo intuitivo para cajeros
- **Tema Consistente**: Paleta de colores azul corporativa

## 🛠️ **Tecnologías Utilizadas**

### Backend
- **Laravel 12.31.1** - Framework PHP robusto y moderno
- **PHP 8.4.12** - Lenguaje de programación
- **PostgreSQL 17.5** - Base de datos relacional
- **Eloquent ORM** - Manejo elegante de base de datos
- **Carbon** - Manipulación avanzada de fechas

### Frontend
- **Bootstrap 5.3.2** - Framework CSS responsive
- **FontAwesome 6.4.0** - Librería de iconos
- **JavaScript Vanilla** - Interactividad sin dependencias
- **AJAX** - Actualizaciones dinámicas
- **CSS3** - Estilos modernos y personalizados

### Herramientas Adicionales
- **DomPDF** - Generación de reportes PDF
- **Migrations** - Control de versiones de base de datos
- **Seeders** - Datos de prueba y configuración inicial
- **Middleware** - Seguridad y control de acceso

## 📋 **Requisitos del Sistema**

### Servidor Web
- **PHP**: 8.1 o superior
- **Extensiones PHP**: mbstring, openssl, pdo, tokenizer, xml, ctype, json, bcmath, fileinfo
- **Servidor Web**: Apache/Nginx
- **Base de Datos**: PostgreSQL 12+ o MySQL 8+
- **Composer**: Para gestión de dependencias

### Desarrollo Local
- **XAMPP** o **WAMP** (recomendado para Windows)
- **Node.js** (para compilación de assets, opcional)
- **Git** (para control de versiones)

## ⚙️ **Instalación y Configuración**

### 1. Clonar el Repositorio
```bash
git clone https://github.com/Crisalex720/FactuX.git
cd FactuX
```

### 2. Instalar Dependencias
```bash
composer install
```

### 3. Configurar Base de Datos
```bash
# Copiar archivo de configuración
cp .env.example .env

# Editar .env con datos de tu base de datos
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=factux
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_password
```

### 4. Ejecutar Migraciones
```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
```

### 5. Configurar Usuario Maestro
```bash
# Ejecutar script de usuario maestro
php manage_master_user.php
```

### 6. Iniciar Servidor
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## 📖 **Guía de Uso**

### Gestión de Inventario
1. Acceder a **Inventario** desde el menú principal
2. **Agregar Producto**: Completar formulario con código, nombre, precios e IVA
3. **Editar**: Click en botón "Editar" de cualquier producto
4. **Ajustar Stock**: Usar botón "Ajustar Stock" para modificaciones masivas

### Proceso de Facturación
1. Acceder a **Facturación** desde el menú
2. **Seleccionar Cliente**: Elegir cliente o usar "Cliente Final" por defecto
3. **Agregar Productos**: Escanear código de barras o buscar manualmente
4. **Verificar Carrito**: Revisar productos, cantidades y totales de IVA
5. **Finalizar**: Click en "Finalizar y Registrar Factura"
6. **Imprimir**: Tirilla se genera automáticamente para impresión

### Gestión de Caja
1. **Abrir Caja**: Registrar dinero inicial del día
2. **Procesar Ventas**: Las facturas se registran automáticamente
3. **Cerrar Caja**: Contar dinero final y generar reporte
4. **Ver Reportes**: Acceder a historial de cierres

## 🗂️ **Estructura del Proyecto**

```
FactuX/
├── app/
│   ├── Http/Controllers/          # Controladores principales
│   │   ├── InventarioController.php
│   │   ├── FacturacionController.php
│   │   └── CajaController.php
│   ├── Models/                    # Modelos Eloquent
│   │   ├── Producto.php
│   │   ├── Factura.php
│   │   └── Caja.php
│   └── Services/                  # Servicios de negocio
├── database/
│   ├── migrations/                # Migraciones de BD
│   └── seeders/                   # Datos iniciales
├── resources/
│   └── views/                     # Templates Blade
│       ├── inventario/
│       ├── facturacion/
│       └── caja/
├── public/
│   ├── css/                       # Estilos personalizados
│   │   └── tirilla-pos.css
│   └── uploads/                   # Imágenes de productos
└── routes/
    └── web.php                    # Rutas de la aplicación
```

## 🔒 **Seguridad**

- **Autenticación Personalizada**: Sistema propio basado en trabajadores
- **Middleware de Protección**: Rutas protegidas por roles
- **Validación de Formularios**: Sanitización de datos de entrada
- **Protección CSRF**: Tokens en todos los formularios
- **Inyección SQL**: Prevención mediante Eloquent ORM

## 📊 **Base de Datos**

### Tablas Principales
- `producto` - Inventario con IVA
- `factura` - Registro de ventas
- `lista_prod` - Detalles de facturas
- `cliente` - Información de clientes
- `trabajadores` - Usuarios del sistema
- `caja` - Control de caja registradora

## 🎨 **Personalización**

### Estilos CSS
- **Tirilla POS**: Editar `public/css/tirilla-pos.css`
- **Tema General**: Modificar variables Bootstrap en `resources/css/app.css`
- **Colores Corporativos**: Paleta azul definida en CSS personalizado

### Configuración de Empresa
- **Datos de Empresa**: Modificar en `resources/views/facturacion/tirilla-pos.blade.php`
- **Numeración**: Ajustar prefijos y rangos en controladores
- **Información Fiscal**: Actualizar datos DIAN en tirillas

## 🐛 **Solución de Problemas**

### Errores Comunes
1. **Error de Permisos**: Verificar permisos de carpeta `storage` y `bootstrap/cache`
2. **Base de Datos**: Confirmar credenciales en archivo `.env`
3. **Dependencias**: Ejecutar `composer install` si faltan librerías
4. **Migraciones**: Usar `php artisan migrate:fresh --seed` para resetear BD

### Logs del Sistema
- **Errores Laravel**: `storage/logs/laravel.log`
- **Errores de Facturación**: Logs detallados en controllers
- **Debugging**: Activar `APP_DEBUG=true` en `.env`

## 📞 **Soporte y Documentación**

### Documentación Adicional
- [`IVA_FUNCTIONALITY.md`](IVA_FUNCTIONALITY.md) - Guía completa del sistema de IVA
- [`CREAR_USUARIO_MAESTRO.md`](CREAR_USUARIO_MAESTRO.md) - Configuración inicial de usuarios

### Contacto
- **Desarrollador**: Crisalex720
- **Repositorio**: [GitHub - FactuX](https://github.com/Crisalex720/FactuX)
- **Versión**: 1.0.0
- **Licencia**: MIT

---

**FactuX** - Sistema de Facturación Empresarial desarrollado con ❤️ usando Laravel
