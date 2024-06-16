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

        // Asignar fechas y horas
        $datos['fecha_creacion'] = date('Y-m-d');
        $datos['hora_creacion'] = date('H:i:s');
        $datos['fecha_actualizado'] = date('Y-m-d');

        /*Verificar si existe el puntoacceso*/
        try {
            $existe = $this->PuntosAreasModel->ExistPuntoAccesoModel($datos);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }


        // Validar fechas y horas
        if (!$this->validacionesModel->ValidarFecha($datos['fecha_creacion']) ||
            !$this->validacionesModel->ValidarHora($datos['hora_creacion']) ||
            !$this->validacionesModel->ValidarFecha($datos['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "Las fechas u horas no son válidas."]]);
            return;
        }

        try {
            /*Si existe campo solamente lo actualizara, sino lo desactivara*/
            if ($existe) {
                $resultado = $this->PuntosAreasModel->ActivatePuntoAreaModel($datos);
                echo json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);
            }else{
                $resultado = $this->PuntosAreasModel->InsertPuntoAreaModel($datos);
                echo json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);
            }
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Inserta/Reactiva un punto en una area en especifico
     */
    public function Desactivate_PuntoAreaController($datos) {

        // Asignar fechas y horas
        $datos['fecha_actualizado'] = date('Y-m-d');



        // Validar fechas y horas
        if (!$this->validacionesModel->ValidarFecha($datos['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "Las fechas u horas no son válidas."]]);
            return;
        }

        try {
            $resultado = $this->PuntosAreasModel->DesactivatePuntoAreaModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);
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
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);

            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

}
