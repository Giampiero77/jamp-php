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
if($_SESSION["jdbadmin"]['lang'] == "EN") $xml = new ClsXML("view.xml");
else $xml = new ClsXML(array("view.xml", "lang/".$_SESSION["jdbadmin"]['lang']."/view.xml"));
$event = new ClsEvent($xml);
$DS_CONN["ds1"] = $conn; 
$DS_CONN["dsGrid"] = $conn;
$event->managerRequest();

function html_load()
{
	global $xml, $event;
	$_SESSION["jdbadmin"]['database'] = $_GET['database'];
	$_SESSION["jdbadmin"]['view'] = $_GET['node'];
	$code = '
		function updateGrid(id, box)
		{
 			if(box == "tab2")
			{
				AJAX.rewriteObj("gridds1", "view.php");
 				AJAX.request("POST", "view.php", "data=load&dsobjname=dsGrid", true, true);
			}
		}
     	SYSTEMEVENT.addAfterCustomFunction("TABS","setFocus", "updateGrid");

		/*function ds_delete(dsObjName)
		{
			if (dsObjName=="dsGrid") 
			{
				alert("'.MESSAGE::translate("MSG001").'");
				/*var dsObj = $("dsGrid");
				if (dsObj.DSpos == -1) return;
				if (confirm("'.MESSAGE::translate("MSG002").'")) 
				{
					var dswhere = "";
					var i = 0;
					for (var field in dsObj.DSresult[dsObj.DSpos])
					{
						if(dsObj.DSresult[dsObj.DSpos].hasOwnProperty(field))
						{
							alert(dsObj.DSresult[dsObj.DSpos][field]);
							dswhere += "&where["+i+"]="+encodeURIComponent("(`"+field+"`=\'"+dsObj.DSresult[dsObj.DSpos][field]+"\')"); 
							i++;
						}
					}
  					AJAX.request("POST", "view.php", "data=delete&dsobjname=dsGrid"+dswhere, true, true);
					GRIDDS.refreshObj("gridds1");
					Resize();
				}
			}
			return false;
		}
		SYSTEMEVENT.addBeforeCustomFunction("DS","dsdelete", "ds_delete");*/

		function Truncate()
		{
			if (confirm("'.MESSAGE::translate("MSG003").'")) 
			{
				AJAX.request("POST", "view.php", "data=delete&dsobjname=dsGrid&truncate=yes", true, true);
			}
		}
		';
	$event->setCodeJS($code);
}

function data_select_before($ds) 
{
	global $xml, $event;
	$result = array();
	if ($ds->getPropertyName("id")=="ds1") 
	{
		$result[1] = $ds->ds->dsShowView($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['view']);
		$result[1]["Code"] = $result[1]["Code"];
		$ds->ds->property['tot'] = "1";
		$ds->setProperty("xml", $xml->dataJSON($result));
	}
	else if ($ds->getPropertyName("id")=="dsGrid")
	{
		$_SESSION["jdbadmin"]["BLOB"] = array();
		$ds->ds->dsDBSelect($_SESSION["jdbadmin"]["database"]);
		$item = array();
		foreach($ds->ds->dsShowColumnsResult($_SESSION["jdbadmin"]["view"]) as $row)
		{
			if ($row->blob)
			{
				$item[] = "concat('BLOB ',round(length(`".$row->name."`)/1024,2), 'KB') as `".$row->name."`";
				$_SESSION["jdbadmin"]["BLOB"][] = $row->name;
			}
			else $item[]= "`".$row->name."`";
		}
		$qry = "SELECT SQL_CALC_FOUND_ROWS ".implode(", ", $item)." FROM `".$_SESSION["jdbadmin"]["view"]."`";
		if (isset($_REQUEST["dsorder"]))
		{
			$pos = stripos($qry, " ORDER BY ");
			if ($pos !== false) $qry = substr($qry, 0, $pos);
			$order = explode(" ", $_REQUEST["dsorder"]);
			if (count($order)==1) $qry .= " ORDER BY `".$order[0]."`";
			else $qry .= " ORDER BY `".$order[0]."` ".$order[1]; 
		}
		$ds->setProperty("dsquery_select", $qry);
		$ds->ds->setQryCount($qry);
	}
}

function html_rewrite($gridds)
{
	global $xml, $event;
	$ds = $xml->getObjById("dsGrid");
	$ds->ds->dsConnect();
	$ds->ds->dsDBSelect($_SESSION["jdbadmin"]['database']);
	$grid["grid_table"] = array("Field","Type", "Lenght", "Null");
	foreach($ds->ds->dsShowColumnsResult($_SESSION["jdbadmin"]["view"]) as $row) 
	{
		if ($row->blob)
		{
			$grid["grid_table"]["format"][] = null;
			$grid["grid_table"]["objtype"][] = "label";
		}
		else
		{
			switch($row->type)
			{
				case "DATE";
					$grid["grid_table"]["format"][] = "date|EN|yyyy-mmm-dd|IT|dd/mmm/yyyy";
					$grid["grid_table"]["objtype"][] = "text";
				break;
				default:
					$grid["grid_table"]["format"][] = null;
					$grid["grid_table"]["objtype"][] = "text";
			}
			$grid["grid_table"]["maxlenght"][] = $row->max_length;
			$grid["grid_table"]["minlenght"][] = ($row->not_null == true) ? "1" : null;
			if($row->primary_key == true) $grid["key"] = $row->name;
		}
		$grid["grid_table"]["dsitem"][] = $row->name;
		$grid["grid_table"]["colwidth"][] = "150px";
	}
 	$gridds->setProperty("objtype", $grid["grid_table"]["objtype"]);
 	$gridds->setProperty("dsitem", $grid["grid_table"]["dsitem"]);
 	$gridds->setProperty("itemlabel", $grid["grid_table"]["dsitem"]);
 	$gridds->setProperty("maxlenght", $grid["grid_table"]["maxlenght"]);
	$gridds->setProperty("minlenght", $grid["grid_table"]["minlenght"]);
 	$gridds->setProperty("format", $grid["grid_table"]["format"]);
 	$gridds->setProperty("colwidth", $grid["grid_table"]["colwidth"]);
	if (isset($grid["key"])) $event->setCodeJS("$('dsGrid').p.DSkey = '".$grid["key"]."';");
}

function data_update($ds) 
{
    $dabatase = $_SESSION["jdbadmin"]['database'];
    if ($ds->getPropertyName("id")=="ds1") 
	{		
 		$ds->ds->dsConnect();
 		$ds->ds->SaveView($dabatase, $_POST['Name'], $_POST['Code'], $_POST['User'], $_POST['Host']);
		return false;
	}
	else if ($ds->getPropertyName("id")=="dsGrid") 
	{	
		foreach($_SESSION["jdbadmin"]["BLOB"] as $item) unset($_POST[$item]);
		$ds->setProperty("dsdefault", $_SESSION["jdbadmin"]["database"]);
		$ds->setProperty("dstable", $_SESSION["jdbadmin"]["view"]);
	}
}

function data_delete($ds) 
{	
	if ($ds->getPropertyName("id")=="dsGrid") 
	{
 		$ds->ds->dsConnect();
 		$ds->ds->dsDBSelect($_SESSION["jdbadmin"]['database']);
		if (isset($_POST["truncate"])) 
		{
			$ds->ds->dsDBSelect($_SESSION["jdbadmin"]["database"]);
			$ds->ds->property["dstable"] = $_SESSION["jdbadmin"]["view"];
			$ds->ds->dsQueryDeleteAll();
			return false;
		}
		else 
		{
			$ds->setProperty("dsdefault", $_SESSION["jdbadmin"]["database"]);
			$ds->setProperty("dstable", $_SESSION["jdbadmin"]["view"]);
			$ds->setProperty("dskey", $_POST["keynamevalue"]); 
		}
	}
}

function data_new($ds) 
{
	if ($ds->getPropertyName("id")=="ds1") return false;
	if ($ds->getPropertyName("id")=="dsGrid") 
	{
		$ds->setProperty("dsdefault", $_SESSION["jdbadmin"]["database"]);
		$ds->setProperty("dstable", $_SESSION["jdbadmin"]["view"]);
	}
}
