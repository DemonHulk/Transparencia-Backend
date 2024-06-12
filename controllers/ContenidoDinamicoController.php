<?php

require_once 'models/ContenidoDinamicoModel.php';
require_once 'middleware/ExceptionHandler.php';

class ContenidoDinamicoController {

    private $ContenidoDinamicoModel;

    public function __construct() {
        $this->ContenidoDinamicoModel = new ContenidoDinamicoModel();
    }

    public function QueryAllController() {
        try {
            $resultado = $this->ContenidoDinamicoModel->QueryAllModel();
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryOneController($id) {
        try {
            $resultado = $this->ContenidoDinamicoModel->QueryOneModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function InsertDocumentoController($datos) {
        try {
            $resultado = $this->ContenidoDinamicoModel->InsertDocumentoModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function InsertContenidoEstaticoController($datos) {
        try {
            $resultado = $this->ContenidoDinamicoModel->InsertContenidoModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function UpdateController($id, $datos) {
        try {
            $resultado = $this->ContenidoDinamicoModel->UpdateModel($id, $datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function DeleteController($id) {
        try {
            $resultado = $this->ContenidoDinamicoModel->DeleteModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
}
?>
