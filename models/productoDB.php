<?php

class ProductoDB {
    private $db;
    private $table = 'productos';

    public function __construct($database) {
        $this->db = $database->getConexion();
    }

    public function getAll(){
        $sql = "SELECT * FROM {$this->table}";
        $resultado = $this->db->query($sql);

        if($resultado && $resultado->num_rows > 0){
            $productos = [];
         
            while($row = $resultado->fetch_assoc()){
                $productos[] = $row;
            }

            return $productos;
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

    public function createProducto($datos){
        $sql = "INSERT INTO {$this->table} (codigo, nombre, precio, descripcion, imagen) 
                VALUES (?, ?, ?, ?, ?)";
        $stsm = $this->db->prepare($sql);
        if($stsm){
            $stsm->bind_param(
                "ssdss",
                $datos['codigo'],
                $datos['nombre'],
                $datos['precio'],
                $datos['descripcion'],
                $datos['imagen']
            );
            $stsm->execute();
            $stsm->close();
            return true;
        }
        return false;
    }

    public function updateProducto($id, $datos){
        $sql = "UPDATE {$this->table} SET codigo = ?, nombre = ?, precio = ?, descripcion = ?, imagen = ? 
                WHERE id = ?";
        $stsm = $this->db->prepare($sql);
        if($stsm){
            $stsm->bind_param(
                "ssdssi",
                $datos['codigo'],
                $datos['nombre'],
                $datos['precio'],
                $datos['descripcion'],
                $datos['imagen'],
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