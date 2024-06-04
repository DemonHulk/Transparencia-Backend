<?php

require_once 'database/conexion.php';

class AreaModel {
    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM area");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todas las áreas: " . $e->getMessage());
        }
    }

    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM area WHERE id_area = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el área con ID $id: " . $e->getMessage());
        }
    }

    public function InsertModel($datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("INSERT INTO area (nombre_area, fecha_creacion, hora_creacion, fecha_actualizado) VALUES (:nombre_area, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            $stmt->execute($datos);
            return "Área guardada exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el área: " . $e->getMessage());
        }
    }

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE area SET nombre_area = :nombre_area, fecha_actualizado = :fecha_actualizado WHERE id_area = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre_area', $datos['nombre_area'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un área con ID $id");
            }
            return "Área con ID $id actualizada exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el área: " . $e->getMessage());
        }
    }

    public function DeleteModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("DELETE FROM area WHERE id_area = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un área con ID $id");
            }
            return "Área con ID $id eliminada exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el área: " . $e->getMessage());
        }
    }
}
