@echo off
title Crear Usuario Maestro - FactuX
color 0A
echo.
echo ================================
echo    FACTU-X - CREAR USUARIO MAESTRO
echo ================================
echo.
echo Ejecutando script de creacion del usuario maestro...
echo.

php create_master_user.php

echo.
echo Presiona cualquier tecla para continuar...
pause >nul