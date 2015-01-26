<?php

require_once(__DIR__ . '/RollupShell.php');
App::uses('AppShell', 'Console/Command');
App::uses('Model', 'Model');
App::uses('CakeEmail', 'Network/Email');

class RequestedRollupsShell extends AppShell
{

    public function mark_as_processed ($row)
    {
        $processed = new Model(null, 'requested_rollups_processed', 'rollups');
        $data      = [
            'location_id'    => $row['location_id'],
            'start_date'     => $row['start_date'],
            'end_date'       => $row['end_date'],
            'override'       => $row['override'],
            'rebuild'        => $row['rebuild'],
            'reporter_email' => $row['reporter_email'],
            'ts_queue'       => $row['ts'],
            'ts'             => $row['ts'],
        ];
        $processed->save([$processed->alias => $data], false, array_keys($data));
        $queue     = new Model(null, 'requested_rollups_queue', 'rollups');
        $queue->deleteAll([$queue->alias . '.id' => $row['id']]);
    }

    public function send_email ($row)
    {
        $timestamp = date('Y-m-d H:i:s');
        $Email     = new CakeEmail();
        $Email->from(['info@swarm-mobile.com' => 'Info']);
        $Email->to($row['reporter_email']);
        $Email->subject('Requested Rollup for Location #' . $row['location_id'] . ' finished');
        $text      = <<<TEXT
Rollup Details:
Location # {$row['location_id']}
Start Date {$row['start_date']}
End Date {$row['end_date']}
Override {$row['override']}
Rebuild {$row['rebuild']}

Time Requested: {$row['ts']}
Time Processed: {$timestamp}
TEXT;
        $Email->send($text);
    }

    public function main ()
    {
        $this->setEnvironment();
        $queue     = new Model(null, 'requested_rollups_queue', 'rollups');
        $toProcess = $queue->find('all');
        if (!empty($toProcess)) {
            foreach ($toProcess as $requestRollup) {
                $row                           = $requestRollup[$queue->alias];
                $this->mark_as_processed($row);
                $rollup                        = new RollupShell();
                $rollup->params['location_id'] = $row['location_id'];
                $rollup->params['start_date']  = $row['start_date'];
                $rollup->params['end_date']    = $row['end_date'];
                $rollup->params['override']    = $row['override'];
                $rollup->params['rebuild']     = $row['rebuild'];
                $rollup->params['part']        = '1/1';
                $rollup->main();
                $this->send_email($row);
            }
        }
    }

}
