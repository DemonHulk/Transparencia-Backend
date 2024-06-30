<?php

class Conexion {

    static public function InfoDatabase() {
        return array(
            "database" => "transparencia5",  
            "user" => "transparencia_user_uh345",
            "pass" => "jkjrytuf*53sd",
            "host" => "localhost",
            "port" => "5432"     
        );
    }

    static public function Conexion() {
        $infoDB = self::InfoDatabase();
        $dsn = "pgsql:host={$infoDB['host']};port={$infoDB['port']};dbname={$infoDB['database']};";
        
        try {
            $conn = new PDO($dsn, $infoDB['user'], $infoDB['pass']);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec("set names 'utf8'");
            return $conn;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}