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
if($_SESSION["jdbadmin"]['lang'] == "EN") $xml = new ClsXML("server.xml");
else $xml = new ClsXML(array("server.xml", "lang/".$_SESSION["jdbadmin"]['lang']."/server.xml"));
$event = new ClsEvent($xml);
$DS_CONN["ds1"] = $conn; 
$DS_CONN["ds2"] = $conn;
$DS_CONN["ds3"] = $conn;
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
	global $event;
	$code = "
		function updateGrid(id, box)
		{
			if(box == 'tab2') AJAX.request('POST', 'server.php', 'data=load&dsobjname=ds2', false, true);
		}
		SYSTEMEVENT.addAfterCustomFunction('TABS','setFocus', 'updateGrid');

		function upload_posted()
		{	
			var filename = encodeURIComponent(TEXT.objposted.value);
			AJAX.request('POST', 'server.php', 'data=update&dsobjname=ds1&import='+filename, true, true);
		}
		SYSTEMEVENT.addAfterCustomFunction('TEXT', 'AfterPost', 'upload_posted');
		function ReloadPrivileges()
		{
			AJAX.request('POST', 'server.php', 'data=update&dsobjname=ds3', true, true);
		}
	";
	$event->setCodeJS($code);
}

function data_select_before($ds) 
{
    if ($ds->getPropertyName("id")=="ds1") $ds->ds->dsShowDatabases();
    else if ($ds->getPropertyName("id")=="ds2") $ds->ds->dsShowUsers();
}

function data_update($ds) 
{
	$MSG["EN"] = "Cannot rename a database";
	$MSG["IT"] = "Impossibile rinominare il database";
	global $event, $refresh;
    if ($ds->getPropertyName("id")=="ds1") 
	{
		if (isset($_POST["import"])) 
		{
			if (preg_match('/gz$|bz2$|zip$/i', $_POST["import"], $type)) 
			{
				require_once($system->dir_real_jamp."/class/compress.class.php");
				$compress = new Compress();
				$sql = $compress->Read($_POST["import"], $type[0]);
			}
			else $sql = file($_POST["import"]);
			$query = $ds->ds->dsQueryParsing($sql, false);
			unlink($_POST["import"]);
			$ds->ds->dsConnect();
			foreach ($query as $qry) if (!preg_match("/^SELECT/i", $qry)) $ds->ds->dsQuery($qry);
	    	$event->setCodeJs($refresh);
			$event->setCodeJS("alert('SQL Executed'); $('importtxt').value='';");
		}	
		else $event->setCodeJs("alert('".$MSG[$_SESSION["jdbadmin"]['lang']]."');");
	}
	else if ($ds->getPropertyName("id")=="ds2") 
	{		
		$ds->ds->dsConnect();
		$ds->ds->dsModUser($_POST);
	}
	else if ($ds->getPropertyName("id")=="ds3") $ds->ds->reloadPrivileges();
	return false;
}

function data_new($ds) 
{
	global $event, $refresh;
    if ($ds->getPropertyName("id")=="ds1") 
	{		
		$ds->ds->dsConnect();
		$ds->ds->CreateDatabase($_POST['Database']);
    	$event->setCodeJs($refresh);
	}
	return false;
}

function data_delete($ds) 
{
	global $event, $refresh;
    if ($ds->getPropertyName("id")=="ds1") 
	{
 		$ds->ds->dsConnect();
   		$ds->ds->DropDatabase($_POST['keynamevalue']);
     	$event->setCodeJs($refresh);
	}
	return false;
}