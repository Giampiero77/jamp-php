<?php
/**
* Object VIDEO
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_video extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"]				= array("value" => $id,   "inherit" => false, "html" => true);
		$this->property["src"]				= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["width"]			= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["height"]			= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["autoplay"]			= array("value" => "false",  "inherit" => false, "html" => false);
		$this->property["controls"]			= array("value" => "true",  "inherit" => false, "html" => false);
 		$this->property["wmode"]			= array("value" => "transparent", "inherit" => false, "html" => false);
 		$this->property["loop"]				= array("value" => "false", "inherit" => false, "html" => false);
 		$this->property["console"]			= array("value" => "video", "inherit" => false, "html" => false);//Non usato
 		$this->property["nojava"]			= array("value" => "false", "inherit" => false, "html" => false);//Non usato
 		$this->property["allowfullscreen"]	= array("value" => "false", "inherit" => false, "html" => false);
 		$this->property["showdigits"]		= array("value" => "true", "inherit" => false, "html" => false);
 		$this->property["player"]			= array("value" => "player.swf", "inherit" => false, "html" => false);
 		$this->property["allowsciptaccess"]	= array("value" => "always", "inherit" => false, "html" => false);
 		$this->property["quality"]  		= array("value" => "true", "inherit" => false, "html" => false);
 		$this->property["name"]				= array("value" => "single", "inherit" => false, "html" => false);
 		$this->property["style"]  			= array("value" => "", "inherit" => false, "html" => false);
		$this->property["accesskey"]		= array("value" => null,  "inherit" => false, "html" => true);
 		$this->property["dsobj"]  			= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]			= array("value" => null, "inherit" => false, "html" => false);
		$this->property["java"]				= array("value" => "video.js", "inherit" => false, "html" => false);
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		global $system;
		$this->codejs = "";
		$id = $this->property["id"]["value"];
		$this->Player = $system->dir_web_jamp.$system->dir_template."objcss/default/video/".$this->property["player"]["value"];

		$this->propertyJS["wmode"] = $this->property["wmode"]["value"];
		$this->propertyJS["width"] = $this->property["width"]["value"];
		$this->propertyJS["height"] = $this->property["height"]["value"];
		$this->propertyJS["autostart"] = $this->property["autoplay"]["value"];
		$this->propertyJS["showdigits"] = $this->property["showdigits"]["value"];
		$this->propertyJS["allowfullscreen"] = $this->property["allowfullscreen"]["value"];
		$this->propertyJS["allowsciptaccess"] = $this->property["allowsciptaccess"]["value"];
		$this->propertyJS["name"] = $this->property["name"]["value"];
		$this->propertyJS["quality"] = $this->property["quality"]["value"];
		$this->propertyJS["style"] = $this->property["style"]["value"];
		$this->propertyJS["src"] = $this->property["src"]["value"];
		$this->propertyJS["controls"] = $this->property["controls"]["value"];
		$this->propertyJS["player"] = $this->Player;

		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
			$this->addEvent($id, $dsobj."Move", "VIDEO.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "VIDEO.refreshObj(\"$id\");");
		}
		else if (!empty($this->property["src"]["value"])) $this->setCodeJs("VIDEO.refreshObj(\"".$id."\");");
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
		return "\n$tab<div id=\"".$this->property["id"]["value"]."\"></div>";
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return (empty($this->property["src"]["value"])) ? "VIDEO.refreshObj(\"".$this->property["id"]["value"]."\");" : "";
	}
}
?>
