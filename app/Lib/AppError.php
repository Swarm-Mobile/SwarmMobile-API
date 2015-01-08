<?php

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
        if (extension_loaded('newrelic')) {
            newrelic_notice_error($error->getMessage(), $error);
        }
        switch(get_class($error)){
            case 'Swarm\BadRequestException':           $code = 400; break;
            case 'Swarm\NotFoundException':             $code = 404; break;
            case 'Swarm\UnauthorizedException':         $code = 401; break;
            case 'Swarm\UnprocessableEntityException':  $code = 422; break;
            case 'Swarm\ApplicationErrorException':
            default:
                $code = (!empty($error->getCode()))?$error->getCode():500;
                
        }        
        $description = ($code == 404)?'Not Found':$error->getMessage();
        header("HTTP/1.1 $code");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET");
        header("Access-Control-Allow-Headers: X-PINGOTHER");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Max-Age: 1728000");
        header("Pragma: no-cache");
        header("Cache-Control: no-store; no-cache;must-revalidate; post-check=0; pre-check=0");
        echo json_encode(
            [
                'error'             => (!empty($error->getCode()))?$error->getCode():999, 
                'error_description' => $description
            ]
        );
        die();
    }
}