<?php 
/**
* PHP Source File
* @author	Alyx-Software Innovation <info@alyx.it>
* @version 1.0
* @copyright Alyx-Software Innovation 2012
* @license GNU Public License
*/

require_once("./../class/system.class.php");
$system = new ClsSystem(true);
$xml = new ClsXML("connection.xml");
$event = new ClsEvent($xml);
$event->managerRequest();

function html_load() 
{
	global $xml, $event;
	if (!isset($_SESSION["jampadmin"]))	header('Location: login.php');
	$errorjs = "Inserire il nome della connessione";
	$error = "Errore azione non specificata";
	$title = "CONNESSIONI";
	if (LANGUAGE=='EN')
	{
		$xml->getObjById("conn")->setProperty("label", "\\nConnection:\\n");
		$xml->getObjById("type")->setProperty("label", "\\nType:\\n");
		$xml->getObjById("user")->setProperty("label", "\\nUser:\\n");
		$xml->getObjById("port")->setProperty("label", "\\nPort:\\n");
		$xml->getObjById("button")->setProperty("value", "Confirm");
		$errorjs = "Insert connection name";
		$error = "Action is not specified";
		$title = "CONNECTIONS";
	}
	if (!isset($_REQUEST['action'])) die("Azione non settata");
	$xml->getObjById("title")->setProperty("value", $title);
	switch ($_REQUEST['action'])
	{
		case "new_connection":
			break;
		case "modify_connection":
		case "delete_connection":
				$conn = explode("|", $_REQUEST['row']);
				if ($conn[0]=="jamp")
				{
					$xml->getObjById("type")->setProperty("optionvalue", array("security"));
					$xml->getObjById("host")->setProperty("readonly", "true");
				}
				$xml->getObjById("conn")->setProperty("value", $conn[0]);
				$xml->getObjById("conn")->setProperty("readonly", "true");
				$xml->getObjById("type")->setProperty("value", $conn[1]);
				$xml->getObjById("host")->setProperty("value", $conn[2]);
				$xml->getObjById("user")->setProperty("value", $conn[3]);
				$xml->getObjById("pwd")->setProperty("value", $conn[4]);
				$xml->getObjById("port")->setProperty("value", $conn[5]);
				$xml->getObjById("button")->setProperty("onclick", "Confirm('save_connection');");
			break;
		default:
			die($error);
	}
	$code = "
	function Confirm()
	{
		if (REGEXP.errorpage==true) return;
		if ($('conn').value.length<1) 
		{
			SYSTEMEVENT.showHTML('Errore', '$errorjs');
			return;
		}
		var string = $('conn').value+'|'+$('type').value+'|'+$('host').value+'|'+$('user').value+'|'+$('pwd').value+'|'+$('port').value;
		AJAX.request('POST', 'connection.php', 'data=".$_REQUEST['action']."&string='+encodeURIComponent(string), false, true);
	}";
	$event->setCodeJs($code);
}

function fwrite_stream($lines, $type) 
{
	global $event;
	$file = fopen('../conf/conn.inc.php', $type);
	foreach ($lines as $line) if (trim($line)!="") fwrite($file, $line);
	fclose($file);
	$code  = "window.parent.location.href = 'index.php';";
	$code .= "window.parent.LIGHTBOX.end();";
	$event->setCodeJs($code);
}

function new_connection() 
{
	if (LANGUAGE=='IT') $error = "Nome connessione esistente!!!";
	else $error = "Connection exist !!!";
	$newconn = explode("|", $_REQUEST['string']); 
	$lines = file("../conf/conn.inc.php");
	foreach ($lines as $line_num => $line)
	{
		$conn = explode("|", $line);
		if ($conn[0]==$newconn[0]) die($error);
	}
	fwrite_stream(array(PHP_EOL.$_REQUEST['string']), 'a');
}

function modify_connection() 
{
	$modconn = explode("|", $_REQUEST['string']); 
	$lines = file("../conf/conn.inc.php");
	foreach ($lines as $line_num => $line)
	{
		$conn = explode("|", $line);
		if ($conn[0]==$modconn[0]) $lines[$line_num] = $_REQUEST['string'].PHP_EOL;
	}
	fwrite_stream($lines, 'w');
}

function delete_connection() 
{
	$newconn = explode("|", $_REQUEST['string']); 
	$lines = file("../conf/conn.inc.php");
	foreach ($lines as $line_num => $line)
	{
		$conn = explode("|", $line);
		if ($conn[0]==$newconn[0]) unset($lines[$line_num]);
	}
	fwrite_stream($lines, 'w');
}
?>