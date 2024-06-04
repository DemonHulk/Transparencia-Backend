<?php

require_once 'database/conexion.php';

class ApartadoPuntoModel {
    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM apartadoPunto WHERE activo = true ORDER BY id_apartado_punto");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los apartados de punto: " . $e->getMessage());
        }
    }

    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM apartadoPunto WHERE id_apartado_punto = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el apartado de punto con ID $id: " . $e->getMessage());
        }
    }

    public function InsertModel($datos) {
        try {
            $conn = Conexion::Conexion();
            $conn->beginTransaction();

            if (!isset($datos['orden_apartado']) || empty($datos['orden_apartado'])) {
                // Obtener el último orden para el punto dado y sumar 1
                if (!empty($datos['fk_apartado_punto'])) {
                    $stmt = $conn->prepare("SELECT MAX(orden_apartado) FROM apartadoPunto WHERE fk_apartado_punto = :fk_apartado_punto");
                    $stmt->bindParam(':fk_apartado_punto', $datos['fk_apartado_punto'], PDO::PARAM_INT);
                } else {
                    $stmt = $conn->prepare("SELECT MAX(orden_apartado) FROM apartadoPunto WHERE id_punto = :id_punto AND fk_apartado_punto IS NULL");
                    $stmt->bindParam(':id_punto', $datos['id_punto'], PDO::PARAM_INT);
                }
                $stmt->execute();
                $lastOrder = $stmt->fetchColumn();
                $datos['orden_apartado'] = $lastOrder + 1;
            }

            $stmt = $conn->prepare("INSERT INTO apartadoPunto (nombre_apartado, id_punto, tipo_contenido, eslink, url_link, orden_apartado, fk_apartado_punto, subtema, activo, fecha_creacion, hora_creacion, fecha_actualizado) VALUES (:nombre_apartado, :id_punto, :tipo_contenido, :eslink, :url_link, :orden_apartado, :fk_apartado_punto, :subtema, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            $stmt->bindParam(':nombre_apartado', $datos['nombre_apartado'], PDO::PARAM_STR);
            $stmt->bindParam(':id_punto', $datos['id_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':tipo_contenido', $datos['tipo_contenido'], PDO::PARAM_INT);
            $stmt->bindParam(':eslink', $datos['eslink'], PDO::PARAM_BOOL);
            $stmt->bindParam(':url_link', $datos['url_link'], PDO::PARAM_STR);
            $stmt->bindParam(':orden_apartado', $datos['orden_apartado'], PDO::PARAM_INT);
            $stmt->bindParam(':fk_apartado_punto', $datos['fk_apartado_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':subtema', $datos['subtema'], PDO::PARAM_BOOL);
            $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $datos['hora_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);

            $stmt->execute();

            $conn->commit();
            return "Apartado de punto guardado exitosamente";
        } catch (PDOException $e) {
            $conn->rollBack();
            throw new Exception("Error al insertar el apartado de punto: " . $e->getMessage());
        }
    }


    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE apartadoPunto SET nombre_apartado = :nombre_apartado, id_punto = :id_punto, tipo_contenido = :tipo_contenido, eslink = :eslink, url_link = :url_link, orden_apartado = :orden_apartado, fk_apartado_punto = :fk_apartado_punto, subtema = :subtema, activo = :activo, fecha_actualizado = :fecha_actualizado WHERE id_apartado_punto = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute($datos);
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un apartado de punto activo con ID $id");
            }
            return "Apartado de punto con ID $id actualizado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el apartado de punto: " . $e->getMessage());
        }
    }

    public function DeleteModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE apartadoPunto SET activo = false WHERE id_apartado_punto = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un apartado de punto activo con ID $id");
            }
            return "Apartado de punto con ID $id desactivado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al desactivar el apartado de punto: " . $e->getMessage());
        }
    }
}
