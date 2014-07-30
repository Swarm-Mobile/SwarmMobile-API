<?php

App::uses('CakeEmail', 'Network/Email');

class EmailQueueComponent {

    public static function queueEmail($from_email, $from_name, $to_email, $to_name, $subject, $message) {
        $oDb = DBComponent::getInstance('email_queue', 'backstage');
        $sSQL = <<<SQL
INSERT INTO email_queue
    SET from_email    = :from_email,
        from_name     = :from_name,
        to_email      = :to_email,
        to_name       = :to_name,
        subject       = :subject,
        message       = :message,
        ts            = NOW()                
SQL;
        $oDb->query($sSQL, [
            ':from_email' => $from_email,
            ':from_name' => $from_name,
            ':to_email' => $to_email,
            ':to_name' => $to_name,
            ':subject' => $subject,
            ':message' => $message,
                ]
        );
    }

    public static function process() {
        $oDb = DBComponent::getInstance('email_queue', 'backstage');
        $sSQL = "SELECT * FROM email_queue e";
        $aRes = $oDb->fetchAll($sSQL);
        foreach ($aRes as $oRow) {
            try {
                $sSQL = <<<SQL
INSERT INTO email_queue_processed
    SET from_email    = :from_email,
        from_name     = :from_name,
        to_email      = :to_email,
        to_name       = :to_name,
        subject       = :subject,
        message       = :message,
        ts_queue      = :ts_queue,
        ts            = NOW()                
SQL;
                $oDb->query($sSQL, [
                    ':from_email' => $oRow['e']['from_email'],
                    ':from_name' => $oRow['e']['from_name'],
                    ':to_email' => $oRow['e']['to_email'],
                    ':to_name' => $oRow['e']['to_name'],
                    ':subject' => $oRow['e']['subject'],
                    ':message' => $oRow['e']['message'],
                    ':ts_queue' => $oRow['e']['ts'],
                        ]
                );
                $oDb->query("DELETE FROM email_queue WHERE id = :id", [':id' => $oRow['e']['id']]);                
                $oEmail = new CakeEmail('smtp');
                $oEmail->emailFormat("html");
                $oEmail->subject($oRow['e']['subject']);
                $oEmail->from([$oRow['e']['from_email']=>$oRow['e']['from_name']]);                
                $oEmail->to([$oRow['e']['to_email']=>$oRow['e']['to_name']]);                
                $oEmail->send($oRow['e']['message']);           
                echo 'ok';     
            } catch (Exception $e) {                
                echo 'ko';
                $oEmail = new CakeEmail('smtp');
                $oEmail->emailFormat("html");
                $oEmail->from(['info@swarm-mobile.com'=>'Info']);
                $oEmail->subject('Error Occur sending email');
                $oEmail->to(['dev@swarm-mobile.com'=>'Dev']);
                $message = <<<EMAIL
An error ocurred when the email queue try to send the next email
FROM    : <{$oRow['e']['from_email']}>{$oRow['e']['from_name']}
TO      : <{$oRow['e']['to_email']}>{$oRow['e']['to_name']}
SUBJECT : {$oRow['e']['subject']} 
MESSAGE : 
{$oRow['e']['message']}

EMAIL;
                $oEmail->send($message);
            }
        }
    }

}
