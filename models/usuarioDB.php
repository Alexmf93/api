<?php

class UsuarioDB {
    private $db;
    private $table = 'usuarios';

    public function __construct($database) {
        $this->db = $database->getConexion();
    }

    public function getAll(){
        $sql = "SELECT * FROM {$this->table}";
        $resultado = $this->db->query($sql);

        if($resultado && $resultado->num_rows > 0){
            $usuarios = [];
         
            while($row = $resultado->fetch_assoc()){
                $usuarios[] = $row;
            }

            return $usuarios;
        }else{
            return [];
        }    
    }

    public function getById($id){
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stsm = $this->db->prepare($sql);
        if($stsm){
            $stsm->bind_param("i", $id);
            $stsm->execute();
            $resultado = $stsm->get_result();
            if($resultado && $resultado->num_rows > 0){
                return $resultado->fetch_assoc();
            }
            $stsm->close();
        }
        return null;
    }

    public function createUsuario($datos){

    $password_hash = password_hash($datos['password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO {$this->table} (nombre, mail, password) 
                VALUES (?, ?, ?)";
        $stsm = $this->db->prepare($sql);
        if($stsm){
            $stsm->bind_param(
                "sss",
                $datos['nombre'],
                $datos['mail'],
                $password_hash
            );
            $stsm->execute();
            $stsm->close();
            return true;
        }
        return false;
    }

    public function updateUsuario($id, $datos){
        $password_hash = password_hash($datos['password'], PASSWORD_BCRYPT);
        $sql = "UPDATE {$this->table} SET nombre = ?, mail = ?, password = ?,
                WHERE id = ?";
        $stsm = $this->db->prepare($sql);
        if($stsm){
            $stsm->bind_param(
                "ssdssi",
                $datos['nombre'],
                $datos['mail'],
                $password_hash,
                $id
            );
            $stsm->execute();
            $stsm->close();
            return true;
        }
        return false;
    }

    public function delete ($id){
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stsm = $this->db->prepare($sql);
        if($stsm){
            $stsm->bind_param("i", $id);
            $stsm->execute();
            $stsm->close();
            return true;
        }
        return false;
    }

}