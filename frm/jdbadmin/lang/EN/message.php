<?php
/**
* Source File PHP
* @name JdbAdmin
* @author Alyx-Software Innovation <info@alyx.it>
* @version 1.4
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
*/

class MESSAGE
{
	static $package = array(
	"UNDEFINED" 	=> "Undefined message",
	"MSG001" 		=> "I am sorry, in this version you cannot delete data",
	"MSG002" 		=> "Vuoi eliminare la riga?",
	"MSG003" 		=> "Do you truncate view?",
	"MSG004" 		=> "Select a Field (CTRL+Click Mouse)",
	"MSG005" 		=> "Select Foreign Keys (CTRL+Click Mouse)",
	"MSG006" 		=> "Select Reference Keys (CTRL+Click Mouse)",
	"MSG007" 		=> "Do you whant delete database grant?",
	"MSG008" 		=> "Invalid login!",
	"MSG009" 	 	=> "Do you truncate table?",
	"TABLES" 		=> "Tables",
	"VIEWS" 	 	=> "Views",
	"PROCEDURES" 	=> "Procedures",
	"FUNCTIONS"  	=> "Functions",
	);

	public static function translate($code)
	{
		if (isset(self::$package[$code])) $message = self::$package[$code];
		else
		{
 			$message = self::$package["UNDEFINED"];
			$arg = array($code);
		}
		return $message;
	}
}
?>