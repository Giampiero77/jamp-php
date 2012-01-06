<?php
/**
* PHP Source File
* @name JApp
* @author Alyx-Software Innovation <info@alyx.it>
* @version 1.0
* @copyright Alyx-Software Innovation 2008-2009
* @license GNU Public License
*/

require_once("./../class/system.class.php");
$system = new ClsSystem(true);
$xml 	 = new ClsXML("login.xml");
$event = new ClsEvent($xml);
$event->managerRequest();

function data_login()
{
 	global $event;
	if (LANGUAGE=='IT')
	{
		$LNG["errorinput1"] = "Inserire utente e password";
		$LNG["errorinput2"] = "Nome Utente o Password non validi!";
		$LNG["connjamp"] = "Errore la connessione jamp non esiste!!!";
	}
	else 
	{
		$LNG["errorinput1"] = "Insert user and password";
		$LNG["errorinput2"] = "User or password not correct!";
		$LNG["connjamp"] = "Error jamp connection not exists!!!";
	}
	if (empty($_POST["user"]) || empty($_POST["pwd"])) 
	{
		$event->setCodeJs("alert('".$LNG["errorinput1"]."')");
		return false;
	}
	$testconn = file("conn.inc.php");
	unset($testconn[0]);
	$conn = explode("|", trim($testconn[1]));
	if ($conn[0]!="jamp")
	{
		$event->setCodeJs("alert('".$LNG["connjamp"]."')");
		return false;
	}
	if ($conn[3]==$_POST["user"] && $conn[4]==$_POST["pwd"])
	{
		$_SESSION["jampadmin"] = true;
		$event->setCodeJs("window.location = 'index.php'");
	}
	else $event->setCodeJs("alert('".$LNG["errorinput2"]."')");
	return false;
}
?>