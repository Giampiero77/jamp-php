<?php
/**
* Class to manage the language
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class LANG
{
	public static $language = "";
	public static $load = null;
	public static $package = array();

	/**
	* Returns the text in the language set
	* @param string $code Code message
	* @param array $arg Arguments
	*/
	public static function translate($code, $arg = array())
	{
		if (self::$load != self::$language)
		{
			require("lang/".self::$language.".php");
			self::$load = self::$language;
		}
		if (isset(self::$package[$code])) $message = self::$package[$code];
		else
		{
 			$message = self::$package["UNDEFINED"];
			$arg = array($code);
		}
		foreach($arg as $key => $value)
		{
			$message = str_replace('$$'.$key.'$$', $value, $message);
		}
		return $message;
	}
}
?>