# Script de Creación de Usuario Maestro

Este directorio contiene scripts para crear automáticamente el usuario maestro después de hacer pull del repositorio.

## 📋 Archivos incluidos

### Scripts Básicos
- `create_master_user.php` - Script simple para crear el usuario maestro
- `create_master_user.bat` - Script batch para Windows (ejecuta el PHP simple)

### Scripts Avanzados
- `manage_master_user.php` - Script completo para gestionar el usuario maestro
- `manage_master_user.bat` - Interfaz batch interactiva para el script avanzado

## 🚀 Uso

### Script Simple (Recomendado para uso rápido)

#### Opción 1: Ejecutar el archivo batch
```bash
create_master_user.bat
```

#### Opción 2: Ejecutar directamente el PHP
```bash
php create_master_user.php
```

### Script Avanzado (Para gestión completa)

#### Opción 1: Interfaz interactiva (Recomendado)
```bash
manage_master_user.bat
```

#### Opción 2: Comandos directos
```bash
# Crear usuario maestro
php manage_master_user.php create

# Mostrar información del usuario
php manage_master_user.php info

# Actualizar usuario existente
php manage_master_user.php update

# Resetear (eliminar y recrear)
php manage_master_user.php reset

# Eliminar usuario maestro
php manage_master_user.php delete

# Mostrar ayuda
php manage_master_user.php help
```

## 👤 Datos del Usuario Maestro

- **Cédula:** 999999999
- **Contraseña:** master123
- **Cargo:** maestro
- **Nombre:** Usuario Maestro

## ⚙️ Funcionalidades

✅ **Creación automática:** Si no existe el usuario, lo crea automáticamente
✅ **Actualización segura:** Si ya existe, pregunta si deseas actualizar la contraseña
✅ **Datos de ubicación:** Crea automáticamente país, departamento y ciudad básicos si no existen
✅ **Validaciones:** Verifica la integridad de los datos antes de crear
✅ **ID automático:** Asigna automáticamente el siguiente ID disponible
✅ **Contraseña hasheada:** Usa el sistema de hash de Laravel para la seguridad

## 🔧 Cuándo usar este script

- Después de hacer `git pull` en un nuevo ambiente
- Cuando la base de datos se restablezca y pierdas el usuario maestro
- En configuraciones de desarrollo o producción nuevas
- Cuando necesites resetear la contraseña del usuario maestro

## 🛡️ Seguridad

- La contraseña se almacena usando el hash de Laravel
- Verifica si el usuario ya existe antes de crear uno nuevo
- Proporciona confirmación antes de actualizar datos existentes

## 📝 Notas

- Asegúrate de que la base de datos esté configurada y accesible
- El script requiere que Laravel esté correctamente instalado
- En caso de error, el script mostrará información detallada para debugging