<?php
/**
* Object BUTTON
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_button extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id) 
	{
		$this->property["id"] 		  	= array("value" => $id,   "inherit" => false, "html" => true);
		$this->property["name"] 	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["type"]			= array("value" => "button",  "inherit" => false, "html" => true);
		$this->property["value"] 	  	= array("value" => "button",  "inherit" => false, "html" => true);
		$this->property["tabindex"]  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["size"] 	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["alt"] 		  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["src"] 		  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["readonly"]  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["align"] 	  	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["accesskey"] 	= array("value" => null,  "inherit" => false, "html" => true);

		$this->property["onblur"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onchange"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onclick"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["ondblclick"]	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onfocus"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onmousedown"]	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onmousemove"]	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onmouseout"]	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onmouseover"]	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onmouseup"]	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onkeydown"]	= array("value" => null,  "inherit" => false, "html" => true);       
		$this->property["onkeypress"]	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onkeyup"]		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["onselect"]		= array("value" => null,  "inherit" => false, "html" => true);

		$this->property["disabled"]  	= array("value" => false, "inherit" => false, "html" => false);
		$this->property["label"] 	  	= array("value" => null,   "inherit" => false, "html" => false);
		$this->property["labelalign"]	= array("value" => "left", "inherit" => false, "html" => false);
		$this->property["labelwidth"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelstyle"]	= array("value" => null, "inherit" => false, "html" => false);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$code = "\n$tab<input ".$this->getProperty("html", true, false);
		if ($this->property["disabled"]["value"] == "true") $code.=" disabled";
		$code .=">";
		$code = $this->getCodeLabelAlign($code, $tab);
		return $code;
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
}
?>