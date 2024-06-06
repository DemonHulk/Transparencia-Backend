<?php

require_once 'models/ValidacionesModel.php';

class ValidacionesController {
    
    private $validacionesModel;

    public function __construct() {
        $this->validacionesModel = new ValidacionesModel();
    }

    public function ValidarTextoController($texto) {
        try {
            return $this->validacionesModel->ValidarTexto($texto);
        } catch (Exception $e) {
            return false;
        }
    }

    public function ValidarTextoNumeroController($texto) {
        try {
            return $this->validacionesModel->ValidarTextoNumero($texto);
        } catch (Exception $e) {
            return false;
        }
    }

    public function ValidarFechaController($fecha) {
        try {
            return $this->validacionesModel->ValidarFecha($fecha);
        } catch (Exception $e) {
            return false;
        }
    }

    public function ValidarHoraController($hora) {
        try {
            return $this->validacionesModel->ValidarHora($hora);
        } catch (Exception $e) {
            return false;
        }
    }

    public function ValidarCorreoController($correo) {
        try {
            return $this->validacionesModel->ValidarCorreo($correo);
        } catch (Exception $e) {
            return false;
        }
    }

    public function ValidarNumerosController($cadena) {
        try {
            return $this->validacionesModel->ValidarNumeros($cadena);
        } catch (Exception $e) {
            return false;
        }
    }

    public function ValidarPasswordController($texto) {
        try {
            return $this->validacionesModel->ValidarPassword($texto);
        } catch (Exception $e) {
            return false;
        }
    }

    public function ValidarNoScriptController($texto) {
        try {
            return $this->validacionesModel->ValidarNoScript($texto);
        } catch (Exception $e) {
            return false;
        }
    }
}
