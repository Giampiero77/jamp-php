<?php 
/**
* PHP Source File
* @author	Alyx-Software Innovation <info@alyx.it>
* @version	Factory
* @copyright Alyx-Software Innovation 2008
* @license GNU Public License
*/

require_once("./../class/system.class.php");
$system = new ClsSystem(true);
$xml = new ClsXML("setting.xml");
$event = new ClsEvent($xml);
$event->managerRequest();

function html_load() 
{
	global $xml, $event;
	if (LANGUAGE=='EN')  
	{
		$title = "JAMP SETTING"; 
		$errorjs = "Insert connection name";
		$xml->getObjById("button")->setProperty("value", "Confirm");
	}
	else 
	{
		$title = "IMPOSTAZIONI DI JAMP";
		$errorjs = "Inserire il nome della connessione";
		$xml->getObjById("button")->setProperty("value", "Conferma");
	}
	if (!isset($_SESSION["jampadmin"]))	header('Location: login.php');
	$xml->getObjById("title")->setProperty("value", $title);

	$file = file("../conf/setting.inc.php");
	$i = 0;
	foreach($file as $row)
	{
		if ($pos = strpos($row, "define(") !== false)
		{
			list($constant[$i], $value) = explode(',', $row);
			$constant[$i] = substr(trim($constant[$i]),8,-1);
			list($value) = explode(');', $value);
			$value = trim(str_replace('"', "", $value));
 			$xml->getObjById($constant[$i])->setProperty("value", $value);
			$i++;
		}
	}
	$code = "
	function Confirm()
	{
		if (REGEXP.errorpage==true) return;
		var string = '';\n";
		foreach($constant as $key => $const) $code .= "\t\tstring += '&$const=' + encodeURIComponent($('$const').value);\n";
		$code .= "\t\tAJAX.request('POST', 'setting.php', 'data=modify_setting'+string, false, true);";
 	$code .= "}";
	$event->setCodeJs($code);
}

function fwrite_stream($lines) 
{
	global $event;
	$file = fopen('../conf/setting.inc.php', "w");
	foreach ($lines as $line) fwrite($file, $line);
	fclose($file);
	$code  = "window.parent.location.href = 'index.php';";
	$code .= "window.parent.LIGHTBOX.end();";
	$event->setCodeJs($code);
}

function modify_setting() 
{
	$lines[] = "<?php\n";
	$lines[] = "\tdefine(\"LANGUAGE\", \"".$_POST['LANGUAGE']."\");\t// IT, EN".PHP_EOL;
	$lines[] = "\tdefine(\"TIMEZONE\", \"".$_POST['TIMEZONE']."\");\t// default_timezone".PHP_EOL;
	$lines[] = "\tdefine(\"TEMPLATE\", \"".$_POST['TEMPLATE']."\");\t// default template".PHP_EOL;
	$lines[] = "\tdefine(\"ERROR_LEVEL\", ".$_POST['ERROR_LEVEL'].");\t// 0 - Error, 1 - Debug Error".PHP_EOL;
	$lines[] = "\tdefine(\"ERROR_REPORTING\", ".$_POST['ERROR_REPORTING'].");\t// use these constant names in php.ini".PHP_EOL;
	$lines[] = "\tdefine(\"COMPRESSHTML\", ".$_POST['COMPRESSHTML'].");\t// Compress html output".PHP_EOL;
	$lines[] = "\tdefine(\"COMPRESSXML\", ".$_POST['COMPRESSXML'].");\t// Compress data xml output".PHP_EOL;
  $lines[] = "\tdefine(\"COMPRESSJS\", ".$_POST['COMPRESSJS'].");\t// Compress javascript output".PHP_EOL;
	$lines[] = "\tdefine(\"NOCACHEPHP\", ".$_POST['NOCACHEPHP'].");\t// true - No cache php, false - Browser/Proxy setting".PHP_EOL;
	$lines[] = "\tdefine(\"NOCACHEJS\", ".$_POST['NOCACHEJS'].");\t// time(): No cache javascript, <constant>(es. 0505023232), false: Browser/Proxy setting".PHP_EOL;
	$lines[] = "\tdefine(\"NOCACHECSS\", ".$_POST['NOCACHECSS'].");\t// time(): No chache CSS, <constant>(es. 0505023232), false: Browser/Proxy setting".PHP_EOL;
	$lines[] = "\tdefine(\"NOUPLOAD\", ".$_POST['NOUPLOAD'].");			// true - disable UPLOAD, false enable upload".PHP_EOL;
	$lines[] = "\tdefine(\"GHOSTDATA\", ".$_POST['GHOSTDATA'].");			// true - display action ghost message, false - no display action ghost message".PHP_EOL;
	$lines[] = "?>";
	fwrite_stream($lines);
}
?>
