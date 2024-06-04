<?php

require_once 'models/PuntosAreasModel.php';
require_once 'middleware/ExceptionHandler.php';

class PuntosAreasController {

    private $PuntosAreasModel;

    public function __construct() {
        $this->PuntosAreasModel = new PuntosAreasModel();
    }

}
