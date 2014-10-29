<?php
App::uses('AppModel', 'Model');

/**
 * Class Employee
 *
 * @property $id integer
 * @property $user_id integer
 * @property $firstname string
 * @property $lastname string
 * @property $ts_update string MySQL Timestamp
 */
class Employee  extends AppModel {
	public $useDbConfig = 'backstage';
	public $useTable = 'employee';
	public $displayField = 'lastname';
	public $id = 'id';

	public $hasMany = 'LocationEmployee';

}