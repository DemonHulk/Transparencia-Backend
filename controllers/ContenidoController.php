<?php

require_once 'models/ContenidoModel.php';
require_once 'middleware/ExceptionHandler.php';

class ContenidoController {

    private $ContenidoModel;

    public function __construct() {
        $this->ContenidoModel = new ContenidoModel();
    }

}
