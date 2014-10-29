<?php

require_once(__DIR__ . '/../../Controller/Component/DBComponent.php');
App::uses('EmailQueueComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');

class VisitorEventsAlertsShell extends AppShell
{

    public function main ()
    {
        $this->setEnvironment();
        $portalDb    = DBComponent::getInstance('visitorEvent', 'portal');

        $sSQL   = <<<SQL
SELECT ts_creation FROM visitorEvent ORDER BY ts_creation DESC LIMIT 1;         
SQL;
        $result = $portalDb->fetchAll($sSQL);
        $msg    = '';
        if(!empty($result) && is_array($result)) {
            if(!empty($result[0]['visitorEvent']['ts_creation'])) {
                $date = new DateTime('now', new DateTimeZone('GMT'));
                $currentTime = $date->format('Y-m-d H:i:s');
                $timeDiff = round(abs(strtotime($currentTime) - strtotime($result[0]['visitorEvent']['ts_creation'])) / 60, 2);
                if($timeDiff > 1) {
                    $msg .= "<p>Alert: No portal event seen for the last 10 mins.</p>";
                    $msg .= "<p>Last message seen at: " .$result[0]['visitorEvent']['ts_creation']. " GMT time</p>";
                }
            }
        }
        if (!empty($msg)) {
            $this->out("Email Send!");
            EmailQueueComponent::queueEmail(
                    'info@swarm-mobile.com', 'Info', 'jinesh.lalan@swarm-mobile.com', 'AM', 'Alert: Portal devices may not be syncing', $msg
            );
        } else {
            $this->out("Visitor Events working fine!");
        }
    }
}