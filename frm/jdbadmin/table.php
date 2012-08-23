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
if($_SESSION["jdbadmin"]['lang'] == "EN") $xml = new ClsXML("table.xml");
else $xml = new ClsXML(array("table.xml", "lang/".$_SESSION["jdbadmin"]['lang']."/table.xml"));
$event = new ClsEvent($xml);

$DS_CONN["ds0"] = $conn; 
$DS_CONN["ds1"] = $conn;
$DS_CONN["ds21"] = $conn;
$DS_CONN["dsFK"] = $conn;
$DS_CONN["dsGrid"] = $conn;
$event->managerRequest();

function html_load()
{
	$_SESSION["jdbadmin"]['database'] = $_GET['database'];
	$_SESSION["jdbadmin"]['table'] = $_GET['node'];
	global $event;
	$code = '
		function updateGrid(id, box)
		{
			if(box == "tab2")
			{
				AJAX.rewriteObj("gridds2", "table.php");
				AJAX.request("POST", "table.php", "data=load&dsobjname=dsGrid", true, true);
			}
		}
		SYSTEMEVENT.addAfterCustomFunction("TABS","setFocus", "updateGrid");

		function CreateIndex(type)
		{
			var ds1 = $("ds1");
			if (ds1.DSmultipos.length == 0) alert("'.MESSAGE::translate("MSG004").'");
			else
			{
				post = "data=new&dsobjname=ds21&type=" + type + "&items=";
				for (var key in ds1.DSmultipos)
				{
					if(ds1.DSmultipos.hasOwnProperty(key)) post += ds1.DSresult[key]["Field"] + "|";
				}
				AJAX.request("POST", ds1.p.DSaction, post, true, true);
			}
		}

		function CreateFK()
		{
			SYSTEMEVENT.showHTML("FOREIGN KEYS", "<iframe src=\"fk.php\" width=\"100%\" height=\"210\" style=\"border: 0\"></iframe>");		
		}

		function Truncate()
		{
			if (confirm("'.MESSAGE::translate("MSG009").'")) 
			{
				AJAX.request("POST", "table.php", "data=delete&dsobjname=dsGrid&truncate=yes", true, true);
			}
		}

		function ShowSQL()
		{
			var code = $("textSQL_code");
			if (code.clientHeight == 0)
			{
				var w = code.offsetParent.clientWidth - 6;
				var h = code.offsetParent.clientHeight;
				var top = (h - 203);
				code.style.top = h;
				code.style.opacity = "0";
				code.style.position = "absolute";
				code.style.zIndex = "999";
				code.style.filter = "alpha(opacity:0);";

				ANIMATE.animate("textSQL_code", "top:"+top+"px;height:200px;width:" + w + "px;opacity:1;borderTopWidth:3px;borderBottomStyle:3px;borderLeftWidth:3px;borderRightWidth:3px", "1000", "", "default");
			}
			else ANIMATE.animate("textSQL_code", "top:"+code.offsetParent.clientHeight+"px;height:0px;width:0px;opacity:0;borderTopWidth:1px;borderBottomStyle:1px;borderLeftWidth:1px;borderRightWidth:1px", "500", "", "default");
		}
	';
	$event->setCodeJS($code);
}

function data_select_before($ds) 
{
	global $xml;
	$i=1;
	$result = array();
   if ($ds->getPropertyName("id")=="ds0") $ds->ds->dsShowCollation();
	else if ($ds->getPropertyName("id")=="ds1") 
	{
		$ds->ds->dsShowColumns($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['table']);
		while($ds->ds->dsGetRow())
		{
			$ds->ds->property["row"]->name = $result[$i]['Field'] = $ds->ds->property["row"]->Field;
			$ds->ds->property["row"]->type = $result[$i]['Type'] = preg_replace("/\W(.*)/","",$ds->ds->property["row"]->Type);
			$ds->ds->property["row"]->max_length = $result[$i]['Lenght'] = preg_replace("/[".$result[$i]['Type']."()]/","",$ds->ds->property["row"]->Type);
 			$ds->ds->property["row"]->not_null = $result[$i]['Null'] = $ds->ds->property["row"]->Null;
			$result[$i]['Default'] = $ds->ds->property["row"]->Default;
 			$result[$i]['Extra'] = $ds->ds->property["row"]->Extra;
// 			$result[$i]['Privileges'] = $ds->ds->property["row"]->Privileges;
			$result[$i]['Collation'] = $ds->ds->property["row"]->Collation;	
			$result[$i++]['Comment'] = $ds->ds->property["row"]->Comment;
 			$ds->ds->property["row"]->primary_key = $ds->ds->property["row"]->Key;
		}
	}
	else if ($ds->getPropertyName("id")=="ds21") 
	{
		$ds->ds->dsShowIndex($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['table']);
		$ar = array();
		while($ds->ds->dsGetRow())
		{
			$name = $ds->ds->property["row"]->Key_name; 
			if (isset($ar[$name])) $result[$i-1]['Column_name'] = $result[$i-1]['Column_name'].",".$ds->ds->property["row"]->Column_name;
			else 
			{
				$ar[$name] = true; 
				$result[$i]['Key_name'] = $ds->ds->property["row"]->Key_name;
				$result[$i]['Non_unique'] = $ds->ds->property["row"]->Non_unique;
				$result[$i++]['Column_name'] = $ds->ds->property["row"]->Column_name;
			}
		}
	} 
	else if ($ds->getPropertyName("id")=="dsGrid")
	{
		$_SESSION["jdbadmin"]["BLOB"] = array();
		$ds->ds->dsDBSelect($_SESSION["jdbadmin"]["database"]);
		$item = array();
		foreach($ds->ds->dsShowColumnsResult($_SESSION["jdbadmin"]["table"]) as $row)
		{
			if ($row->blob)
			{
				$item[] = "concat('BLOB ',round(length(`".$row->name."`)/1024,2), 'KB') as `".$row->name."`";
				$_SESSION["jdbadmin"]["BLOB"][] = $row->name;
			}
			else $item[]= "`".$row->name."`";
		}
		$qry = "SELECT SQL_CALC_FOUND_ROWS ".implode(", ", $item)." FROM `".$_SESSION["jdbadmin"]["table"]."`";
		if (isset($_REQUEST["dsGridwhere"])) $qry .= " WHERE ".stripslashes($_REQUEST["dsGridwhere"]);
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
 	else if ($ds->getPropertyName("id")=="dsFK") $result = $ds->ds->dsShowForeignKey($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['table']);
	if (count($result)>0) 
	{
		$ds->setProperty("xml", $xml->dataJSON($result));
 		return false;
	}
}

function html_rewrite($gridds)
{
	global $xml, $event;
	$ds = $xml->getObjById("dsGrid");
	$ds->ds->dsConnect();
	$ds->ds->dsDBSelect($_SESSION["jdbadmin"]['database']);
	$grid["grid_table"] = array("Field","Type", "Lenght", "Null");
	foreach($ds->ds->dsShowColumnsResult($_SESSION["jdbadmin"]["table"]) as $row) 
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
				case "DATE":
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

function data_new($ds) 
{
	global $xml;
	$value = array();
	if ($ds->getPropertyName("id")=="ds1") 
	{		
		$ds->ds->dsConnect();
		$ds->ds->AlterTable($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['table'], $_POST, "ADD");
		data_after_new($ds);
		return false;
	}
	if ($ds->getPropertyName("id")=="dsGrid") 
	{		
		$ds->setProperty("dsdefault", $_SESSION["jdbadmin"]["database"]);
		$ds->setProperty("dstable", $_SESSION["jdbadmin"]["table"]);
	}
	if ($ds->getPropertyName("id")=="ds21") 
	{
		$ds->ds->dsConnect();
		$fields = explode("|",$_POST["items"]);
		unset($fields[count($fields)-1]);
		$ds->ds->AddIndex($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['table'], $_POST["type"], $fields);
		data_after_new($ds);
		return false;
	}
}

function data_update($ds) 
{	
	global $xml;
	$value = array();
	if ($ds->getPropertyName("id")=="ds1") 
	{		
		$ds->ds->dsConnect();
		$ds->ds->AlterTable($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['table'], $_POST, "CHANGE");
		data_after_new($ds);
		return false;
	}
	if ($ds->getPropertyName("id")=="dsGrid") 
	{	
		foreach($_SESSION["jdbadmin"]["BLOB"] as $item) unset($_POST[$item]);
		$ds->setProperty("dsdefault", $_SESSION["jdbadmin"]["database"]);
		$ds->setProperty("dstable", $_SESSION["jdbadmin"]["table"]);
	}
}

function data_delete($ds) 
{
	global $xml, $event;
	$ds->ds->dsConnect();
	switch ($ds->getPropertyName("id")) 
	{
		case "ds1":
	 		$ds->ds->DropField($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['table'], $_POST['keynamevalue']);
			data_after_new($ds);
			return false;
		  break;	
		case "dsGrid":
			$ds->ds->dsDBSelect($_SESSION["jdbadmin"]['database']);	
			if (isset($_POST["truncate"])) 
			{
 				$ds->ds->property["dstable"] = $_SESSION["jdbadmin"]["table"];
  				$ds->ds->dsQueryDeleteAll();
				$event->callEvent("data_after_deleteall", $ds);
				data_after_new($ds);
				return false;
			}
			else
			{
				$ds->setProperty("dsdefault", $_SESSION["jdbadmin"]["database"]);
				$ds->setProperty("dstable", $_SESSION["jdbadmin"]["table"]);
			}
		  break;	
		case "ds21":
	 		$ds->ds->DropIndex($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['table'], $_POST["keynamevalue"]);
			data_after_new($ds);
			return false;
		  break;	
		case "dsFK":
			$ds->ds->DropForeignKey($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['table'], $_POST["keynamevalue"]);
			data_after_new($ds);
			return false;
		  break;	
	}
}

function data_after_new($ds) 
{
	global $event;
	$sql = str_replace("'", "\'", $ds->ds->property['qrylast']); 
	if ($ds->getPropertyName("id")=="dsGrid") 
	{		
		$code = "$('textSQL_code').setCode($('textSQL_code').getCode()+'".$sql."\\n');
					$('textSQL_code').editor.syntaxHighlight('init');
					ANIMATE.animate('btSQL2', 'opacity:0', '1000', function(){ANIMATE.animate('btSQL2', 'opacity:1', '2000', '', 'default');}, 'default');
					";
		$event->setCodeJS($code);
	} else {		
		$code = "$('textSQL_code').setCode($('textSQL_code').getCode()+'".$sql."\\n');
					$('textSQL_code').editor.syntaxHighlight('init');
					ANIMATE.animate('btSQL1', 'opacity:0', '1000', function(){ANIMATE.animate('btSQL1', 'opacity:1', '2000', '', 'default');}, 'default');
					";
		$event->setCodeJS($code);
	}
}

function data_select_after($ds) {data_after_new($ds);}
function data_after_delete($ds) {data_after_new($ds);}
function data_after_deleteall($ds) {data_after_new($ds);}
function data_after_update($ds) {data_after_new($ds);}

