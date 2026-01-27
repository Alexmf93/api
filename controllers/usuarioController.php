<?php

class UsuarioController {
    private $usuarioDB;
    private $requestMethod;
    private $usuarioID;

    public function __construct($db, $requestMethod, $usuarioId=null) {
        $this->usuarioDB = new UsuarioDB($db);
        $this->requestMethod = $requestMethod;
        $this->usuarioID = $usuarioId;
    }

    public function processRequest(){
        $method = $this->requestMethod;
        //Comprobar el metodo de llamada
        switch($method){
            case 'GET':
                if($this->usuarioID){
                    $respuesta = $this->getUsuario($this->usuarioID);
                }else{
                    $respuesta = $this->getAllUsuarios();
                }
                break;
            case 'POST':
                $respuesta = $this->createUsuario();
                break;
            case 'PUT':
                $respuesta = $this->actualizarUsuario();
                break;
            case 'DELETE':
                if($this->usuarioID){
                    $respuesta = $this->deleteUsuario($this->usuarioID);
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
    private function getUsuario($id){
            $usuario = $this->usuarioDB->getById($id);
            if(!$usuario){
                return $this->respuestaNoEncontrada();
            }
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'data' => $usuario
            ]);
            return $respuesta;
    }

    private function getAllUsuarios(){
            $usuarios = $this->usuarioDB->getAll();
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'data' => $usuarios,
                'count' => count($usuarios)
            ]);
            return $respuesta;   
    }

    private function createUsuario(){
        $input = json_decode(file_get_contents("php://input"), true);
        
        // Sanitizar datos
        $nombre = isset($input['nombre']) ? Validator::sanitizeString($input['nombre']) : '';
        $mail = isset($input['mail']) ? Validator::sanitizeEmail($input['mail']) : '';
        $password = isset($input['password']) ? $input['password'] : '';
        
        // Validar datos
        $validator = new Validator();
        $validator->validateRequired($nombre, 'nombre');
        $validator->validateRequired($password, 'password');
        $validator->validateEmail($mail, 'mail');
        $validator->validateMinLength($password, 8, 'password');
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
        
        // Crear el array con datos sanitizados
        $datosLimpios = [
            'nombre' => $nombre,
            'mail' => $mail,
            'password' => $password
        ];
        
        if($this->usuarioDB->createUsuario($datosLimpios)){
            $respuesta['status_code_header'] = 'HTTP/1.1 201 Created';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'message' => 'Usuario creado exitosamente'
            ]);
            return $respuesta;
        }
        
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Error al crear el usuario'
        ]);
        return $respuesta;
    }

    private function actualizarUsuario(){
        if(!$this->usuarioID){
            $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $respuesta['body'] = json_encode([
                'succes' => false,
                'error' => 'ID de usuario requerido'
            ]);
            return $respuesta;
        }
        
        // Verificar si el usuario existe
        $usuario = $this->usuarioDB->getById($this->usuarioID);
        if(!$usuario){
            return $this->respuestaNoEncontrada();
        }
        
        $input = json_decode(file_get_contents("php://input"), true);
        
        if(!$input || !isset($input['nombre']) || !isset($input['mail']) || 
           !isset($input['password'])){
            $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $respuesta['body'] = json_encode([
                'succes' => false,
                'error' => 'Faltan campos requeridos: nombre, mail, password'
            ]);
            return $respuesta;
        }
        
        if($this->usuarioDB->updateUsuario($this->usuarioID, $input)){
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'message' => 'Usuario actualizado exitosamente'
            ]);
            return $respuesta;
        }
        
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Error al actualizar el usuario'
        ]);
        return $respuesta;
    }

    private function deleteUsuario($id){
        // Verificar si el usuario existe
        $usuario = $this->usuarioDB->getById($id);
        if(!$usuario){
            return $this->respuestaNoEncontrada();
        }
        
        // Si existe, eliminarlo
        if($this->usuarioDB->delete($id)){
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'succes' => true,
                'message' => 'Usuario eliminado'
            ]);
            return $respuesta;
        }
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Error al eliminar el usuario'
        ]);
        return $respuesta;
    }

    private function respuestaNoEncontrada(){
        $respuesta['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $respuesta['body'] = json_encode([
            'succes' => false,
            'error' => 'Usuario no encontrado'
        ]);
        return $respuesta;
            
    }


}
