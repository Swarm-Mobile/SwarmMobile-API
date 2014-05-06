<?php
App::uses('APIComponent', 'Controller/Component');

class AnalyticsComponent extends APIComponent {
    
    public function temporaryTables(){
        $sSQL = <<<SQL
SELECT COUNT(*) 
FROM information_schema.tables 
WHERE table_name LIKE 'sessions\_%\_%'
SQL;
    }
    
    public function responseAvgTime(){
        $oModel = new Model(false, 'calls', 'mongodb');
        $command = <<<NOSQL
{
    group:
    {
        ns: 'calls',            
        cond: { modified: {\$gt: new Date()}},
        \$reduce: function( curr, result ) {
            result.total += curr.response_time;
            result.count++;
        },
        initial: { total : 0, count: 0 },
        finalize: function(result) {
            result.avg = result.total / result.count;
            delete result.total;
            delete result.count;
            delete result.date;
        }
    }
}                
NOSQL;
        $aRes = $oModel->query($command);
        var_dump($aRes);
    }
}