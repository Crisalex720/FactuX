<?php
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=facturacion_facilito','postgres','3142');
    echo "Conexion OK"; 
} catch (Throwable $e) {
    echo "Error: ".$e->getMessage();
}
