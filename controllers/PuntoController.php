<?php

require_once 'models/PuntoModel.php';
require_once 'models/ValidacionesModel.php';
require_once 'middleware/ExceptionHandler.php';
require_once 'models/EncryptModel.php';

class PuntoController {
    private $puntoModel;
    private $validacionesModel;
    private $EncryptModel;

    public function __construct() {
        $this->puntoModel = new PuntoModel();
        $this->EncryptModel = new EncryptModel();
        $this->validacionesModel = new ValidacionesModel();
    }

    public function QueryAllController() {
        try {
            $resultado = $this->puntoModel->QueryAllModel();
            $response =  json_encode(['estado' => 200, 'resultado' => $resultado]);
            
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
            $resultado = $this->puntoModel->QueryOneModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);

            // Mandamos los datos a encriptar
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
        
        //desencriptacion
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


        // Validar nombre del punto
        if (!$this->validacionesModel->ValidarTextoNumero($decryptedData['nombrePunto'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre del punto no es válido."]]);
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

        try {
            // Insertar punto
            $resultado = $this->puntoModel->InsertModel($decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' =>  $resultado]);

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
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
            return;
        }

        // Validar nombre del punto
        if (!$this->validacionesModel->ValidarTextoNumero($decryptedData['nombrePunto'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre del punto no es válido."]]);
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

        try {
            $resultado = $this->puntoModel->UpdateModel($decryptedID, $decryptedData);

            $response = json_encode(['estado' => 200, 'resultado' =>  $resultado]);

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

        try {
            $decryptedID = $this->EncryptModel->decryptData($id);

            //desactivar punto
            $resultado = $this->puntoModel->DeleteModel($decryptedID,$datos);
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

        try {
            $decryptedID = $this->EncryptModel->decryptData($id);

            //desactivar punto
            $resultado = $this->puntoModel->ActivateModel($decryptedID,$datos);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);

            // Retornamos los datos ya encriptados
            echo $encryptedResponse;

        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }

    }

    public function QueryPuntoUserController($id) {
        try {
            // Mandamos la ID y la desencriptamos
            $decryptedID = $this->EncryptModel->decryptData($id);
        } catch (Exception $e) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
            return;
        }

        try {
            $resultado = $this->puntoModel->QueryPuntoUserModel($decryptedID);
            $response =  json_encode(['estado' => 200, 'resultado' => $resultado]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

     public function UpdateOrderPuntos($datos) {
        // Obtener los datos encriptados
        $encryptedData = $datos['data'];
        
        //desencriptacion
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

        try {
            // Insertar punto
            $resultado = $this->puntoModel->actualizarOrdenPuntos($decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' =>  $resultado]);

             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }

    }
}
