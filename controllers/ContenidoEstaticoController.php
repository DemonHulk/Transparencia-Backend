<?php

require_once 'models/ContenidoEstaticoModel.php';
require_once 'middleware/ExceptionHandler.php';

class ContenidoEstaticoController {

    private $ContenidoEstaticoModel;

    public function __construct() {
        $this->ContenidoEstaticoModel = new ContenidoEstaticoModel();
    }

    public function QueryAllController() {
        try {
            $resultado = $this->ContenidoEstaticoModel->QueryAllModel();
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryOneController($id) {
        try {
            $resultado = $this->ContenidoEstaticoModel->QueryOneModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function InsertDocumentoController($datos) {
        try {
            $resultado = $this->ContenidoEstaticoModel->InsertDocumentoModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function InsertContenidoEstaticoController($datos) {
        try {
            $resultado = $this->ContenidoEstaticoModel->InsertContenidoModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function UpdateController($id, $datos) {
        try {
            $resultado = $this->ContenidoEstaticoModel->UpdateModel($id, $datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function DeleteController($id) {
        try {
            $resultado = $this->ContenidoEstaticoModel->DeleteModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
}
?>
