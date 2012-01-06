<?php
/**
* Source File PHP
* @name JdbAdmin
* @author Alyx-Software Innovation <info@alyx.it>
* @version 1.4
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
*/

require_once("./../../class/system.class.php");
$system = new ClsSystem(true);
if(isset($_REQUEST["logout"])) unset($_SESSION["jdbadmin"]);
if(isset($_REQUEST["lang"])) LANG::$language = $_REQUEST["lang"];
if(LANG::$language == "EN") $xml = new ClsXML("index.xml");
else $xml = new ClsXML(array("index.xml", "lang/".LANG::$language."/index.xml"));
require("lang/".LANG::$language."/message.php");
$event = new ClsEvent($xml);
$event->managerRequest();

function data_after_load() 
{
	global $system;
	global $event;
	$_SESSION["jdbadmin"] = array();
	if (isset($_POST['server'])) 
	{
		$_SESSION["jdbadmin"]['engine'] = $_POST['engine'];
		$_SESSION["jdbadmin"]['server'] = $_POST['server'];
		$_SESSION["jdbadmin"]['port'] = $_POST['port'];
		$_SESSION["jdbadmin"]['user'] = $_POST['user'];
		$_SESSION["jdbadmin"]['pwd'] = $_POST['pwd'];
		$_SESSION["jdbadmin"]['lang'] = $_POST['lang'];
		$dsConn = $system->newObj("dsConn", "ds");
		$dsConn->setProperty("dsengine", $_SESSION["jdbadmin"]['engine']);
		$dsConn->setProperty("dshost", $_SESSION["jdbadmin"]['server']);
		$dsConn->setProperty("dsport", $_SESSION["jdbadmin"]['port']);
		$dsConn->setProperty("dsuser", $_SESSION["jdbadmin"]['user']);
		$dsConn->setProperty("dspwd", $_SESSION["jdbadmin"]['pwd']);
		$dsConn->ds->dsConnect();
		if($dsConn->ds->property["open"] == true) $event->setCodeJS("window.location = 'index2.php';");
	}
}

function before_exception_error($exception) 
{
 	$exception->param['title'] = "Login...";
	$exception->param['message'] = MESSAGE::translate("MSG008");
	return true;
}

function html_load()
{
	global $xml;
	$lang = $xml->getObjById(LANG::$language);
	$lang->setProperty("checked", "true"); 

	global $event;
	$code = "
		lastlang = '".LANG::$language."';
		function changeLang(obj)
		{
			if (lastlang != obj.id) location.href  = './index.php?lang='+obj.id;
		}
		SYSTEMEVENT.addAfterCustomFunction('RADIO','toogle', 'changeLang');

		function getValue(id, mes)
		{
			var value = $(id).value;
			if (value==undefined || value=='') 
				{
					SYSTEMEVENT.errorHTML(mes);
					return false;
			}
				return value;
		}

		function engine_change(sel)
		{
			AJAX.request('POST', 'index.php', 'data=loadall&dsobjname=ds1&engine='+sel.value, true, true); 
		}
	
		function login()
		{
			var engine = getValue('engine', 'Insert database');
			var server = getValue('server', 'Insert IP address');
			var port = getValue('port', 'Insert connection port');
			var user = getValue('user', 'Insert username');
			var pwd = ($('pwd').value==undefined || $('pwd').value=='') ? '' : getValue('pwd', 'Insert password');
			var lang = RADIO.getCheckedObj('lang');
			var string = 'data=load&dsobjname=ds1';
			string += '&engine='+encodeURIComponent(engine);
			string += '&server='+encodeURIComponent(server);
			string += '&port='+encodeURIComponent(port);
			string += '&user='+encodeURIComponent(user);
			string += '&pwd='+encodeURIComponent(pwd);
			string += '&lang='+lang.id;
			AJAX.request('POST', 'index.php', string, true, true);
		}
	";
	$event->setCodeJs($code);
}

function data_after() 
{ 
    global $xml;
    $lines = file('../../conf/conn.inc.php');
	$engine = "mysql";
	$i = 0;
	$rs = array();
	if (isset($_REQUEST['engine'])) $engine = $_REQUEST['engine'];
	$result = array();
	foreach ($lines as $line) 
    {
        if ($i>0 && trim($line)!="") 
        {
            list($rs["name"],$rs["engine"],$rs["host"],$rs["user"],$reset,$rs["port"]) = explode("|", trim($line));
            if ($rs["engine"]==$engine) 
			{
				if (strpos($rs["host"], "@")!==false) $rs["host"] = substr($rs["host"], 0, strpos($rs["host"], "@"));
				$result[$i-1] = $rs;
	        }
        }
		$i++;
    }
    $ds = $xml->getObjById("ds1");
	
//  	$out = "<ds1 start=\"0\" end=\"0\" limit=\"0\" tot=\"".count($result)."\" action=\"index.php\">\n";
//    	if (isset($result)) $out .= $xml->dataXML($result);
//  	$out .= "\t</ds1>\n";

	$out = "\n<script>\n";
	$out .= "<![CDATA[";
	$out .= "\n$('ds1').DSaction = 'index.php';";
	$out .= "\n$('ds1').DSresult = [";
  	if (isset($result)) $out .= $xml->dataJSON($result);
	$out .= "];";
	$out .= "\nAJAX.setDsJSON('ds1',0,0,".count($result).",0);";
	$out .= "\n]]>";
	$out .= "\n</script>\n";
	$ds->setProperty("xml", $out);
}
?>
