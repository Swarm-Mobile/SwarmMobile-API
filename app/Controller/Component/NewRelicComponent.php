<?php

class NewRelicComponent
{

    public static function createTransaction ($name, $params = [])
    {
        if (extension_loaded('newrelic')) {
            newrelic_name_transaction($name);
            foreach ($params as $k => $v) {
                newrelic_add_custom_parameter($k, $v);
            }
        }
    }

}
