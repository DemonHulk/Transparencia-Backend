<?php

require_once 'database/conexion.php';

class EjercicioModel {

    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM ejercicio WHERE activo = true ORDER BY id_ejercicio");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los ejercicios: " . $e->getMessage());
        }
    }

    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM ejercicio WHERE id_ejercicio = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el ejercicio con ID $id: " . $e->getMessage());
        }
    }

    public function InsertModel($datos) {
        try {
            $fecha = date('Y-m-d');
            $hora_creacion = date('H:i:s');

            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                INSERT INTO ejercicio (ejercicio, activo, fecha_creacion, hora_creacion, fecha_actualizado) 
                VALUES (:ejercicio, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
        
            $stmt->bindParam(':ejercicio', $datos['ejercicio'], PDO::PARAM_STR);
            $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':fecha_creacion', $fecha, PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $hora_creacion, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $fecha, PDO::PARAM_STR);

            $stmt->execute();
            return "Ejercicio guardado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el ejercicio: " . $e->getMessage());
        }
    }

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                UPDATE ejercicio 
                SET ejercicio = :ejercicio, fecha_actualizado = :fecha_actualizado 
                WHERE id_ejercicio = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':ejercicio', $datos['ejercicio'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un ejercicio activo con ID $id");
            }
            return "Ejercicio con ID $id actualizado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el ejercicio: " . $e->getMessage());
        }
    }

    public function DeleteModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                UPDATE ejercicio 
                SET activo = false 
                WHERE id_ejercicio = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un ejercicio activo con ID $id");
            }
            return "Ejercicio con ID $id desactivado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al desactivar el ejercicio: " . $e->getMessage());
        }
    }
}
