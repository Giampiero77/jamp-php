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
if($_SESSION["jdbadmin"]['lang'] == "EN") $xml = new ClsXML("tables.xml");
else $xml = new ClsXML(array("tables.xml", "lang/".$_SESSION["jdbadmin"]['lang']."/tables.xml"));
$event = new ClsEvent($xml);
$DS_CONN["ds0"] = $conn; 
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
    if ($ds->getPropertyName("id")=="ds0") $ds->ds->dsShowCollation();
    else if ($ds->getPropertyName("id")=="ds1") $ds->ds->dsShowTables($_SESSION["jdbadmin"]['database']);
}

function data_update($ds) 
{
	global $event, $refresh;
	$msg = "You can change only the table name!";
	if($_SESSION["jdbadmin"]['lang'] == "IT") $msg = "Puoi cambiare il nome a una sola tabella";

    if ($_POST['keynamevalue']==$_POST['Name']) $event->setCodeJs("alert('$msg');");
    else if ($ds->getPropertyName("id")=="ds1") 
	{		
	    $dabatase = $_SESSION["jdbadmin"]['database'];
 		$ds->ds->dsConnect();
 		$ds->ds->RenameTable($dabatase, $_POST['keynamevalue'], $dabatase, $_POST['Name']);
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
 		$ds->ds->DropTable($dabatase, $_POST['keynamevalue']);
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
        $engine = "";
        if (isset($_POST['Engine'])) $name = $_POST['Engine']; 
        $collation = "";
        if (isset($_POST['Collation'])) $collation = $_POST['Collation']; 
    	$ds->ds->CreateTable($dabatase, $_POST['Name'], $engine, $collation);
    	$event->setCodeJs($refresh);
	}
    return false;
}
