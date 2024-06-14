<?php

require_once 'models/UsuarioModel.php';
require_once 'models/ValidacionesModel.php';
require_once 'middleware/ExceptionHandler.php';

class UsuarioController {

    private $usuarioModel;
    private $validacionesModel;
    
    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->validacionesModel = new ValidacionesModel();
    }

    public function QueryAllController() {
        try {
            $resultado = $this->usuarioModel->QueryAllUserArea();
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryOneController($id) {
        try {
            $resultado = $this->usuarioModel->QueryOneModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function InsertController($datos) {
        // Validar el nombre del usuario
        if (!$this->validacionesModel->ValidarTexto($datos['nombre'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre del usuario no es válido."]]);
            return;
        }
        
        // Validar el primer apellido del usuario
        if (!$this->validacionesModel->ValidarTexto($datos['primerApellido'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El primer apellido no es válido."]]);
            return;
        }
        
        // Validar el segundo apellido del usuario en caso de que exista
        if (isset($datos['segundoApellido']) && $datos['segundoApellido'] !== '' && !$this->validacionesModel->ValidarTexto($datos['segundoApellido'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El segundo apellido no es válido."]]);
            return;
        }
    
        // Validar el correo electrónico
        if (!$this->validacionesModel->ValidarCorreo($datos['correo'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El correo electrónico no es válido."]]);
            return;
        }

        if (isset($datos['password']) && !$this->validacionesModel->ValidarPassword($datos['password'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La contraseña no es válida."]]);
            return;
        }
    
        // Asignar fechas y horas
        $datos['fecha_creacion'] = date('Y-m-d');
        $datos['hora_creacion'] = date('H:i:s');
        $datos['fecha_actualizado'] = date('Y-m-d');
    
        // Validar fechas y horas
        if (!$this->validacionesModel->ValidarFecha($datos['fecha_creacion']) ||
            !$this->validacionesModel->ValidarHora($datos['hora_creacion']) ||
            !$this->validacionesModel->ValidarFecha($datos['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "Las fechas u horas no son válidas."]]);
            return;
        }
    
        try {
            // Insertar usuario
            $resultado = $this->usuarioModel->InsertModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
    

    public function UpdateController($id, $datos) {
        // Validar cada campo sólo si está presente en $datos
        if (isset($datos['nombre']) && !$this->validacionesModel->ValidarTexto($datos['nombre'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre del usuario no es válido."]]);
            return;
        }
    
        if (isset($datos['primerApellido']) && !$this->validacionesModel->ValidarTexto($datos['primerApellido'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El primer apellido no es válido."]]);
            return;
        }
    
        if (isset($datos['segundoApellido']) && !$this->validacionesModel->ValidarTexto($datos['segundoApellido'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El segundo apellido no es válido."]]);
            return;
        }
    
        if (isset($datos['correo']) && !$this->validacionesModel->ValidarCorreo($datos['correo'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El correo electrónico no es válido."]]);
            return;
        }

        if (isset($datos['password']) && !$this->validacionesModel->ValidarPassword($datos['password'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La contraseña no es válida."]]);
            return;
        }
    
        // Asignar la fecha actualizada
        $datos['fecha_creacion'] = date('Y-m-d');
        $datos['hora_creacion'] = date('H:i:s');
        $datos['fecha_actualizado'] = date('Y-m-d');

        // Validar fechas y horas
        if (!$this->validacionesModel->ValidarFecha($datos['fecha_creacion']) ||
            !$this->validacionesModel->ValidarHora($datos['hora_creacion']) ||
            !$this->validacionesModel->ValidarFecha($datos['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "Las fechas u horas no son válidas."]]);
            return;
        }
    
        try {
            // Actualizar usuario
            $resultado = $this->usuarioModel->UpdateModel($id, $datos);
            echo json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
    


    public function DeleteController($id) {
        try {
            $resultado = $this->usuarioModel->DeleteModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function ActivateController($id) {
        try {
            $resultado = $this->usuarioModel->ActivateModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Obtiene el id de un area en especifico y extrae todos sus usuarios 
     * que tienen activa la area
     */
    public function QueryAllUsuariosAccesoAreaController($id) {
        try {
            $resultado = $this->usuarioModel->QueryAllUsuariosAccesoAreaModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    // Recibe las credenciales del usuario, verifica que sean correctas y regresa los datos necesarios para la sesión 
    public function VerificarUserController($datos) {
        try {
            $resultado = $this->usuarioModel->verificarUserModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

}
