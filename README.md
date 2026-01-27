# API REST - Sistema de Gestión de Pedidos

Una API REST desarrollada en PHP para gestionar productos, usuarios, pedidos y líneas de pedido. Implementa operaciones CRUD completas con base de datos MySQL.

## 📋 Características

- ✅ Gestión de productos (crear, leer, actualizar, eliminar)
- ✅ Gestión de usuarios con contraseñas hasheadas (BCrypt)
- ✅ Gestión de pedidos con información de líneas asociadas
- ✅ Gestión de líneas de pedido
- ✅ API RESTful con métodos HTTP estándar (GET, POST, PUT, DELETE)
- ✅ Respuestas en formato JSON
- ✅ Validación de datos de entrada
- ✅ Manejo de errores robusto
- ✅ Headers de seguridad CORS
- ✅ Soporte para múltiples endpoints

## 🏗️ Estructura del Proyecto

```
api/
├── config/
│   ├── config.php          # Configuración de base de datos (no versionado)
│   ├── config_plantilla.php # Plantilla de configuración
│   └── database.php        # Clase de conexión a BD
├── controllers/
│   ├── productoController.php      # Controlador de productos
│   ├── usuarioController.php       # Controlador de usuarios
│   ├── pedidosController.php       # Controlador de pedidos
│   └── linea_pedidoController.php  # Controlador de líneas de pedido
├── models/
│   ├── productoDB.php      # Modelo de productos
│   ├── usuarioDB.php       # Modelo de usuarios
│   ├── pedidosDB.php       # Modelo de pedidos
│   └── linea_pedidoDB.php  # Modelo de líneas de pedido
├── api/
│   └── index.php           # Punto de entrada de la API
├── test_pedido.php         # Tests para pedidos
├── test_pedidos.php        # Tests para pedidos
├── test_linea_pedido.php   # Tests para líneas de pedido
├── insert_productos.sql    # Script para insertar productos
└── .htaccess               # Reescritura de URLs
```

## 🚀 Instalación

### Requisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- XAMPP (con Apache y MySQL)

### Pasos

1. **Clonar/Descargar el proyecto** en `C:\xampp\htdocs\api`

2. **Configurar la base de datos**
   ```bash
   cp config/config_plantilla.php config/config.php
   ```
   Editar `config/config.php` con tus credenciales:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'api');
   ```

3. **Crear la base de datos**
   ```sql
   CREATE DATABASE api;
   ```

4. **Crear las tablas necesarias**
   ```sql
   -- Tabla de usuarios
   CREATE TABLE usuarios (
       id INT PRIMARY KEY AUTO_INCREMENT,
       nombre VARCHAR(255) NOT NULL,
       mail VARCHAR(255) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL
   );
   
   -- Tabla de productos
   CREATE TABLE productos (
       id INT PRIMARY KEY AUTO_INCREMENT,
       codigo VARCHAR(100) NOT NULL UNIQUE,
       nombre VARCHAR(255) NOT NULL,
       precio DECIMAL(10, 2) NOT NULL,
       descripcion TEXT,
       imagen VARCHAR(500)
   );
   
   -- Tabla de pedidos
   CREATE TABLE pedidos (
       id INT PRIMARY KEY AUTO_INCREMENT,
       id_usuarios INT NOT NULL,
       fecha DATETIME NOT NULL,
       numero_factura VARCHAR(100) NOT NULL UNIQUE,
       total DECIMAL(10, 2) NOT NULL,
       FOREIGN KEY (id_usuarios) REFERENCES usuarios(id)
   );
   
   -- Tabla de líneas de pedido
   CREATE TABLE linea_pedidos (
       id INT PRIMARY KEY AUTO_INCREMENT,
       id_pedidos INT NOT NULL,
       id_productos INT NOT NULL,
       cantidad INT NOT NULL,
       precio_unitario DECIMAL(10, 2) NOT NULL,
       FOREIGN KEY (id_pedidos) REFERENCES pedidos(id),
       FOREIGN KEY (id_productos) REFERENCES productos(id)
   );
   ```

5. **Insertar productos de ejemplo** (opcional)
   ```bash
   mysql -u root api < insert_productos.sql
   ```

## 📡 Endpoints de la API

### Productos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/productos` | Obtener todos los productos |
| GET | `/api/productos/{id}` | Obtener un producto específico |
| POST | `/api/productos` | Crear un nuevo producto |
| PUT | `/api/productos/{id}` | Actualizar un producto |
| DELETE | `/api/productos/{id}` | Eliminar un producto |

**Ejemplo POST:**
```json
{
  "codigo": "PROD-001",
  "nombre": "Proteína Whey",
  "precio": 35.99,
  "descripcion": "Proteína de suero de alta pureza",
  "imagen": "https://example.com/imagen.jpg"
}
```

### Usuarios

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/usuarios` | Obtener todos los usuarios |
| GET | `/api/usuarios/{id}` | Obtener un usuario específico |
| POST | `/api/usuarios` | Crear un nuevo usuario |
| PUT | `/api/usuarios/{id}` | Actualizar un usuario |
| DELETE | `/api/usuarios/{id}` | Eliminar un usuario |

**Ejemplo POST:**
```json
{
  "nombre": "Juan Pérez",
  "mail": "juan@example.com",
  "password": "password123"
}
```

### Pedidos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/pedidos` | Obtener todos los pedidos |
| GET | `/api/pedidos/{id}` | Obtener un pedido con sus líneas |
| POST | `/api/pedidos` | Crear un nuevo pedido |
| PUT | `/api/pedidos/{id}` | Actualizar un pedido |
| DELETE | `/api/pedidos/{id}` | Eliminar un pedido |

**Ejemplo POST:**
```json
{
  "id_usuarios": 1,
  "fecha": "2026-01-27 10:30:00",
  "numero_factura": "FAC-001",
  "total": 500.50
}
```

**Respuesta GET /api/pedidos/1:**
```json
{
  "succes": true,
  "data": {
    "id": 1,
    "id_usuarios": 1,
    "fecha": "2026-01-27 10:30:00",
    "numero_factura": "FAC-001",
    "total": 500.50,
    "lineas_pedido": [
      {
        "id": 1,
        "id_pedidos": 1,
        "id_productos": 1,
        "cantidad": 2,
        "precio_unitario": 35.99
      }
    ]
  }
}
```

### Líneas de Pedido

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/linea_pedido/{id_pedido}` | Obtener líneas de un pedido |
| POST | `/api/linea_pedido` | Crear una línea de pedido |
| PUT | `/api/linea_pedido/{id_pedido}/{id_linea}` | Actualizar una línea |
| DELETE | `/api/linea_pedido/{id_pedido}/{id_linea}` | Eliminar una línea |

**Ejemplo POST:**
```json
{
  "id_pedidos": 1,
  "id_productos": 1,
  "cantidad": 2,
  "precio_unitario": 35.99
}
```

## 🧪 Testing

### Ejecutar tests de Pedidos
```bash
php test_pedidos.php
```

### Ejecutar tests de Líneas de Pedido
```bash
php test_linea_pedido.php
```

O desde Postman, importa y ejecuta los requests a:
- `http://localhost/api/api/pedidos`
- `http://localhost/api/api/linea_pedido`

## 📚 Documentación con Swagger

### Opción 1: Acceder directamente (Recomendado)

Abre tu navegador y accede a:
```
http://localhost/api/swagger-ui.html
```

Esta página carga la especificación OpenAPI (`openapi.yaml`) y proporciona una interfaz interactiva para:
- Visualizar todos los endpoints
- Ver esquemas de request/response
- Probar los endpoints directamente ("Try it out")
- Descargar la especificación

### Opción 2: Servir con PHP standalone

Desde la terminal, en el directorio del proyecto:
```bash
php -S localhost:8000 swagger-server.php
```

Luego accede a: `http://localhost:8000`

### Opción 3: Usar Docker (Opcional)

Si tienes Docker instalado, puedes servir Swagger UI con:
```bash
docker run -p 80:8080 -e SWAGGER_JSON=/openapi.yaml -v $(pwd)/openapi.yaml:/openapi.yaml swaggerapi/swagger-ui
```

Accede a: `http://localhost`

### Archivos relacionados

- **openapi.yaml** - Especificación OpenAPI 3.0 completa
- **swagger-ui.html** - Página HTML de Swagger UI
- **swagger-server.php** - Servidor simple para servir Swagger localmente

## 📝 Headers HTTP

Todos los requests deben incluir:
```
Content-Type: application/json
```

### Respuestas de la API

**Exitosa (200/201):**
```json
{
  "succes": true,
  "data": {...},
  "message": "Operación realizada"
}
```

**Error (400/404/500):**
```json
{
  "succes": false,
  "error": "Descripción del error"
}
```

## 🔒 Seguridad

- ✅ Contraseñas hasheadas con BCrypt
- ✅ Headers CORS configurados
- ✅ Validación de entrada en todos los endpoints
- ✅ Prepared statements para prevenir SQL injection
- ✅ Headers de seguridad (X-Frame-Options, X-XSS-Protection, etc.)

## 📋 Tecnologías Utilizadas

- **PHP 7.4+** - Backend
- **MySQL** - Base de datos
- **Apache** - Servidor web
- **JSON** - Formato de respuesta

## 📧 Contacto

Para reportar problemas o sugerencias, crea un issue en el repositorio.

## 📄 Licencia

Este proyecto está bajo la licencia MIT.
