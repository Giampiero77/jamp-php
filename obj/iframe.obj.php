<?php
/**
* Object IFRAME
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_iframe extends ClsObject {
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
		$this->property["id"] 	 		= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["name"]  		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["src"] 			= array("value" => null, "inherit" => false, "html" => true);
		$this->property["width"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["height"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["frameborder"]  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["align"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["longdesc"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["marginheight"] = array("value" => null, "inherit" => false, "html" => true);
		$this->property["marginwidth"]  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["scrolling"] 	= array("value" => null, "inherit" => false, "html" => true);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		return "\n$tab<iframe ".$this->getProperty("html", true, false)."></iframe>";
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

			case "width":
				if ($this->property["width"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["width"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
				}
			break;
		}
	}
}
?>