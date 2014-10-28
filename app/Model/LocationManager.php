<?php
App::uses( 'AppModel', 'Model' );

/**
 * Class LocationManager
 *
 * @property $id integer
 * @property $user_id integer
 * @property $firstname string
 * @property $lastname string
 * @property $phone_no
 * @property $ts_update
 */
class LocationManager extends AppModel {
	public $useDbConfig = 'backstage';
	public $useTable = 'locationmanager';
	public $displayField = 'firstname';
	public $id = 'id';

	public $hasMany = [
		'LocationLocationmanager' => [
			'className' => 'LocationLocationmanager',
			'foreignKey' => 'locationmanager_id'
		]
	];

} 