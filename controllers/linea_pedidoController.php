<?php

class Linea_pedidoController {
    private $lineaPedidoDB;
    private $requestMethod;
    private $pedidoID;
    private $lineaPedidoID;

    public function __construct($db, $requestMethod, $pedidoId=null, $lineaPedidoId=null) {
        $this->lineaPedidoDB = new LineaPedidoDB($db);
        $this->requestMethod = $requestMethod;
        $this->pedidoID = $pedidoId;
        $this->lineaPedidoID = $lineaPedidoId;
    }

    public function processRequest(){
        $method = $this->requestMethod;
        //Comprobar el metodo de llamada
        switch($method){
            case 'GET':
                if($this->pedidoID){
                    $respuesta = $this->getLineasPedidoByPedidoId($this->pedidoID);
                }else{
                    $respuesta = $this->respuestaNoEncontrada();
                }
                break;
            case 'POST':
                $respuesta = $this->createLineaPedido();
                break;
            case 'PUT':
                $respuesta = $this->actualizarLineaPedido();
                break;
            case 'DELETE':
                if($this->lineaPedidoID){
                    $respuesta = $this->deleteLineaPedido($this->lineaPedidoID);
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

    private function getLineasPedidoByPedidoId($pedidoId){
        $lineasPedido = $this->lineaPedidoDB->getAllFromByPedidoId($pedidoId);
        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'succes' => true,
            'data' => $lineasPedido,
            'count' => count($lineasPedido)
        ]);
        return $respuesta;   
    }

    private function createLineaPedido(){
        $input = json_decode(file_get_contents("php://input"), true);
        
        if(!$input || !isset($input['id_pedidos']) || !isset($input['id_productos']) || 
           !isset($input['cantidad']) || !isset($input['precio_unitario'])){
            $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $respuesta['body'] = json_encode([
                'succes' => false,
                'error' => 'Faltan campos requeridos: id_pedidos, id_productos, cantidad, precio_unitario'
            ]);
            return $respuesta;
        }
        
        if($this->lineaPedidoDB->create_lineaPedido($input)){
            $respuesta['status_code_header'] = 'HTTP/1.1 201 Created';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'message' => 'Línea de pedido creada exitosamente'
            ]);
            return $respuesta;
        }
        
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Error al crear la línea de pedido'
        ]);
        return $respuesta;
    }

    private function actualizarLineaPedido(){
        if(!$this->lineaPedidoID){
            $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $respuesta['body'] = json_encode([
                'succes' => false,
                'error' => 'ID de línea de pedido requerido'
            ]);
            return $respuesta;
        }
        
        $input = json_decode(file_get_contents("php://input"), true);
        
        if(!$input || !isset($input['id_pedidos']) || !isset($input['id_productos']) || 
           !isset($input['cantidad']) || !isset($input['precio_unitario'])){
            $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $respuesta['body'] = json_encode([
                'succes' => false,
                'error' => 'Faltan campos requeridos: id_pedidos, id_productos, cantidad, precio_unitario'
            ]);
            return $respuesta;
        }
        
        if($this->lineaPedidoDB->update_lineaPedido($this->lineaPedidoID, $input)){
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'message' => 'Línea de pedido actualizada exitosamente'
            ]);
            return $respuesta;
        }
        
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Error al actualizar la línea de pedido'
        ]);
        return $respuesta;
    }

    private function deleteLineaPedido($id){
        if($this->lineaPedidoDB->delete_lineaPedido($id)){
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'message' => 'Línea de pedido eliminada'
            ]);
            return $respuesta;
        }
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Error al eliminar la línea de pedido'
        ]);
        return $respuesta;
    }

    private function respuestaNoEncontrada(){
        $respuesta['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Línea de pedido no encontrada'
        ]);
        return $respuesta;
            
    }


}