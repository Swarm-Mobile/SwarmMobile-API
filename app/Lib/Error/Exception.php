<?php

namespace Swarm;

require_once(__DIR__.'/../../Controller/Component/SwarmErrorCodes.php');

class Exception extends \Exception
{
    public function __construct ($message, $code = 500, $previous = null)
    {        
        $error = (!empty(\SwarmErrorCodes::$messages[$message]))
                ?\SwarmErrorCodes::$messages[$message]
                :'Application Error';        
        parent::__construct(_($error), $message, $previous);
    }
}
