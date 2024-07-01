<?php

/******
MOSTRANDO ERRORES
*******/
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error_log');

// Encabezados CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Manejar solicitudes OPTIONS (preflight)
   
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    http_response_code(200);
    exit();
} else {
   
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
}

// Definimos la zona horaria
date_default_timezone_set("America/Mazatlan");
require_once "database/conexion.php"; 
require_once 'routers/FrontController.php';

$frontController = new FrontController();
$frontController->manejarSolicitud();
?>