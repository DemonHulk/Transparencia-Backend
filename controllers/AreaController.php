<?php

require_once 'models/AreaModel.php';
require_once 'models/ValidacionesModel.php';
require_once 'middleware/ExceptionHandler.php';
require_once 'models/EncryptModel.php';

class AreaController {

    private $areaModel;
    private $validacionesModel;
    private $EncryptModel;

    public function __construct() {
        $this->areaModel = new AreaModel();
        $this->validacionesModel = new ValidacionesModel();
        $this->EncryptModel = new EncryptModel();
    }

    /**
     * Obtiene todas las áreas 
     */
    public function QueryAllController() {
        try {
            $resultado = $this->areaModel->QueryAllModel();
            $response =  json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);

            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Obtiene todas las áreas activas.
     */
    public function QueryActController() {
        try {
            $resultado = $this->areaModel->QueryActModel();
            $response =  json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);

            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    /**
     * Obtiene una área activa por ID.
     * @param int $id ID del área.
     */
    public function QueryOneController($id) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->areaModel->QueryOneModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);

            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;

        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Inserta una nueva área.
     * @param array $datos Datos del área.
     */
    public function InsertController($datos) {
        // Obtener los datos encriptados
        $encryptedData = $datos['data'];
    
        try {
            // Mandamos los datos encriptados a la funcion para desencriptarlos
            $decryptedData = $this->EncryptModel->decryptData($encryptedData);
        } catch (Exception $e) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
            return;
        }

        // Validar nombre del área
        if (!$this->validacionesModel->ValidarTexto($decryptedData['nombreArea'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre del área no es válido."]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
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
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "Las fechas u horas no son válidas."]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
            return;
        }

        try {
            // Insertar área
            $resultado = $this->areaModel->InsertModel($decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Actualiza un área existente.
     * @param int $id ID del área.
     * @param array $datos Datos del área.
     */
    public function UpdateController($id, $datos) {
        // Obtener los datos encriptados
        $encryptedData = $datos['data'];
    
        try {
            // Mandamos los datos encriptados a la funcion para desencriptarlos
            $decryptedData = $this->EncryptModel->decryptData($encryptedData);
            $decryptedID = $this->EncryptModel->decryptData($id);
        } catch (Exception $e) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
            return;
        }

        // Validar nombre del área si está presente
        if (isset($decryptedData['nombreArea']) && !$this->validacionesModel->ValidarTexto($decryptedData['nombreArea'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre del área no es válido."]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
            return;
        }

        // Asignar fecha actualizada
        $decryptedData['fecha_actualizado'] = date('Y-m-d');

        // Validar fecha actualizada
        if (!$this->validacionesModel->ValidarFecha($decryptedData['fecha_actualizado'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La fecha actualizada no es válida."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }

        try {
            // Actualizar área
            $resultado = $this->areaModel->UpdateModel($decryptedID, $decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Desactiva un área.
     * @param int $id ID del área.
     */
    public function DeleteController($id) {
        // Asignar fecha actualizada
        $datos['fecha_actualizado'] = date('Y-m-d');

        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            // Desactivar área
            $resultado = $this->areaModel->DeleteModel($decryptedID, $datos);
            $response = json_encode(['estado' => 200, 'resultado' =>['res' => true, 'data' => $resultado]]);
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
            // Activar área
            $resultado = $this->areaModel->ActivateModel($decryptedID, $datos);
            $response = json_encode(['estado' => 200, 'resultado' =>['res' => true, 'data' => $resultado]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

}
