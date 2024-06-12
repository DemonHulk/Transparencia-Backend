<?php

require_once 'database/conexion.php';

class TitulosPuntoModel {

    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM titulos_punto WHERE activo = true ORDER BY id_titulos_punto");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los títulos_punto: " . $e->getMessage());
        }
    }

    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM titulos_punto WHERE id_titulos_punto = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el título_punto con ID $id: " . $e->getMessage());
        }
    }

    public function InsertModel($datos) {
        try {
            $fecha = date('Y-m-d');
            $hora_creacion = date('H:i:s');

            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                INSERT INTO titulos_punto (id_titulo, id_punto, fk_titulos_punto, link, punto_destino, orden_titulos_punto, activo, fecha_creacion, hora_creacion, fecha_actualizado) 
                VALUES (:id_titulo, :id_punto, :fk_titulos_punto, :link, :punto_destino, :orden_titulos_punto, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
        
            $stmt->bindParam(':id_titulo', $datos['id_titulo'], PDO::PARAM_INT);
            $stmt->bindParam(':id_punto', $datos['id_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':fk_titulos_punto', $datos['fk_titulos_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':link', $datos['link'], PDO::PARAM_BOOL);
            $stmt->bindParam(':punto_destino', $datos['punto_destino'], PDO::PARAM_INT);
            $stmt->bindParam(':orden_titulos_punto', $datos['orden_titulos_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':fecha_creacion', $fecha, PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $hora_creacion, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $fecha, PDO::PARAM_STR);

            $stmt->execute();
            return "Título_punto guardado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el título_punto: " . $e->getMessage());
        }
    }

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                UPDATE titulos_punto 
                SET id_titulo = :id_titulo, id_punto = :id_punto, fk_titulos_punto = :fk_titulos_punto, 
                    link = :link, punto_destino = :punto_destino, orden_titulos_punto = :orden_titulos_punto, 
                    fecha_actualizado = :fecha_actualizado 
                WHERE id_titulos_punto = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_titulo', $datos['id_titulo'], PDO::PARAM_INT);
            $stmt->bindParam(':id_punto', $datos['id_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':fk_titulos_punto', $datos['fk_titulos_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':link', $datos['link'], PDO::PARAM_BOOL);
            $stmt->bindParam(':punto_destino', $datos['punto_destino'], PDO::PARAM_INT);
            $stmt->bindParam(':orden_titulos_punto', $datos['orden_titulos_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un título_punto activo con ID $id");
            }
            return "Título_punto con ID $id actualizado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el título_punto: " . $e->getMessage());
        }
    }

    public function DeleteModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                UPDATE titulos_punto 
                SET activo = false 
                WHERE id_titulos_punto = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un título_punto activo con ID $id");
            }
            return "Título_punto con ID $id desactivado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al desactivar el título_punto: " . $e->getMessage());
        }
    }
}
