<?php
/**
* Object TAB
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_tab extends ClsObject {
	/**
	* @var $container Array containing the child objects
	*/
	var $child = array();

	/**
	* @var $tabs Tabs name
	*/
	var $tabs = "";

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["align"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["id"] 	 	= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["label"]   = array("value" => null, "inherit" => false, "html" => false);
		$this->property["display"] = array("value" => $id,  "inherit" => false, "html" => false);	
		$this->property["tabs"]	  	= array("value" => $id,  "inherit" => true, "html" => false);

		$this->property["style"]["value"] = "display: none;";
		$this->property["class"]["value"] = "tab";
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$code = "\n$tab<div ".$this->getProperty("html", true, false).">";
		foreach ($this->child as $obj) $code .= $obj->codeHTML($tab."\t");
		$code .= "\n$tab</div>";
		return $code;
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		foreach ($this->child as $obj) $obj->codePDF($pdf);
	}

	/**
	* Generate the code text
	*/
	public function codeTXT()
	{
		foreach ($this->child as $obj) $obj->codeTXT();
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return 'AJAX.rewriteObj("'.$this->property["tabs"]["value"].'", "'.$_SERVER['PHP_SELF'].'");';
	}
}
?>
