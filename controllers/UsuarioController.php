<?php
// Inmportaciones del PHPMiler
require_once __DIR__ . '/../assets/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'models/UsuarioModel.php';
require_once 'models/ValidacionesModel.php';
require_once 'middleware/ExceptionHandler.php';
require_once 'models/EncryptModel.php';

class UsuarioController {

    private $usuarioModel;
    private $validacionesModel;
    private $EncryptModel;
    
    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->validacionesModel = new ValidacionesModel();
        $this->EncryptModel = new EncryptModel();
    }

    public function QueryAllController() {
        try {
            $resultado = $this->usuarioModel->QueryAllUserArea();
            $response = json_encode(['estado' => 200, 'resultado' =>['res' => true, 'data' => $resultado]]);
            // Mandamos los datos a encriptar
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
            $resultado = $this->usuarioModel->QueryOneModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' =>['res' => true, 'data' => $resultado]]);
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;

        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


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
    
        // Validar los datos desencriptados
        if (!isset($decryptedData['nombre']) || !$this->validacionesModel->ValidarTexto($decryptedData['nombre'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre del usuario no es válido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }
    
        if (!isset($decryptedData['primerApellido']) || !$this->validacionesModel->ValidarTexto($decryptedData['primerApellido'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El primer apellido no es válido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }
    
        if (isset($decryptedData['segundoApellido']) && $decryptedData['segundoApellido'] !== '' && !$this->validacionesModel->ValidarTexto($decryptedData['segundoApellido'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El segundo apellido no es válido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }
    
        if (!isset($decryptedData['correo']) || !$this->validacionesModel->ValidarCorreo($decryptedData['correo'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El correo electrónico no es válido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }
    
        if (isset($decryptedData['password']) && !$this->validacionesModel->ValidarPassword($decryptedData['password'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La contraseña no es válida."]]);
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
            $resultado = $this->usuarioModel->InsertModel($decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' =>['res' => true, 'data' => $resultado]]);
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

        // Validar los datos desencriptados
        if (!isset($decryptedData['nombre']) || !$this->validacionesModel->ValidarTexto($decryptedData['nombre'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El nombre del usuario no es válido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }
    
        if (!isset($decryptedData['primerApellido']) || !$this->validacionesModel->ValidarTexto($decryptedData['primerApellido'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El primer apellido no es válido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }
    
        if (isset($decryptedData['segundoApellido']) && $decryptedData['segundoApellido'] !== '' && !$this->validacionesModel->ValidarTexto($decryptedData['segundoApellido'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El segundo apellido no es válido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }
    
        if (!isset($decryptedData['correo']) || !$this->validacionesModel->ValidarCorreo($decryptedData['correo'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "El correo electrónico no es válido."]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
            return;
        }
    
        if (isset($decryptedData['password']) && !$this->validacionesModel->ValidarPassword($decryptedData['password'])) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "La contraseña no es válida."]]);
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
            // Actualizar usuario
            $resultado = $this->usuarioModel->UpdateModel($decryptedID, $decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' =>['res' => true, 'data' => $resultado]]);
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
            $resultado = $this->usuarioModel->DeleteModel($decryptedID, $datos);
            $response = json_encode(['estado' => 200, 'resultado' =>['res' => true, 'data' => $resultado]]);
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
            $resultado = $this->usuarioModel->ActivateModel($decryptedID, $datos);
             // Actualizamos el estado
             $response = json_encode(['estado' => 200, 'resultado' =>['res' => true, 'data' => $resultado]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Obtiene el id de un area en especifico y extrae todos sus usuarios 
     * que tienen activa la area
     */
    public function QueryAllUsuariosAccesoAreaController($id) {
        try {
            $decryptedID = $this->EncryptModel->decryptData($id);
            $resultado = $this->usuarioModel->QueryAllUsuariosAccesoAreaModel($decryptedID);
            $response = json_encode(['estado' => 200, 'resultado' =>['res' => true, 'data' => $resultado]]);

            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    // Recibe las credenciales del usuario, verifica que sean correctas y regresa los datos necesarios para la sesión 
    public function VerificarUserController($datos) {
        // Obtener los datos encriptados
        $encryptedData = $datos['data'];

        try {
            // Mandamos los datos encriptados a la funcion para desencriptarlos
            $decryptedData = $this->EncryptModel->decryptData($encryptedData);
        } catch (Exception $e) {
            echo json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => $e->getMessage()]]);
            return;
        }

        try {
            $resultado = $this->usuarioModel->verificarUserModel($decryptedData);
            $response = json_encode(['estado' => 200, 'resultado' =>['res' => true, 'data' => $resultado]]);
             // Mandamos los datos a encriptar
             $encryptedResponse = $this->EncryptModel->encryptJSON($response);
             // Retornamos los datos ya encriptados
             echo $encryptedResponse;
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    
    // Función para verificar si el correo está dado de alta en el sistema
    public function recoverPassword($datos) {
        // Obtener los datos encriptados
        $encryptedData = $datos['data'];
    
        try {
            // Mandamos los datos encriptados a la función para desencriptarlos
            $decryptedData = $this->EncryptModel->decryptData($encryptedData);
        } catch (Exception $e) {
            $response = json_encode(['estado' => 200, 'resultado' => ['res' => false, 'data' => "Error al desencriptar los datos"]]);
            // Mandamos los datos a encriptar
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
            return;
        }
        
        $correo = $decryptedData['correo'];
    
        try {
            // Verifica que el correo exista en la base de datos
            $usuario = $this->usuarioModel->getUserByEmail($correo);
    
            if ($usuario) {
                // Genera una nueva contraseña aleatoria
                $nuevaContrasenia = bin2hex(random_bytes(8)); // Genera una contraseña de 16 caracteres
                $hashedContrasenia = password_hash($nuevaContrasenia, PASSWORD_DEFAULT);
    
                // Iniciar transacción
                $conn = Conexion::Conexion();
                $conn->beginTransaction();
    
                // Actualiza la contraseña en la base de datos
                $this->usuarioModel->updatePassword($usuario['id_usuario'], $hashedContrasenia);
    
                // Confirmar la transacción
                $conn->commit();
    
                // Envía un correo electrónico con la nueva contraseña
                if ($this->sendRecoveryEmail($correo, $nuevaContrasenia)) {
                    $response = json_encode(['estado' => 200, 'resultado' => ['res' => true, 'data' => 'Revise su correo, la nueva contraseña fue enviada']]);
                } else {
                    $response = json_encode(['estado' => 500, 'resultado' => ['res' => false, 'data' => 'Ocurrio un error al enviar el correo']]);
                }
                
                $encryptedResponse = $this->EncryptModel->encryptJSON($response);
                // Retornamos los datos ya encriptados
                echo $encryptedResponse;
            } else {
                $response = json_encode(['estado' => 404, 'resultado' => ['res' => false, 'data' => 'Correo no registrado']]);
                $encryptedResponse = $this->EncryptModel->encryptJSON($response);
                // Retornamos los datos ya encriptados
                echo $encryptedResponse;
            }
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            if (isset($conn)) {
                $conn->rollBack();
            }
            $response = json_encode(['estado' => 500, 'resultado' => ['res' => false, 'data' => 'Error al recuperar contraseña']]);
            $encryptedResponse = $this->EncryptModel->encryptJSON($response);
            // Retornamos los datos ya encriptados
            echo $encryptedResponse;
        }
    }
    
    public function sendRecoveryEmail($correo, $nuevaContrasenia) {
        $mail = new PHPMailer(true);
    
        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'zxzero24xz@gmail.com'; // Tu dirección de Gmail completa
            $mail->Password   = 'uynrwxeqyjnnlqvf'; // Tu contraseña de aplicación de Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Usa SMTPS (SSL/TLS implícito)
            $mail->Port       = 465; // Puerto para SMTPS
    
            // Configurar UTF-8
            $mail->CharSet = 'UTF-8';
    
            // Destinatarios
            $mail->setFrom('zxzero24xz@gmail.com', 'Servicio de recuperación de contraseña');
            $mail->addAddress($correo);
    
            // URL de la imagen en Google Drive
            $imageUrl = 'https://drive.google.com/uc?export=view&id=1Sk5KWTTNM7fAsuNNQs-9RxFaj6TyZcCC'; // Enlace directo
    
            // Contenido del correo con diseño e imagen por URL
            $mail->Body = "
            <html>
            <head>
                 <style>
                    body {
                        margin: 0;
                        padding: 0;
                        width: 100%;
                        height: 100%;
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                    }
                    .email-container {
                        position: relative;
                        width: 100%;
                        height: 300px;
                        background: url('$imageUrl') no-repeat center center;
                        background-size: cover;
                    }
                    .overlay {
                        position: relative;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                    }
                    .content {
                        text-align: center;
                        color: white;
                        width: 100%;
                    }
                    .content-inner {
                        position: relative;
                        margin-top: 80px;
                        top: 50%;
                        transform: translateY(-50%);
                    }
                    .content h1 {
                        font-size: 3em;
                        margin: 0;
                    }
                    .content h3 {
                        font-size: 2em;
                        margin: 0;
                        margin-top: 10px;
                    }
                    .body-content {
                        padding: 20px;
                        text-align: left;
                        background-color: #ffffff;
                    }
                    .footer {
                        padding: 20px;
                        text-align: center;
                        font-size: 14px;
                        color: white;
                        background-color: #043D3D;
                    }
                    p {
                      font-size: 1.5em;  
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='overlay'>
                        <div class='content'>
                            <div class='content-inner'>
                                <h1>PORTAL DE TRANSPARENCIA</h1>
                                <h3>Recuperación de contraseña</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='body-content'>
                    <p>Hola,</p>
                    <p>Tu nueva contraseña es: <strong>$nuevaContrasenia</strong></p>
                    <p>Por favor, cambia tu contraseña después de iniciar sesión para mayor seguridad.</p>
                </div>
                <div class='footer' style='background-color: #043D3D;'>
                    <p style='color: white;'>Gracias por usar nuestro servicio de recuperación de contraseña.</p>
                </div>
            </body>
            </html>";
    
            $mail->isHTML(true);  // Establece el formato de correo electrónico a HTML
            $mail->Subject = 'Recuperación de contraseña';
    
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar el correo a $correo: {$mail->ErrorInfo}");
            throw new Exception("Error al enviar el correo: {$mail->ErrorInfo}");
        }
    }
    
    
    
    
    
    
    
}
