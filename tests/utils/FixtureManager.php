<?php

class FixtureManager
{

    protected static $db;   
    
    public static function getDbInstance()
    {
        if (empty(self::$db)) {
            self::$db = new PDO('mysql:host=localhost;port=3306', 'root', 'vagrant');
            self::$db->exec('DROP DATABASE IF EXISTS phpunit');
            self::$db->exec('CREATE DATABASE phpunit');
        }
        return self::$db;
    }

    public static function prepareTable ($database, $table)
    {
        $db    = self::getDbInstance($database);        
        $query = file_get_contents(__DIR__ . DS . 'fixtures' . DS . $database . DS . $table . '.sql');
        self::$db->exec('USE phpunit');
        $db->exec($query);    
        return $db->errorInfo();
    }

    public static function healthcheck ()
    {
        $baseDir = __DIR__.DS . 'fixtures' . DS;
        if ($handle  = opendir($baseDir)) {
            while (false !== ($database = readdir($handle))) {
                if (is_dir($baseDir . $database) && !in_array($database, ['.', '..'])) {
                    if ($handle2 = opendir($baseDir . $database . DS)) {
                        while (false !== ($table = readdir($handle2))) {
                            if (is_file($baseDir . $database . DS . $table) && !in_array($table, ['.', '..'])) {
                                $info = FixtureManager::prepareTable($database, str_replace('.sql', '', $table));
                                if($info[0] != '00000'){
                                    echo $database.
                                         str_repeat(' ', 15 - strlen($database)).
                                         $table.
                                         str_repeat(' ', 35 - strlen($table)).
                                         $info[2]."\n";                                   
                                }
                            }
                        }
                    }
                    closedir($handle2);
                }
            }
            closedir($handle);
        }
    }

}
