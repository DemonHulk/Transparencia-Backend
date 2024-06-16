<?php

require_once 'models/EncryptModel.php';

class EncryptController {
    
    private $EncryptModel;

    public function __construct() {
        $this->EncryptModel = new EncryptModel();
    }
    
    // Función para desencriptar los datos que vienen de angular
    public function decrypData($encryptedData) {
        try {
            $secretKey = 'ea2df1a3c1540005189bb447bd15e80f'; // La clave secreta debe coincidir con la usada en Angular
            return $this->EncryptModel->decryptData($encryptedData, $secretKey);
        } catch (Exception $e) {
            return false;
        }
    }

    // Funcion para encriptar los datos que enviaremos a angular
    public function encryptData($encryptedData) {
        try {
            return $this->EncryptModel->encryptJSON($encryptedData);
        } catch (Exception $e) {
            return false;
        }
    }
}
