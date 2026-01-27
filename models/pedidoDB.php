<?php

class pedidoDB{
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

    }
    
    




}