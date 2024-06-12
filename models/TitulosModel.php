<?php

require_once 'database/conexion.php';

class TitulosModel {

    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM titulos WHERE activo = true ORDER BY id_titulo");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los títulos: " . $e->getMessage());
        }
    }

    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM titulos WHERE id_titulo = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el título con ID $id: " . $e->getMessage());
        }
    }

    public function InsertModel($datos) {
        try {
            $fecha = date('Y-m-d');
            $hora_creacion = date('H:i:s');

            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                INSERT INTO titulos (nombre_titulo, tipo_contenido, activo, fecha_creacion, hora_creacion, fecha_actualizado) 
                VALUES (:nombre_titulo, :tipo_contenido, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
        
            // Pasamos los datos que recibimos del frontend y ponemos los datos de la fecha y hora
            $stmt->bindParam(':nombre_titulo', $datos['nombre_titulo'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo_contenido', $datos['tipo_contenido'], PDO::PARAM_INT);
            $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':fecha_creacion', $fecha, PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $hora_creacion, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $fecha, PDO::PARAM_STR);

            $stmt->execute();
            return "Título guardado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el título: " . $e->getMessage());
        }
    }

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                UPDATE titulos 
                SET nombre_titulo = :nombre_titulo, tipo_contenido = :tipo_contenido, fecha_actualizado = :fecha_actualizado 
                WHERE id_titulo = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre_titulo', $datos['nombre_titulo'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo_contenido', $datos['tipo_contenido'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un título activo con ID $id");
            }
            return "Título con ID $id actualizado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el título: " . $e->getMessage());
        }
    }

    public function DeleteModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                UPDATE titulos 
                SET activo = false 
                WHERE id_titulo = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un título activo con ID $id");
            }
            return "Título con ID $id desactivado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al desactivar el título: " . $e->getMessage());
        }
    }
}
