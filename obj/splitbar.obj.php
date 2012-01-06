<?php
/**
* Object SPLITBAR
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_splitbar extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"] 	   = array("value" => $id,   "inherit" => false, "html" => true);
 		$this->property["size"]    = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["type"]    = array("value" => "horizontal", "inherit" => false, "html" => false);
 		$this->property["java"]    = array("value" => "splitbar.js", "inherit" => false, "html" => false);
		$this->property["cssfile"] = array("value" => null, "inherit" => false, "html" => false);
		$this->addEventListener("window", "resize", "Resize");
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		$pdf->CellObj($this);
	}

	/**
	* Generate the code text
	*/
	public function codeTXT()
	{
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		return "\n$tab<div ".$this->getProperty("html", true, false)."><span>&nbsp;</span></div>";
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$this->setCSS();
		$id = $this->property["id"]["value"];
		$this->property["class"]["value"] .= "_".$this->property["type"]["value"];
		if ($this->property["type"]["value"] == "horizontal")
		{
			$this->property["onmousedown"]["value"] = "SPLITBAR.beginDragHorizontal(this, event); ".$this->property["onmousedown"]["value"];
			$this->addEventBefore($id, "Resize", "SPLITBAR.initWidth('$id');");
		}
		if ($this->property["type"]["value"] == "vertical")
		{
			$this->property["onmousedown"]["value"] = "SPLITBAR.beginDragVertical(this, event); ".$this->property["onmousedown"]["value"];
			$this->addEventBefore($id, "Resize", "SPLITBAR.initHeight('$id');");
		}
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
