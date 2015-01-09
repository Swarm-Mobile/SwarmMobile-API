<?php

class NewRelicComponent
{

    public static function noticeError (Exception $e, $params)
    {
        if (extension_loaded('newrelic')) {
            newrelic_notice_error($e->getMessage(), $e);
            foreach ($params as $k => $v) {
                newrelic_add_custom_parameter($k, $v);
            }
        }
    }

    public static function startTransaction ($transactionName, $isBackground = false)
    {
        if (extension_loaded('newrelic')) {            
            newrelic_start_transaction();
            newrelic_background_job($isBackground);
            newrelic_name_transaction($transactionName);
        }
    }

    public static function endTransaction ($ignore = false)
    {
        if (extension_loaded('newrelic')) {
            newrelic_end_transaction($ignore);
        }
    }

    public static function addCustomParameter ($key, $value)
    {
        if (extension_loaded('newrelic')) {
            newrelic_add_custom_parameter($key, $value);
        }
    }

}
