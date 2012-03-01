<?php	
/**
* @name 	Test File
* @author	Alyx-Software Innovation <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright Alyx-Software Innovation 2008-2009
* @license GNU Public License
* You can find documentation and source to the official website of JAMP
* http://jamp.alyx.it/
*/
require_once("../class/system.class.php");
$system = new ClsSystem(true);

$xml   = new ClsXML("index.xml");
$event = new ClsEvent($xml);

$event->managerRequest();

global $error;
// function before_exception_error($exception) { global $error; $error = $exception->param['message']; return false; };
// function after_exception_error()  { return false; };
// function before_error() { global $error; $error = ClsError::$param['message']; return false; };
// function after_error()  { return false; };

function testExtension($name)
{
	switch($name)
	{
		case "filesystem":
		case "record":
		case "csv":
		case "xml":		
		case "sms":		
			return -1;
		break;
		
		case "ssh":
			return (int)function_exists('ssh2_connect');
		break;

		case "gd":
		case "pgsql":
		case "odbc":
		case "zlib":		
		case "mysql":
		case "mssql":
		case "ldap":
		case "ftp":
			return (int)extension_loaded($name);
		break;

		default:
			return -2;
	}
	return false;
}

function checkExtension($name, $LNG)
{
	$require = $code = "";
	$isload = testExtension($name);
	if ($isload==1) $status = "<img src=\"test/success.png\" alt=\"OK\">";
	else if ($isload==0)
	{
		$require = $LNG['ext'];
		$status = "<img src=\"test/alert.png\" alt=\"ALERT\">";
	}
	if (!empty($status))
	{
		$code  = "\n\t<tr>";
		$code .= "\n\t\t<td nowrap>$status</td><td width=\"200\"><b>$name</b></td>";
		$code .= "\n\t\t<td nowrap>$require</td>";
		$code .= "\n\t</tr>";
	}
	return $code;
}

function testVer($name, $ver, $ok, $alert, $fail)
{
	$require = "";
	$intver = intval($ver);
	if ($intver >= $ok) $status = "<img src=\"test/success.png\" alt=\"OK\">";
	else 
	{
		$require = "Recommended version $ok or higher";
		if (LANGUAGE=="IT") $require = "Versione raccomandata $ok or higher";
		if ($intver < $ok && $intver >= $alert) $status = "<img src=\"test/alert.png\" alt=\"ALERT\">";
		else if ($intver <= $fail)	$status = "<img src=\"test/cancel.png\" alt=\"FAILED\">";
	}
	$code  = "\n\t<tr>";
	$code .= "\n\t\t<td nowrap>$status</td><td width=\"200\"><b>$name</b></td><td>Ver. $ver</td>";
	if (!empty($require)) $require = "&nbsp;<img src=\"test/help.png\" alt=\"HELP\">$require";
	$code .= "\n\t\t<td nowrap>$require</td>";
	$code .= "\n\t</tr>";
	return $code;
}

function test($name, $desc, $test, $err)
{
	$status = "";
	if ($test)
	{
		$status = "<img src=\"test/success.png\" alt=\"OK\">";
		$err = "";
	}
	else $status = "<img src=\"test/cancel.png\" alt=\"FAILED\">";
	$code  = "\n\t<tr>";
	$code .= "\n\t\t<td nowrap valign=\"top\">$status</td><td valign=\"top\"><b>$name</b></td><td><i>$desc</i></td>";
	$code .= "\n\t\t<td nowrap>$err</td>";
	$code .= "\n\t</tr>";
	return $code;
}

function testConnect($LNG)
{
	$code = "";

	$testconn = file("../conf/conn.inc.php");
	unset($testconn[0]);
	$conn = array();
	foreach($testconn as $row) if (trim($row)!="") $conn[] = explode("|", $row);
	foreach($conn as $row) $extension[] = $row[1];
	$extension = array_unique($extension);
	foreach($extension as $row) $code .= checkExtension($row, $LNG);

	$jdbadmin = "<a href=\"../frm/jdbadmin/index.php\" target=\"_black\" />Jdbadmin</a>";
	$code .= testTitle($jdbadmin, "database.png");

	$newconn  = "<a href=\"connection.php?action=new_connection\" rel=\"lightframe\" rev=\"width: 600px; height: 320px;\" onclick=\"LIGHTBOX.start($('lightbox'), this, false, true);return false;\" />".$LNG["txt0"]."</a>";
	$code .= testTitle($newconn, "socket.png");

	$header = array("Name", "Engine", "Host[<i>@database</i>]", "User", "Port", "Error");
	if (LANGUAGE=='IT') $header = array("Nome", "Engine", "Host[<i>@database</i>]", "Utente", "Porta", "Errore");
	$code  .= "\n</table>";
	$code  .= "\n<table border=\"1\" cellpadding=\"3\">";
	$code  .= "\n<tr>";
	$code  .= "\n<th style=\"background: rgb(18, 115, 151); color: white;\"></th>";
	$code  .= "\n<th width=\"1\" style=\"background: rgb(18, 115, 151); color: white;\">$header[0]</th>";
	$code  .= "\n<th width=\"100\" style=\"background: rgb(18, 115, 151); color: white;\">$header[1]</th>";
	$code  .= "\n<th style=\"background: rgb(18, 115, 151); color: white;\">$header[2]</th>";
	$code  .= "\n<th style=\"background: rgb(18, 115, 151); color: white;\">$header[3]</th>";
	$code  .= "\n<th style=\"background: rgb(18, 115, 151); color: white;\">$header[4]</th>";
	$code  .= "\n<th style=\"background: rgb(18, 115, 151); color: white;\">$header[5]</th>";
	$code  .= "\n<th style=\"background: rgb(18, 115, 151); color: white;\">&nbsp</th>";
	$code  .= "\n</tr>";

	global $system, $error;
	$ds = $system->newObj("ds", "ds");
	foreach($conn as $row)
	{
		$error = "";
		if ($row[0] == "jamp") 
		{
			if ($row[4] == "none") $status = "<img src=\"test/decrypted.png\" alt=\"ALERT\">";
			else $status = "<img src=\"test/encrypted.png\" alt=\"OK\">";
			$code .= "\n\t<tr>";
			$code .= "\n\t\t<td nowrap>$status</td>";
			$code .= "\n\t\t<td nowrap><a href=\"connection.php?action=modify_connection&amp;row=".urlencode(implode("|",$row))."\" rel=\"lightframe\" rev=\"width: 600px; height: 320px;\" onclick=\"LIGHTBOX.start($('lightbox'), this, false, true);return false;\" /><b>$row[0]</b></a></td>";
			$code .= "<td colspan=\"6\"><font color=\"red\">".$LNG["jamp"]."</font></td>";	
			$code .= "\n\t</tr>";
		}
		else
		{
			$isload = testExtension($row[1]);
			if ($isload==-1 || $isload==1)
			{	
				if ($row[1] == "filesystem" || $row[1] == "xml" || $row[1] == "csv" || $row[1] == "record") 
				{
				  $status = "<img src=\"test/success.png\" alt=\"OK\">";
				}
				else
				{
					$ds->setProperty("conn", $row[0]);
					$ds->ds->dsClose();
					$error = "";
					$ds->ds->dsConnect();
					if ($error=="" && $ds->ds->property["open"] == true) $status = "<img src=\"test/success.png\" alt=\"OK\">";
					else $status = "<img src=\"test/cancel.png\" alt=\"FAILED\">";
				}
			}
			else 
			{
				$status = "<img src=\"test/cancel.png\" alt=\"FAILED\">";
				if ($isload==-2) $error = $row[1]." ".$LNG["engineerr"];
				else $error = $row[1]." ".$LNG['ext'];
			}
			$code .= "\n\t<tr>";
			$code .= "\n\t\t<td nowrap>$status</td>";
			$code .= "\n\t\t<td nowrap><a href=\"connection.php?action=modify_connection&amp;row=".urlencode(implode("|",$row))."\" rel=\"lightframe\" rev=\"width: 600px; height: 320px;\" onclick=\"LIGHTBOX.start($('lightbox'), this, false, true);return false;\" /><b>$row[0]</b></a></td>";
			for ($i=1; $i<6; $i++) if ($i!=4) $code .= "<td nowrap><i>$row[$i]</i></td>";
			$code .= "<td>$error</td>";
	
			$code .= "\n\t\t<td nowrap><a href=\"connection.php?action=delete_connection&amp;row=".urlencode(implode("|",$row))."\" rel=\"lightframe\" rev=\"width: 600px; height: 320px;\" onclick=\"LIGHTBOX.start($('lightbox'), this, false, true);return false;\" /><img src=\"test/delete.png\" border=\"0\" alt=\"$row[0]\"></a></td>";
	
			$code .= "\n\t</tr>";
		}
	}
	$code  .= "\n<tr>";
	$code  .= "\n<td colspan=\"7\"><img src=\"test/help.png\" alt=\"HELP\"> ".$LNG["help"]."</td>";
	$code  .= "\n</tr>";
	$code  .= "\n</table>";
	return $code;
}

function testTitle($text, $img)
{
	return "\n\t<tr><td colspan=\"4\"><b><br><img src=\"test/$img\" alt=\"$img\" align=\"left\"><span style=\"font-weight:bold; font-size:2em; color:#127397;\">$text</span><br><br></b></td></tr>";
}

function html_load() 
{
	if (LANGUAGE=='IT')
	{
		$LNG["ext"] = "Estensione PHP non installata o non caricata!";
		$LNG["permission"] = "Controllo della directory";
		$LNG["permission_err"] = "Non posso leggere i file controlla i permessi";
		$LNG["connjamp"] = "Errore la connessione jamp non esiste!!!";
		$LNG["jamp"] = "Questa connessione serve a proteggere il framework, si consiglia di impostare utente e password";
		$LNG["engineerr"] = "Engine non valido";
		$LNG["const"] = "Costante non definita";
		$LNG["ver"] = "Controllo versione di JAMP su http://jamp.alyx.it - ";
		$LNG["verlast"] = "Ultima Versione Disponibile: ";
		$LNG["verok"] = "Versione corrente";
		$LNG["vererr"] = "Versione non aggiornata: <a href=\"http://jamp.alyx.it\" target=\"new\">DOWNLOAD</a>";
		$LNG["txt0"] = "NUOVA CONNESSIONE";
		$LNG["txt1"] = "CONTROLLO VERSIONE";
		$LNG["txt2"] = "ESTENSIONI PHP";
		$LNG["txt3"] = "IMPOSTAZIONI DI JAMP";
		$LNG["txt4"] = "SITO http://jamp.alyx.it/ NON RAGGIUNGIBILE";
		$LNG["txt5"] = "Costante definita. Per verifica la correttezza della chiave usa il servizio";
		$LNG["txt6"] = "PERMESSI DELLE DIRECTORY";
		$LNG["help"] = "Per le connessioni a tracciati record o file csv il campo <b>Porta</b> specifica la modalità di apertura del file(\"r\" sola lettura)";
	}
	else 
	{
		$LNG["ext"] = "PHP Extension is not installed or not loaded!";
		$LNG["permission"] = "Directory check";
		$LNG["permission_err"] = "Can not access to directory, check permissions";
		$LNG["connjamp"] = "Error jamp connection not exists!!!";
		$LNG["jamp"] = "This connection serves to protect the framework, you may want to set user and password";
		$LNG["engineerr"] = "Not valid Engine";
		$LNG["const"] = "Undefined constant";
		$LNG["ver"] = "Version Control JAMP on http://jamp.alyx.it - ";
		$LNG["verlast"] = "Last Version: ";
		$LNG["verok"] = "Last Version";
		$LNG["vererr"] = "Outdated version: <a href=\"http://jamp.alyx.it\" target=\"new\">DOWNLOAD</a>";
		$LNG["txt0"] = "NEW CONNECTION";
		$LNG["txt1"] = "CHECK VERSION";
		$LNG["txt2"] = "PHP EXTENSION";
		$LNG["txt3"] = "JAMP SETTING";
		$LNG["txt4"] = "SITE IS NOT REACHED: http://jamp.alyx.it/";
		$LNG["txt5"] = "Constant defined. For Verify the correctness of the key use the service";
		$LNG["txt6"] = "DIRECTORY PERMISSIONS";
		$LNG["help"] = "Connections to record file or csv file the <b>Port</b> field specifies the file open mode(\"r\" read only)";
	}
	global $xml, $system;
	if (!isset($_SESSION["jampadmin"]))
	{
		$testconn = file("../conf/conn.inc.php");
		unset($testconn[0]);
		$conn = explode("|", trim($testconn[1]));
		if ($conn[0]!="jamp") die($LNG["connjamp"]);
		if ($conn[4]=="none") $_SESSION["jampadmin"] = true;
		else header('Location: login.php');
	}
	$label1 = $xml->getObjById("div1");
	$code  = "\n<table>";
	$code .= testTitle($LNG["txt6"], "folder.png");
	$code .= test("admin", $LNG["permission"], dir("../admin"), $LNG["permission_err"]);
	$code .= test("class", $LNG["permission"], dir("../class"), $LNG["permission_err"]);
	$code .= test("conf", $LNG["permission"], dir("../conf"), $LNG["permission_err"]);
	$code .= test("develop", $LNG["permission"], dir("../develop"), $LNG["permission_err"]);
	$code .= test("examples", $LNG["permission"], dir("../examples"), $LNG["permission_err"]);
	$code .= test("frm", $LNG["permission"], dir("../frm"), $LNG["permission_err"]);
	$code .= test("js", $LNG["permission"], dir("../js"), $LNG["permission_err"]);
	$code .= test("obj", $LNG["permission"], dir("../obj"), $LNG["permission_err"]);
	$code .= test("plugin", $LNG["permission"], dir("../plugin"), $LNG["permission_err"]);
	$code .= test("template", $LNG["permission"], dir("../template"), $LNG["permission_err"]);
	$code .= test("tmp", $LNG["permission"], dir("../tmp"), $LNG["permission_err"]);
	$code .= test("update", $LNG["permission"], dir("../update"), $LNG["permission_err"]);

	//VERSION
	$code .= testTitle($LNG["txt1"], "version.png");
	$code .= testVer("PHP", phpversion(), 5 , 5, 5);
	$version = file("http://jamp.alyx.it/release.php?ver=".$system->version);
	$version = trim($version[0]);
 	if (!empty($version))
 	{
 		$status = "<img src=\"test/success.png\" alt=\"OK\">";
 		$testver = false;
 		if ((strtoupper($system->version) == strtoupper($version))) 
		{
			$LNG["vererr"] = $LNG["verok"];
	 		$testver = true;
		}
 		else $status = "<img src=\"test/alert.png\" alt=\"FAILED\">";
 		$code .= "\n\t<tr>";
 		$code .= "\n\t\t<td nowrap valign=\"top\">$status</td><td valign=\"top\"><b><i>Ver:</i> ".$system->version."</b></td><td><i>".$LNG["ver"]."</i></td>";
 		$code .= "\n\t\t<td nowrap>".$LNG["vererr"]." <b>$version</b></td>";
 		$code .= "\n\t</tr>";
 	} 
 	else 
	{
		$code .= "\n\t<tr>";
 		$code .= "\n\t\t<td nowrap valign=\"top\"><img src=\"test/alert.png\" alt=\"ALERT\"></td><td valign=\"top\"><b><i>Ver:</i> ".$system->version."</b></td><td><i>".$LNG["ver"]."</i></td>";
 		$code .= "\n\t\t<td nowrap>".$LNG["txt4"]."</td>";
 		$code .= "\n\t</tr>";
 	} 
	
	//PHP
	$code .= testTitle($LNG["txt2"], "phppg.png");
	$code .= checkExtension("gd", $LNG);
	$code .= checkExtension("zlib", $LNG);
	$code .= testConnect($LNG);

	$code  .= "\n<table>";

	//SETTING
	$header = array("Name", "Value", "Error");
	if (LANGUAGE=='IT') $header = array("Nome", "Valore", "Errore");	

	$setting = "<a href=\"setting.php\" rel=\"lightframe\" rev=\"width: 600px; height: 400px;\" onclick=\"LIGHTBOX.start($('lightbox'), this, false, true);return false;\" />".$LNG["txt3"]."</a>";

	$code .= testTitle($setting , "setting.png");
	$code .= "\n</table>";

	$code  .= "\n<table border=\"1\" cellpadding=\"3\" width=\"100%\">";
	$code  .= "\n<tr>";
	$code  .= "\n<th width=\"1\" style=\"background: rgb(18, 115, 151);\"></th>";
	$code  .= "\n<th width=\"150\" style=\"background: rgb(18, 115, 151); color: white;\">$header[0]</th>";
	$code  .= "\n<th width=\"1\" style=\"background: rgb(18, 115, 151); color: white;\">$header[1]</th>";
	$code  .= "\n<th style=\"background: rgb(18, 115, 151); color: white;\">$header[2]</th>";
	$code  .= "\n</tr>";


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
			$code .= test($constant[$i], $value, defined($constant[$i]), $LNG["const"]);
			$i++;
		}
	}

	$code .= "\n</table>";
	$code .= "<br><center><a href=\"http://validator.w3.org/check?uri=referer\"><img border=\"0\" src=\"test/valid-html401-blue.png\" alt=\"Valid HTML 4.01 Transitional\" height=\"31\" width=\"88\"></a>&nbsp;<a href=\"./../gpl-3.0.txt\" target=\"gpl\"><img alt=\"GPL3\" src=\"test/gplv3.png\" border=\"0\" > GNU Public License</a><br><a href=\"http://www.alyx.it\">&copy; 2008 - ".date(Y).", ALYX - Software Innovation</a></center>";
	
	$label1->setProperty("value", $code);
} 
?>