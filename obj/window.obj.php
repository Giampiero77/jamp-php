<?php
/**
* Object WINDOW
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_window extends ClsObject {

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
		$this->property["display"] 	= array("value" => null,  "inherit" => false, "html" => false);	
		$this->property["align"]  		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["id"] 	  		= array("value" => $id,   "inherit" => false, "html" => true);
		$this->property["value"]  		= array("value" => null,  "inherit" => false, "html" => false);
		$this->property["width"]  		= array("value" => "100px",  "inherit" => false, "html" => false);
		$this->property["height"]  	= array("value" => "100px",  "inherit" => false, "html" => false);
		$this->property["duration"] 	= array("value" =>"200",  "inherit" => false, "html" => false);
		$this->property["alg"] 			= array("value" => "default",  "inherit" => false, "html" => false);
		$this->property["expanded"]	= array("value" => "true", "inherit" => false, "html" => false); //true, false, none
		$this->property["label"] 	  	= array("value" => null,   "inherit" => false, "html" => false);
		$this->property["java"] 	  	= array("value" => "window.js",   "inherit" => false, "html" => false);
		$this->property["type"] 	  	= array("value" => "vertical",   "inherit" => false, "html" => false);

		$this->property["cssfile"] 		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["class"]["value"]		= "window";
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
		$id = $this->property["id"]["value"];
		$class = $this->property["class"]["value"];
		$this->property["id"]["value"] .= "_title";
		$this->property["class"]["value"] .= "_title"; 
		if ($this->property["type"]["value"]=="vertical")
		{
    		$this->property["style"]["value"] .= " width:".$this->property["width"]["value"].";";
    		$code = "\n$tab<div ".$this->getProperty("html", true, false)."><table><tr><td width=\"100%\">";
    		if (!empty($this->property["label"]["value"])) $code .= $this->property["label"]["value"];
    		$code .= "\n$tab</td>";
    		if ($this->property["expanded"]["value"] != "none") $code .= "<td nowrap=\"nowrap\" id=\"".$this->property["id"]["value"]."_icon\" class=\"".$class."_iconV_open\" onclick=\"WIN.slide('$id', '".$this->property["duration"]["value"]."', '".$this->property["alg"]["value"]."');\">&nbsp;</td>";
    		$code .= "</table>";
    		$code .= "\n$tab</div>";
    		$this->property["style"]["value"] .= " height:".$this->property["height"]["value"].";";
		}
		else
		{
			$this->property["class"]["value"] .= "_horizontal"; 
    		$this->property["style"]["value"] .= " width: 10px;";
    		$this->property["style"]["value"] .= " height:".($this->property["height"]["value"]+4)."px;float: left;";
    		$code = "\n$tab<div ".$this->getProperty("html", true, false)."><table style=\"margin-left:-10px;\"><tr>";
    		if ($this->property["expanded"]["value"] != "none") $code .= "<td nowrap=\"nowrap\" id=\"".$this->property["id"]["value"]."_icon\" class=\"".$class."_iconH_open\" onclick=\"WIN.slide('$id', '".$this->property["duration"]["value"]."', '".$this->property["alg"]["value"]."', '".$this->property["alg"]["value"]."');\">&nbsp;</td>";
    		$code .= "<tr><td width=\"100%\" align=\"center\">";
    		if (!empty($this->property["label"]["value"]))
			{
				$length = strlen($this->property["label"]["value"]);
				for ($i = 0; $i < $length; $i++)	$code .= substr($this->property["label"]["value"], $i, 1)."<br>";
			}
    		$code .= "\n$tab</td></tr>";
    		$code .= "</table>";
    		$code .= "\n$tab</div>";
    		$this->property["style"]["value"] = " width:".$this->property["width"]["value"].";";
    		$this->property["style"]["value"] .= " height:".$this->property["height"]["value"].";float: left;";
		}
		$this->property["id"]["value"] = $id;
		$this->property["class"]["value"] = $class;
		if ($this->property["expanded"]["value"] == "false") $this->property["style"]["value"] .= " display: none";
		$code .= "\n$tab<div ".$this->getProperty("html", true, false).">";
		if (!empty($this->property["value"]["value"])) $code .= "\n\t".$tab.$this->property["value"]["value"];
		foreach ($this->child as $obj) $code .= $obj->codeHTML($tab."\t");
		$code .= "\n$tab</div>";
		return $code;
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$this->setCSS();
		$this->codejs = "";
		if ($this->property["expanded"]["value"] == "none") return; 
        if ($this->property["type"]["value"]=="vertical")
        {
    		if ($this->property["height"]["value"] == "0px") $this->property["expanded"]["value"] = "true";
    		if ($this->property["expanded"]["value"] == "false")
    		{ 
    			$this->setCodeJs("$('".$this->property["id"]["value"]."').isExpanded = false;");
    			$this->setCodeJs("$('".$this->property["id"]["value"]."').contentheight = parseInt(\"".$this->property["height"]["value"]."\");");
    			$this->setCodeJs("$('".$this->property["id"]["value"]."_title_icon').className = \"".$this->property["class"]["value"]."_iconV_close\";");
    			$this->property["height"]["value"] = "0px";
    		}
			else $this->setCodeJs("$('".$this->property["id"]["value"]."').isExpanded = true;");
        }
        else
        {
    		if ($this->property["width"]["value"] == "0px") $this->property["expanded"]["value"] = "true";
    		if ($this->property["expanded"]["value"] == "false")
    		{ 
    			$this->setCodeJs("\t$('".$this->property["id"]["value"]."').isExpanded = false;");
    			$this->setCodeJs("\t$('".$this->property["id"]["value"]."').contentwidth = parseInt(\"".$this->property["width"]["value"]."\");");
    			$this->setCodeJs("\t$('".$this->property["id"]["value"]."_title_icon').className = \"".$this->property["class"]["value"]."_iconH_close\";");
    			$this->property["width"]["value"] = "0px";
    		}
			else $this->setCodeJs("$('".$this->property["id"]["value"]."').isExpanded = true;");
        }
        $this->setCodeJs("\t$('".$this->property["id"]["value"]."').type = \"".$this->property["type"]["value"]."\";");
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		switch($name)
		{
			case "width":
				if ($this->property["width"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["width"]["value"] = "0px";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoWidth('".$id."_title');");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
				}
			break;

			case "height":
				if ($this->property["height"]["value"] == "autosize")
				{
					$id = $this->property["id"]["value"];
					$this->property["height"]["value"] = "0px";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEvent($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
				}	
			break;
		}
	}
}
?>
