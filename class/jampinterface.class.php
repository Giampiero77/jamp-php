<?php

/**
* Interface jampInterface
* @author	Fulvio Alessio <afulvio@gmail.com>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

interface jampInterface {

	// new methods for class use
	public function setEnv($var, $value);
	public function getEnv($var);
	public function doEvent($xmlFile);
	public function setCodeJs($code);
	public function setRule($dsName, $code);
	public function setPropertyXML($ds, $data);
	public function returnRequest($javascript);
	public function connect($datasource);
	public function loadClass($className);
	public function getVersion();

	// Global properties
	public function getSystem();
	public function getEvent();
	public function getXml();

	// Jamp Events
	public function html_load();
	public function html_before_load();
	public function html_after_load();
	public function data_select_before($obj);
	public function data_select_after($obj);
	public function data_before();
	public function data_before_load();
	public function data_before_loadall();
	public function data();
	public function data_load();
	public function data_after_load();
	public function data_loadall();
	public function data_after_loadall();
	public function data_after();
	public function data_before_login();
	public function data_login($obj);
	public function data_after_login($obj);
	public function data_before_new();
	public function data_new($obj);
	public function data_after_new($obj);
	public function data_before_update();
	public function data_update($obj);
	public function data_after_update($obj);
	public function data_before_delete();
	public function data_delete($obj);
	public function data_after_delete($obj);
	public function data_before_deleteall();
	public function data_deleteall($obj);
	public function data_after_deleteall($obj);
	public function data_before_changepasswd();
	public function data_changepasswd($obj);
	public function data_after_changepasswd($rows);
	public function data_before_store();
	public function data_store();
	public function data_after_store();
	public function data_keepalive();
	public function pdf_before_addpage($obj);
	public function pdf_after_addpage($obj);
	public function pdf_before_code($obj);
	public function pdf_after_code($obj);
	public function before_exception_error($obj);
	public function after_exception_error($obj);
	public function before_error();
	public function after_error($obj);
	public function data_import_after($from, $to);
	public function before_plot($graph);
	public function after_plot($graph);
	public function start_graph($xml_graph);
	public function end_graph($graphs);
}