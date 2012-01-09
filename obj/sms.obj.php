<?php
/**
* Object SMS (http://www.progettosms.it/alyx.php)
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_sms extends ClsObject {
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
		$this->property["id"] 			= array("value" => $id, "inherit" => false, "html" => true);
 		$this->property["java"]  	  	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["cssfile"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["class"] 	  	= array("value" => null,  "inherit" => false, "html" => true);
 		$this->property["lbOAdC"] 	  	= array("value" => "Mittente:", "inherit" => false, "html" => false);
		$this->multiObj = true;
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$class = $this->property["class"]["value"];	
		$this->codejs = "";

		if (!empty($this->property["lbOAdC"]))
		{
			$ObjSMS = $this->addChild($id."_OAdC", "text");
			$ObjSMS->setProperty("class", $class."_OAdC");
			$ObjSMS->setProperty("value", "Mittente:");
		}
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
		$class = $this->property["class"]["value"];	
		$code = "\n".$tab."<div ".$this->getProperty("html", true, false).">";

		//Label
		if (!empty($this->property["label"]["value"])) $code .= "<div id=\"".$id."_label\" class=\"".$class."_label\" >".$this->property["label"]["value"]."</div>";
		
		$code .= "\n\t".$tab."<div>";
		foreach ($this->child as $obj) $code .= "\t\t".$tab.$obj->codeHTML($tab."\t\t");
		$code .= "\n\t".$tab."</div>";
		$code .= "\n".$tab."</div>";
		return $code;
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		switch($name)
		{
			case "autoscroll":
				if ($this->property["autoscroll"]["value"] == "true")	$this->propertyJS["autoscroll"] = true;
			break;

			case "height":
				if ($this->property["height"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["height"]["value"] = "0px";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "GRIDDS.autoHeight('$id');");
				}
			break;

			case "width":
				if ($this->property["width"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["width"]["value"] = "0px";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "GRIDDS.autoWidth('$id');");
				}
			break;
		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		if (empty($this->property["display"]["value"])) return "GRIDDS.refreshObj(\"".$this->property["id"]["value"]."\");";
		return "GRIDDS.displayObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>
