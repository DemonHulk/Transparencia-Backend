<?php

require_once 'database/conexion.php';

class TitulosModel {

    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM titulos WHERE activo = true ORDER BY orden_titulos");
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
                return ['res' => false, 'data' => "Ya existe el tema con ese nombre"];
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

                // Crear la carpeta si no existe
                $dir = 'C:/xampp/htdocs/Transparencia-Backend/assets/documents/' . $datos['nombreTitulo'];
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                $conn->commit();
                return ['res' => true, 'data' => "Tema guardado exitosamente"];
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
                return ['res' => false, 'data' => "Error al obtener los Tema del punto"];
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
                return ['res' => false, 'data' => "Error al obtener los datos del Tema"];
            }
        }


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

            // Obtener el nombre actual del título
                $stmt = $conn->prepare("SELECT nombre_titulo FROM titulos WHERE id_titulo = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $currentNombre = $stmt->fetchColumn();

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
                $esLinkBool = filter_var($datos['esLink'], FILTER_VALIDATE_BOOLEAN);
                $stmt->bindParam(':eslink', $esLinkBool, PDO::PARAM_BOOL);

                $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->rowCount() == 0) {
                    $conn->rollBack();
                    return ['res' => false, 'data' => "Error al actualizar el Tema"];
                }

            // Actualizar el nombre de la carpeta si el nombre del título ha cambiado
                if ($currentNombre !== $datos['nombreTitulo']) {
                 // Definir la ruta base
                    $baseDir = __DIR__ . '/../assets/documents/';

                    // Construir las rutas completas
                    $currentDir = $baseDir . $currentNombre;
                    $newDir = $baseDir . $datos['nombreTitulo'];

                    if (file_exists($currentDir)) {
                        rename($currentDir, $newDir);
                    } else {
                        if (!file_exists($newDir)) {
                            mkdir($newDir, 0777, true);
                        }
                    }


                // Actualizar las rutas de los documentos en la tabla contenido_dinamico
                    $stmt = $conn->prepare("
                        UPDATE contenido_dinamico 
                        SET ruta_documento = REPLACE(ruta_documento, :currentPath, :newPath)
                        WHERE id_titulo = :id");
                    $currentPath = 'assets/documents/' . $currentNombre;
                    $newPath = 'assets/documents/' . $datos['nombreTitulo'];
                    $stmt->bindParam(':currentPath', $currentPath, PDO::PARAM_STR);
                    $stmt->bindParam(':newPath', $newPath, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
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
                    return ['res' => false, 'data' => "No se encontro el Tema, intente mas tarde"];
                }
                return ['res' => true, 'data' => "Tema desactivado"];
            } catch (PDOException $e) {
                return ['res' => false, 'data' => "Error al desactivar el Tema: " . $e->getMessage()];
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
                    return ['res' => false, 'data' => "No se encontro el Tema, intente mas tarde"];
                }
                return ['res' => true, 'data' => "Tema activado"];
            } catch (PDOException $e) {
                return ['res' => false, 'data' => "Error al desactivar el Tema: " . $e->getMessage()];
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
                    ");
            }
            $stmt->bindParam(':new_order', $newOrder, PDO::PARAM_INT);
            $stmt->bindParam(':id_punto', $idPunto, PDO::PARAM_INT);
            $stmt->execute();
        }

        public function QueryAllModelByPunto($idPunto) {
            try {
                $conn = Conexion::Conexion();
                $stmt = $conn->prepare("SELECT * FROM titulos WHERE activo = true and id_punto = $idPunto ORDER BY orden_titulos");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Error al obtener todos los títulos: " . $e->getMessage());
            }
        }

        public function getContenidoDinamico($id_titulo) {
            try {
                $conn = Conexion::Conexion();
                $stmt = $conn->prepare("
                    SELECT ce.*, u.correo, a.nombre_area  FROM contenido_dinamico ce  
                    LEFT JOIN usuario u ON u.id_usuario = ce.id_usuario 
                    LEFT JOIN area a ON a.id_area  = u.id_area 
                    WHERE ce.id_titulo = :id_titulo and ce.activo = true ORDER BY orden");
                $stmt->bindParam(':id_titulo', $id_titulo, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Error al obtener contenido dinámico: " . $e->getMessage());
            }
        }

        public function getContenidoEstatico($id_titulo) {
            try {
                $conn = Conexion::Conexion();
                $stmt = $conn->prepare("SELECT ce.*, u.correo, a.nombre_area  FROM contenido_estatico ce 
                    LEFT JOIN usuario u ON u.id_usuario = ce.id_usuario 
                    LEFT JOIN area a ON a.id_area  = u.id_area 
                    WHERE ce.id_titulo = :id_titulo and ce.activo = true ORDER BY orden");
                $stmt->bindParam(':id_titulo', $id_titulo, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Error al obtener contenido estático: " . $e->getMessage());
            }
        }

        private function construirJerarquia($titulos) {
            $jerarquia = [];
            $titulosIndex = [];

            foreach ($titulos as $titulo) {
                $titulo['contenido'] = $this->obtenerContenido($titulo['id_titulo'], $titulo['tipo_contenido']);
                $titulosIndex[$titulo['id_titulo']] = $titulo;
            }

            foreach ($titulos as $titulo) {
                if ($titulo['fk_titulos'] == null) {
                    $jerarquia[] = &$titulosIndex[$titulo['id_titulo']];
                } else {
                    $titulosIndex[$titulo['fk_titulos']]['subtitulos'][] = &$titulosIndex[$titulo['id_titulo']];
                }
            }
            return $jerarquia;
        }

        private function obtenerContenido($id_titulo, $tipo_contenido) {
            if ($tipo_contenido == 2) {
                return $this->getContenidoDinamico($id_titulo);
            } else if ($tipo_contenido == 1) {
                return $this->getContenidoEstatico($id_titulo);
            } else {
                return null;
            }
        }

        public function mostrarJerarquia($idPunto) {
            try {
                $titulos = $this->QueryAllModelByPunto($idPunto);
                $jerarquia = $this->construirJerarquia($titulos);
                return json_encode($jerarquia, JSON_PRETTY_PRINT);
            } catch (Exception $e) {
                return json_encode(['error' => "Error: " . $e->getMessage()]);
            }
        }



        public function QueryAllSubtitulosByTitulo($idTitulo) {
            try {
                $conn = Conexion::Conexion();
                $stmt = $conn->prepare("
                    WITH RECURSIVE jerarquia_titulos AS (
                -- Selección inicial del título principal y sus subtitulos directos
                SELECT
                id_titulo,
                id_punto,
                fk_titulos,
                nombre_titulo,
                tipo_contenido,
                link,
                punto_destino,
                orden_titulos,
                activo,
                fecha_creacion,
                hora_creacion,
                fecha_actualizado
                FROM
                titulos
                WHERE
                    id_titulo = :id_titulo_principal -- ID del título principal padre

                    UNION ALL

                -- Selección recursiva de los subtitulos
                SELECT
                t.id_titulo,
                t.id_punto,
                t.fk_titulos,
                t.nombre_titulo,
                t.tipo_contenido,
                t.link,
                t.punto_destino,
                t.orden_titulos,
                t.activo,
                t.fecha_creacion,
                t.hora_creacion,
                t.fecha_actualizado
                FROM
                titulos t
                INNER JOIN
                    jerarquia_titulos jt ON t.fk_titulos = jt.id_titulo -- Relación con el título padre
                    )
                    SELECT
                *
                    FROM
                    jerarquia_titulos
                    ORDER BY
                    orden_titulos
                    ");

                $stmt->bindParam(':id_titulo_principal', $idTitulo, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Error al obtener todos los subtitulos por título: " . $e->getMessage());
            }
        }

        public function getContenidoDinamicoSubtitulo($id_titulo) {
            try {
                $conn = Conexion::Conexion();
                $stmt = $conn->prepare("
                    SELECT ce.*, u.correo, a.nombre_area, t.trimestre  FROM contenido_dinamico ce  
                    LEFT JOIN usuario u ON u.id_usuario = ce.id_usuario 
                    LEFT JOIN area a ON a.id_area  = u.id_area
                    LEFT JOIN trimestre t  ON t.id_trimestre  = ce.id_trimestre 
                    WHERE ce.id_titulo = :id_titulo ORDER BY orden");
                $stmt->bindParam(':id_titulo', $id_titulo, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Error al obtener contenido dinámico: " . $e->getMessage());
            }
        }

        public function getContenidoEstaticoSubtitulo($id_titulo) {
            try {
                $conn = Conexion::Conexion();
                $stmt = $conn->prepare("SELECT ce.*, u.correo, a.nombre_area  FROM contenido_estatico ce 
                    LEFT JOIN usuario u ON u.id_usuario = ce.id_usuario 
                    LEFT JOIN area a ON a.id_area  = u.id_area 
                    WHERE ce.id_titulo = :id_titulo ORDER BY orden");
                $stmt->bindParam(':id_titulo', $id_titulo, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Error al obtener contenido estático: " . $e->getMessage());
            }
        }

        private function obtenerContenidoSubtitulos($id_titulo, $tipo_contenido) {
            if ($tipo_contenido == 2) {
                return $this->getContenidoDinamicoSubtitulo($id_titulo);
            } else if ($tipo_contenido == 1) {
                return $this->getContenidoEstaticoSubtitulo($id_titulo);
            } else {
                return null;
            }
        }

        private function construirJerarquiaSubtitulos($titulos) {
            $jerarquia = [];
            $titulosIndex = [];

            foreach ($titulos as $titulo) {
                $titulo['contenido'] = $this->obtenerContenidoSubtitulos($titulo['id_titulo'], $titulo['tipo_contenido']);
                $titulosIndex[$titulo['id_titulo']] = $titulo;
            }

            foreach ($titulos as $titulo) {
                if ($titulo['fk_titulos'] == null) {
                    $jerarquia[] = &$titulosIndex[$titulo['id_titulo']];
                } else {
                    $titulosIndex[$titulo['fk_titulos']]['subtitulos'][] = &$titulosIndex[$titulo['id_titulo']];
                }
            }
            return $jerarquia;
        }

        public function mostrarJerarquiaSubtitulos($idTitulo) {
            try {
                $titulos = $this->QueryAllSubtitulosByTitulo($idTitulo);
                $jerarquias = $this->construirJerarquiaSubtitulos($titulos);
                return json_encode($jerarquias, JSON_PRETTY_PRINT);
            } catch (Exception $e) {
                return json_encode(['error' => "Error: " . $e->getMessage()]);
            }
        }

        public function InsertModelSubtema($datos) {
            try {
                $conn = Conexion::Conexion();
                $conn->beginTransaction();

        // Validar si el nombre del título ya existe
                $stmt = $conn->prepare("SELECT COUNT(*) FROM titulos WHERE nombre_titulo = :nombre AND id_punto = :punto AND fk_titulos = :titulo");
                $stmt->bindParam(':nombre', $datos['nombreTitulo'], PDO::PARAM_STR);
                $stmt->bindParam(':punto', $datos['punto'], PDO::PARAM_INT);
                $stmt->bindParam(':titulo', $datos['titulo'], PDO::PARAM_INT);

                $stmt->execute();
                $existingCount = $stmt->fetchColumn();

                if ($existingCount > 0) {
                    $conn->rollBack();
                    return ['res' => false, 'data' => "Ya existe el subtema con ese nombre"];
                }

                if (!isset($datos['orden_titulos']) || empty($datos['orden_titulos'])) {
            // Obtener el último orden existente sin importar el estado
                    $stmt = $conn->prepare("SELECT MAX(orden_titulos) FROM titulos WHERE id_punto = :punto AND fk_titulos = :titulo");
                    $stmt->bindParam(':punto', $datos['punto'], PDO::PARAM_INT);
                    $stmt->bindParam(':titulo', $datos['titulo'], PDO::PARAM_INT);

                    $stmt->execute();
                    $lastOrder = $stmt->fetchColumn();
                    $datos['orden_titulos'] = $lastOrder + 1;
                }

        // Ajustar los valores de orden_titulos
                $this->adjustOrderTitulos($conn, $datos['orden_titulos'], 0, $datos['punto']);

        // Insertar el punto en la tabla punto
                $stmt = $conn->prepare("
                    INSERT INTO titulos (id_punto, fk_titulos, nombre_titulo, tipo_contenido, punto_destino, orden_titulos, activo, fecha_creacion, hora_creacion, fecha_actualizado) 
                    VALUES(:punto, :titulo, :nombre, :tipocontenido, :puntodestino, :orden, true, :fecha_creacion, :hora_creacion, :fecha_actualizado);");
                $stmt->bindParam(':punto', $datos['punto'], PDO::PARAM_INT);
                $stmt->bindParam(':titulo', $datos['titulo'], PDO::PARAM_INT);
                $stmt->bindParam(':nombre', $datos['nombreTitulo'], PDO::PARAM_STR);
                $stmt->bindParam(':tipocontenido', $datos['tipoContenido'], PDO::PARAM_STR);
        $puntoDestino = isset($datos['puntodestino']) ? $datos['puntodestino'] : null; // Manejar NULL
        $stmt->bindParam(':puntodestino', $puntoDestino, PDO::PARAM_INT);
        $stmt->bindParam(':orden', $datos['orden_titulos'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
        $stmt->bindParam(':hora_creacion', $datos['hora_creacion'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
        $stmt->execute();

        // Obtener la ruta completa de todos los padres
        $fullPath = $this->getFullPath($conn, $datos['titulo'], $datos['nombreTitulo']);

        // Crear la carpeta si no existe
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        $conn->commit();
        return ['res' => true, 'data' => "Subtema guardado exitosamente"];
    } catch (PDOException $e) {
        $conn->rollBack();
        throw new Exception("Error al insertar el Subtema: " . $e->getMessage());
    }
}




public function obtenerTituloPrincipal($idTitulo) {
    try {
        $conn = Conexion::Conexion();
        $stmt = $conn->prepare("
            WITH RECURSIVE titulo_ancestros AS (
                SELECT id_titulo, fk_titulos, nombre_titulo
                FROM titulos
                WHERE id_titulo = :idTitulo

                UNION ALL

                SELECT t.id_titulo, t.fk_titulos, t.nombre_titulo
                FROM titulos t
                JOIN titulo_ancestros a ON t.id_titulo = a.fk_titulos
                )
            SELECT id_titulo, fk_titulos, nombre_titulo
            FROM titulo_ancestros
            WHERE fk_titulos IS NULL;
            ");
        $stmt->bindParam(':idTitulo', $idTitulo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Error al obtener el título principal: " . $e->getMessage());
    }
}

public function UpdateModelSubtema($id, $datos) {
    try {
        $conn = Conexion::Conexion();
        $conn->beginTransaction();

        // Validar si el nombre del título ya existe
        $stmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM titulos 
            WHERE nombre_titulo = :nombre 
            AND id_punto = :punto 
            AND fk_titulos = :titulo
            AND id_titulo != :id");
        $stmt->bindParam(':nombre', $datos['nombreTitulo'], PDO::PARAM_STR);
        $stmt->bindParam(':punto', $datos['punto'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $datos['titulo'], PDO::PARAM_INT);

        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $conn->rollBack();
            return ['res' => false, 'data' => "Error: Ya existe otro Tema con el mismo nombre"];
        }

        // Obtener el nombre actual del título y la jerarquía completa
        $stmt = $conn->prepare("SELECT nombre_titulo, fk_titulos FROM titulos WHERE id_titulo = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $currentTitulo = $stmt->fetch(PDO::FETCH_ASSOC);

        // Proceder con la actualización si no se encuentra duplicado
        $stmt = $conn->prepare("
            UPDATE titulos 
            SET 
            nombre_titulo = :nombreTitulo, 
            tipo_contenido = :tipocontenido, 
            fecha_actualizado = :fecha_actualizado 
            WHERE id_titulo = :id");

        // Vincular los parámetros correctamente
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombreTitulo', $datos['nombreTitulo'], PDO::PARAM_STR);
        $stmt->bindParam(':tipocontenido', $datos['tipoContenido'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $conn->rollBack();
            return ['res' => false, 'data' => "Error al actualizar el Tema"];
        }

        // Actualizar el nombre de la carpeta si el nombre del título ha cambiado
        if ($currentTitulo['nombre_titulo'] !== $datos['nombreTitulo']) {
            // Obtener la ruta completa de todos los padres para el nombre actual
            $currentFullPath = $this->getFullPath($conn, $currentTitulo['fk_titulos'], $currentTitulo['nombre_titulo']);

            // Obtener la ruta completa de todos los padres para el nuevo nombre
            $newFullPath = $this->getFullPath($conn, $currentTitulo['fk_titulos'], $datos['nombreTitulo']);

            if (file_exists($currentFullPath)) {
                rename($currentFullPath, $newFullPath);
            } else {
                if (!file_exists($newFullPath)) {
                    mkdir($newFullPath, 0777, true);
                }
            }
        }

        $conn->commit();
        return ['res' => true, 'data' => "Tema actualizado exitosamente"];
    } catch (PDOException $e) {
        $conn->rollBack();
        return ['res' => false, 'data' => "Error al actualizar el Tema: " . $e->getMessage()];
    }
}

// Función recursiva para obtener la ruta completa
private function getFullPath($conn, $tituloId, $subtitulo) {
    $path = [];
    while ($tituloId != null) {
        $stmt = $conn->prepare("SELECT nombre_titulo, fk_titulos FROM titulos WHERE id_titulo = :id");
        $stmt->bindParam(':id', $tituloId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        array_unshift($path, $result['nombre_titulo']);
        $tituloId = $result['fk_titulos'];
    }
    $path = implode('/', $path);
    return __DIR__ . '/../assets/documents/' . $path . '/' . $subtitulo;
}


}
