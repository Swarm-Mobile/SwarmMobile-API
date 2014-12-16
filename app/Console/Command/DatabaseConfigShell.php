<?php

require_once(__DIR__ . '/../../Controller/Component/S3FactoryComponent.php');
App::uses('AppShell', 'Console/Command');
App::uses('S3Factory' , 'Controller/Component');

class DatabaseConfigShell extends AppShell
{
    public function main ()
    {
        $this->setEnvironment();
        $filepath = '/var/app/ondeck/app/Config/database.php';
        $s3factory = new S3FactoryComponent(new ComponentCollection());
        $s3Client  = $s3factory->loadS3();
        $ret = $s3Client->getObject(array(
                'Bucket'        => 'data-api-configs',
                'Key'           => 'database.php',
                'SaveAs'        => $filepath
            )
        );
        if(empty($ret)) {
            $this->out("Failed to download the config file.");
        } else {
            shell_exec("chown -R webapp:webapp $filepath");
            $this->out("Download successfull.");
        }
    }
}
