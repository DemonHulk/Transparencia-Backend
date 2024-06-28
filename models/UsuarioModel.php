<?php

require_once 'database/conexion.php';

class UsuarioModel {

    public function QueryAllUserArea() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT u.id_usuario, u.correo, u.activo, a.nombre_area FROM usuario u JOIN area a ON u.id_area = a.id_area ORDER BY  u.id_usuario");
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al obtener todos los usuarios: " . $e->getMessage()];
        }
    }

    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT u.*, a.id_area, a.nombre_area FROM usuario u JOIN area a ON u.id_area = a.id_area WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al obtener los datos del usuario: " . $e->getMessage()];
        }
    }

    public function InsertModel($datos) {
        try {            
            // Encriptar la contraseña usando password_hash PASSWORD_DEFAULT para que use la mejor versión disponible de encriptación
            $contrasenia_encriptada = password_hash($datos['password'], PASSWORD_DEFAULT);
            
            // Actualizar el array $datos con la contraseña encriptada
            $datos['password'] = $contrasenia_encriptada;
            
            $conn = Conexion::Conexion();

             // Verificar si el correo ya está registrado
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM usuario WHERE LOWER(correo) = LOWER(:correo)");
            $checkStmt->bindParam(':correo', $datos['correo'], PDO::PARAM_STR);
            $checkStmt->execute();
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                return ['res' => false, 'data' => "El correo ya esta registrado en el sistema"];
            }
                
            $stmt = $conn->prepare("INSERT INTO usuario (correo, contrasenia, id_area, fecha_creacion, hora_creacion, fecha_actualizado) VALUES ( :correo, :contrasenia, :id_area, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            $stmt->bindParam(':correo', $datos['correo'], PDO::PARAM_STR);
            $stmt->bindParam(':contrasenia', $datos['password'], PDO::PARAM_STR);
            $stmt->bindParam(':id_area', $datos['id_area'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $datos['hora_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            
            $stmt->execute();
            return ['res' => true, 'data' => "Usuario insertado exitosamente"];
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el usuario: " . $e->getMessage());
        }
    }

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
    
            // Verificar si el nuevo correo ya está registrado por otro usuario
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM usuario WHERE LOWER(correo) = LOWER(:correo) AND id_usuario != :id");
            $checkStmt->bindParam(':correo', $datos['correo'], PDO::PARAM_STR);
            $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $checkStmt->execute();
            $count = $checkStmt->fetchColumn();
    
            if ($count > 0) {
                return ['res' => false, 'data' => "El correo ya está registrado por otro usuario"];
            }
   
    
            // Inicializar $set_password y $set_id_area
            $set_password = '';
            $set_id_area = '';
    
            // Verificar si se ha proporcionado una nueva contraseña
            if (isset($datos['password']) && !empty($datos['password'])) {
                // Encriptar la contraseña usando password_hash
                $contrasenia_encriptada = password_hash($datos['password'], PASSWORD_DEFAULT);
                // Actualizar el array $datos con la contraseña encriptada
                $datos['password'] = $contrasenia_encriptada;
                $set_password = ", contrasenia = :password";
            }
    
            // Verificar si se ha proporcionado un id_area
            if (isset($datos['id_area']) && !is_null($datos['id_area'])) {
                $set_id_area = ", id_area = :id_area";
            }
    
            $sql = "UPDATE usuario SET correo = :correo $set_id_area, fecha_actualizado = :fecha_actualizado $set_password WHERE id_usuario = :id AND activo = TRUE";
    
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':correo', $datos['correo'], PDO::PARAM_STR);
    
            // Vincular el id_area solo si se proporciona
            if (!empty($set_id_area)) {
                $stmt->bindParam(':id_area', $datos['id_area'], PDO::PARAM_INT);
            }
    
            // Vincular la contraseña solo si se proporciona
            if (!empty($set_password)) {
                $stmt->bindParam(':password', $datos['password'], PDO::PARAM_STR);
            }
    
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
    
            $stmt->execute();
    
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró al usuario");
            }
            return ['res' => true, 'data' => "Usuario actualizado exitosamente"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al actualizar el usuario: " . $e->getMessage()];
        }
    }
    
    

    public function DeleteModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE usuario SET activo = false, fecha_actualizado = :fecha_actualizado WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontró el usuario, intente mas tarde"];
            }
            return ['res' => true, 'data' => "Usuario desactivado"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al desactivar el usuario: " . $e->getMessage()];
        }
    }

    public function ActivateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE usuario SET activo = true, fecha_actualizado = :fecha_actualizado WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'data' => "No se encontró el usuario, intente mas tarde"];
            }
            return ['res' => true, 'data' => "Usuario activado"];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error al activar el usuario: " . $e->getMessage()];
        }
    }

    // Comprobar credenciales de usuario, verificar si se encuentra activo y enviarlos para guardar en el localStorage
    public function verificarUserModel($datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT u.id_usuario, u.contrasenia, u.activo, a.id_area, a.nombre_area FROM usuario u JOIN area a ON u.id_area = a.id_area WHERE u.correo = :correo");
            $stmt->bindParam(':correo', $datos['correo'], PDO::PARAM_STR);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
           
            if ($usuario) {
                
                if (!$usuario['activo']) {
                    return ['res' => false, 'message' => 'Cuenta suspendida'];
                }
                
                if (password_verify($datos['contrasenia'], $usuario['contrasenia'])) {
                    
                    unset($usuario['activo']); // Eliminamos los campos 'activo y contrasenia' antes de devolver los datos del usuario
                    unset($usuario['contrasenia']); 
                    return ['res' => true, 'message' => 'Inicio de Sesión Exitoso', 'user' => $usuario];
                } else {
                    return ['res' => false, 'message' => 'Credenciales Incorrectas'];
                }
            } else {
                return ['res' => false, 'message' => 'Correo no Registrado en el Sistema'];
            }
        } catch (PDOException $e) {
            // Manejo de excepciones
            throw new Exception("Error al iniciar sesión: " . $e->getMessage());
        }
    }
    


    /********************
        extraer los usuarios que tienen acceso a un area en especifica segun el id
    ********************/
    public function QueryAllUsuariosAccesoAreaModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * from usuario u where id_area  = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'data' => "Error los puntos de acceso con el $id: " . $e->getMessage()];
        }
    }

    // Funcion para verificar el correo en el sistema
    public function getUserByEmail($correo) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT id_usuario, correo FROM usuario WHERE correo = :correo");
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al buscar el usuario: " . $e->getMessage());
        }
    }
    
    // Funcion para actualizar la contraseña por una ramdom
    public function updatePassword($idUsuario, $hashedContrasenia) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE usuario SET contrasenia = :contrasenia WHERE id_usuario = :idUsuario");
            $stmt->bindParam(':contrasenia', $hashedContrasenia, PDO::PARAM_STR);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la contraseña: " . $e->getMessage());
        }
    }
}
