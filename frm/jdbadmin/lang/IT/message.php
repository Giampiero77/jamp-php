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
	"UNDEFINED"  	=> "Messaggio non definito",
	"MSG001" 	 	=> "Mi dispiace, ma in questa versione non si possono eliminare i dati",
	"MSG002" 	 	=> "Vuoi eliminare la riga?",
	"MSG003" 	 	=> "Vuoi svuotare la vista?",
	"MSG004" 	 	=> "Seleziona il Campo (CTRL+Click Mouse)",
	"MSG005" 	 	=> "Seleziona la Foreign Keys (CTRL+Click Mouse)",
	"MSG006" 	 	=> "Seleziona la Reference Keys (CTRL+Click Mouse)",
	"MSG007" 		=> "Vuoi eliminare i permessi del database?",
	"MSG008" 	 	=> "Login non valido!",
	"MSG009" 	 	=> "Vuoi svuotare la tabella?",
	"TABLES" 	 	=> "Tabelle",
	"VIEWS"			=> "Viste",
	"PROCEDURES"	=> "Procedure",
	"FUNCTIONS"		=> "Funzioni",
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