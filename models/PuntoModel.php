<?php

require_once 'database/conexion.php';

class PuntoModel {
    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM punto WHERE activo = true ORDER BY orden_punto");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los puntos: " . $e->getMessage());
        }
    }

    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM punto WHERE id_punto = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el punto con ID $id: " . $e->getMessage());
        }
    }

    public function InsertModel($datos) {
        try {
            $conn = Conexion::Conexion();
            $conn->beginTransaction();

            if (!isset($datos['orden_punto']) || empty($datos['orden_punto'])) {
                // Obtener el último orden existente sin importar el estado
                $stmt = $conn->prepare("SELECT MAX(orden_punto) FROM punto");
                $stmt->execute();
                $lastOrder = $stmt->fetchColumn();
                $datos['orden_punto'] = $lastOrder + 1;
            }

            // Ajustar los valores de orden_punto
            $this->adjustOrder($conn, $datos['orden_punto'], 0);

            $stmt = $conn->prepare("INSERT INTO punto (nombre_punto, orden_punto, activo, fecha_creacion, hora_creacion, fecha_actualizado) VALUES (:nombre_punto, :orden_punto, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            $stmt->execute($datos);

            $conn->commit();
            return "Punto guardado exitosamente";
        } catch (PDOException $e) {
            $conn->rollBack();
            throw new Exception("Error al insertar el punto: " . $e->getMessage());
        }
    }

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $conn->beginTransaction();

            // Obtener el orden_punto actual del registro
            $stmt = $conn->prepare("SELECT orden_punto FROM punto WHERE id_punto = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $currentOrder = $stmt->fetchColumn();

            // Ajustar los valores de orden_punto
            $this->adjustOrder($conn, $datos['orden_punto'], $currentOrder);

            $stmt = $conn->prepare("UPDATE punto SET nombre_punto = :nombre_punto, orden_punto = :orden_punto, activo = :activo, fecha_actualizado = :fecha_actualizado WHERE id_punto = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre_punto', $datos['nombre_punto'], PDO::PARAM_STR);
            $stmt->bindParam(':orden_punto', $datos['orden_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un punto con ID $id");
            }

            $conn->commit();
            return "Punto con ID $id actualizado exitosamente";
        } catch (PDOException $e) {
            $conn->rollBack();
            throw new Exception("Error al actualizar el punto: " . $e->getMessage());
        }
    }

    public function DeleteModel($id) {
        try {
            $conn = Conexion::Conexion();
            $conn->beginTransaction();

            $stmt = $conn->prepare("UPDATE punto SET activo = false WHERE id_punto = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un punto con ID $id");
            }

            $conn->commit();
            return "Punto con ID $id desactivado exitosamente";
        } catch (PDOException $e) {
            $conn->rollBack();
            throw new Exception("Error al desactivar el punto: " . $e->getMessage());
        }
    }

    private function adjustOrder($conn, $newOrder, $currentOrder) {
        if ($newOrder < $currentOrder || $currentOrder == 0) {
            $stmt = $conn->prepare("UPDATE punto SET orden_punto = orden_punto + 1 WHERE orden_punto >= :orden_punto AND activo = true");
        } else {
            $stmt = $conn->prepare("UPDATE punto SET orden_punto = orden_punto - 1 WHERE orden_punto > :orden_punto AND orden_punto <= :current_order AND activo = true");
            $stmt->bindParam(':current_order', $currentOrder, PDO::PARAM_INT);
        }
        $stmt->bindParam(':orden_punto', $newOrder, PDO::PARAM_INT);
        $stmt->execute();
    }
}
