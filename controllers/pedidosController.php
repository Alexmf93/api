<?php

class PedidoController {
    private $pedidoDB;
    private $requestMethod;
    private $pedidoID;

    public function __construct($db, $requestMethod, $pedidoId=null) {
        $this->pedidoDB = new PedidosDB($db);
        $this->requestMethod = $requestMethod;
        $this->pedidoID = $pedidoId;
    }

    public function processRequest(){
        $method = $this->requestMethod;
        //Comprobar el metodo de llamada
        switch($method){
            case 'GET':
                if($this->pedidoID){
                    $respuesta = $this->getPedido($this->pedidoID);
                }else{
                    $respuesta = $this->getAllPedidos();
                }
                break;
            case 'POST':
                $respuesta = $this->createPedido();
                break;
            case 'PUT':
                $respuesta = $this->actualizarPedido();
                break;
            case 'DELETE':
                if($this->pedidoID){
                    $respuesta = $this->deletePedido($this->pedidoID);
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

    private function getPedido($id){
        $pedido = $this->pedidoDB->getById($id);
        if(!$pedido){
            return $this->respuestaNoEncontrada();
        }
        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'succes' => true,
            'data' => $pedido
        ]);
        return $respuesta;
    }

    private function getAllPedidos(){
        $pedidos = $this->pedidoDB->getAll();
        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'succes' => true,
            'data' => $pedidos,
            'count' => count($pedidos)
        ]);
        return $respuesta;   
    }

    private function createPedido(){
        $input = json_decode(file_get_contents("php://input"), true);
        
        if(!$input || !isset($input['id_usuarios']) || !isset($input['fecha']) || 
           !isset($input['numero_factura']) || !isset($input['total'])){
            $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $respuesta['body'] = json_encode([
                'succes' => false,
                'error' => 'Faltan campos requeridos: id_usuarios, fecha, numero_factura, total'
            ]);
            return $respuesta;
        }
        
        if($this->pedidoDB->createPedidos($input)){
            $respuesta['status_code_header'] = 'HTTP/1.1 201 Created';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'message' => 'Pedido creado exitosamente'
            ]);
            return $respuesta;
        }
        
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Error al crear el pedido'
        ]);
        return $respuesta;
    }

    private function actualizarPedido(){
        if(!$this->pedidoID){
            $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $respuesta['body'] = json_encode([
                'succes' => false,
                'error' => 'ID de pedido requerido'
            ]);
            return $respuesta;
        }
        
        // Verificar si el pedido existe
        $pedido = $this->pedidoDB->getById($this->pedidoID);
        if(!$pedido){
            return $this->respuestaNoEncontrada();
        }
        
        $input = json_decode(file_get_contents("php://input"), true);
        
        if(!$input || !isset($input['id_usuarios']) || !isset($input['fecha']) || 
           !isset($input['numero_factura']) || !isset($input['total'])){
            $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $respuesta['body'] = json_encode([
                'succes' => false,
                'error' => 'Faltan campos requeridos: id_usuarios, fecha, numero_factura, total'
            ]);
            return $respuesta;
        }
        
        if($this->pedidoDB->updatePedidos($this->pedidoID, $input)){
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'message' => 'Pedido actualizado exitosamente'
            ]);
            return $respuesta;
        }
        
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Error al actualizar el pedido'
        ]);
        return $respuesta;
    }

    private function deletePedido($id){
        // Verificar si el pedido existe
        $pedido = $this->pedidoDB->getById($id);
        if(!$pedido){
            return $this->respuestaNoEncontrada();
        }

        if($this->pedidoDB->delete($id)){
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'message' => 'Pedido eliminado exitosamente'
            ]);
            return $respuesta;
        }

        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Error al eliminar el pedido'
        ]);
        return $respuesta;
    }

    private function respuestaNoEncontrada(){
        $respuesta['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'message' => 'El recurso no fue encontrado'
        ]);
        return $respuesta;
    }
}