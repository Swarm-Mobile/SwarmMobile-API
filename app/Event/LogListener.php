<?php

App::uses('CakeEventListener', 'Event');
App::uses('CakeEvent', 'Event');

class LogListener implements CakeEventListener
{

    /**
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents ()
    {
        return [
            'Dispatcher.beforeDispatch' => [
                'callable' => 'logRequest',
                'priority' => '1'
            ],
            'Dispatcher.afterDispatch'  => [
                'callable' => 'removeLog',
                'priority' => '1'
            ],
        ];
    }

    /**
     * Logs the request if is a POST Request
     * 
     * @param CakeEvent $event
     */
    public function logRequest (CakeEvent $event)
    {
        if (!empty($event->data['request']->data)) {
            $logFolder = APP . 'tmp/logs/post/';
            if (!is_dir($logFolder)) {
                mkdir($logFolder);
            }
            $name   = date('Y_m_d_H_i_s');
            $name .= '_' . uniqid() . '_';
            $name .= str_replace('/', '_', $_SERVER['REQUEST_URI']);
            $name .= '.log';
            $handle = fopen($logFolder . $name, 'w+');
            if ($handle) {
                Configure::write('request_identifier_file', $name);
                $content = "<?php \n";
                $content .= '$post = '."\n".var_export($event->data['request']->data, true).";\n";
                $content .= '$get = '."\n".var_export($event->data['request']->query, true).";\n";
                $content .= '$server = '."\n".var_export($_SERVER, true).";\n";
                fwrite($handle, $content);
                fclose($handle);
            }
        }
    }

    /**
     * Removes the log file (the request was processed successfully)
     * @param CakeEvent $event
     */
    public function removeLog (CakeEvent $event)
    {
        $file = Configure::read('request_identifier_file');
        if (!empty($file)) {
            $logFolder = APP . 'tmp/logs/post/';
            if (!is_dir($logFolder.'success/')) {
                mkdir($logFolder.'success/');
            }
            rename($logFolder.$file, $logFolder.'success/'.$file);           
        }
    }

}
