<?php

require_once 'models/AreaModel.php';
require_once 'models/ValidacionesModel.php';
require_once 'middleware/ExceptionHandler.php';

class AreaController {

    private $areaModel;
    private $validacionesModel;

    public function __construct() {
        $this->areaModel = new AreaModel();
        $this->validacionesModel = new ValidacionesModel();
    }

    /**
     * Obtiene todas las áreas activas.
     */
    public function QueryAllController() {
        try {
            $resultado = $this->areaModel->QueryAllModel();
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Obtiene una área activa por ID.
     * @param int $id ID del área.
     */
    public function QueryOneController($id) {
        try {
            $resultado = $this->areaModel->QueryOneModel($id);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Inserta una nueva área.
     * @param array $datos Datos del área.
     */
    public function InsertController($datos) {
        // Validar nombre del área
        if (!$this->validacionesModel->ValidarTexto($datos['nombreArea'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre del área no es válido."]]);
            return;
        }

        // Asignar fechas y horas
        $datos['fecha_creacion'] = date('Y-m-d');
        $datos['hora_creacion'] = date('H:i:s');
        $datos['fecha_actualizado'] = date('Y-m-d');

        // Validar fechas y horas
        if (!$this->validacionesModel->ValidarFecha($datos['fecha_creacion']) ||
            !$this->validacionesModel->ValidarHora($datos['hora_creacion']) ||
            !$this->validacionesModel->ValidarFecha($datos['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "Las fechas u horas no son válidas."]]);
            return;
        }

        try {
            // Insertar área
            $resultado = $this->areaModel->InsertModel($datos);
            echo json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => $resultado]]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Actualiza un área existente.
     * @param int $id ID del área.
     * @param array $datos Datos del área.
     */
    public function UpdateController($id, $datos) {

        // Validar nombre del área si está presente
        if (isset($datos['nombreArea']) && !$this->validacionesModel->ValidarTexto($datos['nombreArea'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre del área no es válido."]]);
            return;
        }

        // Asignar fecha actualizada
        $datos['fecha_actualizado'] = date('Y-m-d');

        // Validar fecha actualizada
        if (!$this->validacionesModel->ValidarFecha($datos['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La fecha actualizada no es válida."]]);
            return;
        }

        try {
            // Actualizar área
            $resultado = $this->areaModel->UpdateModel($id, $datos);
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Desactiva un área.
     * @param int $id ID del área.
     */
    public function DeleteController($id) {
        // Asignar fecha actualizada
        $datos['fecha_actualizado'] = date('Y-m-d');

        // Validar fecha actualizada
        if (!$this->validacionesModel->ValidarFecha($datos['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La fecha actualizada no es válida."]]);
            return;
        }

        try {
            // Desactivar área
            $resultado = $this->areaModel->DeleteModel($id);
            echo json_encode(['estado' => 200, 'resultado' =>  $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function ActivateController($id) {
        // Asignar fecha actualizada
        $datos['fecha_actualizado'] = date('Y-m-d');

        // Validar fecha actualizada
        if (!$this->validacionesModel->ValidarFecha($datos['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La fecha actualizada no es válida."]]);
            return;
        }

        try {
            // Activar área
            $resultado = $this->areaModel->ActivateModel($id);
            echo json_encode(['estado' => 200, 'resultado' =>  $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

}
