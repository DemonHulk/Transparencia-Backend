<?php

require_once 'models/EjercicioModel.php';
require_once 'middleware/ExceptionHandler.php';
require_once 'models/ValidacionesModel.php';
require_once 'models/EncryptModel.php';



class EjercicioController {

    private $EjercicioModel;
    private $validacionesModel;
    private $EncryptModel;

    public function __construct() {
        $this->EjercicioModel = new EjercicioModel();
        $this->validacionesModel = new ValidacionesModel();
        $this->EncryptModel = new EncryptModel();
    }

    public function QueryAllController() {
        try {
            $resultado = $this->EjercicioModel->QueryAllModel();
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
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->EjercicioModel->QueryOneModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);
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
            list($success, $message) = $this->EjercicioModel->InsertModel($decryptedData);
            
            // Si la inserción fue exitosa, preparamos una respuesta con estado 200
            if ($success) {
                $response = json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $message]]);
            } else {
                // Si hubo un error (ejercicio duplicado), preparamos una respuesta con estado 400 o el que sea adecuado
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
            // Llamamos al método UpdateModel y guardamos el resultado
            list($success, $message) = $this->EjercicioModel->UpdateModel($decryptedID, $decryptedData);
            
            // Construimos la respuesta JSON basada en el resultado de UpdateModel
            if ($success) {
                $response = json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $message]]);
            } else {
                $response = json_encode(['estado' => 400, 'resultado' => ['res' => false, 'data' => $message]]);
            }

            // Encriptamos la respuesta JSON
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            // Manejamos la excepción utilizando el manejador de excepciones
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
            $resultado = $this->EjercicioModel->DeleteModel($decryptedID);
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
            $resultado = $this->EjercicioModel->ActivateModel($decryptedID, $datos);
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
