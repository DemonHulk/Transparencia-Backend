<?php

require_once 'database/conexion.php';

class UsuarioModel {
    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM usuario WHERE activo = true ORDER BY id_usuario");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los usuarios: " . $e->getMessage());
        }
    }

    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM usuario WHERE id_usuario = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el usuario con ID $id: " . $e->getMessage());
        }
    }

    public function InsertModel($datos) {
        try {
            // Encriptar la contraseña usando password_hash PASSWORD_DEFAULT para que use la mejor versión disponible de encriptación
            $contrasenia_encriptada = password_hash($datos['contrasenia'], PASSWORD_DEFAULT);
    
            // Actualizar el array $datos con la contraseña encriptada
            $datos['contrasenia'] = $contrasenia_encriptada;
    
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("INSERT INTO usuario (nombre, apellido1, apellido2, telefono, correo, contrasenia, id_area, activo, fecha_creacion, hora_creacion, fecha_actualizado) VALUES (:nombre, :apellido1, :apellido2, :telefono, :correo, :contrasenia, :id_area, :activo, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            $stmt->execute($datos);
            return "Usuario guardado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el usuario: " . $e->getMessage());
        }
    }

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE usuario SET nombre = :nombre, apellido1 = :apellido1, apellido2 = :apellido2, telefono = :telefono, correo = :correo, contrasenia = :contrasenia, id_area = :id_area, fecha_actualizado = :fecha_actualizado WHERE id_usuario = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':apellido1', $datos['apellido1'], PDO::PARAM_STR);
            $stmt->bindParam(':apellido2', $datos['apellido2'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':correo', $datos['correo'], PDO::PARAM_STR);
            $stmt->bindParam(':contrasenia', $datos['contrasenia'], PDO::PARAM_STR);
            $stmt->bindParam(':id_area', $datos['id_area'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un usuario activo con ID $id");
            }
            return "Usuario con ID $id actualizado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el usuario: " . $e->getMessage());
        }
    }

    public function DeleteModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE usuario SET activo = false WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró un usuario activo con ID $id");
            }
            return "Usuario con ID $id desactivado exitosamente";
        } catch (PDOException $e) {
            throw new Exception("Error al desactivar el usuario: " . $e->getMessage());
        }
    }

    public function verificarUserModel($datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT u.id_usuario, u.contrasenia, a.id_area FROM usuario u JOIN area a ON u.id_area = a.id_area WHERE u.correo = :correo AND u.activo = true");
            $stmt->bindParam(':correo', $datos['correo'], PDO::PARAM_STR);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si el correo se encuentra en el sistema
            if ($usuario) {
                // Verificar la contraseña con la de la bd utilizando password_verify
                if (password_verify($datos['contrasenia'], $usuario['contrasenia'])) {

                    //Credenciales válidas regresa el mensaje de inicio de sesion y envia los datos del usuario
                    return [ 
                        'res' => true,'message' => 'Inicio de Sesión Exitoso', 'user' => $usuario
                    ];
                } else {
                    // Credenciales incorrectas
                    return [
                        'res' => false,'message' => 'Contraseña Invalida'
                    ];
                }
            } else {
                // Usuario no encontrado
                return [
                    'res' => false, 'message' => 'Correo Invalido'
                ];
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
