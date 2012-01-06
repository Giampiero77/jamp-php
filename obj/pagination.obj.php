<?php
/**
* Object PAGINATION
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_pagination extends ClsObject {

	/**
	* @var $container Array containing the child objects
	*/
	var $child = array();

	/**
	* @var $child_property Properties of child objects
	*/
	private $child_property;

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"] 	 	 = array("value" => $id, "inherit" => false, "html" => true);
		$this->property["class"] 	 = array("value" => "pagination", "inherit" => false, "html" => true);
 		$this->property["java"]  	 = array("value" => "pagination.js", "inherit" => false, "html" => false);
 		$this->property["cssfile"]  = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsobj"]  	 = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]   = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["btTotal"]  	 = array("value" => "true", "inherit" => false, "html" => false);
	}

	/**
	* Set the properties of the object
	* @param string $name Name property
	* @param string $value 	Value of Property
	*/
	public function setProperty($name, $value)
	{
		if (is_array($value)) $this->child_property[$name] = $value;
		else parent::setProperty($name, $value);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$id = $this->property["id"]["value"];
		$class = $this->property["class"]["value"];
		$code = "\n$tab<div ".$this->getProperty("html", true, false)."></div>";
		return $code;
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$id = $this->property["id"]["value"];
		$dsobj = $this->property["dsobj"]["value"];
		$this->addEvent($id, $dsobj."Move", "PAGINATION.refreshObj(\"$id\");");
		$this->addEvent($id, $dsobj."Refresh", "PAGINATION.refreshObj(\"$id\");");
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
	}

	/**
	* Generate the code text
	*/
	public function codeTXT()
	{
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
		return "PAGINATION.refreshObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>
