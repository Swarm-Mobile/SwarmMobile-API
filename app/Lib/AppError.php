<?php

class AppError
{

    public static function handleError ($code, $description, $file = null, $line = null, $context = null)
    {
        if (extension_loaded('newrelic')) {
            newrelic_notice_error($code);
        }
        header("HTTP/1.1 $code");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET");
        header("Access-Control-Allow-Headers: X-PINGOTHER");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Max-Age: 1728000");
        header("Pragma: no-cache");
        header("Cache-Control: no-store; no-cache;must-revalidate; post-check=0; pre-check=0");
        echo json_encode(['error'=> $code,'error_description' => $description]);
        die();
    }

    public static function handleException (Exception $error)
    {
        $code = (!empty($error->getCode())) ? $error->getCode() : 400;
        if (extension_loaded('newrelic')) {
            newrelic_notice_error($error->getMessage(), $error);
        }
        header("HTTP/1.1 $code");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET");
        header("Access-Control-Allow-Headers: X-PINGOTHER");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Max-Age: 1728000");
        header("Pragma: no-cache");
        header("Cache-Control: no-store; no-cache;must-revalidate; post-check=0; pre-check=0");
        echo json_encode(['error'=> $code,'error_description' => $error->getMessage()]);
        die();
    }

}
