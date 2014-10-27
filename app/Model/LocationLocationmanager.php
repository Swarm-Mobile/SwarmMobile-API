<?php
App::uses( 'AppModel', 'Model' );

/**
 * Class LocationLocationmanager
 *
 * This is a reference table to relate Location to Location Managers
 *
 * @property $id integer
 * @property $location_id integer Primary Key of the Location Table
 * @property $employee_id integer Primary Key of the Employee Table
 * @property $ts_update string MySQL Timestamp
 */
class LocationLocationmanager extends AppModel {
	public $useDbConfig = 'backstage';
	public $useTable = 'locationmanager_location';
	public $id = 'id';

	public $belongsTo = [
		'LocationManager', 'Location'
	];
}