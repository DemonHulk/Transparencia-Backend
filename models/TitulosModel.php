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
            $conn = Conexion::Conexion();
            $conn->beginTransaction();

            // Validar si el nombre del titulo ya existe
            $stmt = $conn->prepare("SELECT COUNT(*) FROM titulos WHERE nombre_titulo = :nombre and id_punto = :punto and fk_titulos IS NULL");
            $stmt->bindParam(':nombre', $datos['nombreTitulo'], PDO::PARAM_STR);
            $stmt->bindParam(':punto', $datos['punto'], PDO::PARAM_INT);
            $stmt->execute();
            $existingCount = $stmt->fetchColumn();

            if ($existingCount > 0) {
                $conn->rollBack();
                return ['res' => false, 'data' => "Ya existe el titulo con ese nombre"];
            }

            if (!isset($datos['orden_titulos']) || empty($datos['orden_titulos'])) {
                // Obtener el último orden existente sin importar el estado
                $stmt = $conn->prepare("SELECT MAX(orden_titulos) FROM titulos WHERE id_punto = :punto AND fk_titulos IS NULL");
                $stmt->bindParam(':punto', $datos['punto'], PDO::PARAM_INT);
                $stmt->execute();
                $lastOrder = $stmt->fetchColumn();
                $datos['orden_titulos'] = $lastOrder + 1;
            }

            // Ajustar los valores de orden_titulos
            $this->adjustOrderTitulos($conn, $datos['orden_titulos'], 0, $datos['punto']);


            // Insertar el punto en la tabla punto
            $stmt = $conn->prepare("
                INSERT INTO titulos (id_punto, nombre_titulo, tipo_contenido, link, punto_destino, orden_titulos, activo, fecha_creacion, hora_creacion, fecha_actualizado) 
                VALUES(:punto, :nombre, :tipocontenido, :eslink, :puntodestino, :orden, true, :fecha_creacion, :hora_creacion, :fecha_actualizado);");
            $stmt->bindParam(':punto', $datos['punto'], PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datos['nombreTitulo'], PDO::PARAM_STR);
            $stmt->bindParam(':tipocontenido', $datos['tipoContenido'], PDO::PARAM_STR);
            $esLinkBool = ($datos['esLink'] == '' ? false : true); // Convertir a booleano
            $stmt->bindParam(':eslink', $esLinkBool, PDO::PARAM_BOOL);
            $puntoDestino = isset($datos['puntodestino']) ? $datos['puntodestino'] : null; // Manejar NULL
            $stmt->bindParam(':puntodestino', $puntoDestino, PDO::PARAM_INT);
            $stmt->bindParam(':orden', $datos['orden_titulos'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $datos['hora_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();


            $conn->commit();
            return ['res' => true, 'data' => "Punto guardado exitosamente"];
        } catch (PDOException $e) {
            $conn->rollBack();
            throw new Exception("Error al insertar el Titulo: " . $e->getMessage());
        }
    }


    public function QueryTitulosPuntoModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM titulos WHERE id_punto = :punto AND fk_titulos IS NULL order by orden_titulos asc");
            $stmt->bindParam(':punto', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al obtener los titulos del punto"];
        }
    }

    public function QueryTitulosMasPuntoModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT t.*, p.nombre_punto, p.id_punto  FROM titulos t inner join punto p on p.id_punto = t.id_punto  WHERE t.id_titulo  = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetch(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al obtener los datos del titulo"];
        }
    }


    // public function UpdateModel($id, $datos) {
    //     try {
    //         $conn = Conexion::Conexion();
    //         $stmt = $conn->prepare("
    //             UPDATE titulos 
    //             SET nombre_titulo = :nombre_titulo, tipo_contenido = :tipo_contenido, fecha_actualizado = :fecha_actualizado 
    //             WHERE id_titulo = :id AND activo = true");
    //         $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    //         $stmt->bindParam(':nombre_titulo', $datos['nombre_titulo'], PDO::PARAM_STR);
    //         $stmt->bindParam(':tipo_contenido', $datos['tipo_contenido'], PDO::PARAM_INT);
    //         $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
    //         $stmt->execute();
    //         if ($stmt->rowCount() == 0) {
    //             throw new Exception("No se encontró un título activo con ID $id");
    //         }
    //         return "Título con ID $id actualizado exitosamente";
    //     } catch (PDOException $e) {
    //         throw new Exception("Error al actualizar el título: " . $e->getMessage());
    //     }
    // }

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $conn->beginTransaction();

            // Validar si el nombre del título ya existe
            $stmt = $conn->prepare("
                SELECT COUNT(*) 
                FROM titulos 
                WHERE nombre_titulo = :nombre 
                  AND id_punto = :punto 
                  AND fk_titulos IS NULL 
                  AND id_titulo != :id");
            $stmt->bindParam(':nombre', $datos['nombreTitulo'], PDO::PARAM_STR);
            $stmt->bindParam(':punto', $datos['punto'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $conn->rollBack();
                return ['res' => false, 'data' => "Error: Ya existe otro Tema con el mismo nombre"];
            }

            // Proceder con la actualización si no se encuentra duplicado
            $stmt = $conn->prepare("
                UPDATE titulos 
                SET 
                    nombre_titulo = :nombreTitulo, 
                    tipo_contenido = :tipocontenido, 
                    link = :eslink, 
                    fecha_actualizado = :fecha_actualizado 
                WHERE id_titulo = :id");
            
            // Vincular los parámetros correctamente
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombreTitulo', $datos['nombreTitulo'], PDO::PARAM_STR);
            $stmt->bindParam(':tipocontenido', $datos['tipoContenido'], PDO::PARAM_STR);
            
            // Convertir el valor a booleano y vincularlo correctamente
            $esLinkBool = filter_var($datos['esLink'], FILTER_VALIDATE_BOOLEAN); // Mejor manejo de booleanos
            $stmt->bindParam(':eslink', $esLinkBool, PDO::PARAM_BOOL);
            
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                $conn->rollBack();
                return ['res' => false, 'data' => "Error al actualizar el Tema"];
            }

            $conn->commit();
            return ['res' => true, 'data' => "Tema actualizado exitosamente"];
        } catch (PDOException $e) {
            $conn->rollBack();
            return ['res' => false, 'data' => "Error al actualizar el Tema: " . $e->getMessage()];
        }
    }




    public function DeleteModel($id, $datos) {

        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                UPDATE titulos 
                SET activo = false ,
                fecha_actualizado = :fecha_actualizado
                WHERE id_titulo = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontro el titulo, intente mas tarde"];
            }
            return ['res' => true, 'data' => "Titulo desactivado"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al desactivar el titulo: " . $e->getMessage()];
        }
    }

    public function ActivateModel($id, $datos) {

        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("
                UPDATE titulos 
                SET activo = true ,
                fecha_actualizado = :fecha_actualizado
                WHERE id_titulo = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontro el titulo, intente mas tarde"];
            }
            return ['res' => true, 'data' => "Titulo activado"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al desactivar el titulo: " . $e->getMessage()];
        }
    }


    private function adjustOrderTitulos($conn, $newOrder, $currentOrder, $idPunto) {
        if ($newOrder < $currentOrder || $currentOrder == 0) {
            // Incrementar orden para aquellos registros con `fk_titulos` que sean `NULL` y `id_punto` igual a `$idPunto`
            $stmt = $conn->prepare("
                UPDATE titulos 
                SET orden_titulos = orden_titulos + 1 
                WHERE orden_titulos >= :new_order 
                  AND id_punto = :id_punto 
                  AND fk_titulos IS NULL 
                  AND activo = true
            ");
        }
        $stmt->bindParam(':new_order', $newOrder, PDO::PARAM_INT);
        $stmt->bindParam(':id_punto', $idPunto, PDO::PARAM_INT);
        $stmt->execute();
    }




}
