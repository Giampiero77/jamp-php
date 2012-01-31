<?php
/**
* Object OBJECT
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_object extends ClsObject {
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
		$this->property["id"] 		= array("value" => $id, "inherit" => false, "html" => true);
		$this->property["type"]		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["align"]   	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["archive"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["border"]   = array("value" => null, "inherit" => false, "html" => true);
		$this->property["classid"]  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["codebase"] = array("value" => null, "inherit" => false, "html" => true);
		$this->property["codetype"] = array("value" => null, "inherit" => false, "html" => true);
		$this->property["data"]   	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["declare"]  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["height"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["width"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["hspace"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["name"]	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["standby"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["usemap"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["vspace"]	= array("value" => null, "inherit" => false, "html" => true);

		$this->addEventListener("window", "resize", "Resize");
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
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$code = "\n".$tab."<object ".$this->getProperty("html", true, false).">";
		foreach ($this->child as $obj) $code .= $obj->codeHTML($tab."\t");
		$code .= "\n".$tab."</object>";
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
					$this->property["height"]["value"] = "auto";
					$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
				}
			break;
			case "width":
				if ($this->property["width"]["value"] == "autosize")
				{
					$this->property["width"]["value"] = "auto";
					$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
				}
			break;
		}
	}
}
?>
