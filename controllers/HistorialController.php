<?php

require_once 'models/HistorialModel.php';
require_once 'middleware/ExceptionHandler.php';

class HistorialController {

    private $HistorialModel;

    public function __construct() {
        $this->HistorialModel = new HistorialModel();
    }

}
