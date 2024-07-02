<?php
require_once 'models/EncryptModel.php';

class FrontController {
    private $rutas;
    private $EncryptModel;

    public function __construct() {
        $this->rutas = require 'routes.php';
        $this->EncryptModel = new EncryptModel();

    }


    private function obtenerUri() {
        // Obtener la URI completa
        $uri = $_SERVER['REQUEST_URI'];
        
        // Encontrar la posición de 'index.php' en la URI
        $indexPhpPosition = strpos($uri, 'index.php');
        
        // Extraer la parte de la URI después de 'index.php'
        $uri = substr($uri, $indexPhpPosition + strlen('index.php'));


        return trim($uri, '/');
    }

    
    public function manejarSolicitud() {
        $metodo = $_SERVER['REQUEST_METHOD'];
        $uri = $this->obtenerUri();

        if (isset($this->rutas[$metodo])) {
            foreach ($this->rutas[$metodo] as $ruta => $configuracion) {
                if (preg_match("#^$ruta$#", $uri, $matches)) {
                    array_shift($matches);
                    // Verificar si la ruta requiere sesión
                    if (isset($configuracion['require_session']) && $configuracion['require_session']) {
                        // Verificar sesión antes de despachar la acción
                        if (!$this->verificarSesion()) {
                            // Si la sesión no es válida, enviar respuesta de no autorizado
                            http_response_code(200);
                            echo $encryptedResponse = $this->EncryptModel->encryptJSON(json_encode(['estado' => false, 'resultado'=> ['data' => 'No autorizado']]));
                          
                            exit;
                        }
                    }   
                    // Despachar la acción del controlador
                    return $this->despachar($configuracion, $matches);
                }
            }
        }

        $this->enviarRespuestaNoEncontrada();
    }

    private function verificarSesion() {
        // Aquí implementa tu lógica para verificar si la sesión es válida
        // Por ejemplo, verificar el token de sesión almacenado en una cookie o en el cuerpo de la solicitud encriptada
        // Devuelve true si la sesión es válida, o false si no lo es
        return false; // Ejemplo simple, ajusta según tus necesidades
    }


    private function despachar($configuracion, $parametros) {
        $nombreControlador = $configuracion['controller'];
        $accion = $configuracion['action'];
        
        require_once "controllers/$nombreControlador.php";
        $controlador = new $nombreControlador();

        // Obtener el método de la solicitud
        $metodo = $_SERVER['REQUEST_METHOD'];

        if ($metodo === 'PUT') {
            // Obtener el ID del área de los parámetros de la URL
            $id = array_shift($parametros);
            // Obtener los datos del cuerpo de la solicitud
            $datos = json_decode(file_get_contents('php://input'), true);
            // Llamar a la función del controlador con el ID y los datos
            call_user_func([$controlador, $accion], $id, $datos);
        } elseif (!empty($parametros)) {
            // Si hay parámetros en la URL, llamar a la función del controlador con esos parámetros
            call_user_func_array([$controlador, $accion], $parametros);
        } else {
            // Si no hay parámetros en la URL, los datos deben estar en el cuerpo de la solicitud
            $datos = json_decode(file_get_contents('php://input'), true);
            // Llamar a la función del controlador con los datos
            call_user_func([$controlador, $accion], $datos);
        }
    }



    private function enviarRespuestaNoEncontrada() {
        http_response_code(404);
        echo json_encode(['estado' => 404, 'resultado' => 'No encontrado']);
    }
}
