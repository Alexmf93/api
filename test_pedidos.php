<?php

/**
 * Test para probar pedidos
 */

// URL base de la API
$apiUrl = 'http://localhost/api/api/pedidos';

echo "=== PRUEBA: Obtener todos los pedidos ===\n\n";

// Realizar la petición GET
$url = $apiUrl;
echo "URL: " . $url . "\n";
echo "Método: GET\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Separar headers y body
$parts = explode("\r\n\r\n", $response, 2);
$body = isset($parts[1]) ? $parts[1] : '';

echo "Código HTTP: " . $httpCode . "\n";
echo "Respuesta:\n";
echo json_encode(json_decode($body, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Validar resultado
if($httpCode === 200){
    $data = json_decode($body, true);
    if(isset($data['data']) && is_array($data['data'])){
        echo "✓ TEST PASADO: Se obtuvieron los pedidos correctamente\n";
        echo "Total de pedidos: " . $data['count'] . "\n";
    } else {
        echo "✗ TEST FALLIDO: Formato de respuesta incorrecto\n";
    }
} else {
    echo "✗ TEST FALLIDO: Código HTTP inesperado\n";
}

echo "\n=== PRUEBA: Crear un nuevo pedido ===\n\n";

$url = $apiUrl;
echo "URL: " . $url . "\n";
echo "Método: POST\n";

$datos = [
    'id_usuarios' => 1,
    'fecha' => date('Y-m-d H:i:s'),
    'numero_factura' => 'FAC-' . uniqid(),
    'total' => 500.50
];

echo "Datos:\n";
echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Separar headers y body
$parts = explode("\r\n\r\n", $response, 2);
$body = isset($parts[1]) ? $parts[1] : '';

echo "Código HTTP: " . $httpCode . "\n";
echo "Respuesta:\n";
echo json_encode(json_decode($body, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

if($httpCode === 201){
    echo "✓ TEST PASADO: Pedido creado exitosamente\n";
} else {
    echo "✗ TEST FALLIDO: Error al crear el pedido\n";
}

echo "\n=== PRUEBA: Obtener un pedido por ID ===\n\n";

$pedidoId = 1;
$url = $apiUrl . '/' . $pedidoId;
echo "URL: " . $url . "\n";
echo "Método: GET\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Separar headers y body
$parts = explode("\r\n\r\n", $response, 2);
$body = isset($parts[1]) ? $parts[1] : '';

echo "Código HTTP: " . $httpCode . "\n";
echo "Respuesta:\n";
echo json_encode(json_decode($body, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

if($httpCode === 200){
    echo "✓ TEST PASADO: Pedido obtenido correctamente\n";
} else if($httpCode === 404){
    echo "⚠ TEST INCONCLUSO: Pedido no encontrado\n";
} else {
    echo "✗ TEST FALLIDO: Error en la solicitud\n";
}

echo "\n=== PRUEBA: Actualizar un pedido ===\n\n";

$pedidoId = 1;
$url = $apiUrl . '/' . $pedidoId;
echo "URL: " . $url . "\n";
echo "Método: PUT\n";

$datos = [
    'id_usuarios' => 1,
    'fecha' => date('Y-m-d H:i:s'),
    'numero_factura' => 'FAC-ACTUALIZADO',
    'total' => 750.75
];

echo "Datos:\n";
echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Separar headers y body
$parts = explode("\r\n\r\n", $response, 2);
$body = isset($parts[1]) ? $parts[1] : '';

echo "Código HTTP: " . $httpCode . "\n";
echo "Respuesta:\n";
echo json_encode(json_decode($body, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

if($httpCode === 200){
    echo "✓ TEST PASADO: Pedido actualizado correctamente\n";
} else if($httpCode === 404){
    echo "⚠ TEST INCONCLUSO: Pedido no encontrado\n";
} else {
    echo "✗ TEST FALLIDO: Error al actualizar el pedido\n";
}

echo "\n=== PRUEBA: Obtener un pedido que no existe ===\n\n";

$pedidoIdNoExistente = 9999;
$url = $apiUrl . '/' . $pedidoIdNoExistente;
echo "URL: " . $url . "\n";
echo "Método: GET\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Separar headers y body
$parts = explode("\r\n\r\n", $response, 2);
$body = isset($parts[1]) ? $parts[1] : '';

echo "Código HTTP: " . $httpCode . "\n";
echo "Respuesta:\n";
echo json_encode(json_decode($body, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

if($httpCode === 404){
    echo "✓ TEST PASADO: Se retorna 404 para un pedido inexistente\n";
} else {
    echo "✗ TEST FALLIDO: Código HTTP inesperado\n";
}

echo "\n=== PRUEBA: Eliminar un pedido ===\n\n";

$pedidoId = 1;
$url = $apiUrl . '/' . $pedidoId;
echo "URL: " . $url . "\n";
echo "Método: DELETE\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Separar headers y body
$parts = explode("\r\n\r\n", $response, 2);
$body = isset($parts[1]) ? $parts[1] : '';

echo "Código HTTP: " . $httpCode . "\n";
echo "Respuesta:\n";
echo json_encode(json_decode($body, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

if($httpCode === 200){
    echo "✓ TEST PASADO: Pedido eliminado correctamente\n";
} else if($httpCode === 404){
    echo "⚠ TEST INCONCLUSO: Pedido no encontrado\n";
} else {
    echo "✗ TEST FALLIDO: Error al eliminar el pedido\n";
}

?>
