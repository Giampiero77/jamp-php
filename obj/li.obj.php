<?php
/**
* Object LI
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_li extends ClsObject {
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
		$this->property["id"] 	   = array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["value"]   = array("value" => null, "inherit" => false, "html" => false);
		$this->property["href"]    = array("value" => null, "inherit" => false, "html" => false);
		$this->property["target"]  = array("value" => null, "inherit" => false, "html" => false);
		$this->property["compact"] = array("value" => null,  "inherit" => false, "html" => true);
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
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$code = "\n$tab<li ".$this->getProperty("html", true, false).">";
		if (empty($this->property["href"]["value"])) $code .= "\n\t$tab".$this->property["value"]["value"];
		else
		{
			if (empty($this->property["target"]["value"])) $code .= "\n\t$tab<a href=\"".$this->property["href"]["value"]."\">".$this->property["value"]["value"]."</a>";
			else  $code .= "\n\t$tab<a target=\"".$this->property["target"]["value"]."\" href=\"".$this->property["href"]["value"]."\">".$this->property["value"]["value"]."</a>";
		}
		foreach ($this->child as $obj)	$code .= $tab.$obj->codeHTML($tab."\t");
		$code .= "\n$tab</li>";
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