<?php

class ExceptionHandler {
    public static function handle($e) {
        http_response_code(500);
        echo json_encode(['estado' => 500, 'error' => $e->getMessage()]);
    }
}
