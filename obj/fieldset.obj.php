<?php
/**
* Object FIELDSET
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_fieldset extends ClsObject {

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
		$this->property["id"] 	 	= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["legend"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["display"]	= array("value" => null, "inherit" => true,  "html" => false);
		$this->property["width"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["height"]	= array("value" => null, "inherit" => false, "html" => false);
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
		if (!empty($this->property["width"]["value"])) $this->property["style"]["value"] .= "width:".$this->property["width"]["value"].";";
		if (!empty($this->property["height"]["value"])) $this->property["style"]["value"] .= "height:".$this->property["height"]["value"].";";
		$class = "";
		if (!empty($this->property["class"]["value"])) $class = " class=\"".$this->property["class"]["value"]."\"";
		$code = "\n$tab<fieldset ".$this->getProperty("html", true, false).">\n";
		$code .= "\n$tab<legend><b>".$this->property["legend"]["value"]."</b></legend>\n";
		foreach ($this->child as $obj) $code .= $tab.$obj->codeHTML($tab."\t");
		$code .= "\n$tab</fieldset>";
		return $code;
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		$id = $this->property["id"]["value"];
		switch($name){
			case "height":
				if ($this->property["height"]["value"] == "autosize")
				{
					$this->property["height"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
				}
			break;
			case "width":
				if ($this->property["width"]["value"] == "autosize")
				{
					$this->property["width"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
				}
				if ($this->property["width"]["value"] == "autosize_center")
				{
					$this->property["width"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoWidthCenter('$id');");
				}
			break;
		}
	}
}
?>
