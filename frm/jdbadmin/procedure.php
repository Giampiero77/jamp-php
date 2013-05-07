<?php
/**
* Source File PHP
* @name JdbAdmin
* @author Alyx-Software Innovation <info@alyx.it>
* @version 1.4
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
*/

require_once("session.php");
if($_SESSION["jdbadmin"]['lang'] == "EN") $xml = new ClsXML("procedure.xml");
else $xml = new ClsXML(array("procedure.xml", "lang/".$_SESSION["jdbadmin"]['lang']."/procedure.xml"));
$event = new ClsEvent($xml);
$DS_CONN["ds1"] = $conn; 
$event->managerRequest();

function html_load()
{
	global $xml;
	$_SESSION["jdbadmin"]['database'] = $_GET['database'];
	$_SESSION["jdbadmin"]['procedure'] = $_GET['node'];
	$label1 = $xml->getObjById("label1");
	$label1->setProperty("value", $_GET['database']);
}

function data_select_before($ds) 
{
	global $xml;
	$result = array();
	if ($ds->getPropertyName("id")=="ds1") 
	{
		$result[1] = $ds->ds->dsShowProcedure($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['procedure']);
		$result[1]["Code"] = $result[1]["Code"];
		$ds->ds->property['tot'] = "1";
		if (count($result)>0) 
		{
			$ds->setProperty("xml", $xml->dataJSON($result));
			return false;
		}
	}
}

function data_update($ds) 
{
    $dabatase = $_SESSION["jdbadmin"]['database'];
    if ($ds->getPropertyName("id")=="ds1") 
	{		
 		$ds->ds->dsConnect();
 		$ds->ds->SaveProcedure($dabatase, $_POST['Name'], $_POST['Code'], $_POST['User'], $_POST['Host']);
	}
	return false;
}

function data_delete($ds) {	return false; }
function data_new($ds) { return false; }
