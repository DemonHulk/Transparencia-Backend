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

         // Validar los datos desencriptados
         if (!isset($decryptedData['nombreInterno']) || !$this->validacionesModel->ValidarTextoNumero($decryptedData['nombreInterno'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre interno del documento no es valido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }

        if (!isset($decryptedData['nombreExterno']) || !$this->validacionesModel->ValidarTextoNumero($decryptedData['nombreExterno'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre externo del documento no es valido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }

        if (!isset($decryptedData['descripcion']) || !$this->validacionesModel->ValidarTextoNumero($decryptedData['descripcion'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La descripción del documento no es valido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
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
            ExceptionHandler::handle($e);
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

        // Validar los datos desencriptados
        if (!isset($decryptedData['nombreInterno']) || !$this->validacionesModel->ValidarTextoNumero($decryptedData['nombreInterno'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre interno del documento no es valido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }

        if (!isset($decryptedData['nombreExterno']) || !$this->validacionesModel->ValidarTextoNumero($decryptedData['nombreExterno'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre externo del documento no es valido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }

        if (!isset($decryptedData['descripcion']) || !$this->validacionesModel->ValidarTextoNumero($decryptedData['descripcion'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La descripción del documento no es valido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
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

    public function getDocument($id_documento_dinamico) {
        error_log("Nombre de archivo encriptado recibido: " . $id_documento_dinamico);
        
        try {
            $decryptedName = $this->EncryptModel->decryptData($id_documento_dinamico);
            error_log("Nombre de archivo desencriptado: " . $decryptedName);
        } catch (\Throwable $th) {
            error_log("Error al desencriptar el nombre del archivo: " . $th->getMessage());
            $response = json_encode(['estado' => 400, 'resultado' => "Error al desencriptar datos"]);
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            echo $encryptedResponse;
            return;
        }
    
        try {
            $resultado = $this->ContenidoDinamicoModel->getDocument($decryptedName);
            error_log("Resultado de getDocument: " . json_encode($resultado));
            $response = json_encode(['estado' => $resultado['res'] ? 200 : 404, 'resultado' => $resultado]);
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            error_log("Respuesta encriptada: " . $encryptedResponse);
            echo $encryptedResponse;
        } catch (Exception $e) {
            error_log("Error en getDocument: " . $e->getMessage());
            $response = json_encode(['estado' => 500, 'resultado' => "Error interno del servidor: " . $e->getMessage()]);
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            echo $encryptedResponse;
        }
    }

    public function SearchFile($data) {
        try {
            $decryptText = $this->EncryptModel->decryptData($data['data']);
            // Actualizamos el estado
            $resultado = $this->ContenidoDinamicoModel->SearchPDF($decryptText);
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
