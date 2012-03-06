<?php
/**
* Object TREE
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_tree extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		global $system;
		$this->property["width"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["height"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["target"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->propertyJS["fileopen"] 	= $system->dir_web_jamp.$system->dir_class;
	
		$this->property["name"]		  	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["id"]			= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["value"]		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onchange"]		= array("value" => null, "inherit" => false, "html" => true);

		$this->property["refresh"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dragable"]		= array("value" => false, "inherit" => false, "html" => false);
		$this->property["checkbox"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["oncontextmenu"]= array("value" => null, "inherit" => false, "html" => false);
		$this->property["menuname"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["menufunction"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["menufilter"]	= array("value" => null, "inherit" => false, "html" => false);

		$this->property["dslink"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsorder"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsicon"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsnochild"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsdragable"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dscheckstate"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["java"]			= array("value" => "tree.js", "inherit" => false, "html" => false);
		$this->property["cssfile"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsobj"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsitem"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["nav"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["objtype"]  	= array("value" => null, "inherit" => false, "html" => false);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$width = $this->property["width"]["value"]; 
		$height = $this->property["height"]["value"]; 
		$code = "\n$tab<div ".$this->getProperty("html", true, false)." style=\"width: ".$width.";height:".$height."\"></div>";
		return $code;
	}
	
	/**
	* Builds the object
	*/
	public function BuildObj()	
	{
		$this->setCSS();
		$id = $this->property["id"]["value"];
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
			$this->addEvent($id, $dsobj."Refresh", "TREE.refreshObj(\"$id\");");
		}
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
				if($this->property["height"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["height"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
				}
			break;

			case "width":
				if($this->property["width"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["width"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
				}
			break;
			case "nav":
				if ($this->property["nav"]["value"] == true) $this->propertyJS["dsNav"] = true;
			break;

			case "menuname":
			case "menufunction":
			case "menufilter":
			case "refresh":
			case "checkbox":
			case "dragable":
			case "oncontextmenu":
			case "dskey":
			case "dsparentkey":
			case "dsname":
			case "dslink":
			case "dsicon":
			case "dsorder":
			case "dsnochild":
			case "dsdragable":
			case "target":
				$this->propertyJS[$name] = $this->property[$name]["value"];
	  		break;
		}
	}

	/**
	* Generate the code text
	*/
	public function codeTXT()
	{
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "TREE.refreshObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>
