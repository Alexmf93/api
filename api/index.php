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
require_once '../models/usuarioDB.php';
require_once '../controllers/usuarioController.php';
require_once '../models/linea_pedidoDB.php';
require_once '../controllers/linea_pedidoController.php';
require_once '../models/pedidosDB.php';
require_once '../controllers/pedidosController.php';



//averiguar la url de la peticion
$requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

//obtener el metodo utilizado de la peticion
$requestMethod = $_SERVER['REQUEST_METHOD'];

//dividir en segmentos
$segmentos = explode('/', trim($requestUrl, '/'));

// Verificar que es una ruta de API
if($segmentos[1] !== 'api' || !isset($segmentos[2])){
    $respuesta['status_code_header']=('HTTP/1.1 404 Not Found');
    echo json_encode([
        'sucess' => false,
        'error' => 'Ruta no encontrada'
    ]);
    exit;
}

$database = new Database();

// Manejo de rutas de productos
if($segmentos[2] === 'productos'){
    $productoId = null;
    if(isset($segmentos[3])){
        $productoId = $segmentos[3];
    }
    
    $productoController = new ProductoController($database, $requestMethod, $productoId);
    $productoController->processRequest();
}
// Manejo de rutas de usuarios
elseif($segmentos[2] === 'usuarios'){
    $usuarioId = null;
    if(isset($segmentos[3])){
        $usuarioId = $segmentos[3];
    }
    
    $usuarioController = new UsuarioController($database, $requestMethod, $usuarioId);
    $usuarioController->processRequest();
}
// Manejo de rutas de líneas de pedido
elseif($segmentos[2] === 'linea_pedido'){
    $pedidoId = null;
    $lineaPedidoId = null;
    
    if(isset($segmentos[3])){
        $pedidoId = $segmentos[3];
    }
    
    if(isset($segmentos[4])){
        $lineaPedidoId = $segmentos[4];
    }
    
    $lineaPedidoController = new Linea_pedidoController($database, $requestMethod, $pedidoId, $lineaPedidoId);
    $lineaPedidoController->processRequest();
}
// Manejo de rutas de pedidos
elseif($segmentos[2] === 'pedidos'){
    $pedidoId = null;
    if(isset($segmentos[3])){
        $pedidoId = $segmentos[3];
    }
    
    $pedidoController = new PedidoController($database, $requestMethod, $pedidoId);
    $pedidoController->processRequest();
}
else{
    echo json_encode([
        'sucess' => false,
        'error' => 'Ruta no encontrada'
    ]);
}

$database->close();

