<?php
App::uses('CakeResponse', 'Network');

class JsonResponse extends CakeResponse
{

    protected $_statusCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'Unsupported Version'
    ];
    protected $_headers     = [
        'Access-Control-Allow-Origin'  => '*',
        'Access-Control-Allow-Methods' => 'POST, GET',
        'Access-Control-Allow-Headers' => 'X-PINGOTHER',
        'Access-Control-Max-Age'       => '1728000',
        'Pragma'                       => 'no-cache',
        'Cache-Control'                => 'no-store; no-cache;must-revalidate; post-check=0; pre-check=0',
    ];
    protected $_contentType = 'application/json';
    protected $_body = '{}';

    public function __construct (array $options = array ())
    {
        if (isset($options['body'])) {
            $this->body(json_encode($options['body']));
        }
        if (isset($options['status'])) {
            $this->statusCode($options['status']);
        }
    }

}
