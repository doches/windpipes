<?php

require_once("response_transport.php");

// class Response extends XMLResponse { }
class Response extends XMLResponse { }


class ErrorResponse extends Response {
    // Defined error responses
    const CreateFail = "[ACCOUNT_CREATION_FAIL]";
    const CreateExists = "[ACCOUNT_CREATION_FAIL_USERNAME_EXISTS]";
    const CreateInvalid = "[ACCOUNT_CREATION_FAIL_USERNAME_INVALID]";
    const LoginNoExist = "[LOGIN_FAIL_INVALID_ACCOUNT]";
    const LoginFail = "[LOGIN_FAIL]";
    const OperationFailAuth = "[OPERATION_FAIL_NOT_ALLOWED]";
    const OperationFailToken = "[OPERATION_FAIL_INVALID_TOKEN]";
    const OperationFail = "[OPERATION_FAIL]";
    const OperationFailNotFound = "[OPERATION_FAIL_NOT_FOUND]";
    const OperationFailMaxCap = "[OPERATION_FAIL_MAX_CAPACITY]";

    function __construct($message) {
        parent::__construct();
        parent::set("error", $message);
        parent::set("status", "FAIL");
    }

    function output() {
        parent::output();
        die;
    }
}

class OKResponse extends Response {
    function __construct() {
        parent::__construct();
        parent::set("status", "OK");
    }
}

// Convenience function for emitting an error response if a condition is true
function emitErrorIf($message, $assert, $extra=null) {
    if ($assert) {
        $response = new ErrorResponse($message);
        $response->set("debug", $assert);
        if($extra != null) {
          $response->set("extra", $extra);
        }
        $response->output();
    }
}

// Convenience function for checking whether $haystack is prefixed by $needle
function startsWith($haystack, $needle) {
    return !strncmp(strtolower($haystack), strtolower($needle), strlen($needle));
}

// Convenience function to emit an OK response
function emitOK() {
    $response = new OKResponse();
    $response->output();
}

?>