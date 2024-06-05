<?php

require_once 'models/ApartadoPuntoModel.php';
require_once 'middleware/ExceptionHandler.php';

class ApartadoPuntoController {

    private $apartadoPuntoModel;

    public function __construct() {
        $this->apartadoPuntoModel = new ApartadoPuntoModel();
    }

    public function QueryAllController() {
        try {
            $resultado = $this->apartadoPuntoModel->QueryAllModel();
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryOneController($id) {
        try {
            $resultado = $this->apartadoPuntoModel->QueryOneModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function InsertController($datos) {
        try {
            $resultado = $this->apartadoPuntoModel->InsertModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function UpdateController($id, $datos) {
        try {
            $resultado = $this->apartadoPuntoModel->UpdateModel($id, $datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
       }
   }
}
