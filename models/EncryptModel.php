<?php

class EncryptModel {

    // Función interna para desencriptar los datos
    public function decryptData($encryptedData) {
        try {
            $cipher = "aes-256-cbc";
            $secretKey = 'ea2df1a3c1540005189bb447bd15e80f';
        // Separar el IV y los datos encriptados
        list($ivBase64, $encryptedBase64) = explode(':', $encryptedData, 2);

        // Decodificar el IV y los datos encriptados desde base64
        $iv = substr(base64_decode($ivBase64), 0, 16); // Asegurar que el IV sea de 16 bytes
        $encrypted = base64_decode($encryptedBase64);

        // Desencriptar los datos
        $decryptedData = openssl_decrypt(
            $encrypted,
            $cipher,
            $secretKey,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decryptedData === false) {
            throw new Exception("Desencriptación fallida: " . openssl_error_string());
        }

        return json_decode($decryptedData, true); // Devolver los datos desencriptados como un array asociativo
        } catch (\Throwable $th) {
            return false;
        }
    }

    // Función interna para encriptar los datos
    public function encryptJSON($data) {
        $secretKey = 'ea2df1a3c1540005189bb447bd15e80f';
            // Generar un IV (vector de inicialización) aleatorio
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    
            // Encriptar los datos
            $encrypted = openssl_encrypt($data, 'aes-256-cbc', $secretKey, OPENSSL_RAW_DATA, $iv);
    
            // Concatenar IV y datos cifrados en base64
            $ivBase64 = base64_encode($iv);
            $encryptedBase64 = base64_encode($encrypted);
    
            return $ivBase64 . ':' . $encryptedBase64;
    }
    
}
