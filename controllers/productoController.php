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

    private function respuestaNoEncontrada(){
        $respuesta['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Producto no enccontrado'
        ]);
        return $respuesta;
            
    }
}
