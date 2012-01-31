<?php
/**
* Object TXTRESIZE
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_txtresize extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"] 	 	= array("value" => $id, "inherit" => false, "html" => true);
		$this->property["class"] 	= array("value" => "txtresize", "inherit" => false, "html" => true);
		$this->property["style"] 	= array("value" => null, "inherit" => false, "html" => true);
 		$this->property["java"]  	= array("value" => "txtresize.js", "inherit" => false, "html" => false);
		$this->property["cssfile"]	= array("value" => null, "inherit" => false, "html" => false);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$class =  $this->property["class"]["value"];
 		$code  = "\n$tab<div ".$this->getProperty("html", true, false).">";
		$code .= "\n$tab\t<span class=\"".$class."_less\" onclick=\"TXTRESIZE.resize(-1);\">&nbsp;</span>";
		$code .= "\n$tab\t<span class=\"".$class."_plus\" onclick=\"TXTRESIZE.resize(1);\">&nbsp;</span>";
 		$code .= "\n$tab</div>";
		return $code;
	}

	/**
	* Builds the object
	*/
	public function BuildObj()	
	{
		$this->setCSS();
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