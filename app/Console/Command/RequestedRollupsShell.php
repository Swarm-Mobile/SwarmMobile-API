<?php

require_once(__DIR__ . '/../../Controller/ApiController.php');
require_once(__DIR__ . '/../../Controller/Component/DBComponent.php');
require_once(__DIR__ . '/RollupShell.php');
App::uses('APIComponent', 'Controller/Component');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');
App::uses('CakeEmail', 'Network/Email');

class RequestedRollupsShell extends AppShell
{

    public function mark_as_processed ($oRow)
    {
        $oDb  = DBComponent::getInstance('requested_rollups_queue', 'rollups');
        $sSQL = <<<SQL
INSERT INTO requested_rollups_processed
    SET location_id     = :location_id,
        start_date      = :start_date,        
        end_date        = :end_date,        
        override        = :override,        
        rebuild         = :rebuild,        
        reporter_email  = :reporter_email,        
        ts_queue        = :ts_queue,        
        ts              = NOW()
SQL;
        $oDb->query($sSQL, [
            ':location_id'    => $oRow['q']['location_id'],
            ':start_date'     => $oRow['q']['start_date'],
            ':end_date'       => $oRow['q']['end_date'],
            ':override'       => $oRow['q']['override'],
            ':rebuild'        => $oRow['q']['rebuild'],
            ':reporter_email' => $oRow['q']['reporter_email'],
            ':ts_queue'       => $oRow['q']['ts'],
        ]);
        $oDb->query('DELETE FROM requested_rollups_queue WHERE id = :id', [':id' => $oRow['q']['id']]);
    }

    public function send_email ($oRow)
    {
        $timestamp = date('Y-m-d H:i:s');
        $Email     = new CakeEmail();
        $Email->from(array ('info@swarm-mobile.com' => 'Info'));
        $Email->to($oRow['q']['reporter_email']);
        $Email->subject('Requested Rollup for Location #' . $oRow['q']['location_id'] . ' finished');
        $text      = <<<TEXT
Rollup Details:
Location # {$oRow['q']['location_id']}
Start Date {$oRow['q']['start_date']}
End Date {$oRow['q']['end_date']}
Override {$oRow['q']['override']}
Rebuild {$oRow['q']['rebuild']}

Time Requested: {$oRow['q']['ts']}
Time Processed: {$timestamp}
TEXT;
        $Email->send($text);
    }

    public function main ()
    {
        $this->setEnvironment();
        $oDb  = DBComponent::getInstance('requested_rollups_queue', 'rollups');
        $aRes = $oDb->fetchAll("SELECT * FROM requested_rollups_queue q");
        if (!empty($aRes)) {
            foreach ($aRes as $oRow) {
                $this->mark_as_processed($oRow);
                $oRollup                        = new RollupShell();
                $oRollup->params['location_id'] = $oRow['q']['location_id'];
                $oRollup->params['start_date']  = $oRow['q']['start_date'];
                $oRollup->params['end_date']    = $oRow['q']['end_date'];
                $oRollup->params['override']    = $oRow['q']['override'];
                $oRollup->params['rebuild']     = $oRow['q']['rebuild'];
                $oRollup->params['part']        = '1/1';
                $oRollup->main();
                $this->send_email($oRow);
            }
        }
    }

}
