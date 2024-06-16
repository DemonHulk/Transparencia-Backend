<?php

require_once 'database/conexion.php';

class UsuarioModel {

    public function QueryAllUserArea() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT u.id_usuario, u.nombre, u.apellido1, u.apellido2, u.activo, a.nombre_area FROM usuario u JOIN area a ON u.id_area = a.id_area ORDER BY  u.id_usuario");
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

            // Verificar que el número de teléfono no este registrado en el sistema
            $checktell = $conn->prepare("SELECT COUNT(*) FROM usuario WHERE telefono = :telefono");
            $checktell->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $checktell->execute();
            $countTell = $checktell->fetchColumn();

            if ($countTell > 0) {
                return ['res' => false, 'data' => "El Número de Teléfono ya esta registrado en el sistema"];
            }
                
            $stmt = $conn->prepare("INSERT INTO usuario (nombre, apellido1, apellido2, telefono, correo, contrasenia, id_area, fecha_creacion, hora_creacion, fecha_actualizado) VALUES (:nombre, :apellido1, :apellido2, :telefono, :correo, :contrasenia, :id_area, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':apellido1', $datos['primerApellido'], PDO::PARAM_STR);
            // Manejar para que segundoApellido pueda estar vacío
            $segundoApellido = isset($datos['segundoApellido']) ? $datos['segundoApellido'] : '';
            $stmt->bindParam(':apellido2', $segundoApellido, PDO::PARAM_STR);
            
            $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
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

            // Verificar que el número de teléfono no este registrado en el sistema
            $checktell = $conn->prepare("SELECT COUNT(*) FROM usuario WHERE telefono = :telefono AND id_usuario != :id");
            $checktell->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $checktell->bindParam(':id', $id, PDO::PARAM_INT);
            $checktell->execute();
            $countTell = $checktell->fetchColumn();

            if ($countTell > 0) {
                return ['res' => false, 'data' => "El Número de Teléfono ya esta registrado por otro usuario"];
            }
    
            // Inicializar $set_password
            $set_password = '';
    
            // Verificar si se ha proporcionado una nueva contraseña
            if (isset($datos['password']) && !empty($datos['password'])) {
                // Encriptar la contraseña usando password_hash
                $contrasenia_encriptada = password_hash($datos['password'], PASSWORD_DEFAULT);
                // Actualizar el array $datos con la contraseña encriptada
                $datos['password'] = $contrasenia_encriptada;
                $set_password = ", contrasenia = :password";
            }
    
            $sql = "UPDATE usuario SET nombre = :nombre, apellido1 = :apellido1, apellido2 = :apellido2, telefono = :telefono, correo = :correo, id_area = :id_area, fecha_actualizado = :fecha_actualizado $set_password WHERE id_usuario = :id AND activo = TRUE";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':apellido1', $datos['primerApellido'], PDO::PARAM_STR);
            $stmt->bindParam(':apellido2', $datos['segundoApellido'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':correo', $datos['correo'], PDO::PARAM_STR);
            $stmt->bindParam(':id_area', $datos['id_area'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
    
            // Vincular la contraseña solo si se proporciona
            if (!empty($set_password)) {
                $stmt->bindParam(':password', $datos['password'], PDO::PARAM_STR);
            }
    
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
                    return ['res' => false, 'message' => 'Contraseña Inválida'];
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

}
