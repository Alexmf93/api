<?php
/**
 * Servidor simple para servir archivos de Swagger UI localmente
 * Ejecutar: php -S localhost:8000 swagger-server.php
 * Acceso: http://localhost:8000
 */

$file = __DIR__ . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Servir openapi.yaml con el content-type correcto
if (basename($file) === 'openapi.yaml') {
    header('Content-Type: application/x-yaml');
    readfile($file);
    exit;
}

// Servir swagger-ui.html como archivo por defecto
if (is_dir($file) || $file === __DIR__) {
    $file = __DIR__ . '/swagger-ui.html';
}

// Servir archivos estáticos
if (file_exists($file)) {
    header('Content-Type: ' . mime_content_type($file));
    readfile($file);
    exit;
}

http_response_code(404);
echo "404 - Archivo no encontrado";
?>
