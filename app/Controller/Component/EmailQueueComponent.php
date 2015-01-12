<?php

App::uses('CakeEmail', 'Network/Email');

class EmailQueueComponent
{

    public static function queueEmail ($from_email, $from_name, $to_email, $to_name, $subject, $message)
    {
        $model = new Model(null, 'email_queue', 'backstage');
        $data  = [
            'from_email' => $from_email,
            'from_name'  => $from_name,
            'to_email'   => $to_email,
            'to_name'    => $to_name,
            'subject'    => $subject,
            'message'    => $message
        ];
        $model->save([$model->alias => $data], false, array_keys($data));
    }

    public static function process ()
    {
        $queue     = new Model(null, 'email_queue', 'backstage');
        $toProcess = $queue->find('all');
        if (!empty($toProcess)) {
            foreach ($toProcess as $row) {
                $r = &$row[$queue->alias];
                try {
                    $processed = new Model(null, 'email_queue_processed', 'backstage');
                    $data      = [
                        'from_email' => $r['from_email'],
                        'from_name'  => $r['from_name'],
                        'to_email'   => $r['to_email'],
                        'to_name'    => $r['to_name'],
                        'subject'    => $r['subject'],
                        'message'    => $r['message'],
                        'ts_queue'   => $r['ts'],
                    ];
                    $processed->save([$processed->alias => $data], false, array_keys($data));
                    $queue->delete($row[$queue->alias]['id']);
                    $oEmail    = new CakeEmail('smtp');
                    $oEmail->emailFormat("html");
                    $oEmail->subject($r['subject']);
                    $oEmail->from([$r['from_email'] => $r['from_name']]);
                    $oEmail->to([$r['to_email'] => $r['to_name']]);
                    $oEmail->send($r['message']);
                }
                catch (Exception $e) {
                    $oEmail  = new CakeEmail('smtp');
                    $oEmail->emailFormat("html");
                    $oEmail->from(['info@swarm-mobile.com' => 'Info']);
                    $oEmail->subject('Error Occur sending email');
                    $oEmail->to(['dev@swarm-mobile.com' => 'Dev']);
                    $message = <<<EMAIL
An error ocurred when the email queue try to send the next email
FROM    : <{$r['from_email']}>{$r['from_name']}
TO      : <{$r['to_email']}>{$r['to_name']}
SUBJECT : {$r['subject']} 
MESSAGE : 
{$r['message']}
EMAIL;
                    $oEmail->send($message);
                }
            }
        }
    }

}
