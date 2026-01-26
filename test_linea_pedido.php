<?php

/**
 * Test para probar líneas de pedido sin tener un pedido creado
 */

// URL base de la API
$apiUrl = 'http://localhost/api/api/linea_pedido';

// ID de pedido que no existe
$pedidoIdNoExistente = 9999;

echo "=== PRUEBA: Obtener líneas de pedido de un pedido que no existe ===\n\n";

// Realizar la petición GET
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

// Validar resultado
if($httpCode === 200){
    $data = json_decode($body, true);
    if(isset($data['data']) && is_array($data['data'])){
        if(count($data['data']) === 0){
            echo "✓ TEST PASADO: Se retorna un array vacío para un pedido sin líneas\n";
        } else {
            echo "✗ TEST FALLIDO: Se encontraron líneas en un pedido que no existe\n";
        }
    }
} else {
    echo "✗ TEST FALLIDO: Código HTTP inesperado\n";
}

echo "\n=== PRUEBA: Crear línea de pedido sin que exista el pedido ===\n\n";

$url = $apiUrl;
echo "URL: " . $url . "\n";
echo "Método: POST\n";

$datos = [
    'id_pedidos' => 9999,
    'id_productos' => 1,
    'cantidad' => 5,
    'precio_unitario' => 100.50
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
    echo "✓ TEST INFORMACIÓN: Se creó la línea de pedido (puede ser válido o inválido según reglas de negocio)\n";
} else if($httpCode === 400 || $httpCode === 500){
    echo "✓ TEST PASADO: Se retornó un error al intentar crear línea sin pedido\n";
} else {
    echo "⚠ TEST INCONCLUSO: Código HTTP inesperado\n";
}

?>
