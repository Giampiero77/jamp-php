<?php
/**
* Object GRAPH
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_graphic extends ClsObject {
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
		global $system;
		$path = $system->dir_web_jamp.$system->dir_tmp.$id.".png";
		$this->property["id"]		= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["width"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["height"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["src"] 		= array("value" => "", "inherit" => false, "html" => true);
		$this->property["path"] 	= array("value" => $path, "inherit" => false, "html" => false);
		$this->property["hide"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["align"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["alt"] 		= array("value" => "", "inherit" => false, "html" => true);
		$this->property["hspace"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["vspace"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["border"]	= array("value" => "0",  "inherit" => false, "html" => true);
		$this->property["href"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["target"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["ismap"] 	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["longdesc"]	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["name"] 	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["usemap"] 	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["value"]  	= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["java"]  	= array("value" => "graphic.js", "inherit" => false, "html" => false);
		$tmpfile = $system->dir_web_jamp.$system->dir_class."tmpfile.php?filename=".$id.".png";
		$this->propertyJS["path"] = $tmpfile;
		$this->addEventListener($id, "load", "function(event) { GRAPHIC.loader = false; }");
		$this->addEventListener($id, "error", "function(event) { GRAPHIC.loader = false; }");
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		global $system;
		if ($this->property["hide"]["value"]!="true")
		{
			$id = $this->property["id"]["value"];
			$img = $this->addChild($id, "img");
			$img->setProperty("style", $this->property["style"]["value"]);
			$img->setProperty("src", $system->dir_real_web.$this->property["path"]["value"]);
			$pdf->CellObj($img);
		}
		@unlink($system->dir_real_web.$this->property["path"]["value"]);
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
		if ($this->property["hide"]["value"]=="true") 	
		{
			$width = $this->property["width"]["value"];
			$height = $this->property["height"]["value"];			
			$this->property["width"]["value"] = 0;
			$this->property["height"]["value"] = 0;			
			$code = "\n$tab<img ".$this->getProperty("html", true, false).">";
			$this->property["width"]["value"] = $width;
			$this->property["height"]["value"] = $height;			
		}
		else $code = "\n$tab<img ".$this->getProperty("html", true, false).">";
 		return $code;
	}

	/**
	* Set the properties of the object
	* @param string $name Name property
	* @param string $value 	Value of Property
	*/
	public function setProperty($name, $value)
	{
		if (!isset($this->property[$name])) $this->property[$name] = array("value" => null, "inherit" => false, "html" => false);
		$this->property[$name]["value"] = $this->parseValue($value);
		$this->setPropertyAfter($name);
	}

	/**
	* Returns the properties desired
	* @param string $property Name property
	* @param boolean $i Index property set
	* @return string
	*/
	public function getPropertyName($property, $i = -1)
	{	
		if (isset($this->property[$property]["value"][$i]))	 return $this->property[$property]["value"][$i];
		else if ($i<0) if (isset($this->property[$property])) return $this->property[$property]["value"];
		return null;
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
