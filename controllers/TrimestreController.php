<?php

require_once 'models/TrimestreModel.php';
require_once 'middleware/ExceptionHandler.php';

class TrimestreController {

    private $TrimestreModel;

    public function __construct() {
        $this->TrimestreModel = new TrimestreModel();
    }

    public function QueryAllController() {
        try {
            $resultado = $this->TrimestreModel->QueryAllModel();
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
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
        try {
            $resultado = $this->TrimestreModel->InsertModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
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
