<?php

// Headers de seguridad
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// Headers CORS (si es necesario)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Headers de caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Header de contenido
header("Content-Type: application/json; charset=utf-8");

require_once '../config/database.php';
require_once '../models/productoDB.php';
require_once '../controllers/productoController.php';


//averiguar la url de la peticion
$requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

//obtener el metodo utilizado de la peticion
$requestMethod = $_SERVER['REQUEST_METHOD'];

//dividir en segmentos
$segmentos = explode('/', trim($requestUrl, '/'));

if($segmentos[1] !== 'api' || !isset($segmentos[2]) || $segmentos[2] !== 'productos'){
    $respuesta['status_code_header']=('HTTP/1.1 404 Not Found');
    echo json_encode([
        'sucess' => false,
        'error' => 'Ruta no encontrada'
    ]);
    exit;
}

$productoId = null;
if(isset($segmentos[3])){
    $productoId = $segmentos[3];
}

$database = new Database ();
$productoController = new ProductoController($database, $requestMethod, $productoId);
$productoController->processRequest();

$database->close();

