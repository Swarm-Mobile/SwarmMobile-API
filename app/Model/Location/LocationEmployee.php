<?php
App::uses( 'AppModel', 'Model' );

/**
 * Class LocationEmployee
 *
 * This is a reference table to relate Location to Employees
 *
 * @property $id integer
 * @property $location_id integer Primary Key of the Location Table
 * @property $employee_id integer Primary Key of the Employee Table
 * @property $ts_update string MySQL Timestamp
 */
class LocationEmployee extends AppModel {
    public $actsAs = ['Containable'];
    public $useDbConfig = 'backstage';
    public $useTable = 'location_employee';
    public $id = 'id';
}