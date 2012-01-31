<?php
/**
* Object FOOTER
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_footer extends ClsObject {
	/**
	* @var $container Array containing the child objects
	*/
	var $child = array();

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"] 	= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["top"] 	= array("value" => "-15",  "inherit" => false, "html" => false);
		$this->property["class"] 	= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["value"] = array("value" => null, "inherit" => false, "html" => false);
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
	* Generate the footer (pdf)
	* @param string $pdf Class PDF
	*/
	public function footerPDF($pdf)
	{
		foreach ($this->child as $obj) $obj->codePDF($pdf);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$code = "\n$tab<footer ".$this->getProperty("html", true, false).">";
		if (!empty($this->property["value"]["value"])) $code .= "\n\t".$tab.$this->property["value"]["value"];
		foreach ($this->child as $obj) $code .= $obj->codeHTML($tab."\t");
		$code .= "\n$tab</footer>";
		return $code;
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