<?php
App::uses( 'AppModel', 'Model' );

/**
 * Class UserLocationReport
 *
 * @property $id integer Primary Key
 * @property $user_id boolean
 * @property $location_id integer
 * @property $daily boolean
 * @property $weekly boolean
 * @property $monthly boolean
 * @property $zero_highlights boolean
 * @property $ts_update string MySQL Timestamp
 */
class UserLocationReport extends AppModel {
	public $useDbConfig = 'backstage';
	public $useTable = 'user_location_report';
	public $id = 'id';
} 