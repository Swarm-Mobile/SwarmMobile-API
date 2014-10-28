<?php

App::uses( 'APIController', 'Controller' );
App::uses( 'ApiComponent', 'Controller/Component' );
App::uses( 'UserType', 'Model' );
App::uses( 'LocationManager', 'Model' );
App::uses( 'Employee', 'Model' );
App::uses( 'LocationEmployee', 'Model' );
App::uses( 'LocationLocationmanager', 'Model' );

class UserController extends APIController {

	/**
	 * Create a new User and Location Manager record from POST
	 * @return CakeResponse Returns response object with JSON already set in the body and status code
	 */
	public function register() {
		$user               = new User();
		$locationManager    = new LocationManager();
		$newUser            = false;
		$newLocationManager = false;

		$user->create( $this->request->data, true );

		if ( $user->validates() ) {
			// Generate Password Hash
			$password = $user->hash_password( $this->request->data( 'password' ) );
			$user->set( 'uuid', uniqid() );
			$user->set( 'salt', $password['salt'] );
			$user->set( 'password', $password['password'] );

			// Generate the confirmation password also to check hashing is working correctly
			$confirmPasswordHash = $user->hash_password( $this->request->data['confirmPassword'], $password['salt'] );
			$user->set( 'confirmPassword', $confirmPasswordHash['password'] );

			$user->getDataSource()->begin(); // Start a new transaction
			$newUser = $user->save();
			if ( $newUser ) {
				// Create an entry for LocationManager
				$locationManager->set( 'user_id', $user->id );
				$locationManager->set( 'firstname', $this->request->data( 'firstname' ) );
				$locationManager->set( 'lastname', $this->request->data( 'lastname' ) );
				$newLocationManager = $locationManager->save();
			}

			if ( $newUser && $newLocationManager ) {
				$user->getDataSource()->commit();
				$result = array(
					'data'    => array(
						'uuid'               => $user->uuid,
						'user_id'            => $user->id,
						'locationmanager_id' => $locationManager->id
					),
					'options' => array(
						'endpoint' => '/user/' . __FUNCTION__,
					),
					'message' => array(
						'success' => 'User has been successfully created.'
					)
				);

				return new CakeResponse(
					array(
						'status' => 201,
						'body'   => json_encode( $result ),
						'type'   => 'json'
					)
				);
			} else {
				$user->getDataSource()->rollback();
				$user->validationErrors[] = 'There was an issue persisting the request. Please try again later';
			}
		}

		return new CakeResponse(
			array(
				'status' => 422,
				'body'   => json_encode( $user->validationErrors ),
				'type'   => 'json'
			)
		);

	}

	/**
	 *
	 * @return CakeResponse Returns response object with JSON already set in the body and status code
	 */
	public function login() {
		$params = $this->request->data;
		if ( empty( $params['username'] ) || empty( $params['password'] ) ) {
			return new CakeResponse(
				array(
					'status' => 401,
					'body'   => 'Username and Password are required and cannot be empty',
					'type'   => 'json'
				)
			);
		}
		$oUser = new User();
		if ( $user = $oUser->authenticate( $params['username'], $params['password'] ) ) {
			$results['data']              = array(
				'user_id'     => $user['id'],
				'uuid'        => $user['uuid'],
				'usertype_id' => $user['usertype_id'],
			);
			$results['data']['locations'] = $this->locations( $user['uuid'], true );
			$results['options']           = array(
				'endpoint' => '/user/' . __FUNCTION__,
				'username' => $user['username']
			);
			$results['message']           = array(
				'success' => 'User login successful.'
			);
			$status                       = 200;
		} else {
			$status  = 401;
			$results = [ 'error' => 'Invalid Credentials Supplied' ];
		}

		return new CakeResponse( [
			'status' => $status,
			'body'   => json_encode( $results ),
			'type'   => 'json'
		] );
	}

	/**
	 * Get User info
	 *
	 * @param Array get data
	 *
	 * @return Array
	 */
	public function getSettings() {

		$uuid = $this->request->query['uuid'];
		if ( empty( $uuid ) ) {
			return new CakeResponse( [
				'status' => 404,
				'body'   => json_encode( [ 'error' => 'User not found. Please provide a valid UUID.' ] ),
				'type'   => 'json'
			] );
		}
		$userModel  = new User();
		$userBundle = $userModel->find(
			'first',
			[
				'conditions' => [ 'uuid' => $uuid ],
			] );

		if ( empty( $userBundle ) ) {
			return new CakeResponse( [
				'status' => 404,
				'body'   => json_encode( [ 'error' => 'User not found with supplied UUID' ] ),
				'type'   => 'json'
			] );
		} else {
			$emailPreferences = $userBundle['UserLocationReport'];
			$user             = $userBundle['User'];
		}

		$ret['data'] = array(
			'username' => $user['username'],
			'email'    => $user['email']
		);


		switch ( $user['usertype_id'] ) {
			case UserType::$LOCATION_MANAGER:
				if ( ! empty( $userBundle['LocationManager'] ) && ! empty( $userBundle['LocationManager']['id'] ) ) {
					$ret['data']['firstname'] = $userBundle['LocationManager']['firstname'];
					$ret['data']['lastname']  = $userBundle['LocationManager']['lastname'];
				}

				$locations = [ ];
				try {
					$locations = $this->_getLocations( $user['usertype_id'], $user['id'] );
				} catch ( Exception $e ) {
					// On this call we will still allow failures through but should prob be logged somewhere?
				}
				$ret['data']['locations'] = $locations;
				break;
			case UserType::$EMPLOYEE:
				if ( ! empty( $userBundle['Employee'] ) && ! empty( $userBundle['Employee']['id'] ) ) {
					$ret['data']['firstname'] = $userBundle['Employee']['firstname'];
					$ret['data']['lastname']  = $userBundle['Employee']['lastname'];
				}

				$locations = [ ];
				try {
					$locations = $this->_getLocations( $user['usertype_id'], $user['id'] );
				} catch ( Exception $e ) {
					// On this call we will still allow failures through but should prob be logged somewhere?
				}
				$ret['data']['locations'] = $locations;
				break;
		}
		$ret['data']['uuid']    = $uuid;
		$ret['data']['user_id'] = $user['id'];
		if ( ! empty( $emailPreferences ) ) {
			foreach ( $emailPreferences as $locationPref ) {
				$str = '';
				if ( $locationPref['daily'] ) {
					$str .= 'daily,';
				}
				if ( $locationPref['weekly'] ) {
					$str .= 'weekly,';
				}
				if ( $locationPref['monthly'] ) {
					$str .= 'monthly,';
				}
				if ( empty( $str ) ) {
					$str .= 'none';
				} else {
					$str = substr( $str, 0, - 1 );
				}
				$ret['data']['emailPrefs'][ $locationPref['location_id'] ] = $str;
			}
		}
		$ret['options'] = array(
			'endpoint' => '/user/' . __FUNCTION__,
			'uuid'     => $uuid,
		);

		return new CakeResponse( [
			'status' => 200,
			'body'   => json_encode( $ret ),
			'type'   => 'json'
		] );
	}

	/**
	 * Update user data
	 *
	 */
	public function updateSettings() {
		$params = $this->request->data;

		if ( empty( $params['uuid'] ) ) {
			return new CakeResponse( [
				'status' => 404,
				'body'   => json_encode( [ 'error' => 'User not found. Please provide a valid UUID.' ] ),
				'type'   => 'json'
			] );
		}

		$userModel = new User();

		$user = $userModel->find( 'first', [
			'recursive'  => - 1,
			'conditions' => [
				'uuid' => $params['uuid']
			]
		] );

		if ( count( $user ) !== 1 ) {
			return new CakeResponse( [
				'status' => 404,
				'body'   => json_encode( [ 'error' => 'User not found. Please provide a valid UUID.' ] ),
				'type'   => 'json'
			] );
		}

		switch ( $user['User']['usertype_id'] ) {
			case UserType::$LOCATION_MANAGER:
				$assocUserModelClassname = 'LocationManager';
				break;
			case UserType::$EMPLOYEE:
				$assocUserModelClassname = 'Employee';
				break;
			default:
				$assocUserModelClassname = false;
		}

		// Validate the User model first, then validate the associated model if it passes
		$userModel->set( $user['User'] );
		$userModel->set( $params );

		if ( ! $userModel->validates() ) {
			return new CakeResponse( [
				'status' => 422,
				'body'   => json_encode( [
					'error'  => 'User data doesnt pass validation',
					'errors' => $userModel->validationErrors
				] ),
				'type'   => 'json'
			] );
		}

		if ( $assocUserModelClassname &&
		     class_exists( $assocUserModelClassname )
		     && ! empty( $user[ $assocUserModelClassname ] )
		) {
			/** @var Model $assocUserModel */
			$assocUserModel = new $assocUserModelClassname;
			$assocUserModel->set( $user[ $assocUserModelClassname ] );
			$assocUserModel->set( $params );

			if ( ! $assocUserModel->validates() ) {
				return new CakeResponse( [
					'status' => 422,
					'body'   => json_encode( [
						'error'  => 'User data doesnt pass validation',
						'errors' => $assocUserModel->validationErrors
					] ),
					'type'   => 'json'
				] );
			}
		} else {
			$assocUserModel = false;
		}


		// If we got this far everything has validated so save the data
		$userModel->set( 'ts_update', date( 'Y-m-d H:i:s' ) );
		$userModel->getDataSource()->begin();
		try {
			$userModel->save( null, false, [ 'username', 'email', 'usertype_id', 'ts_update' ] );

			if ( $assocUserModel ) {
				$assocUserModel->save( null, false, [ 'firstname', 'lastname' ] );
			}
			$userModel->getDataSource()->commit();
		} catch ( Exception $e ) {
			$userModel->getDataSource()->rollback();
			return new CakeResponse( [
				'status' => 422,
				'body'   => json_encode( [
					'error' => 'There was an error processing your request. Please try again or contact support',
				] ),
				'type'   => 'json'
			] );
		}

		return new CakeResponse( [
			'status' => 202,
			'body'   => json_encode( [
				array(
					'message' => array(
						'success' => 'User data has been successfully saved.'
					),
					'options' => array(
						'endpoint' => '/user/' . __FUNCTION__,
						'uuid'     => $params['uuid'],
					)
				)
			] ),
			'type'   => 'json'
		] );

	}

	/**
	 * Update user password
	 *
	 * @param Array POST
	 *
	 * @return Array
	 */
	public function updatePassword( $params ) {
		if ( empty( $params['uuid'] ) ) {
			throw new Exception( 'User not found. Please provide a valid UUID.' );
		}
		if ( empty( $params['currentPassword'] ) ) {
			throw new Exception( 'Current password provided does not match with the password in our records.' );
		}
		if ( empty( $params['password'] ) || empty( $params['confirmPassword'] ) || ( $params['password'] != $params['confirmPassword'] ) ) {
			throw new Exception( 'Password and confirmPassword do not match.' );
		}
		if ( strlen( $params['password'] ) < 5 ) {
			throw new Exception( 'Password must be atleast 5 characters long.' );
		}
		$user = $this->getUserFromUUID( $params['uuid'] );
		if ( empty( $user ) ) {
			throw new Exception( 'User not found. Please provide a valid UUID.' );
		}

		$oUser   = new User();
		$current = $oUser->hash_password( $params['currentPassword'], $user[0]['user']['salt'] );
		if ( $user[0]['user']['password'] != $current['password'] ) {
			throw new Exception( 'Current password provided does not match with the password in our records.' );
		}

		$password = $oUser->hash_password( $params['password'] );
		$oDb      = DBComponent::getInstance( 'user', 'backstage' );
		$sSQL     = <<<SQL
UPDATE  user
    SET `salt`=:salt,
        `password`=:password
    WHERE uuid=:uuid
SQL;
		$oDb->query( $sSQL, array(
				':salt'     => $password['salt'],
				':password' => $password['password'],
				':uuid'     => $params['uuid']
			)
		);
		$ret = array(
			'message' => array(
				'success' => 'Password updated successfully.'
			),
			'options' => array(
				'endpoint' => '/user/' . __FUNCTION__,
				'uuid'     => $params['uuid'],
			)
		);

		return $ret;
	}

	/**
	 * Get locations associated to a user
	 *
	 * @param int $uuid The UUID associated witht the user
	 *
	 * @return CakeResponse
	 */
	public function locations( $uuid ) {
		if ( empty( $uuid ) ) {
			return new CakeResponse( [
				'status' => 401,
				'body'   => json_encode( [ 'error' => 'User not found. Please provide a valid UUID.' ] ),
				'type'   => 'json'
			] );
		}

		$userModel = new User();
		$user      = $userModel->find( 'first', array(
			'recursive'  => - 1,
			'conditions' => array(
				'User.uuid' => $uuid
			)
		) );

		if ( empty( $user ) ) {
			return new CakeResponse( [
				'status' => 404,
				'body'   => json_encode( [ 'error' => 'User not found. Please provide a valid UUID.' ] ),
				'type'   => 'json'
			] );
		} else {
			$user = $user['User'];
		}

		try {
			$locations = $this->_getLocations( $user['usertype_id'], $user['id'] );
		} catch ( InvalidArgumentException $ie ) {
			return new CakeResponse( [
				'status' => 400,
				'body'   => json_encode( [ 'error' => $ie->getMessage() ] ),
				'type'   => 'json'
			] );
		} catch ( Exception $e ) {
			return new CakeResponse( [
				'status' => 500,
				'body'   => json_encode( [ 'error' => $e->getMessage() ] ),
				'type'   => 'json'
			] );
		}


		$res['data']['locations'] = $locations;
		$res['options']           = array(
			'endpoint' => '/user/' . __FUNCTION__,
			'uuid'     => $uuid
		);


		return new CakeResponse( [
			'status' => 200,
			'body'   => json_encode( $res ),
			'type'   => 'json'
		] );
	}

	/**
	 * Internal function to get the locations associated with a user
	 *
	 * @param $usertype_id
	 * @param $user_id
	 *
	 * @return array of Locations
	 * @throws InvalidArgumentException If the usertype is wrong or empty
	 *
	 */
	protected function _getLocations( $usertype_id, $user_id ) {

		if ( empty( $usertype_id ) || ! in_array( $usertype_id, [
				UserType::$LOCATION_MANAGER,
				UserType::$EMPLOYEE
			] )
		) {
			throw new InvalidArgumentException( 'You need to be a location manager or an employee to have locations associated to you.' );
		}

		$locations = [ ];
		switch ( $usertype_id ) {
			case UserType::$LOCATION_MANAGER:
				$locationLocationManagerModel = new LocationLocationmanager();
				$locationsBundle              = $locationLocationManagerModel->find(
					'all',
					[
						'conditions' =>
							[ 'user_id' => $user_id ],
					] );

				if ( ! empty( $locationsBundle ) ) {
					foreach ( $locationsBundle as $locationBundle ) {
						if ( ! empty( $locationBundle ) && ! empty( $locationBundle['Location']['id'] ) ) {
							$locations[] = $locationBundle['Location'];
						}
					}
				}
				break;
			case UserType::$EMPLOYEE:
				$locationEmployeeModel = new LocationEmployee();
				$locationsBundle       = $locationEmployeeModel->find( 'all', [ 'conditions' => [ 'employee_id' => $user_id ] ] );
				if ( ! empty( $locationsBundle ) ) {
					foreach ( $locationsBundle as $locationBundle ) {
						if ( ! empty( $locationBundle ) && ! empty( $locationBundle['Location']['id'] ) ) {
							$locations[] = $locationBundle['Location'];
						}
					}
				}

				return $locations;
				break;
		}

		return $locations;

	}
} 