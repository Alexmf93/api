# 🔒 Validación y Sanitización de Datos

## Descripción

La clase `Validator` proporciona métodos reutilizables para validar y sanitizar datos de entrada en todos los endpoints de la API. Esto asegura que:

- ✅ Los datos sean seguros (previene XSS, SQL injection, etc.)
- ✅ Los datos cumplan con los requisitos esperados
- ✅ Se proporcionen mensajes de error claros

## Ubicación

```
utils/Validator.php
```

## Métodos de Sanitización

### `sanitizeString($value)`
Elimina espacios, convierte caracteres especiales y evita XSS.

```php
$nombre = Validator::sanitizeString($_POST['nombre']);
// "  Juan <script> " → "Juan &lt;script&gt;"
```

### `sanitizeEmail($value)`
Sanitiza y valida formato de email.

```php
$email = Validator::sanitizeEmail($_POST['email']);
```

### `sanitizeInt($value)`
Convierte a número entero.

```php
$id = Validator::sanitizeInt($_POST['id']);
```

### `sanitizeFloat($value)`
Convierte a número decimal.

```php
$precio = Validator::sanitizeFloat($_POST['precio']);
```

## Métodos de Validación

### Validación Simple

#### `validateRequired($value, $fieldName)`
Verifica que el campo no esté vacío.

```php
$validator = new Validator();
$validator->validateRequired($nombre, 'nombre');
```

#### `validateEmail($email, $fieldName)`
Valida formato de email.

```php
$validator->validateEmail($mail, 'email');
```

#### `validateNumber($value, $fieldName, $isInteger)`
Valida que sea un número.

```php
$validator->validateNumber($cantidad, 'cantidad', true); // true = entero
$validator->validateNumber($precio, 'precio', false);     // false = decimal
```

#### `validateUrl($url, $fieldName)`
Valida que sea una URL válida.

```php
$validator->validateUrl($imagen, 'url_imagen');
```

### Validación de Longitud

#### `validateMinLength($value, $min, $fieldName)`
```php
$validator->validateMinLength($password, 8, 'password');
```

#### `validateMaxLength($value, $max, $fieldName)`
```php
$validator->validateMaxLength($descripcion, 500, 'descripcion');
```

### Validación Numérica

#### `validateRange($value, $min, $max, $fieldName)`
Valida que esté entre dos números.

```php
$validator->validateRange($cantidad, 1, 1000, 'cantidad');
```

### Validación Avanzada

#### `validatePassword($password, $fieldName)`
Valida contraseña fuerte (8+ caracteres, mayúscula, minúscula, número).

```php
$validator->validatePassword($password, 'password');
```

#### `validateDate($date, $format, $fieldName)`
Valida formato de fecha.

```php
$validator->validateDate($fecha, 'Y-m-d H:i:s', 'fecha');
```

#### `validatePattern($value, $pattern, $fieldName)`
Valida con expresión regular.

```php
$validator->validatePattern($codigo, '/^[A-Z]{3}-\d{3}$/', 'codigo');
```

#### `validateIn($value, $allowedValues, $fieldName)`
Valida que esté en una lista permitida.

```php
$validator->validateIn($estado, ['activo', 'inactivo'], 'estado');
```

## Uso en Controladores

### Ejemplo 1: Crear Producto

```php
private function createProducto(){
    $input = json_decode(file_get_contents("php://input"), true);
    
    // Sanitizar datos
    $codigo = isset($input['codigo']) ? Validator::sanitizeString($input['codigo']) : '';
    $nombre = isset($input['nombre']) ? Validator::sanitizeString($input['nombre']) : '';
    $precio = isset($input['precio']) ? Validator::sanitizeFloat($input['precio']) : '';
    $descripcion = isset($input['descripcion']) ? Validator::sanitizeString($input['descripcion']) : '';
    $imagen = isset($input['imagen']) ? Validator::sanitizeString($input['imagen']) : '';
    
    // Validar datos
    $validator = new Validator();
    $validator->validateRequired($codigo, 'codigo');
    $validator->validateRequired($nombre, 'nombre');
    $validator->validateRequired($precio, 'precio');
    $validator->validateNumber($precio, 'precio', false);
    $validator->validateRange($precio, 0.01, 999999.99, 'precio');
    $validator->validateMaxLength($descripcion, 1000, 'descripcion');
    $validator->validateUrl($imagen, 'imagen');
    
    // Verificar errores
    if ($validator->hasErrors()) {
        $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Validación fallida',
            'details' => $validator->getErrors()
        ]);
        return $respuesta;
    }
    
    // Crear array con datos limpios
    $datosLimpios = [
        'codigo' => $codigo,
        'nombre' => $nombre,
        'precio' => $precio,
        'descripcion' => $descripcion,
        'imagen' => $imagen
    ];
    
    // Procesar...
}
```

### Ejemplo 2: Crear Usuario

```php
private function createUsuario(){
    $input = json_decode(file_get_contents("php://input"), true);
    
    // Sanitizar
    $nombre = isset($input['nombre']) ? Validator::sanitizeString($input['nombre']) : '';
    $mail = isset($input['mail']) ? Validator::sanitizeEmail($input['mail']) : '';
    $password = isset($input['password']) ? $input['password'] : '';
    
    // Validar
    $validator = new Validator();
    $validator->validateRequired($nombre, 'nombre');
    $validator->validateEmail($mail, 'mail');
    $validator->validatePassword($password, 'password');
    
    if ($validator->hasErrors()) {
        $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Validación fallida',
            'details' => $validator->getErrors()
        ]);
        return $respuesta;
    }
    
    // Procesar...
}
```

### Ejemplo 3: Crear Pedido

```php
private function createPedido(){
    $input = json_decode(file_get_contents("php://input"), true);
    
    // Sanitizar
    $id_usuarios = isset($input['id_usuarios']) ? Validator::sanitizeInt($input['id_usuarios']) : '';
    $fecha = isset($input['fecha']) ? $input['fecha'] : '';
    $numero_factura = isset($input['numero_factura']) ? Validator::sanitizeString($input['numero_factura']) : '';
    $total = isset($input['total']) ? Validator::sanitizeFloat($input['total']) : '';
    
    // Validar
    $validator = new Validator();
    $validator->validateRequired($id_usuarios, 'id_usuarios');
    $validator->validateNumber($id_usuarios, 'id_usuarios', true);
    $validator->validateDate($fecha, 'Y-m-d H:i:s', 'fecha');
    $validator->validateRequired($numero_factura, 'numero_factura');
    $validator->validateNumber($total, 'total', false);
    $validator->validateRange($total, 0, 999999.99, 'total');
    
    if ($validator->hasErrors()) {
        $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Validación fallida',
            'details' => $validator->getErrors()
        ]);
        return $respuesta;
    }
    
    // Procesar...
}
```

## Validación con Reglas

También puedes usar el método `validate()` con reglas definidas:

```php
$validator = new Validator();

$rules = [
    'nombre' => ['required', ['min_length' => 3], ['max_length' => 100]],
    'email' => ['required', 'email'],
    'precio' => ['required', ['number' => false], ['range' => [0, 999999]]],
    'estado' => [['in' => ['activo', 'inactivo']]]
];

if ($validator->validate($input, $rules)) {
    // Validación exitosa
} else {
    $errores = $validator->getErrors();
}
```

## Respuesta de Error

Cuando hay errores de validación, la API retorna:

```json
{
  "succes": false,
  "error": "Validación fallida",
  "details": [
    "nombre es requerido",
    "email no es válido",
    "password debe contener al menos una mayúscula"
  ]
}
```

## Mejores Prácticas

1. **Siempre sanitizar primero**, luego validar
2. **Usar nombres descriptivos** para los campos en errores
3. **Validar en el controlador**, no en el modelo
4. **Proporcionar mensajes de error claros** al cliente
5. **Usar tipos de datos correctos** (string, int, float)
6. **Nunca confiar en datos del cliente**
7. **Registrar intentos de validación fallida** para auditoría

## Seguridad

La clase Validator ayuda a prevenir:

- ✅ **XSS (Cross-Site Scripting)** - Mediante `sanitizeString()`
- ✅ **SQL Injection** - Al validar tipos de datos
- ✅ **Inyección de código** - Escapando caracteres especiales
- ✅ **Datos inválidos** - Mediante validaciones estrictas
- ✅ **Desbordamiento de buffer** - Validando longitudes

## Extensión de Validator

Para agregar validaciones personalizadas:

```php
class CustomValidator extends Validator {
    public function validatePhoneNumber($phone, $fieldName = 'teléfono') {
        if (!preg_match('/^(\+\d{1,3})?\d{6,14}$/', $phone)) {
            $this->addError("$fieldName no es válido");
            return false;
        }
        return true;
    }
}
```
