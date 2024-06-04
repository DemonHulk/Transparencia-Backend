<?php

require_once 'models/UsuarioModel.php';
require_once 'middleware/ExceptionHandler.php';

class UsuarioController {

    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
    }

    public function QueryAllController() {
        try {
            $resultado = $this->usuarioModel->QueryAllModel();
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryOneController($id) {
        try {
            $resultado = $this->usuarioModel->QueryOneModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function InsertController($datos) {
        try {
            $resultado = $this->usuarioModel->InsertModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function UpdateController($id, $datos) {
        try {
            $resultado = $this->usuarioModel->UpdateModel($id, $datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function DeleteController($id) {
        try {
            $resultado = $this->usuarioModel->DeleteModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
}
