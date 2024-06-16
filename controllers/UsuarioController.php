<?php

require_once 'models/UsuarioModel.php';
require_once 'models/ValidacionesModel.php';
require_once 'middleware/ExceptionHandler.php';
require_once 'models/EncryptModel.php';

class UsuarioController {

    private $usuarioModel;
    private $validacionesModel;
    private $EncryptModel;
    
    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->validacionesModel = new ValidacionesModel();
        $this->EncryptModel = new EncryptModel();
    }

    public function QueryAllController() {
        try {
            $resultado = $this->usuarioModel->QueryAllUserArea();
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;

        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryOneController($id) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->usuarioModel->QueryOneModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;

        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }



    public function InsertController($datos) {
        // Obtener los datos encriptados
        $encryptedData = $datos['data'];
    
        try {
            // Mandamos los datos encriptados a la funcion para desencriptarlos
            $decryptedData = $this->EncryptModel->decryptData($encryptedData);
        } catch (Exception $e) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
            return;
        }
    
        // Validar los datos desencriptados
        if (!isset($decryptedData['nombre']) || !$this->validacionesModel->ValidarTexto($decryptedData['nombre'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre del usuario no es válido."]]);
            return;
        }
    
        if (!isset($decryptedData['primerApellido']) || !$this->validacionesModel->ValidarTexto($decryptedData['primerApellido'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El primer apellido no es válido."]]);
            return;
        }
    
        if (isset($decryptedData['segundoApellido']) && $decryptedData['segundoApellido'] !== '' && !$this->validacionesModel->ValidarTexto($decryptedData['segundoApellido'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El segundo apellido no es válido."]]);
            return;
        }
    
        if (!isset($decryptedData['correo']) || !$this->validacionesModel->ValidarCorreo($decryptedData['correo'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El correo electrónico no es válido."]]);
            return;
        }
    
        if (isset($decryptedData['password']) && !$this->validacionesModel->ValidarPassword($decryptedData['password'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La contraseña no es válida."]]);
            return;
        }
    
        // Asignar fechas y horas
        $decryptedData['fecha_creacion'] = date('Y-m-d');
        $decryptedData['hora_creacion'] = date('H:i:s');
        $decryptedData['fecha_actualizado'] = date('Y-m-d');
    
        // Validar fechas y horas
        if (!$this->validacionesModel->ValidarFecha($decryptedData['fecha_creacion']) ||
            !$this->validacionesModel->ValidarHora($decryptedData['hora_creacion']) ||
            !$this->validacionesModel->ValidarFecha($decryptedData['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "Las fechas u horas no son válidas."]]);
            return;
        }
    
        try {
            // Insertar usuario
            $resultado = $this->usuarioModel->InsertModel($decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' =>$resultado]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    public function UpdateController($id, $datos) {

        // Obtener los datos encriptados
        $encryptedData = $datos['data'];

        try {
            // Mandamos los datos encriptados a la funcion para desencriptarlos
            $decryptedData = $this->EncryptModel->decryptData($encryptedData);
            $decryptedID = $this->EncryptModel->decryptData($id);
        } catch (Exception $e) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
            return;
        }

        // Validar cada campo sólo si está presente en $datos
        if (isset($decryptedData['nombre']) && !$this->validacionesModel->ValidarTexto($decryptedData['nombre'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre del usuario no es válido."]]);
            return;
        }
    
        if (isset($decryptedData['primerApellido']) && !$this->validacionesModel->ValidarTexto($decryptedData['primerApellido'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El primer apellido no es válido."]]);
            return;
        }
    
        if (isset($decryptedData['segundoApellido']) && !$this->validacionesModel->ValidarTexto($decryptedData['segundoApellido'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El segundo apellido no es válido."]]);
            return;
        }
    
        if (isset($decryptedData['correo']) && !$this->validacionesModel->ValidarCorreo($decryptedData['correo'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El correo electrónico no es válido."]]);
            return;
        }

        if (isset($decryptedData['password']) && !$this->validacionesModel->ValidarPassword($decryptedData['password'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La contraseña no es válida."]]);
            return;
        }
    
        // Asignar la fecha actualizada
        $decryptedData['fecha_creacion'] = date('Y-m-d');
        $decryptedData['hora_creacion'] = date('H:i:s');
        $decryptedData['fecha_actualizado'] = date('Y-m-d');

        // Validar fechas y horas
        if (!$this->validacionesModel->ValidarFecha($decryptedData['fecha_creacion']) ||
            !$this->validacionesModel->ValidarHora($decryptedData['hora_creacion']) ||
            !$this->validacionesModel->ValidarFecha($decryptedData['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "Las fechas u horas no son válidas."]]);
            return;
        }
    
        try {
            // Actualizar usuario
            $resultado = $this->usuarioModel->UpdateModel($decryptedID, $decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' =>$resultado]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
    


    public function DeleteController($id) {
        // Asignar fecha actualizada
        $datos['fecha_actualizado'] = date('Y-m-d');

        // Validar fecha actualizada
        if (!$this->validacionesModel->ValidarFecha($datos['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La fecha actualizada no es válida."]]);
            return;
        }
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            // Actualizamos el estado
            $resultado = $this->usuarioModel->DeleteModel($decryptedID, $datos);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function ActivateController($id) {
        // Asignar fecha actualizada
        $datos['fecha_actualizado'] = date('Y-m-d');

        // Validar fecha actualizada
        if (!$this->validacionesModel->ValidarFecha($datos['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La fecha actualizada no es válida."]]);
            return;
        }

        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->usuarioModel->ActivateModel($decryptedID, $datos);
             // Actualizamos el estado
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
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
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->usuarioModel->QueryAllUsuariosAccesoAreaModel($decryptedID);
            $response =  json_encode(['estado' => 200, 'resultado' => $resultado]);

            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
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
