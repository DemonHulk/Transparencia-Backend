<?php

require_once 'database/conexion.php';

class EjercicioModel {

    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM ejercicio ORDER BY ejercicio");
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
            // Establecer la fecha y la hora actuales
            $fecha = date('Y-m-d');
            $hora_creacion = date('H:i:s');

            // Conectar a la base de datos
            $conn = Conexion::Conexion();

            // Verificar si ya existe un ejercicio con el mismo nombre
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM ejercicio WHERE ejercicio = :ejercicio");
            $checkStmt->bindParam(':ejercicio', $datos['ejercicio'], PDO::PARAM_STR);
            $checkStmt->execute();
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                return array(false, "Error: Ya existe un ejercicio con el mismo nombre");
            }

            // Preparar la consulta de inserción
            $stmt = $conn->prepare("
                INSERT INTO ejercicio (ejercicio, activo, fecha_creacion, hora_creacion, fecha_actualizado) 
                VALUES (:ejercicio, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            
            $stmt->bindParam(':ejercicio', $datos['ejercicio'], PDO::PARAM_STR);
            $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':fecha_creacion', $fecha, PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $hora_creacion, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $fecha, PDO::PARAM_STR);

            // Ejecutar la inserción
            $stmt->execute();
            return array(true, "Ejercicio guardado exitosamente");
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el ejercicio: " . $e->getMessage());
        }
    }



    public function UpdateModel($id, $datos) {
        try {
            // Conectar a la base de datos
            $conn = Conexion::Conexion();

            // Verificar si hay otro ejercicio activo con el mismo nombre
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM ejercicio WHERE ejercicio = :ejercicio AND id_ejercicio <> :id AND activo = true");
            $checkStmt->bindParam(':ejercicio', $datos['ejercicio'], PDO::PARAM_STR);
            $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $checkStmt->execute();
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                return array(false, "Error: Ya existe otro ejercicio activo con el mismo nombre");
            }

            // Preparar la consulta de actualización
            $stmt = $conn->prepare("
                UPDATE ejercicio 
                SET ejercicio = :ejercicio, fecha_actualizado = :fecha_actualizado 
                WHERE id_ejercicio = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':ejercicio', $datos['ejercicio'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                return array(false, "Error: No se encontró un ejercicio activo con ID $id");
            }

            return array(true, "Ejercicio actualizado exitosamente");
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
            return "Ejercicio desactivado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al desactivar el ejercicio: " . $e->getMessage());
        }
    }

    public function ActivateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE ejercicio SET activo = true, fecha_actualizado = :fecha_actualizado WHERE id_ejercicio = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontró el ejercicio, intente más tarde"];
            }
            return ['res' => true, 'data' => "Ejercicio activado"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al activar el ejercicio: " . $e->getMessage()];
        }
    }
}
