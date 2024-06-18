<?php

require_once 'models/PuntosAreasModel.php';
require_once 'middleware/ExceptionHandler.php';
require_once 'models/ValidacionesModel.php';
require_once 'models/EncryptModel.php';



class PuntosAreasController {

    private $PuntosAreasModel;
    private $validacionesModel;
    private $EncryptModel;

    public function __construct() {
        $this->PuntosAreasModel = new PuntosAreasModel();
        $this->validacionesModel = new ValidacionesModel();
        $this->EncryptModel = new EncryptModel();
    }


    /**
     * Inserta/Reactiva un punto en una area en especifico
     */
    public function InsertOrActivate_PuntoAreaController($datos) {
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

        /*Verificar si existe el puntoacceso*/
        try {
            $existe = $this->PuntosAreasModel->ExistPuntoAccesoModel($decryptedData);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }


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
            /*Si existe campo solamente lo actualizara, sino lo desactivara*/
            if ($existe) {
                $resultado = $this->PuntosAreasModel->ActivatePuntoAreaModel($decryptedData);
                $response = json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);
                // Mandamos los datos a encriptar
                $encryptedResponse = $this->EncryptModel->encryptJSON($response);
                // Retornamos los datos ya encriptados
                echo $encryptedResponse;
            }else{
                $resultado = $this->PuntosAreasModel->InsertPuntoAreaModel($decryptedData);
                $response = json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);
                // Mandamos los datos a encriptar
                $encryptedResponse = $this->EncryptModel->encryptJSON($response);
                // Retornamos los datos ya encriptados
                echo $encryptedResponse;
            }
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Inserta/Reactiva un punto en una area en especifico
     */
    public function Desactivate_PuntoAreaController($datos) {

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
        $decryptedData['fecha_actualizado'] = date('Y-m-d');



        // Validar fechas y horas
        if (!$this->validacionesModel->ValidarFecha($decryptedData['fecha_actualizado'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "Las fechas u horas no son válidas."]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
            return;
        }

        try {
            $resultado = $this->PuntosAreasModel->DesactivatePuntoAreaModel($decryptedData);
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
     * Obtiene el id de un area en especifico y extrae todos sus puntos 
     * a los que esta area tiene acceso
     */
    public function QueryAllPuntosAccesoAreaController($id) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->PuntosAreasModel->QueryAllPuntosAccesoAreaModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);

            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Obtiene el id de un punto en especifico y extrae todos sus areas 
     * que tienen acceso a el punto
     */
    public function QueryAreaPunto_PuntoController($id) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->PuntosAreasModel->QueryAreaPunto_PuntoModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);

            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


}
