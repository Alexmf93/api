<?php

class ProductoController {
    private $productoDB;
    private $requestMethod;
    private $productoID;

    public function __construct($db, $requestMethod, $productoId=null) {
        $this->productoDB = new ProductoDB($db);
        $this->requestMethod = $requestMethod;
        $this->productoID = $productoId;
    }

    public function processRequest(){
        $method = $this->requestMethod;
        //Comprobar el metodo de llamada
        switch($method){
            case 'GET':
                if($this->productoID){
                    $respuesta = $this->getProducto($this->productoID);
                }else{
                    $respuesta = $this->getAllProductos();
                }
                break;
            case 'POST':
                $respuesta = $this->createProducto();
                break;
            case 'DELETE':
                if($this->productoID){
                    $respuesta = $this->deleteProducto($this->productoID);
                }else{
                    $respuesta = $this->respuestaNoEncontrada();
                }
                break;
            default:
                $respuesta = $this->respuestaNoEncontrada();
            }   
            // Enviar la respuesta
            header($respuesta['status_code_header']);
            if($respuesta['body']){
                echo $respuesta['body'];
            }
            return $respuesta;
    }
    private function getProducto($id){
            $producto = $this->productoDB->getById($id);
            if(!$producto){
                return $this->respuestaNoEncontrada();
            }
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'data' => $producto
            ]);
            return $respuesta;
    }

    private function getAllProductos(){
            $productos = $this->productoDB->getAll();
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'data' => $productos,
                'count' => count($productos)
            ]);
            return $respuesta;   
    }

    private function createProducto(){
        $input = json_decode(file_get_contents("php://input"), true);
        
        if(!$input || !isset($input['codigo']) || !isset($input['nombre']) || 
           !isset($input['precio']) || !isset($input['descripcion']) || !isset($input['imagen'])){
            $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $respuesta['body'] = json_encode([
                'succes' => false,
                'error' => 'Faltan campos requeridos: codigo, nombre, precio, descripcion, imagen'
            ]);
            return $respuesta;
        }
        
        if($this->productoDB->createProducto($input)){
            $respuesta['status_code_header'] = 'HTTP/1.1 201 Created';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'message' => 'Producto creado exitosamente'
            ]);
            return $respuesta;
        }
        
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Error al crear el producto'
        ]);
        return $respuesta;
    }

    private function deleteProducto($id){
        // Verificar si el producto existe
        $producto = $this->productoDB->getById($id);
        if(!$producto){
            return $this->respuestaNoEncontrada();
        }
        
        // Si existe, eliminarlo
        if($this->productoDB->delete($id)){
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'message' => 'Producto eliminado'
            ]);
            return $respuesta;
        }
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Error al eliminar el producto'
        ]);
        return $respuesta;
    }

    private function respuestaNoEncontrada(){
        $respuesta['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Producto no enccontrado'
        ]);
        return $respuesta;
            
    }


}
