<?php
/**
* Unified Class to manage all user events (functions and/or class methods)
* @author	Fulvio Alessio <afulvio@gmail.com>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class userEvent {
	static private $classObject = null;

	/*
	 * set Class Object
	 */
	static public function setClassObject($obj) {
		self::$classObject = $obj;
	}

	/*
	 * User calls
	 */
	static public function call($event, $param1=null, $param2=null) {
		if (is_object(self::$classObject)) {
			return call_user_func(array(self::$classObject, $event), $param1, $param2);
		} else {
			if(function_exists($event)) {
				return call_user_func($event, $param1, $param2);
			} else {
				return true;
			}
		}
	}
}