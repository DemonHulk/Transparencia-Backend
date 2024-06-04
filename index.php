<?php

/************************************************
MOSTRANDO ERRORES
*************************************************/

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'php_error_log');

require_once "database/conexion.php"; 
require_once 'routers/FrontController.php';

$frontController = new FrontController();
$frontController->manejarSolicitud();
