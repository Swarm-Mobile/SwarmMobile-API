<?php

class DBComponent {

    protected static $instances;

    public static function getInstance($table, $instance) {
        if (!isset(self::$instances[$instance])) {
            $oModel = new Model(false, $table, $instance);            
            self::$instances[$instance] = $oModel->getDataSource();
        }
        return self::$instances[$instance];
    }
}

?>
