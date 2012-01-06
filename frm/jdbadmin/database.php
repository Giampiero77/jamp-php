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
if($_SESSION["jdbadmin"]['lang'] == "EN") $xml = new ClsXML("database.xml");
else $xml = new ClsXML(array("database.xml", "lang/".$_SESSION["jdbadmin"]['lang']."/database.xml"));
$event = new ClsEvent($xml);
$DS_CONN["ds0"] = $conn;
$DS_CONN["ds1"] = $conn;
$DS_CONN["dsGrid"] = $conn;
$DS_CONN["dsTables"] = $conn;
$DS_CONN["dsViews"] = $conn;
$DS_CONN["dsFunctions"] = $conn;
$DS_CONN["dsProcedures"] = $conn;
$DS_CONN["dsExport"] =  $conn;
$event->managerRequest();

function html_load()
{
	global $event;
   $_SESSION["jdbadmin"]['database'] = $_GET['database'];
	$code = '
		function updateGrid(id, box)
		{
			if(box == "tab3") 
			{
				AJAX.request("POST", "database.php", "data=load&dsobjname=dsTables", false, true);
				AJAX.request("POST", "database.php", "data=load&dsobjname=dsViews", false, true);
				AJAX.request("POST", "database.php", "data=load&dsobjname=dsFunctions", false, true);
				AJAX.request("POST", "database.php", "data=load&dsobjname=dsProcedures", false, true);
			}
		}
		SYSTEMEVENT.addAfterCustomFunction("TABS","setFocus","updateGrid");

		function exportdatabase(outfile)
		{
			var objtables = $("select1");
			var objviews = $("select2");
			var objfunctions = $("select3");
			var objprocedures = $("select4");
			var tables = Array();
			var views = Array();
			var functions = Array();
			var procedures = Array();
			for (var i=0; i<objtables.length; i++) if (objtables.options[i].selected) tables[tables.length] = objtables.options[i].value;
			for (var i=0; i<objviews.length; i++) if (objviews.options[i].selected) views[views.length] = objviews.options[i].value;
			for (var i=0; i<objfunctions.length; i++) if (objfunctions.options[i].selected) functions[functions.length] = objfunctions.options[i].value;
			for (var i=0; i<objprocedures.length; i++) if (objprocedures.options[i].selected) procedures[procedures.length] = objprocedures.options[i].value;
			var post = "data=load&dsobjname=dsExport";
			post += "&createdb=" + $("createdb").value;
			post += "&tables=" + tables.join(",");
			post += "&tablestruc=" + $("tablestruc").value;
			post += "&tabledata=" + $("tabledata").value;
			post += "&tablestartrow=" + $("tablestartrow").value;
			post += "&tablelimitrow=" + $("tablelimitrow").value;
			post += "&views=" + views.join(",");
			post += "&viewstruc=" + $("viewstruc").value;
			post += "&viewdata=" + $("viewdata").value;
			post += "&viewstartrow=" + $("viewstartrow").value;
			post += "&viewlimitrow=" + $("viewlimitrow").value;
			post += "&functions=" + functions.join(",");
			post += "&procedures=" + procedures.join(",");
			if(outfile)
			{
				var checkedRadio = RADIO.getCheckedObj("exptype");
				post += "&exptype=" + checkedRadio.value;
 				$("datapost").value = post;
				$("exportfile").submit();
			}
			else AJAX.request("POST", "database.php", post, false, true);
		}

		function execute()
		{
			var dsGrid = $("dsGrid");
			delete(dsGrid.DSresult);
			var sql = encodeURIComponent(textSQL_cp.getCode());
			if (sql == "") return;
			AJAX.rewriteObj("gridds2", "database.php?sql=" + sql);
			AJAX.request("POST", "database.php", "data=load&dsobjname=dsGrid&sql=" + sql, true, true);
		}

		function ds_delete(dsObjName)
		{
			if (dsObjName=="ds1") 
			{
				if (confirm("'.MESSAGE::translate("MSG007").'")) 
				{
					var user = encodeURIComponent($("select").value);
					var host = encodeURIComponent($("text1").value);
					AJAX.request("POST", "database.php", "data=delete&dsobjname=ds1&user="+user+"&host="+host, true, true);
				}
			}
			return false;
		}

  		function upload_posted()
    	{	
			var filename = encodeURIComponent(TEXT.objposted.value);
			 alert(filename);
			AJAX.request("POST", "database.php", "data=update&dsobjname=ds1&import="+filename, true, true);
    	}
    	SYSTEMEVENT.addAfterCustomFunction("TEXT", "AfterPost", "upload_posted");
		SYSTEMEVENT.addBeforeCustomFunction("DS","dsdelete", "ds_delete");
	';
	$event->setCodeJS($code);
}

function data_select_before($ds) 
{
	global $xml, $version;
	$i = 1;
	$result = array();
	switch ($ds->getPropertyName("id")) 
	{
		case "ds0":
			 if ($_SESSION["jdbadmin"]["mysql"])
			 {
				$ds->ds->dsShowUsers();
				$user = array();
				$i=1;
				while($row = $ds->ds->dsGetRow())
				{
					 if (!isset($user[$ds->ds->property["row"]->User])) $result[$i++]['User'] = $ds->ds->property["row"]->User;
					 $user[$ds->ds->property["row"]->User] = true;
				}
				$ds->ds->property['tot'] = $i-1;
			 }
			break;
		case "ds1":
				if ($_SESSION["jdbadmin"]["mysql"]) $ds->ds->dsShowUsers($_SESSION["jdbadmin"]['database']);
			break;
		case "dsGrid":
			if (isset($_POST["sql"])) $_SESSION["sql"] = $_POST["sql"];
			if (isset($_SESSION["sql"]))
			{
				$query = $ds->ds->dsQueryParsing($_SESSION["sql"]);
				$ds->ds->dsDBSelect($_SESSION["jdbadmin"]['database']);
				foreach ($query as $qry)
				{
					if(preg_match("/^SELECT /i", $qry)) $queryselect = $qry;
					else $ds->ds->dsQuery($qry);
				}
				if (isset($queryselect))
				{
                    if (isset($_REQUEST["dsorder"]))
                    {
                        $pos = stripos($qry, " ORDER BY ");
                		if ($pos !== false) $qry = substr($qry, 0, $pos);
                        $order = explode(" ", $_REQUEST["dsorder"]);
				        if (count($order)==1) $qry .= " ORDER BY `".$order[0]."`";
				        else $qry .= " ORDER BY `".$order[0]."` ".$order[1]; 
                    }
					$ds->ds->dsQuerySelect($qry);
					$out = $ds->ds->dsShowColumnsResult();
					$i = 1;
					while($row = $ds->ds->dsGetRow())
					{
						$k=0;
						foreach($row as $key => $val)
						{
							if ($out[$k++]->blob) $val = "BLOB ".number_format(count($val)/1024,2)." KB";
							$result[$i][$key] = $val;
						}
						$i++;
					}
					$ds->ds->setQryCount($qry);
				}
				else 
				{
					$ds->ds->property["result"] = false;
					return false;
				}
			}
		break;
		case "dsTables":
			$ds->ds->dsShowTables($_SESSION["jdbadmin"]['database']);
			while($row = $ds->ds->dsGetRow()) $result[]['Name'] = $ds->ds->property["row"]->Name;
		break;
		case "dsViews":
			$ds->ds->dsShowViews($_SESSION["jdbadmin"]['database']);
			while($row = $ds->ds->dsGetRow()) $result[]['Name'] = $ds->ds->property["row"]->Name;
		break;
		case "dsFunctions":
			if ($_SESSION["jdbadmin"]["mysql"])
			{
			 $ds->ds->dsShowfunctions($_SESSION["jdbadmin"]['database']);
			 while($row = $ds->ds->dsGetRow()) $result[]['Name'] = $ds->ds->property["row"]->name;
			} 
		break;
		case "dsProcedures":
			if ($_SESSION["jdbadmin"]["mysql"])
			{
			 $ds->ds->dsShowProcedures($_SESSION["jdbadmin"]['database']);
			 while($row = $ds->ds->dsGetRow()) $result[]['Name'] = $ds->ds->property["row"]->name;
			} 
		break;
		case "dsExport": 
 			require_once("export.php");
			$ds->ds->property['tot'] = "1";
		break;
	}
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
	$grid["grid_table"] = array("objtype","dsitem", "maxlenght", "minlenght", "format");
	$query = $ds->ds->dsQueryParsing($_GET["sql"]);
	foreach ($query as $qry) if (preg_match("/^SELECT /i", $qry)) $queryselect = $qry;
	if(isset($queryselect))
	{
		$rows = $ds->ds->dsShowColumnsResult($queryselect);
		foreach($rows as $row) 
		{
			if ($row->blob)
			{
				$grid["grid_table"]["format"][] = null;
				$grid["grid_table"]["objtype"][] = "label";
			}
			else
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
			$grid["grid_table"]["dsitem"][] = $row->name;
			$grid["grid_table"]["maxlenght"][] = $row->max_length;
			$grid["grid_table"]["minlenght"][] = ($row->not_null == true) ? "1" : null;
			if($row->primary_key == true) $grid["key"] = $row->name;
		}
		$gridds->setProperty("objtype", $grid["grid_table"]["objtype"]);
		$gridds->setProperty("dsitem", $grid["grid_table"]["dsitem"]);
		$gridds->setProperty("itemlabel", $grid["grid_table"]["dsitem"]);
		$gridds->setProperty("maxlenght", $grid["grid_table"]["maxlenght"]);
		$gridds->setProperty("minlenght", $grid["grid_table"]["minlenght"]);
		$gridds->setProperty("format", $grid["grid_table"]["format"]);
		if (isset($grid["key"])) $event->setCodeJS("$('dsGrid').p.DSkey = '".$grid["key"]."';");
	}
}

function data_update($ds) 
{
	if ($ds->getPropertyName("id")=="ds1") 
	{		
		global $event, $system;
		$ds->ds->dsConnect();
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
			$ds->ds->dsDBSelect($_SESSION["jdbadmin"]['database']);
			foreach ($query as $qry) if (!preg_match("/^SELECT /i", $qry)) $ds->ds->dsQuery($qry);
			$event->setCodeJS("alert('SQL Executed'); $('importtxt').value='';");
		}	
		else $ds->ds->dsModDbGrant($_POST);
	}
	return false;
}

function data_new($ds) 
{
	if ($ds->getPropertyName("id")=="ds1") 
	{		
		$_POST['Db'] = $_SESSION["jdbadmin"]['database'];
		$ds->ds->dsConnect();
		$ds->ds->dsAddDbGrant($_POST);
	}
	return false;
}

function data_delete($ds) 
{
	if ($ds->getPropertyName("id")=="ds1") 
	{
 		$ds->ds->dsConnect();
   		$ds->ds->DropGrantDb($_SESSION["jdbadmin"]['database'], $_POST['user'], $_POST['host']);
	}
	return false;
}
