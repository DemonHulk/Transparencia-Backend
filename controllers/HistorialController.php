<?php

require_once 'models/HistorialModel.php';
require_once 'middleware/ExceptionHandler.php';
require_once 'models/EncryptModel.php';

class HistorialController {

    private $HistorialModel;
    private $EncryptModel;

    public function __construct() {
        $this->HistorialModel = new HistorialModel();
        $this->EncryptModel = new EncryptModel();
    }

    public function QueryAllVistosController() {
        try {
            $resultado = $this->HistorialModel->QueryAllVistosModel();
            $response =  json_encode(['estado' => 200, 'resultado' => $resultado]);
            
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;

        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryAllNoVistosController() {
        try {
            $resultado = $this->HistorialModel->QueryAllNoVistosModel();
            $response =  json_encode(['estado' => 200, 'resultado' => $resultado]);
            
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;

        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    /**
     *  Cambiamos el estado del campo "visto"
     * @param int $id ID del registro
     */
    public function verController($id) {
        // Asignar fecha actualizada
        $datos['fecha_actualizado'] = date('Y-m-d');

        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            // Desactivar área
            $resultado = $this->HistorialModel->verModel($decryptedID, $datos);
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
     * Desactiva un registro
     * @param int $id ID del Registro.
     */
    public function DeleteController($id) {
        // Asignar fecha actualizada
        $datos['fecha_actualizado'] = date('Y-m-d');

        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            // Desactivar área
            $resultado = $this->HistorialModel->DeleteModel($decryptedID, $datos);
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
     * Activa un registro
     * @param int $id ID del Registro.
     */
    public function ActivateController($id) {
        // Asignar fecha actualizada
        $datos['fecha_actualizado'] = date('Y-m-d');

        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            // Activar Tema
            $resultado = $this->HistorialModel->ActivateModel($decryptedID, $datos);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

}
