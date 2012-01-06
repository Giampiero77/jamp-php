<?php
/**
* Object HIDDEN
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_hidden extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		unset($this->property);
		$this->property["name"] 	  	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["id"] 		  	= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["value"] 	  	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onchange"]   	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["dsobj"] 	  	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["dsitem"] 	  	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["format"] 	  	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["java"] 	  	= array("value" => "hidden.js", "inherit" => false, "html" => false);
 		$this->property["dsobj"]  		= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"]  		= array("value" => null, "inherit" => false, "html" => false);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		return "\n$tab<input type=\"hidden\" ".$this->getProperty("html", true, false).">";
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
	* Builds the object
	*/
	public function BuildObj()	
	{
		$id = $this->property["id"]["value"];
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
			$this->addEvent($id, $dsobj."Move", "HIDDEN.getDsValue(\"$id\");");
			$this->addEvent($id, $dsobj."Refresh", "HIDDEN.refreshObj(\"$id\");");
			$this->property["onchange"]["value"] = "$('$dsobj').DSchange = true; ".$this->property["onchange"]["value"];
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
			case "format":
				$this->propertyJS["format"] = $this->property["format"]["value"];
			break;
		}
	}

	/**
	* object refresh
	*/
	public function refreshOBJ()
	{
		return "HIDDEN.refreshObj(\"".$this->property["id"]["value"]."\");";
	}
}
?>