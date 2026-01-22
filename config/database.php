<?php

require_once 'config.php';

class Database {
    private $host = DB_HOST;
    private $username = DB_USER;
    private $pass = DB_PASS;
    private $name = DB_NAME;

    private $conexion;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conexion = new mysqli($this->host, $this->username, $this->pass, $this->name);

        if($this->conexion->connect_error){
            die("Error de conxión: " . $this->conexion->connect_error);
        }

        $this->conexion->set_charset('utf8');
    
    }

    public function getConexion() {
        return $this->conexion;
    }

    public function close() {
        if($this->conexion) {
            $this->conexion->close();
        }
    }
}

