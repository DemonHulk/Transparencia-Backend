<?php

require_once 'models/TrimestreModel.php';
require_once 'middleware/ExceptionHandler.php';

class TrimestreController {

    private $TrimestreModel;

    public function __construct() {
        $this->TrimestreModel = new TrimestreModel();
    }

}
