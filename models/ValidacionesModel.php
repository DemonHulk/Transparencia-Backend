<?php

require_once 'database/conexion.php';

class ValidacionesModel {
    public function ValidarTexto($texto) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT validar_texto(:texto)");
            $stmt->bindParam(':texto', $texto, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al validar el texto: " . $e->getMessage());
        }
    }

    public function ValidarTextoNumero($texto) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT validar_txt_num(:texto)");
            $stmt->bindParam(':texto', $texto, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al validar el texto numérico: " . $e->getMessage());
        }
    }

    public function ValidarFecha($fecha) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT validar_fecha(:fecha)");
            $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al validar la fecha: " . $e->getMessage());
        }
    }

    public function ValidarHora($hora) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT validar_hora(:hora)");
            $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al validar la hora: " . $e->getMessage());
        }
    }

    public function ValidarCorreo($correo) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT validar_correo(:correo)");
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al validar el correo electrónico: " . $e->getMessage());
        }
    }

    public function ValidarNumeros($cadena) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT validar_numeros(:cadena)");
            $stmt->bindParam(':cadena', $cadena, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al validar los números: " . $e->getMessage());
        }
    }

    public function ValidarPassword($texto) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT validar_password(:texto)");
            $stmt->bindParam(':texto', $texto, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al validar la contraseña: " . $e->getMessage());
        }
    }

    public function ValidarNoScript($texto) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT validar_no_script(:texto)");
            $stmt->bindParam(':texto', $texto, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al validar el script: " . $e->getMessage());
        }
    }

}
