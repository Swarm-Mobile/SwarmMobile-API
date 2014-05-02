<?php
App::uses('Helper', 'View');

/**
 * String helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class StringHelper extends Helper {
	public function randomString($type = 'alnum', $len = 8) {
		switch($type) {
			case 'basic'	: return mt_rand(); 
			    break;
			case 'alnum'	:
			case 'numeric'	:
			case 'nozero'	:
			case 'alpha'	:

				switch ($type){
					case 'alpha'	:	$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'numeric'	:	$pool = '0123456789';
						break;
					case 'nozero'	:	$pool = '123456789';
						break;
				}

				$str = '';
				for ($i=0; $i < $len; $i++)
				{
					$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
				}
				return $str;
			break;
			case 'unique'	:
			case 'md5'		: return md5(uniqid(mt_rand()));
				break;
			case 'encrypt'	:
			case 'sha1'	: return sha1(uniqid(mt_rand(), TRUE));
				break;
		}
	}
}