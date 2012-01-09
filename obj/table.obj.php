<?php
/**
* Object TABLE
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_table extends ClsObject {
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
		$this->property["width"]		= array("value" => null, 	"inherit" => false, "html" => true);
		$this->property["height"]		= array("value" => null, 	"inherit" => false, "html" => true);
		$this->property["cellspacing"]	= array("value" => null, 	"inherit" => false, "html" => true);
		$this->property["border"]		= array("value" => null, 	"inherit" => false, "html" => true);
		$this->property["cellpadding"]	= array("value" => null, 	"inherit" => false, "html" => true);
		$this->property["align"]		= array("value" => null, 	"inherit" => false, "html" => true);
		$this->property["bgcolor"]		= array("value" => null, 	"inherit" => false, "html" => true);
		$this->property["frame"]		= array("value" => null, 	"inherit" => false, "html" => true);
		$this->property["id"]			= array("value" => $id, 	"inherit" => false, "html" => true);
		$this->property["background"]	= array("value" => null, 	"inherit" => false, "html" => true);
		$this->property["rules"]		= array("value" => null, 	"inherit" => false, "html" => true);
		$this->property["summary"]		= array("value" => null, 	"inherit" => true,  "html" => true);
		$this->property["class"]		= array("value" => null, 	"inherit" => true,  "html" => true);
		$this->property["caption"]		= array("value" => null, "inherit" => false, "html" => false);
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
		$class = "";
		if (!empty($this->property["class"]["value"])) $class = " class=\"".$this->property["class"]["value"]."\"";
		$code = "\n$tab<table ".$this->getProperty("html", true, false).">";
		if (!empty($this->property["caption"]["value"])) $code .= "$tab\t<caption$class>".$this->property["caption"]["value"]."</caption>\n";
		foreach ($this->child as $obj) $code .= $tab.$obj->codeHTML($tab."\t");
		$code .= "\n$tab</table>";
		return $code;
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		$id = $this->property["id"]["value"];
		switch($name)
		{
			case "height":
				if ($this->property["height"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["height"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
				}
			break;
		}
	}
}
?>