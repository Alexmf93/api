<?php

class pedidosDB{
    private $db;
    private $table = 'pedidos';

    public function __construct($database) {
        $this->db = $database->getConexion();
    }

    //extraer los pedidos
    public function getAll(){
        $sql = "SELECT * FROM {$this->table}";
        $resultado = $this->db->query($sql);

        if($resultado && $resultado->num_rows > 0){
            $pedidos = [];
         
            while($row = $resultado->fetch_assoc()){
                $pedidos[] = $row;
            }

            return $pedidos;
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

    public function createpedidos($datos){
        $sql = "INSERT INTO {$this->table} (id_usuarios, fecha, numero_factura, total) 
                VALUES (?, ?, ?, ?)";
        $stsm = $this->db->prepare($sql);
        if($stsm){
            $stsm->bind_param(
                "sssi",
                $datos['id_usuarios'],
                $datos['fecha'],
                $datos['numero_factura'],
                $datos['total']
            );
            $stsm->execute();
            $stsm->close();
            return true;
        }
        return false;
    }

    public function updatepedidos($id, $datos){
        $sql = "UPDATE {$this->table} SET id_usuarios = ?, fecha = ?, numero_factura = ?, total = ? 
                WHERE id = ?";
        $stsm = $this->db->prepare($sql);
        if($stsm){
            $stsm->bind_param(
                "sssd",                 
                $datos['id_usuarios'],
                $datos['fecha'],
                $datos['numero_factura'],
                $datos['total'],
                $id
            );
        }
        $stsm->execute();
        $stsm->close();
        return true;
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
