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
if($_SESSION["jdbadmin"]['lang'] == "EN") $xml = new ClsXML("fk.xml");
else $xml = new ClsXML(array("fk.xml", "lang/".$_SESSION["jdbadmin"]['lang']."/fk.xml"));
$event = new ClsEvent($xml);

$DS_CONN["dsLField"] = $_SESSION["jdbadmin"]['engine']."|".$_SESSION["jdbadmin"]['server']."|".$_SESSION["jdbadmin"]['user']."|".$_SESSION["jdbadmin"]['pwd']."|".$_SESSION["jdbadmin"]['port'];
$DS_CONN["dsRDB"] = $DS_CONN["dsLField"];
$DS_CONN["dsRTable"] = $DS_CONN["dsLField"];
$DS_CONN["dsRField"] = $DS_CONN["dsLField"];
$event->managerRequest();

function html_load()
{
	global $event;
	$code = "

		SYSTEMEVENT.messagebox.style.height = '100%';
		SYSTEMEVENT.messagebox.style.width = '100%';
		SYSTEMEVENT.messagebox.style.top = '0px';
		SYSTEMEVENT.messagebox.style.left = '0px';
		SYSTEMEVENT.messagebox.style.marginLeft = '0px';

		function loadRtable(sel)
		{
			AJAX.request('POST', 'fk.php', 'data=load&dsobjname=dsRTable&remotedb='+encodeURIComponent(sel.value), false, true);
		}

		function loadRfield(sel)
		{
			var db = $('database');
			AJAX.request('POST', 'fk.php', 'data=load&dsobjname=dsRField&remotedb='+encodeURIComponent(db.value)+'&remotetable='+encodeURIComponent(sel.value), false, true);
		}

		function createFK()
		{
			var dsLField = $(\"dsLField\");
			var dsRField = $(\"dsRField\");
			var ondelete = $(\"ondelete\");
			var onupdate = $(\"onupdate\");
			var database = $(\"database\");
			var table = $(\"table\");
			var fieldSX = Array();
			var fieldDX = Array();

			if (dsLField.DSmultipos.length == 0) {alert(\"".MESSAGE::translate("MSG005")."\"); return; };
			if (dsRField.DSmultipos.length == 0) {alert(\"".MESSAGE::translate("MSG006")."\"); return; };
			var i = 0;
			for (var key in dsLField.DSmultipos)
			{
				if(dsLField.DSmultipos.hasOwnProperty(key))	fieldSX[i++]= '`'+dsLField.DSresult[key][\"Field\"]+'`';
			}
			i = 0;
			for (var key in dsRField.DSmultipos)
			{
				if(dsRField.DSmultipos.hasOwnProperty(key)) fieldDX[i++]= '`'+dsRField.DSresult[key][\"Field\"]+'`';
			}
			post = \"data=new&dsobjname=dsLField\";
			post += \"&database=\" + database.value;
			post += \"&table=\" + table.value;
			post += \"&fieldSX=\" + fieldSX.join(',');
			post += \"&fieldDX=\" + fieldDX.join(',');
			post += \"&ondelete=\" + ondelete.value;
			post += \"&onupdate=\" + onupdate.value;
			AJAX.request(\"POST\", dsRField.p.DSaction, post, true, true);
		}
	";
	$event->setCodeJS($code);
}

function data_select_before($ds) 
{
	global $xml;
	$i=1;
	$result = array();
 	if ($ds->getPropertyName("id")=="dsLField") 
	{
		$ds->ds->dsShowColumns($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['table']);
		while($ds->ds->dsGetRow()) $result[$i++]['Field'] = $ds->ds->property["row"]->Field;
	}
 	else if ($ds->getPropertyName("id")=="dsRDB") $ds->ds->dsShowDatabases();
 	else if ($ds->getPropertyName("id")=="dsRTable" && isset($_POST['remotedb'])) 
	{
		$ds->ds->dsShowTables($_POST['remotedb']);
		while($ds->ds->dsGetRow()) $result[$i++]['Name'] = $ds->ds->property["row"]->Name;
	}
 	else if ($ds->getPropertyName("id")=="dsRField" && isset($_POST['remotedb'])  && isset($_POST['remotetable'])) 
	{
		$ds->ds->dsShowColumns($_POST['remotedb'], $_POST['remotetable']);
		while($ds->ds->dsGetRow()) $result[$i++]['Field'] = $ds->ds->property["row"]->Field;
	}
	if (count($result)>0) $ds->setProperty("xml", $xml->dataJSON($result));
}

function data_new($ds) 
{
	global $event;
    if ($ds->getPropertyName("id")=="dsLField") 
	{
        $ds->ds->dsConnect();
     	$ds->ds->AddForeignKey($_SESSION["jdbadmin"]['database'], $_SESSION["jdbadmin"]['table'], $_POST['fieldSX'],$_POST['database'],$_POST['table'],$_POST['fieldDX'],$_POST['ondelete'],$_POST['onupdate']);
     	$event->setCodeJs("parent.SYSTEMEVENT.Close();parent.ds.reload('dsFK');");
	}
    return false;
}
