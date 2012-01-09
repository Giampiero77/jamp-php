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
if($_SESSION["jdbadmin"]['lang'] == "EN") $xml = new ClsXML("views.xml");
else $xml = new ClsXML(array("views.xml", "lang/".$_SESSION["jdbadmin"]['lang']."/views.xml"));
$event = new ClsEvent($xml);
$DS_CONN["ds1"] = $conn; 
$refresh = "
    var divTree = window.parent.$('tree1');
    var node = divTree.selectedNode;
    if (node.container.style.display!='none') 
    {
        window.parent.TREE.ExpandClick(divTree, node);
        window.parent.TREE.ExpandClick(divTree, node);
    }
";

$event->managerRequest();

function html_load()
{
	$_SESSION["jdbadmin"]['database'] = $_GET['database'];
}

function data_select_before($ds) 
{
	global $xml;
	$dabatase = $_SESSION["jdbadmin"]['database'];
	$result = $views = array();
	if ($ds->getPropertyName("id")=="ds1") 
	{
		$i=1;
		$ds->ds->dsShowViews($dabatase);
		while ($ds->ds->dsGetRow()) $views[] = $ds->ds->property["row"]->Name;
		foreach ($views as $view) 
		{
			$result[$i] = $ds->ds->dsShowView($_SESSION["jdbadmin"]['database'], $view);
			$result[$i]["Code"] = $result[$i]["Code"];
			$i++;
		}
		if (count($result)>0) 
		{
			$ds->setProperty("xml", $xml->dataJSON($result));
			return false;
		}
	}
}

function data_update($ds) 
{
	global $event, $refresh;
    $dabatase = $_SESSION["jdbadmin"]['database'];
    if ($ds->getPropertyName("id")=="ds1") 
	{		
 		$ds->ds->dsConnect();
 		if ($_POST['keynamevalue']!=$_POST['Name']) $ds->ds->RenameView($dabatase, $_POST['keynamevalue'], $dabatase, $_POST['Name']);
 		$ds->ds->SaveView($dabatase, $_POST['Name'], $_POST['Code'], $_POST['User'], $_POST['Host']);
    	$event->setCodeJs($refresh);
	}
	return false;
}

function data_delete($ds) 
{
	global $event, $refresh;
	$dabatase = $_SESSION["jdbadmin"]['database'];
	if ($ds->getPropertyName("id")=="ds1") 
	{
		$ds->ds->dsConnect();
		$ds->ds->DropView($dabatase, $_POST['keynamevalue']);
		$event->setCodeJs($refresh);
	}
	return false;
}

function data_new($ds) 
{
	global $event, $refresh;
	$dabatase = $_SESSION["jdbadmin"]['database'];
	$ds->ds->property["dsdefault"] = $dabatase;
	if ($ds->getPropertyName("id")=="ds1") 
	{
		$ds->ds->dsConnect();
		if ((!isset($_POST['User'])) || (!isset($_POST['Host']))) $ds->ds->CreateView($dabatase, $_POST['Name'], $_POST['Code']);
		else $ds->ds->CreateView($dabatase, $_POST['Name'], $_POST['Code'], $_POST['User'], $_POST['Host']);
		$event->setCodeJs($refresh);
	}
	return false;
}
