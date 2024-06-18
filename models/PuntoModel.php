<?php

require_once 'database/conexion.php';

class PuntoModel {
    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM punto  ORDER BY orden_punto");
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al obtener todas los puntos: " . $e->getMessage()];
        }
    }

    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM punto WHERE id_punto = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetch(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al obtener el punto: " . $e->getMessage()];
        }
    }


    public function InsertModel($datos) {
        try {
            $conn = Conexion::Conexion();
            $conn->beginTransaction();

            // Validar si el nombre del punto ya existe
            $stmt = $conn->prepare("SELECT COUNT(*) FROM punto WHERE nombre_punto = :nombrePunto");
            $stmt->bindParam(':nombrePunto', $datos['nombrePunto'], PDO::PARAM_STR);
            $stmt->execute();
            $existingCount = $stmt->fetchColumn();

            if ($existingCount > 0) {
                $conn->rollBack();
                return ['res' => false, 'data' => "Ya existe el punto con ese nombre"];
            }

            if (!isset($datos['orden_punto']) || empty($datos['orden_punto'])) {
                // Obtener el último orden existente sin importar el estado
                $stmt = $conn->prepare("SELECT MAX(orden_punto) FROM punto");
                $stmt->execute();
                $lastOrder = $stmt->fetchColumn();
                $datos['orden_punto'] = $lastOrder + 1;
            }

            // Ajustar los valores de orden_punto
            $this->adjustOrder($conn, $datos['orden_punto'], 0);

            // Insertar el punto en la tabla punto
            $stmt = $conn->prepare("INSERT INTO punto (nombre_punto, orden_punto,  fecha_creacion, hora_creacion, fecha_actualizado)
                                    VALUES (:nombrePunto, :orden_punto, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            $stmt->bindParam(':nombrePunto', $datos['nombrePunto'], PDO::PARAM_STR);
            $stmt->bindParam(':orden_punto', $datos['orden_punto'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $datos['hora_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();

            // Obtener el ID del punto recién insertado
            $idPunto = $conn->lastInsertId();

            // Insertar áreas seleccionadas en la tabla puntosareas
            foreach ($datos as $key => $value) {
                if (strpos($key, 'vertical-checkbox-') === 0 && $value === true) {
                    $idArea = substr($key, strlen('vertical-checkbox-'));

                    // Insertar en la tabla puntosareas
                    $stmt = $conn->prepare("INSERT INTO puntosareas (id_punto, id_area, activo, fecha_creacion, hora_creacion, fecha_actualizado)
                                            VALUES (:id_punto, :id_area, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
                    $stmt->bindParam(':id_punto', $idPunto, PDO::PARAM_INT);
                    $stmt->bindParam(':id_area', $idArea, PDO::PARAM_INT);
                    $stmt->bindParam(':activo', $datos['vertical-checkbox-'.$idArea], PDO::PARAM_BOOL);
                    $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
                    $stmt->bindParam(':hora_creacion', $datos['hora_creacion'], PDO::PARAM_STR);
                    $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
                    $stmt->execute();
                }
            }

            $conn->commit();
            return ['res' => true, 'data' => "Punto guardado exitosamente"];
        } catch (PDOException $e) {
            $conn->rollBack();
            throw new Exception("Error al insertar el punto y áreas: " . $e->getMessage());
        }
    }

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $conn->beginTransaction();

            // Verificar si ya existe un registro con el mismo nombre
            $stmt = $conn->prepare("
                SELECT COUNT(*) 
                FROM punto 
                WHERE nombre_punto = :nombre_punto 
                AND id_punto != :id");
            $stmt->bindParam(':nombre_punto', $datos['nombrePunto'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $conn->rollBack();
                return ['res' => false, 'data' => "Error: Ya existe otro punto con el mismo nombre"];
            }

            // Proceder con la actualización si no se encuentra duplicado
            $stmt = $conn->prepare("
                UPDATE punto 
                SET 
                    nombre_punto = :nombre_punto, 
                    fecha_actualizado = :fecha_actualizado 
                WHERE id_punto = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre_punto', $datos['nombrePunto'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                $conn->rollBack();
                return ['res' => false, 'data' => "Error al actualizar el punto"];
            }

            // Insertar o actualizar áreas seleccionadas en la tabla puntosareas
            foreach ($datos as $key => $value) {
                if (strpos($key, 'vertical-checkbox-') === 0 ) {
                    $idArea = substr($key, strlen('vertical-checkbox-'));

                    // Verificar si el registro ya existe
                    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM puntosareas WHERE id_punto = :id_punto AND id_area = :id_area");
                    $checkStmt->bindParam(':id_punto', $id, PDO::PARAM_INT);
                    $checkStmt->bindParam(':id_area', $idArea, PDO::PARAM_INT);
                    $checkStmt->execute();
                    $exists = $checkStmt->fetchColumn();

                    if ($exists) {
                        // Si existe, actualizar
                        $updateStmt = $conn->prepare("
                            UPDATE puntosareas SET
                                activo = :activo,
                                fecha_actualizado = :fecha_actualizado
                            WHERE id_punto = :id_punto AND id_area = :id_area
                        ");
                        $updateStmt->bindParam(':activo', $datos['vertical-checkbox-'.$idArea], PDO::PARAM_BOOL);
                        $updateStmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
                        $updateStmt->bindParam(':id_punto', $id, PDO::PARAM_INT);
                        $updateStmt->bindParam(':id_area', $idArea, PDO::PARAM_INT);
                        $updateStmt->execute();
                    } else {
                        // Si no existe, insertar
                        $insertStmt = $conn->prepare("
                            INSERT INTO puntosareas (id_punto, id_area, activo, fecha_creacion, hora_creacion, fecha_actualizado)
                            VALUES (:id_punto, :id_area, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)
                        ");
                        $insertStmt->bindParam(':id_punto', $id, PDO::PARAM_INT);
                        $insertStmt->bindParam(':id_area', $idArea, PDO::PARAM_INT);
                        $insertStmt->bindParam(':activo', $datos['vertical-checkbox-'.$idArea], PDO::PARAM_BOOL);
                        $insertStmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
                        $insertStmt->bindParam(':hora_creacion', $datos['hora_creacion'], PDO::PARAM_STR);
                        $insertStmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
                        $insertStmt->execute();
                    }
                }
            }


            $conn->commit();
            return ['res' => true, 'data' => "Punto actualizado exitosamente"];
        } catch (PDOException $e) {
            $conn->rollBack();
            return ['res' => false, 'data' => "Error al actualizar el punto: " . $e->getMessage()];
        }
    }


    public function DeleteModel($id,$datos) {
        try {
            $conn = Conexion::Conexion();
            $conn->beginTransaction();

            $stmt = $conn->prepare("UPDATE punto SET activo = FALSE and fecha_actualizado  = :fecha_actualizado  WHERE id_punto = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontro el punto, intente mas tarde"];
            }

            $conn->commit();

            return ['res' => true, 'data' => "Punto desactivado"];

        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al desactivar el Punto: " . $e->getMessage()];
        }
    }


    public function ActivateModel($id,$datos) {
        try {
            $conn = Conexion::Conexion();
            $conn->beginTransaction();

            $stmt = $conn->prepare("UPDATE punto SET activo = TRUE and fecha_actualizado  = :fecha_actualizado  WHERE id_punto = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontro el punto, intente mas tarde"];
            }

            $conn->commit();

            return ['res' => true, 'data' => "Punto activado"];

        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al activado el Punto: " . $e->getMessage()];
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
