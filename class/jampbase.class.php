<?php
/**
* Class jampBase
* @author	Fulvio Alessio <afulvio@gmail.com>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

require(dirname(__FILE__).'/jampinterface.class.php');
abstract class jampBase implements jampInterface {
	protected $_system		= null;
	protected $_event		= null;
	protected $_xml			= null;

	public function __construct() {
		global $system;
		$system = new ClsSystem(true, $this);
		$this->_system = & $system;	// for compatibility
	}

	public function getVersion() {
		return $this->getSystem()->version;
	}

	public function getSystem() {
		return isset($this->_system) ? $this->_system : $GLOBALS['system'];
	}

	public function getEvent() {
		return isset($this->_event) ? $this->_event : $GLOBALS['event'];
	}

	public function getXml() {
		return isset($this->_xml) ? $this->_xml : $GLOBALS['xml'];
	}

	public function getEnv($var) {
		return $_POST[$var];
	}

	public function setEnv($var, $value) {
		$_POST[$var] = $value;
	}

	public function setCodeJs($code) {
		return $this->getEvent()->setCodeJs($code);
	}

	public function setRule($dsName, $code) {
		$GLOBALS['DS_VALIDATE_RULE'][$dsName] = $code;
	}

	public function setPropertyXML($ds, $data) {
		$ds->ds->property["tot"] = Count($data);					// force count
		$ds->setProperty("xml", $this->getXml()->dataJSON($data));	// output XML
	}

	public function returnRequest($javascript) {
		$this->getEvent()->returnRequest("", $javascript);
	}

	public function connect($datasource) {
		$ds = $this->getSystem()->newObj("ds", "ds");
		$ds->setProperty("conn", $datasource);
		$ds->ds->dsConnect();
		return $ds;
	}

	public function loadClass($className) {
		require_once($this->getSystem()->dir_real_jamp."/class/${className}.class.php");
	}

	public function doEvent($xmlfile) {
		global $event, $xml;
		$xml			= new ClsXML($xmlfile);
		$event			= new ClsEvent($xml, $this);	// "$this" is client class
		$this->_xml		= & $xml;						// for compatibility
		$this->_event	= & $event;						// for compatibility

		return $this->getEvent()->managerRequest();
	}

	// Jamp Events
	public function html_load() {}
	public function html_before_load() {}
	public function html_after_load() {}
	public function data_select_before($obj) {}
	public function data_select_after($obj) {}
	public function data_before() {}
	public function data_before_load() {}
	public function data_before_loadall() {}
	public function data() {}
	public function data_load() {}
	public function data_after_load() {}
	public function data_loadall() {}
	public function data_after_loadall() {}
	public function data_after() {}
	public function data_before_login() {}
	public function data_login($obj) {}
	public function data_after_login($obj) {}
	public function data_before_new() {}
	public function data_new($obj) {}
	public function data_after_new($obj) {}
	public function data_before_update() {}
	public function data_update($obj) {}
	public function data_after_update($obj) {}
	public function data_before_delete() {}
	public function data_delete($obj) {}
	public function data_after_delete($obj) {}
	public function data_before_deleteall() {}
	public function data_deleteall($obj) {}
	public function data_after_deleteall($obj) {}
	public function data_before_changepasswd() {}
	public function data_changepasswd($obj) {}
	public function data_after_changepasswd($rows) {}
	public function data_before_store() {}
	public function data_store() {}
	public function data_after_store() {}
	public function data_last_multirequest() {}
	public function data_keepalive() {}
	public function pdf_before_addpage($obj) {}
	public function pdf_after_addpage($obj) {}
	public function pdf_before_code($obj) {}
	public function pdf_after_code($obj) {}
	public function before_exception_error($obj) {}
	public function after_exception_error($obj) {}
	public function before_error() {}
	public function after_error($obj) {}
	public function data_import_after($from, $to) {}
	public function before_plot($graph) {}
	public function after_plot($graph) {}
	public function start_graph($xml_graph) {}
	public function end_graph($graphs) {}
	public function xmlpage_event($code) {
		return true;
	}
}