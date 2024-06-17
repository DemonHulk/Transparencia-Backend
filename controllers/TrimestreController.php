<?php

require_once 'models/TrimestreModel.php';
require_once 'middleware/ExceptionHandler.php';
require_once 'models/EncryptModel.php';
require_once 'models/ValidacionesModel.php';

class TrimestreController {

    private $TrimestreModel;
    private $validacionesModel;
    private $EncryptModel;

    public function __construct() {
        $this->TrimestreModel = new TrimestreModel();
        $this->validacionesModel = new ValidacionesModel();
        $this->EncryptModel = new EncryptModel();
    }

    public function QueryAllController() {
        try {
            $resultado = $this->TrimestreModel->QueryAllModel();
            $response =  json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);
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
            $resultado = $this->TrimestreModel->QueryOneModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
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
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
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
        $decryptedData['activo'] = true;
        try {
            // Llamamos al método InsertModel y guardamos el resultado
            list($success, $message) = $this->TrimestreModel->InsertModel($decryptedData);
            
            // Si la inserción fue exitosa, preparamos una respuesta con estado 200
            if ($success) {
                $response = json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $message]]);
            } else {
                // Si hubo un error (Trimestre duplicado), preparamos una respuesta con estado 400 o el que sea adecuado
                $response = json_encode(['estado' => 400, 'resultado' => ['res' => false, 'data' => $message]]);
            }

            // Encriptamos la respuesta
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function UpdateController($id, $datos) {
        try {
            $resultado = $this->TrimestreModel->UpdateModel($id, $datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function DeleteController($id) {
        try {
            $resultado = $this->TrimestreModel->DeleteModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

}
