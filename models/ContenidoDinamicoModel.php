<?php

require_once 'database/conexion.php';

class ContenidoDinamicoModel {
    public function QueryAllModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT 
                                    contD.id_contenido_dinamico, 
                                    contD.nombre_externo_documento,
                                    contD.nombre_interno_documento,
                                    contD.descripcion,
                                    contD.activo, 
                                    contD.fecha_actualizado,
                                     CONCAT(tr.trimestre, ' ', ej.ejercicio) AS trimestre
                                FROM 
                                    contenido_dinamico contD
                                INNER JOIN 
                                    trimestre tr ON contD.id_trimestre = tr.id_trimestre
                                INNER JOIN
                                    ejercicio ej ON tr.id_ejercicio = ej.id_ejercicio
                                WHERE 
                                    contD.id_titulo = :id
                                    ORDER BY orden
                                    ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al obtener el contenido del tema: " . $e->getMessage()];
        }
    }

    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM contenido_dinamico WHERE id_contenido_dinamico = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al obtener los datos del contenido: " . $e->getMessage()];
        }
    }

    public function InsertDocumentoModel($datos) {
        try {
            $conn = Conexion::Conexion();
            $conn->beginTransaction();
    
            // Validar si ya existe un archivo con el mismo nombre interno
            $stmt = $conn->prepare("SELECT COUNT(*) FROM contenido_dinamico WHERE nombre_interno_documento = :nombre_interno_documento");
            $stmt->bindParam(':nombre_interno_documento', $datos['nombreInterno'], PDO::PARAM_STR);
            $stmt->execute();
            $existingInternalFileCount = $stmt->fetchColumn();
    
            if ($existingInternalFileCount > 0) {
                return ['res' => false, 'data' => "Ya existe un archivo con el mismo nombre interno."];
            }
    
            // Validar si ya existe un archivo con el mismo nombre externo
            $stmt = $conn->prepare("SELECT COUNT(*) FROM contenido_dinamico WHERE nombre_externo_documento = :nombre_externo_documento");
            $stmt->bindParam(':nombre_externo_documento', $datos['nombreExterno'], PDO::PARAM_STR);
            $stmt->execute();
            $existingExternalFileCount = $stmt->fetchColumn();
    
            if ($existingExternalFileCount > 0) {
                return ['res' => false, 'data' => "Ya existe un archivo con el mismo nombre externo."];
            }
    
            // Si no se pasa el orden, obtener el último orden
            if (!isset($datos['orden']) || empty($datos['orden'])) {
                $stmt = $conn->prepare("SELECT MAX(orden) FROM contenido_dinamico WHERE id_titulo = :id_titulo");
                $stmt->bindParam(':id_titulo', $datos['id_titulo'], PDO::PARAM_INT);
                $stmt->execute();
                $lastOrder = $stmt->fetchColumn();
                $datos['orden'] = $lastOrder + 1;
            }
            
            $activo = true;

            // Verificar y manejar la extensión .pdf en nombreInterno
            if (!preg_match('/\.pdf$/i', $datos['nombreInterno'])) {
                $datos['nombreInterno'] .= '.pdf';
            }
    
            // Guardar el archivo en la ruta especificada
            $archivoBase64 = base64_encode($datos['archivo']);
            $contenidoArchivo = base64_decode($archivoBase64);  
            $rutaDocumento = 'assets/documents/' . $datos['nombreInterno'];
            file_put_contents($rutaDocumento, $contenidoArchivo);
    
            $stmt = $conn->prepare("INSERT INTO contenido_dinamico (id_usuario, id_titulo, nombre_externo_documento, nombre_interno_documento, ruta_documento, id_trimestre, descripcion, orden, activo, fecha_creacion, hora_creacion, fecha_actualizado) 
                                    VALUES (:id_usuario, :id_titulo, :nombre_externo_documento, :nombre_interno_documento, :ruta_documento, :id_trimestre, :descripcion, :orden, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':id_titulo', $datos['id_titulo'], PDO::PARAM_INT);
            $stmt->bindParam(':nombre_externo_documento', $datos['nombreExterno'], PDO::PARAM_STR);
            $stmt->bindParam(':nombre_interno_documento', $datos['nombreInterno'], PDO::PARAM_STR);
            $stmt->bindParam(':ruta_documento', $rutaDocumento, PDO::PARAM_STR);
            $stmt->bindParam(':id_trimestre', $datos['id_trimestre'], PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':orden', $datos['orden'], PDO::PARAM_INT);
            $stmt->bindParam(':activo', $activo, PDO::PARAM_BOOL);
            $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $datos['hora_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
    
            $conn->commit();
    
            if ($stmt) {
                return ['res' => true, 'data' => "Documento guardado exitosamente"];
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            return ['res' => false, 'data' => "Error al insertar el contenido: " . $e->getMessage()];
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
            $conn->beginTransaction();
    
            // Obtener la información actual del documento
            $stmtSelect = $conn->prepare("SELECT ruta_documento, nombre_interno_documento, nombre_externo_documento FROM contenido_dinamico WHERE id_contenido_dinamico = :id");
            $stmtSelect->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtSelect->execute();
            $documentoActual = $stmtSelect->fetch(PDO::FETCH_ASSOC);

            $rutaDocumento = $documentoActual['ruta_documento'];
            $nombreAnteriorInterno = $documentoActual['nombre_interno_documento'];
            $nombreAnteriorExterno = $documentoActual['nombre_externo_documento'];
    
            // Validar si ya existe un archivo con el mismo nombre interno (si se cambia)
            if ($nombreAnteriorInterno !== $datos['nombreInterno']) {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM contenido_dinamico WHERE nombre_interno_documento = :nombre_interno_documento AND id_contenido_dinamico != :id");
                $stmt->bindParam(':nombre_interno_documento', $datos['nombreInterno'], PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $existingInternalFileCount = $stmt->fetchColumn();
    
                if ($existingInternalFileCount > 0) {
                    return ['res' => false, 'data' => "Ya existe un archivo con el mismo nombre interno."];
                }
            }
    
            // Validar si ya existe un archivo con el mismo nombre externo (si se cambia)
            if ($nombreAnteriorExterno !== $datos['nombreExterno']) {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM contenido_dinamico WHERE nombre_externo_documento = :nombre_externo_documento AND id_contenido_dinamico != :id");
                $stmt->bindParam(':nombre_externo_documento', $datos['nombreExterno'], PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $existingExternalFileCount = $stmt->fetchColumn();
    
                if ($existingExternalFileCount > 0) {
                    return ['res' => false, 'data' => "Ya existe un archivo con el mismo nombre externo."];
                }
            }
    
            // Actualizar el archivo si se proporciona uno nuevo
            if (!empty($datos['archivo'])) {
                // Eliminar el archivo existente si existe
                if (file_exists($rutaDocumento)) {
                    unlink($rutaDocumento);
                }
                
                $rutaDocumento = 'assets/documents/' . $datos['nombreInterno'];
                file_put_contents($rutaDocumento, $datos['archivo']);
            } 
            // Si solo se cambió el nombre, renombrar el archivo existente
            elseif ($nombreAnteriorInterno !== $datos['nombreInterno']) {
                $rutaAnterior = $rutaDocumento;
                $rutaDocumento = 'assets/documents/' . $datos['nombreInterno'];
                if (file_exists($rutaAnterior)) {
                    rename($rutaAnterior, $rutaDocumento);
                }
            }
    
            // Construir la consulta de actualización
            $sql = "UPDATE contenido_dinamico SET 
                        id_usuario = :id_usuario, 
                        nombre_externo_documento = :nombre_externo_documento, 
                        nombre_interno_documento = :nombre_interno_documento, 
                        id_trimestre = :id_trimestre, 
                        descripcion = :descripcion,  
                        fecha_actualizado = :fecha_actualizado,
                        ruta_documento = :ruta_documento
                    WHERE id_contenido_dinamico = :id";
    
            $stmt = $conn->prepare($sql);
    
            // Vincular parámetros
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':nombre_externo_documento', $datos['nombreExterno'], PDO::PARAM_STR);
            $stmt->bindParam(':nombre_interno_documento', $datos['nombreInterno'], PDO::PARAM_STR);
            $stmt->bindParam(':id_trimestre', $datos['id_trimestre'], PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->bindParam(':ruta_documento', $rutaDocumento, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
            $stmt->execute();
            $conn->commit();
    
            if ($stmt) {
                return ['res' => true, 'data' => "Documento actualizado exitosamente"];
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            throw new Exception("Error al actualizar el documento: " . $e->getMessage());
        }
    }
    
    

    public function DeleteModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE contenido_dinamico SET activo = false, fecha_actualizado = :fecha_actualizado WHERE id_contenido_dinamico = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontró el contenido, intente mas tarde"];
            }
            return ['res' => true, 'data' => "Contenido Desactivado Correctamente"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al desactivar el contenido: " . $e->getMessage()];
        }
    }

    public function ActivateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE contenido_dinamico SET activo = true, fecha_actualizado = :fecha_actualizado WHERE id_contenido_dinamico = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontró el contenido, intente mas tarde"];
            }
            return ['res' => true, 'data' => "Contenido Activado Correctamente"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al activar el contenido: " . $e->getMessage()];
        }
    }


    public function getDocument($fileName) {
        // Definir la ruta base correcta
        $baseDir = 'C:/xampp/htdocs/Transparencia-Backend/assets/documents/';
        
        // Asegurarse de que el nombre del archivo no contenga caracteres maliciosos
        $fileName = basename($fileName);
        
        $filePath = $baseDir . $fileName;

        error_log("Buscando archivo: " . $filePath);

        if (file_exists($filePath) && strpos(realpath($filePath), realpath($baseDir)) === 0) {
            $fileContent = file_get_contents($filePath);
            $mimeType = mime_content_type($filePath);
            return [
                'res' => true, 
                'data' => base64_encode($fileContent), 
                'mime' => $mimeType,
                'filename' => $fileName
            ];
        } else {
            error_log("Archivo no encontrado o fuera del directorio permitido: " . $filePath);
            return ['res' => false, 'data' => "Archivo no Encontrado: " . $fileName . " (Ruta completa: " . $filePath . ")"];
        }
    }
}
?>
