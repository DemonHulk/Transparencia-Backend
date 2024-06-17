<?php

require_once 'database/conexion.php';

class TrimestreModel {

    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT t.id_trimestre, t.trimestre, t.activo, t.fecha_creacion, e.ejercicio FROM trimestre t JOIN ejercicio e ON t.id_ejercicio = e.id_ejercicio ORDER BY  t.id_trimestre");
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al obtener todos los trimestres: " . $e->getMessage()];
        }
    }
    
    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM trimestre WHERE id_trimestre = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el trimestre con: " . $e->getMessage());
        }
    }

    public function InsertModel($datos) {
        try {
            $fecha = date('Y-m-d');
            $hora_creacion = date('H:i:s');

            $conn = Conexion::Conexion();

            // Verificar si ya existe un trimestre con el mismo nombre
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM trimestre WHERE trimestre = :trimestre AND id_ejercicio = :id_ejercicio");
            $checkStmt->bindParam(':trimestre', $datos['trimestre'], PDO::PARAM_STR);
            $checkStmt->bindParam(':id_ejercicio', $datos['ejercicio'], PDO::PARAM_INT);
            $checkStmt->execute();
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                return array(false, "Error: Ya existe un trimestre con el mismo nombre y ejercicio fiscal");
            }

            $stmt = $conn->prepare("
                INSERT INTO trimestre (trimestre, id_ejercicio, activo, fecha_creacion, hora_creacion, fecha_actualizado) 
                VALUES (:trimestre, :ejercicio, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
        
            // Pasamos los datos que recibimos de parte del frontend y ponemos los datos de la fecha y hora
            $stmt->bindParam(':trimestre', $datos['trimestre'], PDO::PARAM_STR);
            $stmt->bindParam(':ejercicio', $datos['ejercicio'], PDO::PARAM_STR);
            $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':fecha_creacion', $fecha, PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $hora_creacion, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $fecha, PDO::PARAM_STR);

            $stmt->execute();
            return array(true, "Trimestre guardado exitosamente");
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el Trimestre: " . $e->getMessage());
        }
    }

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();

            // Verificar si ya existe un trimestre con el mismo nombre
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM trimestre WHERE trimestre = :trimestre AND id_ejercicio = :id_ejercicio AND id_trimestre != :id");
            $checkStmt->bindParam(':trimestre', $datos['trimestre'], PDO::PARAM_STR);
            $checkStmt->bindParam(':id_ejercicio', $datos['ejercicio'], PDO::PARAM_INT);
            $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $checkStmt->execute();
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                return ['res' => false, 'data' => "Ya existe un trimestre con el mismo nombre y ejercicio fiscal"];
            }

            $stmt = $conn->prepare("UPDATE trimestre SET trimestre = :trimestre, id_ejercicio = :ejercicio, fecha_actualizado = :fecha_actualizado WHERE id_trimestre = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':trimestre', $datos['trimestre'], PDO::PARAM_STR);
            $stmt->bindParam(':ejercicio', $datos['ejercicio'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => true, 'data' => "No se encontro el trimestre"];
            }
            return ['res' => true, 'data' => "Trimestre actualizado exitosamente"];
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el trimestre: " . $e->getMessage());
        }
    }

    public function DeleteModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE trimestre SET activo = false, fecha_actualizado = :fecha_actualizado WHERE id_trimestre = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontró el trimestre, intente mas tarde"];
            }
            return ['res' => true, 'data' => "Trimestre desactivado"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al desactivar el trimestre: " . $e->getMessage()];
        }
    }

    public function ActivateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE trimestre SET activo = true, fecha_actualizado = :fecha_actualizado WHERE id_trimestre = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontró el trimestre, intente mas tarde"];
            }
            return ['res' => true, 'data' => "Trimestre activado"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al desactivar el trimestre: " . $e->getMessage()];
        }
    }
}
