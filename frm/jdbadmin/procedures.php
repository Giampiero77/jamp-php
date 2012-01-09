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
if($_SESSION["jdbadmin"]['lang'] == "EN") $xml = new ClsXML("procedures.xml");
else $xml = new ClsXML(array("procedures.xml", "lang/".$_SESSION["jdbadmin"]['lang']."/procedures.xml"));
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
	$result = $procedures = array();
	if ($ds->getPropertyName("id")=="ds1") 
	{
		$ds->ds->dsShowProcedures($dabatase);
		while ($ds->ds->dsGetRow()) $procedures[] = $ds->ds->property["row"]->name;
	   	foreach ($procedures as $rs) 
		{
			$procedure = $ds->ds->dsShowProcedure($dabatase, $rs);
			$procedure['Code'] = "<![CDATA[".$procedure['Code']."]]>";
			$result[] = $procedure;
		}
       	if (count($result)>0) $ds->setProperty("xml", $xml->dataJSON($result));
	}
}

function data_update($ds) 
{
	global $event, $refresh;
    $dabatase = $_SESSION["jdbadmin"]['database'];
    if ($ds->getPropertyName("id")=="ds1") 
	{		
 		$ds->ds->dsConnect();
 		if ($_POST['keynamevalue']!=$_POST['Name']) $ds->ds->RenameProcedure($dabatase, $_POST['keynamevalue'], $dabatase, $_POST['Name']);
 		$ds->ds->SaveProcedure($dabatase, $_POST['Name'], $_POST['Code'], $_POST['User'], $_POST['Host']);
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
 		$ds->ds->DropProcedure($dabatase, $_POST['keynamevalue']);
    	$event->setCodeJs($refresh);
	}
	return false;
}

function data_new($ds) 
{
	global $event, $refresh;
    $dabatase = $_SESSION["jdbadmin"]['database'];
    if ($ds->getPropertyName("id")=="ds1") 
	{
        $ds->ds->dsConnect();
        if ((!isset($_POST['User'])) || (!isset($_POST['Host']))) $ds->ds->CreateProcedure($dabatase, $_POST['Name'], $_POST['Code']);
    	else $ds->ds->CreateProcedure($dabatase, $_POST['Name'], $_POST['Code'], $_POST['User'], $_POST['Host']);
    	$event->setCodeJs($refresh);
	}
    return false;
}
