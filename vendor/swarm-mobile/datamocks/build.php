<?php
set_time_limit(3600);
define('DS', DIRECTORY_SEPARATOR);

$reset = realpath(__DIR__ . DS . 'reset.sql'); 
echo "mysql -uroot -pvagrant < " . $reset."\n";                
exec("mysql -uroot -pvagrant < " . $reset);                

$notrack = array ('.', '..', '_DS', '_query', '_schema.sql');

if ($data_dir = opendir(__DIR__ . DS . 'data'))
{    
    while (false !== ($subfolder = readdir($data_dir)))
    {        
        $pwd = __DIR__ . DS . 'data' . DS . $subfolder;        
        if (is_dir($pwd) && !in_array($subfolder, $notrack))
        {
            $schema = realpath($pwd) . DS . '_schema.sql';            
            if (file_exists($schema))
            {
                echo "mysql -uroot -pvagrant < " . $schema."\n";                
                exec("mysql -uroot -pvagrant < " . $schema);                
            }
            if ($db_dir = opendir($pwd))
            {
                while (false !== ($table_file = readdir($db_dir)))
                {                    
                    if (strpos($table_file, '.sql') && !in_array($table_file, $notrack))
                    {
                        $cpwd = $pwd.DS.$table_file;
                        echo "mysql -uroot -pvagrant < " . $cpwd."\n";
                        exec("mysql -uroot -pvagrant < " . $cpwd);
                    }
                }
                closedir($db_dir);
            }
        }
    }
    closedir($data_dir);
}
?>
