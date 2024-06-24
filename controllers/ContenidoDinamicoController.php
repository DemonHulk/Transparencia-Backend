<?php

require_once 'models/ContenidoDinamicoModel.php';
require_once 'middleware/ExceptionHandler.php';
require_once 'models/EncryptModel.php';
require_once 'models/ValidacionesModel.php';

class ContenidoDinamicoController {

    private $ContenidoDinamicoModel;
    private $validacionesModel;
    private $EncryptModel;

    public function __construct() {
        $this->ContenidoDinamicoModel = new ContenidoDinamicoModel();
        $this->validacionesModel = new ValidacionesModel();
        $this->EncryptModel = new EncryptModel();
    }

    public function QueryAllController($id) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->ContenidoDinamicoModel->QueryAllModel($decryptedID);
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
            $resultado = $this->ContenidoDinamicoModel->QueryOneModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;

        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function InsertDocumentoController() {
        // Verificar si se recibieron datos correctamente
        if (!isset($_POST['encryptedData'])) {
            echo json_encode(['estado' => 400, 'resultado' => ['res' => false, 'data' => 'No se recibieron datos encriptados']]);
            return;
        }
    
        // Obtener datos encriptados y desencriptarlos
        $encryptedData = $_POST['encryptedData'];
    
        try {
            $decryptedData = $this->EncryptModel->decryptData($encryptedData);
        } catch (Exception $e) {
            echo json_encode(['estado' => 400, 'resultado' => ['res' => false, 'data' => 'Error al desencriptar datos: ' . $e->getMessage()]]);
            return;
        }
    
        // Obtener el archivo enviado
        $archivo = $_FILES['archivo'] ?? null;
        if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['estado' => 400, 'resultado' => ['res' => false, 'data' => 'No se recibió el archivo correctamente']]);
            return;
        }
    
        // Leer el contenido del archivo como una cadena de texto
        $contenidoArchivo = file_get_contents($archivo['tmp_name']);
    
        // Agregar el contenido del archivo al array de datos decodificados
        $decryptedData['archivo'] = $contenidoArchivo;
    
        // Asignar fechas y horas
        $decryptedData['fecha_creacion'] = date('Y-m-d');
        $decryptedData['hora_creacion'] = date('H:i:s');
        $decryptedData['fecha_actualizado'] = date('Y-m-d');
    
        try {
            // Envío de datos para la inserción
            $resultado = $this->ContenidoDinamicoModel->InsertDocumentoModel($decryptedData);
            $response =  json_encode(['estado' => 200, 'resultado' => $resultado]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            echo json_encode(['estado' => 500, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
        }
    }

    public function UpdateDocumentoController($id) {
        // Verificar si se recibieron datos encriptados
        if (!isset($_POST['encryptedData'])) {
            echo json_encode(['estado' => 400, 'resultado' => ['res' => false, 'data' => 'No se recibieron datos encriptados']]);
            return;
        }

        $encryptedData = $_POST['encryptedData'];
        
        try {
            $decryptedData = $this->EncryptModel->decryptData($encryptedData);
            $decryptedID = $this->EncryptModel->decryptData($id);
        } catch (Exception $e) {
            echo json_encode(['estado' => 400, 'resultado' => ['res' => false, 'data' => 'Error al desencriptar datos: ' . $e->getMessage()]]);
            return;
        }

        // Manejar el archivo si se incluye
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['archivo'];
            $contenidoArchivo = file_get_contents($archivo['tmp_name']);
            $decryptedData['archivo'] = $contenidoArchivo;
            $decryptedData['nombreArchivo'] = $archivo['name'];
        }

        // Actualizar las fechas y horas de actualización
        $decryptedData['fecha_actualizado'] = date('Y-m-d');
        $decryptedData['hora_actualizado'] = date('H:i:s');

        try {
            // Envío de datos para la actualización
            $resultado = $this->ContenidoDinamicoModel->UpdateModel($decryptedID, $decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            echo json_encode(['estado' => 500, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
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
           $resultado = $this->ContenidoDinamicoModel->DeleteModel($decryptedID, $datos);
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
            $resultado = $this->ContenidoDinamicoModel->ActivateModel($decryptedID, $datos);
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
