<?php

class LineaPedidoDB{
    private $db;
    private $table = 'linea_pedido';

    public function __construct($database) {
        $this->db = $database->getConexion();
    }

    public function getAllFromByPedidoId($id_pedidos){
        $sql = "SELECT * FROM {$this->table} WHERE id_pedidos = ?";
        $stsm = $this->db->prepare($sql);
        if($stsm){
            $stsm->bind_param("i", $id_pedidos);
            $stsm->execute();
            $resultado = $stsm->get_result();
            
            if($resultado && $resultado->num_rows > 0){
                $lineasPedido = [];
                
                while($row = $resultado->fetch_assoc()){
                    $lineasPedido[] = $row;
                }
                
                $stsm->close();
                return $lineasPedido;
            }
            $stsm->close();
        }
        return [];
    }

    public function create_lineaPedido($datos){
        $sql = "INSERT INTO {$this->table} (id_pedidos, id_productos, cantidad, precio_unitario) 
                VALUES (?, ?, ?, ?)";
        $stsm = $this->db->prepare($sql);
        if($stsm){
            $stsm->bind_param(
                "sssi",
                $datos['id_pedidos'],
                $datos['id_productos'],
                $datos['cantidad'],
                $datos['precio_unitario']
            );
            $stsm->execute();
            $stsm->close();
            // Retornar todas las líneas asociadas a ese pedido
            return $this->getAllFromByPedidoId($datos['id_pedidos']);
        }
        return false;

    }

    public function update_lineaPedido($id, $datos){
        $sql = "UPDATE {$this->table} SET id_pedidos = ?, id_productos = ?, cantidad = ?, precio_unitario = ? 
                WHERE id = ?";
        $stsm = $this->db->prepare($sql);
        if($stsm){
            $stsm->bind_param(
                "sssd",
                $datos['id_pedidos'],
                $datos['id_productos'],
                $datos['cantidad'],
                $datos['precio_unitario'],
                $id
            );
            $stsm->execute();
            $stsm->close();
            return true;
        }
        return false;
    }
                
    public function delete_lineaPedido($id){
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