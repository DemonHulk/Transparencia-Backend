<?php

require_once 'models/TitulosModel.php';
require_once 'middleware/ExceptionHandler.php';
require_once 'models/ValidacionesModel.php';
require_once 'models/EncryptModel.php';


class TitulosController {

    private $TitulosModel;
    private $validacionesModel;
    private $EncryptModel;

    public function __construct() {
        $this->TitulosModel = new TitulosModel();
        $this->validacionesModel = new ValidacionesModel();
        $this->EncryptModel = new EncryptModel(); 
    }

    public function QueryAllController() {
        try {
            $resultado = $this->TitulosModel->QueryAllModel();
            echo json_encode(['estado' => 200, 'resultado' => $resultado]);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function mostrarTitulosSubtitulos($idPunto) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($idPunto);
            $resultado = $this->TitulosModel->mostrarJerarquia($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' =>  $resultado]);
            
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function mostrarSubtitulosByTitulo($idTitulo) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($idTitulo);
            $resultado = $this->TitulosModel->mostrarJerarquiaSubtitulos($decryptedID);
            $resultadoTitulo = $this->TitulosModel->QueryOneModel($decryptedID);
            $response = json_encode(['estado' => 200, 'subtitulos' =>  $resultado, 'titulo'=>$resultadoTitulo]);
            
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

            $resultado = $this->TitulosModel->QueryOneModel($decryptedID);
            $response =json_encode(['estado' => 200, 'resultado' => $resultado]);
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

     public function QueryOneControllerSubtema($id) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);

            $resultado = $this->TitulosModel->obtenerSubtituloInformacion($decryptedID);
            $response =json_encode(['estado' => 200, 'resultado' => $resultado]);
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryTitulosPuntoController($id) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->TitulosModel->QueryTitulosPuntoModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' =>  $resultado]);

            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;

        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function QueryTituloPadre($id) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->TitulosModel->obtenerTituloPrincipal($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' =>  $resultado]);

            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;

        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
    /**
     * extrae los datos de un Tema + nombre del punto a que pertenece e id
     */
    public function QueryTitulosMasPuntoController($id) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->TitulosModel->QueryTitulosMasPuntoModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' =>  $resultado]);

            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;

        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    /**
     * Insert para un Tema
     */
    public function InsertController($datos) {
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


        try {
            // Insertar punto
            $resultado = $this->TitulosModel->InsertModel($decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' =>  $resultado]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    public function InsertSubtemaController($datos) {
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


            try {
                // Insertar punto
                $resultado = $this->TitulosModel->InsertModelSubtema($decryptedData);
                $response = json_encode(['estado' => 200, 'resultado' =>  $resultado]);
                 // Mandamos los datos a encriptar
                 $encryptedResponse = $this->EncryptModel->encryptJSON($response);
                 // Retornamos los datos ya encriptados
                 echo $encryptedResponse;
            } catch (Exception $e) {
                ExceptionHandler::handle($e);
            }
        }




    /**
     * Actualiza un titulo existente.
     * @param int $id ID del titulo.
     * @param array $datos Datos del titulo.
     */
    public function UpdateController($id, $datos) {
        // Obtener los datos encriptados
        $encryptedData = $datos['data'];
    
        try {
            // Mandamos los datos encriptados a la funcion para desencriptarlos
            $decryptedData = $this->EncryptModel->decryptData($encryptedData);
            $decryptedID = $this->EncryptModel->decryptData($id);
        } catch (Exception $e) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
            return;
        }

        // Asignar fecha actualizada
        $decryptedData['fecha_actualizado'] = date('Y-m-d');

        try {
            // Actualizar titulo
            $resultado = $this->TitulosModel->UpdateModel($decryptedID, $decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' =>  $resultado]);
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function UpdateControllerSubtema($id, $datos) {
        // Obtener los datos encriptados
        $encryptedData = $datos['data'];
    
        try {
            // Mandamos los datos encriptados a la funcion para desencriptarlos
            $decryptedData = $this->EncryptModel->decryptData($encryptedData);
            $decryptedID = $this->EncryptModel->decryptData($id);
        } catch (Exception $e) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
            return;
        }

        // Asignar fecha actualizada
        $decryptedData['fecha_actualizado'] = date('Y-m-d');

        try {
            // Actualizar titulo
            $resultado = $this->TitulosModel->UpdateModelSubtema($decryptedID, $decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' =>  $resultado]);
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    /**
     * Desactiva un Tema
     * @param int $id ID del Tema.
     */
    public function DeleteController($id) {
        // Asignar fecha actualizada
        $datos['fecha_actualizado'] = date('Y-m-d');

        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            // Desactivar área
            $resultado = $this->TitulosModel->DeleteModel($decryptedID, $datos);
            $response = json_encode(['estado' => 200, 'resultado' => $resultado]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    /**
     * Activa un Tema.
     * @param int $id ID del Tema.
     */
    public function ActivateController($id) {
        // Asignar fecha actualizada
        $datos['fecha_actualizado'] = date('Y-m-d');

        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            // Activar Tema
            $resultado = $this->TitulosModel->ActivateModel($decryptedID, $datos);
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
