<?php

class AppError
{

    public static function handleError ($code, $description, $file = null, $line = null, $context = null)
    {
        NewRelicComponent::noticeError($error, 
            [
                'error_no'          => $code,
                'error_description' => $description,
                'error_file'        => $file,
                'error_line'        => $line,
                'error_context'     => $context,
            ]
        );
        return ErrorHandler::handleError($code, $description, $file, $line, $context);
    }

    public static function handleException (Exception $error)
    {
        switch (get_class($error)) {
            case 'Swarm\BadRequestException' : $code = 400;
                break;
            case 'Swarm\NotFoundException' : $code = 404;
                break;
            case 'Swarm\UnauthorizedException' : $code = 401;
                break;
            case 'Swarm\UnprocessableEntityException' : $code = 422;
                break;
            case 'Swarm\ApplicationErrorException' :
            default : $code = (!empty($error->getCode())) ? $error->getCode() : 500;
        }
        $description = ($code == 404) ? 'Not Found' : $error->getMessage();
        $error_no    = (!empty($error->getCode())) ? $error->getCode() : 999;
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
                    'error'             => $error_no,
                    'error_description' => $description
                ]
        );
        NewRelicComponent::noticeError($error, [
            'error_no'          => $error_no,
            'error_description' => $description,
            'request_uri'       => $_SERVER['REQUEST_URI'],
            'request_params'    => json_encode($_POST),
            'exception_type'    => get_class($error),
            'exception_file'    => $error->getFile(),
            'exception_line'    => $error->getLine(),
                ]
        );
        die();
    }

}
