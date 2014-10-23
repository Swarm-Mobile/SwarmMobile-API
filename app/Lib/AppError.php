<?php

/**
 * Override default cake error handler
 */
App::uses('CakeLog', 'Log');

class AppError
{

    public static function handleError ($code, $description, $file = null, $line = null, $context = null)
    {
        if (extension_loaded('newrelic')) {
            newrelic_notice_error($code);
        }
        return ErrorHandler::handleError($code, $description, $file, $line, $context);
    }

    public static function handleException (Exception $error)
    {                                
        $code = (!empty($error->getCode()))?$error->getCode():400;
        if (extension_loaded('newrelic')) {
            newrelic_notice_error($error->getMessage(), $error);
        }
        
        header("Cache-Control: no-store");
        header("HTTP/1.1 {$error->getCode()}");
        echo json_encode(
            [
                'error'             => $error->getCode(),
                'error_description' => $error->getMessage()
            ]
        );
        die();        
    }

}
