<?php

App::uses('APIComponent', 'Controller/Component');
App::uses('Model', 'Model');

class LocationComponent extends APIComponent {

    /**
     * API Example method
     * @param $params Contains all the request params except access_token
     * @return array Contains the info that you like to return
     */
    public function example($params) {
        /*
         * Define the param's rules that needs
         * to match to continue with the request
         * 
         * Available Validators
         *  1. required => param needs to exist
         *  2. int      => param needs to be an int
         *  3. numeric  => params needs to be numeric
         *  4. date     => param needs to be yyyy-mm-dd format
         *  5. datetime => param needs to be yyyy-mm-dd H:i:s format
         * 
         * To create more validators, go to 
         * validate function into APIComponent
         */
        $rules = [
            'p1' => ['required', 'int'],
            'p2' => ['required', 'int'],
            'p3' => ['required', 'int'],
        ];
        $this->validate($params, $rules);

        /*
         * mytable is a table of the database that you like to use.
         * Can be any one, is just for create the connection
         * 
         * ds is one of the instances defined on /app/Config/database.php
         * Available instances:
         *  1. ee            => Expression Engine database
         *  2. swarmdata     => Sessions Data
         *  3. swarmdataRead => Read-Replica of swarmdata
         *  4. pos           => POS Info
         *  5. mongodb       => Saves aggregate data for StoreComponent (mongodb)
         *  6. consumerAPI   => Saves aggregate data for ConsumerAPIComponent (mongodb)
         *  7. oauth         => OAuth tokens and this stuff
         *  8. backstage     => Locations, resellers, campaigns...major instance
         *  9. portal        => Just created for visitorEvent service
         */

        //Make a query
        $oModel = new Model(false, 'mytable', 'ds');
        $oDb = $oModel->getDataSource();
        $sSQL = <<<SQL
SELECT 
    table.f1, 
    t2.f2, 
    COALESCE(f3, '34') as f3
FROM table
INNER JOIN table2 t2
WHERE a = :a
      b = :b
SQL;
        $binds = [
            ':a' => $a,
            ':b' => $b
        ];
        //This just execute the query (for inserts and updates)
        $oDb->query($sSQL, $binds);

        //If you like to get the result (no combine both lines, or one or the other)
        $aRes = $oDb->fetchAll($sSQL, $binds);
        /*
         * Working with the result
         * 
         * Possible the most tricky thing. Below
         * you have the 3 cases that you can have when you 
         * make a query. Once you understand the concept
         * isn't difficult, but is possible that most of your
         * early bugs and warnings will come from here.
         */
        foreach ($aRes as $oRow) {
            //Is a field that come from a table without alias
            $f1 = $oRow['table']['f1'];
            //Is a field that come from a table with alias
            $f2 = $oRow['t2']['f2'];
            //Is a calculated field
            $f3 = $oRow[0]['f3'];
        }

        //Use the ORM
        /*
         * For every model that you like to use,
         * add on the top of the file:
         * App::uses('MyModel', 'Model'); 
         */
        $oLocation = new Location();

        /*
         * Find the results that you need
         * The first param can be first, list or all
         * The second params are the conditions
         *      If some model have relationships with other
         *      models (belongsto, hasmany...) you can filter   
         *      also by they
         */
        $oLocation = $oLocation->find('first', ['conditions' => ['Location.id' => $location_id]]);

        /*
         * Once you have the results, you can access to the properties
         * using the syntax:
         * $obj['Model']['field']
         * 
         * If the type of search is list, you need to do something like:
         * $obj[$k]['Model']['field']
         * 
         * Type list is for get a list ready for put it on a select.
         * Can be useful sometimes and the return is like:
         *  [
         *      1 => 'Name 1',
         *      2 => 'Name 2'
         *  ]
         *      *keys are the ids and values the display_name var of the Model
         */
        $location_name = $oLocation['Location']['name'];


        /*
         * To finish the request, just return an array.
         * JSON encoding is make it for the API.
         */
        return [
            'foo' => 'bar',
            'example' => 'f1'
        ];
    }

    public function examplePOST($params) {
        /*
         * If all the Component is POST component,
         * there are ways to prevent to put the if
         * statement in every function. If that's
         * the case, tell us and we can make some
         * stuff on the specific components.
         */
        if ($this->api->request->is('post')) {
            return ['foo' => 'bar'];
        } else {
            throw new APIException(401, 'invalid_method', "Method type must be POST");
        }
    }

    public function exampleComposite($params) {
        /*
         * Sometimes you like to use an API call inside another one.
         * If that's the case, you can use internalCall.
         *      NOTE: $params should contain just the params that the call
         *            needs. Why? If you like to cache the request and you send
         *            all the params, you can create more than one cache for the
         *            same request because one of the fields that you're sending
         *            is different for the father request but this is unused for 
         *            the child request.
         */
        $result = $this->api->internalCall('mycomponent', 'myfunctions', $params);
        /*
         * Previous result is an array, not a JSON. JSON encoding is only make it
         * at the end of the request.
         */
        return $result;
    }

    public function whereAmI($params) {}  
    public function whatIsHere($params){}

}
