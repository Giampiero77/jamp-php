<?php
/**
* Source File PHP
* @name JdbAdmin
* @author Alyx-Software Innovation <info@alyx.it>
* @version 1.4
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
*/       

if (!isset($ds))
{
	$post = explode("&", $_POST['datapost']);
	foreach ($post as $val) 
	{
		list($name, $value) = explode("=", $val);
		$_POST[$name] = $value;
	}
	require_once("session.php");
	require_once($system->dir_real_jamp."/".$system->dir_obj."ds.obj.php");

	$ds = new ClsObj_ds("dsExport");
	$ds->setProperty("dsengine", $_SESSION["jdbadmin"]["engine"]);
	$ds->setProperty("dshost", $_SESSION["jdbadmin"]['server']);
	$ds->setProperty("dsuser", $_SESSION["jdbadmin"]['user']);
	$ds->setProperty("dspwd", $_SESSION["jdbadmin"]['pwd']);
	$ds->setProperty("dsport", $_SESSION["jdbadmin"]['port']);
	$ds->ds->dsConnect();
	$print = true;
}
else 
{
    $print = false;
}
$result = array();
$db = $_SESSION["jdbadmin"]['database'];

$createdb = ($_POST['createdb']=="1") ? true : false;
$tables = ($_POST['tables']!="") ? explode(",", $_POST['tables']) : array();
$views = ($_POST['views']!="") ? explode(",", $_POST['views']) : array();
$functions = ($_POST['functions']!="") ? explode(",", $_POST['functions']) : array();
$procedures = ($_POST['procedures']!="") ? explode(",", $_POST['procedures']) : array();

$tablestruc = ($_POST['tablestruc']=="1") ? true : false;
$tabledata = ($_POST['tabledata']=="1") ? true : false;
$tablestartrow = $_POST["viewstartrow"];
$tablelimitrow = $_POST["tablelimitrow"];

$viewstruc = ($_POST['viewstruc']=="1") ? true : false;
$viewdata = ($_POST['viewdata']=="1") ? true : false;
$viewstartrow = $_POST["viewstartrow"];
$viewlimitrow = $_POST["viewlimitrow"];

$result[1]["SQL"]  = "-- jdbAdmin Export Database\r\n";
$result[1]["SQL"] .= "-- @author Alyx Association info@alyx.it\r\n";
$result[1]["SQL"] .= "-- @version $version\r\n";
$result[1]["SQL"] .= "-- @copyright Alyx Association 2008-2010\r\n";
$result[1]["SQL"] .= "-- @license GNU Public License\r\n";
$result[1]["SQL"] .= "-- Date: ".date("d/m/Y H:i:s")."\r\n";
$result[1]["SQL"] .= "\r\n\r\n";

if ($createdb)
{
	$result[1]["SQL"] .= "-- Export Database: $db \r\n";
	$result[1]["SQL"] .= $ds->ds->exportDatabase($db); 
	$result[1]["SQL"] .= "\r\n\r\n";
}
foreach ($tables as $table) 
{
	$result[1]["SQL"] .= "-- Export Table: $table \r\n";
	$result[1]["SQL"] .= $ds->ds->exportTable($db,$table,$tablestruc,$tabledata,$tablestartrow,$tablelimitrow); 
	$result[1]["SQL"] .= "\r\n\r\n";
}			
foreach ($views as $view) 
{
	$result[1]["SQL"] .= "-- Export View: $view \r\n";
	$result[1]["SQL"] .= $ds->ds->exportView($db,$view,$viewstruc,$viewdata,$viewstartrow,$viewlimitrow); 
	$result[1]["SQL"] .= "\r\n\r\n";
}
foreach ($functions as $function) 
{
	$result[1]["SQL"] .= "-- Export Functions: $function \r\n";
	$result[1]["SQL"] .= $ds->ds->exportFunction($db,$function); 
	$result[1]["SQL"] .= "\r\n\r\n";
}
foreach ($procedures as $procedure) 
{
	$result[1]["SQL"] .= "-- Export Procedures: $procedure \r\n";
	$result[1]["SQL"] .= $ds->ds->exportProcedure($db,$procedure); 
	$result[1]["SQL"] .= "\r\n\r\n";
}
if (!$print) $result[1]["SQL"] = htmlspecialchars($result[1]["SQL"]);
else 
{
 	global $system;
	if (isset($_POST["exptype"]) && ($_POST["exptype"]!="none")) 
	{
		require_once($system->dir_real_jamp."/class/compress.class.php");
		$compress = new clsCompress();
		$filename = 'export.sql';
		$filezp = $compress->Save($filename, $result[1]["SQL"], $_POST["exptype"]);
		$compress->Download($filezp);
		unlink($filezp);
	}
	else 
	{
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"export.sql\";"); 
		print $result[1]["SQL"];
	}		
}
