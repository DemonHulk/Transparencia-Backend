<?php

require_once 'database/conexion.php';

class HistorialModel {

    public function QueryAllVistosModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM historial ORDER BY fecha_creacion");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los títulos: " . $e->getMessage());
        }
    }

    public function QueryAllNoVistosModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM historial WHERE visto = false ORDER BY fecha_creacion");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los títulos: " . $e->getMessage());
        }
    }


    public function verModel($id, $datos) {

        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                UPDATE historial 
                SET visto = true,
                fecha_actualizado = :fecha_actualizado
                WHERE id_historial = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontro el Tema, intente mas tarde"];
            }
            return ['res' => true, 'data' => "Tema desactivado"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al desactivar el Tema: " . $e->getMessage()];
        }
    }

    public function DeleteModel($id, $datos) {

        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                UPDATE historial 
                SET activo = false ,
                fecha_actualizado = :fecha_actualizado
                WHERE id_historial = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontro el Registro, intente mas tarde"];
            }
            return ['res' => true, 'data' => "Registro desactivado"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al desactivar el Tema: " . $e->getMessage()];
        }
    }

    public function ActivateModel($id, $datos) {

        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                UPDATE historial
                SET activo = true ,
                fecha_actualizado = :fecha_actualizado
                WHERE id_historial = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontro el Registro, intente mas tarde"];
            }
            return ['res' => true, 'data' => "Registro activado"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al desactivar el Tema: " . $e->getMessage()];
        }
    }

}
