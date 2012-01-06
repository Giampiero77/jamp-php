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
if(isset($_REQUEST["logout"])) unset($_SESSION["jdbadmin"]);
if(isset($_REQUEST["lang"])) LANG::$language = $_REQUEST["lang"];
$xml = new ClsXML("test.xml");

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
		if($dsConn->ds->property["open"] == true) $event->setCodeJS("alert('TEST COMPLETATO!')");
	}
}

function html_load()
{
	global $xml;
	$lang = $xml->getObjById(LANG::$language);
	$lang->setProperty("class", "radio_check"); 

	global $event;
	$code = "
		lastlang = '".LANG::$language."';
		function changeLang(obj)
		{
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
			AJAX.request('POST', 'test.php', 'data=loadall&dsobjname=ds1&engine='+sel.value, true, true); 
		}
	
		function login()
		{
			var engine = getValue('engine', 'Insert database');
			var server = getValue('server', 'Insert IP address');
			var port = getValue('port', 'Insert connection port');
			var user = getValue('user', 'Insert username');
			var pwd = getValue('pwd', 'Insert password');
			var lang = RADIO.getCheckedObj('lang');
			var string = 'data=load&dsobjname=ds1';
			string += '&engine='+encodeURIComponent(engine);
			string += '&server='+encodeURIComponent(server);
			string += '&port='+encodeURIComponent(port);
			string += '&user='+encodeURIComponent(user);
			string += '&pwd='+encodeURIComponent(pwd);
			string += '&lang='+lang.id;
			AJAX.request('POST', 'test.php', string, true, true);
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
	if (isset($_REQUEST['engine'])) $engine = $_REQUEST['engine'];
	$result = array();
	foreach ($lines as $line) 
    {
        if ($i>0) 
        {
            list($rs["name"],$rs["engine"],$rs["host"],$rs["user"],$reset,$rs["port"]) = explode("|", trim($line));
            if ($rs["engine"]==$engine) $result[$i-1] = $rs;
        }
		$i++;
    }
    $ds = $xml->getObjById("ds1");
    $out = "\t<ds1 start=\"0\" end=\"0\" limit=\"0\" tot=\"".count($result)."\" action=\"test.php\" order=\"\">\n";
	if (isset($result)) $out .= $xml->dataXML($result);
	$out .= "\t</ds1>\n";
	$ds->setProperty("xml", $out);
}
?>
