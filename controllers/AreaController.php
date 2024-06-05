<?php

require_once 'models/AreaModel.php';
require_once 'middleware/ExceptionHandler.php';

class AreaController {

    private $areaModel;

    public function __construct() {
        $this->areaModel = new AreaModel();
    }

    public function QueryAllController() {
        try {
            
            $resultado = $this->areaModel->QueryAllModel();
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryOneController($id) {
        try {
            
            $resultado = $this->areaModel->QueryOneModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function InsertController($datos) {
        try {
            $datos['fecha_creacion'] = date('Y-m-d');
            $datos['hora_creacion'] = date('H:i:s');
            $datos['fecha_actualizado'] = date('Y-m-d');
            $resultado = $this->areaModel->InsertModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function UpdateController($id, $datos) {
        try {
            
            $resultado = $this->areaModel->UpdateModel($id, $datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function DeleteController($id) {
        try {
            
            $resultado = $this->areaModel->DeleteModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
}
