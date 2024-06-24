<?php

require_once 'models/ContenidoEstaticoModel.php';
require_once 'middleware/ExceptionHandler.php';
require_once 'models/EncryptModel.php';
require_once 'models/ValidacionesModel.php';

class ContenidoEstaticoController {

    private $ContenidoEstaticoModel;
    private $validacionesModel;
    private $EncryptModel;

    public function __construct() {
        $this->ContenidoEstaticoModel = new ContenidoEstaticoModel();
        $this->validacionesModel = new ValidacionesModel();
        $this->EncryptModel = new EncryptModel();
    }

    public function QueryAllController($id) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->ContenidoEstaticoModel->QueryAllModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryOneController($id) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->ContenidoEstaticoModel->QueryOneModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
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
        // Obtener los datos encriptados
        $encryptedData = $datos['data'];
    
        try {
            // Mandamos los datos encriptados a la funcion para desencriptarlos
            $decryptedData = $this->EncryptModel->decryptData($encryptedData);
        } catch (Exception $e) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }


         // Asignar fechas y horas
         $decryptedData['fecha_creacion'] = date('Y-m-d');
         $decryptedData['hora_creacion'] = date('H:i:s');
         $decryptedData['fecha_actualizado'] = date('Y-m-d');
     
         // Validar fechas y horas
         if (!$this->validacionesModel->ValidarFecha($decryptedData['fecha_creacion']) ||
             !$this->validacionesModel->ValidarHora($decryptedData['hora_creacion']) ||
             !$this->validacionesModel->ValidarFecha($decryptedData['fecha_actualizado'])) {
             $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "Las fechas u horas no son válidas."]]);
              // Mandamos los datos a encriptar
              $encryptedResponse = $this->EncryptModel->encryptJSON($response);
              // Retornamos los datos ya encriptados
              echo $encryptedResponse;
             return;
         }
     
         try {
             // Insertar usuario
             $resultado = $this->ContenidoEstaticoModel->InsertContenidoModel($decryptedData);
             $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
              // Mandamos los datos a encriptar
              $encryptedResponse = $this->EncryptModel->encryptJSON($response);
              // Retornamos los datos ya encriptados
              echo $encryptedResponse;
         } catch (Exception $e) {
             ExceptionHandler::handle($e);
         }
    }

    public function UpdateController($id, $datos) {
        // Obtener los datos encriptados
        $encryptedData = $datos['data'];

        try {
            // Mandamos los datos encriptados a la funcion para desencriptarlos
            $decryptedData = $this->EncryptModel->decryptData($encryptedData);
            $decryptedID = $this->EncryptModel->decryptData($id);
        } catch (Exception $e) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
            return;
        }

        // Asignar fechas y horas
        $decryptedData['fecha_creacion'] = date('Y-m-d');
        $decryptedData['hora_creacion'] = date('H:i:s');
        $decryptedData['fecha_actualizado'] = date('Y-m-d');
    
        // Validar fechas y horas
        if (!$this->validacionesModel->ValidarFecha($decryptedData['fecha_creacion']) ||
            !$this->validacionesModel->ValidarHora($decryptedData['hora_creacion']) ||
            !$this->validacionesModel->ValidarFecha($decryptedData['fecha_actualizado'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "Las fechas u horas no son válidas."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }

        try {
            // Actualizar usuario
            $resultado = $this->ContenidoEstaticoModel->UpdateModel($decryptedID, $decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function DeleteController($id) {
        // Asignar fecha actualizada
        $datos['fecha_actualizado'] = date('Y-m-d');
 
        // Validar fecha actualizada
        if (!$this->validacionesModel->ValidarFecha($datos['fecha_actualizado'])) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La fecha actualizada no es válida."]]);
            return;
        }
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            // Actualizamos el estado
            $resultado = $this->ContenidoEstaticoModel->DeleteModel($decryptedID, $datos);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
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
            $decryptedID = $this->EncryptModel->decryptData($id);
            // Actualizamos el estado
            $resultado = $this->ContenidoEstaticoModel->ActivateModel($decryptedID, $datos);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
     }
}
?>
