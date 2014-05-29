<?php

class DBComponent {

    protected static $instances;

    public static function getInstance($table, $instance) {
        try {
            if (!isset(self::$instances[$instance])) {
                $oModel = new Model(false, $table, $instance);
                self::$instances[$instance] = $oModel->getDataSource();
            }
            return self::$instances[$instance];
        } catch (Exception $e) {
            if ($instance == 'swarmdataRead') {
                return self::getInstance($table, 'swarmdata');
            } else {
                throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
            }
        }
    }

}

?>
