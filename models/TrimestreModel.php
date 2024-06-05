<?php

require_once 'database/conexion.php';

class TrimestreModel {

    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM trimestre WHERE activo = true ORDER BY id_trimestre");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los trimestre: " . $e->getMessage());
        }
    }
    
    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM trimestre WHERE id_trimestre = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el trimestre con ID $id: " . $e->getMessage());
        }
    }

    public function InsertModel($datos) {
        try {
            $fecha = date('Y-m-d');
            $hora_creacion = date('H:i:s');

            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                INSERT INTO trimestre (trimestre, ejercicio, activo, fecha_creacion, hora_creacion, fecha_actualizado) 
                VALUES (:trimestre, :ejercicio, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
        
            // Pasamos los datos que recibimos de parte del frontend y ponemos los datos de la fecha y hora
            $stmt->bindParam(':trimestre', $datos['trimestre'], PDO::PARAM_STR);
            $stmt->bindParam(':ejercicio', $datos['ejercicio'], PDO::PARAM_STR);
            $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':fecha_creacion', $fecha, PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $hora_creacion, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $fecha, PDO::PARAM_STR);

            $stmt->execute();
            return "Trimestre guardado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el Trimestre: " . $e->getMessage());
        }
    }

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE trimestre SET trimestre = :trimestre, ejercicio = :ejercicio, fecha_actualizado = :fecha_actualizado WHERE id_trimestre = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':trimestre', $datos['trimestre'], PDO::PARAM_STR);
            $stmt->bindParam(':ejercicio', $datos['ejercicio'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un trimestre activo con ID $id");
            }
            return "Trimestre con ID $id actualizado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el trimestre: " . $e->getMessage());
        }
    }

    public function DeleteModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE trimestre SET activo = false WHERE id_trimestre = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un trimestre activo con ID $id");
            }
            return "Trimestre con ID $id desactivado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al desactivar el Trimestre: " . $e->getMessage());
        }
    }


}
