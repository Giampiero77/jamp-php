<?php
	define("LANGUAGE", "IT");	// IT, EN
	define("TIMEZONE", "Europe/Rome");	// default_timezone
	define("TEMPLATE", "default");	// default template
	define("ERROR_LEVEL", 0);	// 0 - Error, 1 - Debug Error
	define("ERROR_REPORTING", E_ALL|E_STRICT);	// use these constant names in php.ini
	define("COMPRESSHTML", false);	// Compress html output
	define("COMPRESSXML", false);	// Compress data xml output
	define("COMPRESSJS", false);	// Compress javascript output
	define("NOCACHEPHP", true);	// true - No cache php, false - Browser/Proxy setting
	define("NOCACHEJS", "17012012");	// time(): No cache javascript, <constant>(es. 0505023232), false: Browser/Proxy setting
	define("NOCACHECSS", "17012012");	// time(): No chache CSS, <constant>(es. 0505023232), false: Browser/Proxy setting
	define("NOUPLOAD", false);			// true - disable UPLOAD, false enable upload
	define("GHOSTDATA", true);			// true - display action ghost message, false - no display action ghost message
	define("JAMP_FPDF_VERSION", "17");			// Version of fpdf to use
?>