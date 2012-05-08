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
$xml = new ClsXML("index2.xml");
$event = new ClsEvent($xml);
$DS_CONN["ds1"] = $conn; 
$event->managerRequest();

function html_load()
{	
	global $event, $version, $xml;
	 $ds = $xml->getObjById("ds1");
	 $_SESSION["jdbadmin"]["mysql"] = false;
	 $ds->ds->dsConnect();
	 $ds->ds->dsShowDatabases();
	 while($ds->ds->dsGetRow())
	 {
		  if ($ds->ds->property["row"]->Database == 'mysql')
		  {
				$_SESSION["jdbadmin"]["mysql"] = true;
				break;
		  }
	 }
	$code = "
		function startDrag()
		{
			var myRegex = \"^(table|view|procedure|function)$\";
		if (new RegExp(myRegex).exec(this.parentNode.nodetype)==null) return false;
		}

		function DropNode(e)
		{
			TREE.onRelaseContainer(e);
			if (TREE.sourceNode.nodetype+'s' != TREE.destinationNode.parentNode.nodetype) return false;
			var post = 'data=update&dsobjname=ds1';
			post += '&db1=' + encodeURIComponent(TREE.sourceNode.parentNode.parentNode.parentNode.parentNode.textNode.innerHTML);
			post += '&name=' + encodeURIComponent(TREE.sourceNode.textNode.innerHTML);
			post += '&type=' + encodeURIComponent(TREE.sourceNode.nodetype);
			post += '&db2=' + encodeURIComponent(TREE.destinationNode.parentNode.parentNode.parentNode.textNode.innerHTML);
			if (TREE.destinationNode.parentNode.container.style.display=='none') TREE.destinationNode.parentNode.expand.onclick();
			AJAX.request('POST', 'index2.php', post, true, false);
			TREE.destinationNode.parentNode.container.insertBefore(TREE.sourceNode, TREE.destinationNode.parentNode.container.firstChild);
			return false; 
		}

		function tree1_click()
		{
			var timestamp = new Date().getTime();
			var divTree = $('tree1');
			var node = divTree.selectedNode;
			if (node.nodetype=='base') $('iframe1').src = 'server.php?'+timestamp;
			else if (node.nodetype=='database') 
			{
				var string = '?'+timestamp+'&database='+encodeURIComponent(node.textNode.innerHTML);
				$('iframe1').src = node.nodetype+'.php'+string;
				$('status').innerHTML = '<font color=\"#127397\">JdbAdmin ver.".$version."<\/font> - Database: '+node.textNode.innerHTML;
			}
			else if (node.nodetype=='tables' || node.nodetype=='views' || node.nodetype=='procedures'|| node.nodetype=='functions') 
			{
				var database = node.parentNode.parentNode.textNode.innerHTML;
				var string = '?'+timestamp+'&database='+encodeURIComponent(node.parentNode.parentNode.textNode.innerHTML);
				$('iframe1').src = node.nodetype+'.php'+string;
				$('status').innerHTML = '<font color=\"#127397\">JdbAdmin ver.".$version."<\/font> - Database: '+node.parentNode.parentNode.textNode.innerHTML+' -> '+node.nodetype;
            }
            else if (node.nodetype=='table' || node.nodetype=='view' || node.nodetype=='procedure'|| node.nodetype=='function') 
            {
				var database = node.parentNode.parentNode.parentNode.parentNode.textNode.innerHTML;
				var string = '?'+timestamp+'&database='+encodeURIComponent(node.parentNode.parentNode.parentNode.parentNode.textNode.innerHTML);
				string += '&node='+encodeURIComponent(node.textNode.innerHTML);
				$('iframe1').src = node.nodetype+'.php'+string;
				$('status').innerHTML = '<font color=\"#127397\">JdbAdmin ver.".$version."<\/font> - Database: '+database+' -> '+node.nodetype+': '+node.textNode.innerHTML;
            }
		}
		SYSTEMEVENT.addAfterCustomFunction('TREE', 'onTextClick', 'tree1_click');
		SYSTEMEVENT.addBeforeCustomFunction('TREE', 'onStartDragNode', 'startDrag');
		SYSTEMEVENT.addBeforeCustomFunction('TREE', 'onDropNode', 'DropNode');
		$('status').innerHTML = '<font color=\"#127397\">JdbAdmin ver.".$version."<\/font>' 
		
		function Ins(obj,pre,post)
		{
			if(iframe1 != undefined)
			{
				var txtCODE = (iframe1.$('textSQL_code') == undefined) ? iframe1.$('textarea_code') : iframe1.$('textSQL_code'); 
				if (txtCODE != undefined)
				{ 
					if (obj.nodetype=='table')
					{
						txtCODE.editor.insertCode(pre+'`'+obj.name+'`'+post);
					}
					else
					{	
						var code = obj.parentkey.split(' ');
						txtCODE.editor.insertCode(pre+'`'+code[1]+'`.`'+obj.name+'`'+post);
					}
					iframe1.DS.dschange(iframe1.$('ds1'));
				}
			}
		}

		function Ins1(obj) Ins(obj, '', ',');
		function Ins2(obj) Ins(obj, '\\t', ',\\n');
		function Ins3(obj) Ins(obj, '', '');
		function Ins4(obj) Ins(obj, '\\t', '\\n');
	";
	$event->setCodeJs($code);
}
 
function data_select_before($ds) 
{
    global $xml;
    $i=0;
	$result = array();
	$pref_tables = "Tables";
	$pref_views = "Views";
	$pref_procedures = "Procedures";
	$pref_functions = "Functions";
    if (isset($_REQUEST["dswhere"])) 
    {
		$type = $_REQUEST["type"]; 
        switch ($type) 
        {
            case "base":
                $ds->ds->dsShowDatabases();
                while($ds->ds->dsGetRow())
                {
                    $name = $ds->ds->property["row"]->Database;
                    $result[$i++] = AddNode("server", $name, $name, "database", "false");
                } 
               break;
            case "database":
                $result[0] = AddNode($_REQUEST["name"], $pref_tables."_".$_REQUEST["name"], MESSAGE::translate("TABLES"), "tables", "false");
                $result[1] = AddNode($_REQUEST["name"], $pref_views."_".$_REQUEST["name"], MESSAGE::translate("VIEWS"), "views", "false");
                $result[2] = AddNode($_REQUEST["name"], $pref_procedures."_".$_REQUEST["name"], MESSAGE::translate("PROCEDURES"), "procedures", "false");
                $result[3] = AddNode($_REQUEST["name"], $pref_functions."_".$_REQUEST["name"], MESSAGE::translate("FUNCTIONS"), "functions", "false");
                break;
            case "tables":
						$database = substr($_REQUEST['name'], strlen($pref_tables)+1);
						$ds->ds->dsShowTables($database);
						while($ds->ds->dsGetRow())
						{
						  $name = $ds->ds->property["row"]->Name;
						  $result[$i++] = AddNode($_REQUEST["name"], $database." ".$name, $name, "table", "false");
						} 
					 break;
				case "views":
					 $database = substr($_REQUEST['name'], strlen($pref_views)+1);
					 $ds->ds->dsShowViews($database);
					 while($ds->ds->dsGetRow())
					 {
						  $name = $ds->ds->property["row"]->Name;
						  $result[$i++] = AddNode($_REQUEST["name"], $database." ".$name, $name, "view", "false");
					 } 
               break;
            case "procedures":
					 if ($_SESSION["jdbadmin"]["mysql"])
					 {
						  $ds->ds->dsShowProcedures(substr($_REQUEST['name'], strlen($pref_procedures)+1));
						  while($ds->ds->dsGetRow())
						  {
							 $name = $ds->ds->property["row"]->name;
						$result[$i++] = AddNode($_REQUEST["name"], $name, $name, "procedure", "true");
						  } 
					 }
              break;
            case "functions":
					 if ($_SESSION["jdbadmin"]["mysql"])
					 {
						$ds->ds->dsShowFunctions(substr($_REQUEST['name'], strlen($pref_functions)+1));
						while($ds->ds->dsGetRow())
						{
					 $name = $ds->ds->property["row"]->name;
					 $result[$i++] = AddNode($_REQUEST["name"], $name, $name, "function", "true");
						} 
					} 
               break; 
				 case "table":
					 list($database, $table) = explode(" ", $_REQUEST['name']);
					 $ds->ds->dsDBSelect($database);
					 $fields = $ds->ds->dsShowColumnsResult($table);
					 foreach ($fields as $field)
					 {
						$result[$i++] = AddNode($_REQUEST["name"], $field->name, $field->name, "field", "true");
					 } 
					 break; 
				 case "view":
					 list($database, $view) = explode(" ", $_REQUEST['name']);
					 $ds->ds->dsDBSelect($database);
					 $fields = $ds->ds->dsShowColumnsResult($view);
					 foreach ($fields as $field)
					 {
						$result[$i++] = AddNode($_REQUEST["name"], $field->name, $field->name, "field", "true");
					 } 
				  break; 
		  }
	 }
    else $result[0] = AddNode("", "server", "Server", "base", "false");
	if (count($result)>0) 
	{
		$ds->setProperty("xml", $xml->dataJSON($result));
 		return false;
	}
}

function AddNode($pkey, $key, $name, $type, $nochild) 
{
	$result['parentkey'] = $pkey;
	$result['key'] = $key;
	$result['name'] = $name;
	$result['type'] = $type;
	$result['nochild'] = $nochild;
	return $result;
}

function data_update($ds) 
{
	$type = $_REQUEST["type"]; 
    $ds->ds->dsConnect();
    switch ($type) 
    {
        case "table":
            	$ds->ds->RenameTable($_POST['db1'], $_POST['name'], $_POST['db2'], $_POST['name']);
            break;
        case "view":
            	$ds->ds->RenameView($_POST['db1'], $_POST['name'], $_POST['db2'], $_POST['name']);
            break;
        case "procedure":
            	$ds->ds->RenameProcedure($_POST['db1'], $_POST['name'], $_POST['db2'], $_POST['name']);
            break;
        case "function":
            	$ds->ds->RenameFunction($_POST['db1'], $_POST['name'], $_POST['db2'], $_POST['name']);
            break;
    }
	return false;
}
?>
