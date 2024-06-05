<?php

require_once 'database/conexion.php';

class ContenidoModel {
    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM contenido WHERE activo = true ORDER BY id_contenido");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los contenidos: " . $e->getMessage());
        }
    }

    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM contenido WHERE id_contenido = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el contenido con ID $id: " . $e->getMessage());
        }
    }

    public function InsertDocumentoModel($datos) {
        try {
            $conn = Conexion::Conexion();
            $conn->beginTransaction();

            // Si no se pasa el orden, obtener el último orden
            if (!isset($datos['orden_contenido']) || empty($datos['orden_contenido'])) {
                $stmt = $conn->prepare("SELECT MAX(orden_contenido) FROM contenido WHERE id_apartado_punto = :id_apartado_punto AND (fk_apartado_punto IS NULL OR fk_apartado_punto = :fk_apartado_punto)");
                $stmt->bindParam(':id_apartado_punto', $datos['id_apartado_punto'], PDO::PARAM_INT);
                $stmt->bindParam(':fk_apartado_punto', $datos['fk_apartado_punto'], PDO::PARAM_INT);
                $stmt->execute();
                $lastOrder = $stmt->fetchColumn();
                $datos['orden_contenido'] = $lastOrder + 1;
            }

			// Guardar el archivo en la ruta especificada
			$archivoBase64 = base64_encode($datos['archivo']);
			$contenidoArchivo = base64_decode($archivoBase64);
			$rutaDocumento = 'assets/documents/' . $datos['nombre_interno_documento'];
			file_put_contents($rutaDocumento, $contenidoArchivo);


            $stmt = $conn->prepare("INSERT INTO contenido (id_usuario, id_apartado_punto, nombre_externo_documento, nombre_interno_documento, ruta_documento, id_trimestre, contenido, orden_contenido, activo, fecha_creacion, hora_creacion, fecha_actualizado) VALUES (:id_usuario, :id_apartado_punto, :nombre_externo_documento, :nombre_interno_documento, :ruta_documento, :id_trimestre, :contenido, :orden_contenido, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':id_apartado_punto', $datos['id_apartado_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':nombre_externo_documento', $datos['nombre_externo_documento'], PDO::PARAM_STR);
            $stmt->bindParam(':nombre_interno_documento', $datos['nombre_interno_documento'], PDO::PARAM_STR);
            $stmt->bindParam(':ruta_documento', $rutaDocumento, PDO::PARAM_STR);
            $stmt->bindParam(':id_trimestre', $datos['id_trimestre'], PDO::PARAM_INT);
            $stmt->bindParam(':contenido', $datos['contenido'], PDO::PARAM_STR);
            $stmt->bindParam(':orden_contenido', $datos['orden_contenido'], PDO::PARAM_INT);
            $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $datos['hora_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();

            $conn->commit();
            return "Documento guardado exitosamente";
        } catch (PDOException $e) {
            $conn->rollBack();
            throw new Exception("Error al insertar el documento: " . $e->getMessage());
        }
    }

    public function InsertContenidoModel($datos) {
        try {
            $conn = Conexion::Conexion();
            $conn->beginTransaction();

            // Si no se pasa el orden, obtener el último orden
            if (!isset($datos['orden_contenido']) || empty($datos['orden_contenido'])) {
                $stmt = $conn->prepare("SELECT MAX(orden_contenido) FROM contenido WHERE id_apartado_punto = :id_apartado_punto AND (fk_apartado_punto IS NULL OR fk_apartado_punto = :fk_apartado_punto)");
                $stmt->bindParam(':id_apartado_punto', $datos['id_apartado_punto'], PDO::PARAM_INT);
                $stmt->bindParam(':fk_apartado_punto', $datos['fk_apartado_punto'], PDO::PARAM_INT);
                $stmt->execute();
                $lastOrder = $stmt->fetchColumn();
                $datos['orden_contenido'] = $lastOrder + 1;
            }

            $stmt = $conn->prepare("INSERT INTO contenido (id_usuario, id_apartado_punto, id_trimestre, contenido, orden_contenido, activo, fecha_creacion, hora_creacion, fecha_actualizado) VALUES (:id_usuario, :id_apartado_punto, :id_trimestre, :contenido, :orden_contenido, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':id_apartado_punto', $datos['id_apartado_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':id_trimestre', $datos['id_trimestre'], PDO::PARAM_INT);
            $stmt->bindParam(':contenido', $datos['contenido'], PDO::PARAM_STR);
            $stmt->bindParam(':orden_contenido', $datos['orden_contenido'], PDO::PARAM_INT);
            $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $datos['hora_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();

            $conn->commit();
            return "Contenido guardado exitosamente";
        } catch (PDOException $e) {
            $conn->rollBack();
            throw new Exception("Error al insertar el contenido: " . $e->getMessage());
        }
    }

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE contenido SET id_usuario = :id_usuario, id_apartado_punto = :id_apartado_punto, nombre_externo_documento = :nombre_externo_documento, nombre_interno_documento = :nombre_interno_documento, ruta_documento = :ruta_documento, id_trimestre = :id_trimestre, contenido = :contenido, orden_contenido = :orden_contenido, activo = :activo, fecha_actualizado = :fecha_actualizado WHERE id_contenido = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':id_apartado_punto', $datos['id_apartado_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':nombre_externo_documento', $datos['nombre_externo_documento'], PDO::PARAM_STR);
            $stmt->bindParam(':nombre_interno_documento', $datos['nombre_interno_documento'], PDO::PARAM_STR);
            $stmt->bindParam(':ruta_documento', $datos['ruta_documento'], PDO::PARAM_STR);
            $stmt->bindParam(':id_trimestre', $datos['id_trimestre'], PDO::PARAM_INT);
            $stmt->bindParam(':contenido', $datos['contenido'], PDO::PARAM_STR);
            $stmt->bindParam(':orden_contenido', $datos['orden_contenido'], PDO::PARAM_INT);
            $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un contenido activo con ID $id");
            }
            return "Contenido con ID $id actualizado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el contenido: " . $e->getMessage());
        }
    }

    public function DeleteModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE contenido SET activo = false WHERE id_contenido = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un contenido activo con ID $id");
            }
            return "Contenido con ID $id desactivado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al desactivar el contenido: " . $e->getMessage());
        }
    }
}
?>
