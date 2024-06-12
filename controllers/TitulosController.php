<?php

require_once 'models/TitulosModel.php';
require_once 'middleware/ExceptionHandler.php';
require_once 'models/ValidacionesModel.php';



class TitulosController {

    private $TitulosModel;
    private $validacionesModel;

    public function __construct() {
        $this->TitulosModel = new TitulosModel();
        $this->validacionesModel = new ValidacionesModel();
    }

    public function QueryAllController() {
        try {
            $resultado = $this->TitulosModel->QueryAllModel();
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryOneController($id) {
        try {
            $resultado = $this->TitulosModel->QueryOneModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function InsertController($datos) {
        try {
            $resultado = $this->TitulosModel->InsertModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function UpdateController($id, $datos) {
        try {
            $resultado = $this->TitulosModel->UpdateModel($id, $datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function DeleteController($id) {
        try {
            $resultado = $this->TitulosModel->DeleteModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

}
