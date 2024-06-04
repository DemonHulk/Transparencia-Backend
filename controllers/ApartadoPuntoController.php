<?php

require_once 'models/ApartadoPuntoModel.php';
require_once 'middleware/ExceptionHandler.php';

class ApartadoPuntoController {
    public function QueryAllController() {
        try {
            $ApartadoPuntoModel = new ApartadoPuntoModel();
            $resultado = $ApartadoPuntoModel->QueryAllModel();
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryOneController($id) {
        try {
            $ApartadoPuntoModel = new ApartadoPuntoModel();
            $resultado = $ApartadoPuntoModel->QueryOneModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function InsertController($datos) {
        try {
            $ApartadoPuntoModel = new ApartadoPuntoModel();
            $resultado = $ApartadoPuntoModel->InsertModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function UpdateController($id, $datos) {
        try {
            $ApartadoPuntoModel = new ApartadoPuntoModel();
            $resultado = $ApartadoPuntoModel->UpdateModel($id, $datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function DeleteController($id) {
        try {
            $ApartadoPuntoModel = new ApartadoPuntoModel();
            $resultado = $ApartadoPuntoModel->DeleteModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
}
