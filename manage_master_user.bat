@echo off
title Gestión Usuario Maestro - FactuX
color 0B
echo.
echo =====================================
echo    FACTU-X - GESTION USUARIO MAESTRO
echo =====================================
echo.
echo Comandos disponibles:
echo   1. Crear usuario maestro
echo   2. Actualizar usuario maestro
echo   3. Mostrar información
echo   4. Resetear usuario maestro
echo   5. Eliminar usuario maestro
echo   6. Salir
echo.
set /p choice="Selecciona una opción (1-6): "

if "%choice%"=="1" (
    echo.
    echo Creando usuario maestro...
    php manage_master_user.php create
) else if "%choice%"=="2" (
    echo.
    echo Actualizando usuario maestro...
    php manage_master_user.php update
) else if "%choice%"=="3" (
    echo.
    echo Mostrando información del usuario maestro...
    php manage_master_user.php info
) else if "%choice%"=="4" (
    echo.
    echo Reseteando usuario maestro...
    php manage_master_user.php reset
) else if "%choice%"=="5" (
    echo.
    echo Eliminando usuario maestro...
    php manage_master_user.php delete
) else if "%choice%"=="6" (
    echo.
    echo Saliendo...
    exit /b 0
) else (
    echo.
    echo Opción inválida. Intenta de nuevo.
)

echo.
echo Presiona cualquier tecla para continuar...
pause >nul