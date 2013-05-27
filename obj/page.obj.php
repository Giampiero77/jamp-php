<?php
/**
* Object PAGE
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_page extends ClsObject {
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
		$this->property["id"] 	  	 		= array("value" => $id,	"inherit" => false, "html" => true);
		$this->property["onload"] 	 		= array("value" => null,	"inherit" => false, "html" => true);
		$this->property["loaddata"] 		= array("value" => "false",	"inherit" => false, "html" => false);
		$this->property["out"] 		 		= array("value" => "html",	"inherit" => false, "html" => false);
		$this->property["icon"]		 		= array("value" => "",	"inherit" => false, "html" => false);
		$this->property["alink"] 	 		= array("value" => null,	"inherit" => false, "html" => true);
		$this->property["background"] 	= array("value" => null,	"inherit" => false, "html" => true);
		$this->property["bgcolor"]   		= array("value" => null,	"inherit" => false, "html" => true);
		$this->property["leftmargin"] 	= array("value" => null,	"inherit" => false, "html" => true);
		$this->property["link"] 	 		= array("value" => null,	"inherit" => false, "html" => true);
		$this->property["marginheight"]	= array("value" => null,	"inherit" => false, "html" => true);
		$this->property["marginwidth"] 	= array("value" => null,	"inherit" => false, "html" => true);
		$this->property["onunload"] 		= array("value" => null,	"inherit" => false, "html" => true);
		$this->property["onmousemove"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["text"] 	 		= array("value" => null,	"inherit" => false, "html" => true);
		$this->property["topmargin"] 		= array("value" => null,	"inherit" => false, "html" => true);
		$this->property["vlink"] 	 		= array("value" => null,	"inherit" => false, "html" => true);
		$this->property["title"]			= array("value" => null,	"inherit" => false, "html" => false);
		$this->property["keepalive"]		= array("value" => null,	"inherit" => false, "html" => false);
		$this->property["keepaliveurl"]		= array("value" => null,	"inherit" => false, "html" => false);
		$this->property["java"]				= array("value" => null,	"inherit" => false, "html" => false);
		$this->property["forwardrequest"]= array("value" => null,	"inherit" => false, "html" => false);
		$this->property["cssfile"] 		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["java"]["value"] = array("animate.js");
		$this->property["pageformat"]		= array("value" => "A4",	"inherit" => false, "html" => false);
		$this->property["orientation"]	= array("value" => "L",	"inherit" => false, "html" => false);
		$this->property["pdfname"] 		= array("value" => "", "inherit" => false, "html" => false);
		$this->property["destination"] 	= array("value" => "", "inherit" => false, "html" => false);
		$this->property["compressjs"]		= array("value" => null,	"inherit" => false, "html" => false);
		$this->property["hideloader"]		= array("value" => null,	"inherit" => false, "html" => false);
		$this->property["hidehtmlerror"]	= array("value" => null,	"inherit" => false, "html" => false);
		$this->property["hidejserror"]	= array("value" => null,	"inherit" => false, "html" => false);
		$this->property["hidexmlerror"]	= array("value" => null,	"inherit" => false, "html" => false);
		$this->property["ajax"]			= array("value" => "true",	"inherit" => false, "html" => false);

		$this->addEventListener("window", "resize", "Resize");
		$this->addEvent($id, "Resize", "//Autosize");
	}

	public function isJampEngineActivated() {
		return $this->property["ajax"]["value"] == 'false' ? false : true;
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		global $xml, $system;
		$allDs = $xml->getObjByType("ds");
		foreach ($allDs as $ds) $ds->manualConnect();
		$system->plugin("pdf");
		$pdf = new ClsPDF($this->property["orientation"]["value"],"mm",$this->property["pageformat"]["value"]);

		$footer = $xml->getObjById("footer");
		$pdf->topFooter = 0;
		if($footer != null) $pdf->topFooter = abs(intval($footer->getPropertyName("top", true, false)));

		$pdf->AddPage($this->property["orientation"]["value"],'A4');
		$pdf->AliasNbPages('{nb}');
		userEvent::call("pdf_before_code", $pdf);
		foreach ($this->child as $obj) $obj->codePDF($pdf);
		userEvent::call("pdf_after_code", $pdf);
		$pdf->Output($this->property["pdfname"]["value"], $this->property["destination"]["value"]);
	}

	/**
	 * Generate the code xls
	 */
	public function codeXLS()
	{
		global $xml, $system;
		$allDs = $xml->getObjByType("ds");
		$code = "";
		foreach ($allDs as $ds) $ds->manualConnect();
		userEvent::call("xls_before_code");
		foreach ($this->child as $obj) $code .= $obj->codeXLS();
		userEvent::call("xls_after_code");
		return $code;
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
		global $system, $xml;
		$heads = $xml->getObjByType("head");
		if (is_object($xml) && count($heads)>0) $head = current($heads);
		else $head = $this->addChild("head", "head");
		$code = $head->codeHEAD($this, $this->isJampEngineActivated());
		$code .= "\n<body ".$this->getProperty("html", true, false).">";
		if ($this->isJampEngineActivated()) {
			if ($this->property["loaddata"]["value"] != "true")
			{
				$code .= "\n\t<script type=\"text/javascript\" language=\"JavaScript1.5\">";
				$code .= "\n\t\tfunction LoadComplete()";
				$code .= "\n\t\t{";
				$code .= "\n\t\t\t$(\"pageLoader\").style.display = \"none\";";
				$code .= "\n\t\t\t$(\"pageLock\").style.display = \"none\";";
				$code .= "\n\t\t\tAJAX.loadall = false;";
				$code .= "\n\t\t\tResize();";
				$code .= "\n\t\t}";
				$code .= "\n\t\tif (window.attachEvent) window.attachEvent('onload',LoadComplete);";
				$code .= "\n\t\telse window.addEventListener('DOMContentLoaded',LoadComplete,false);";
				$code .= "\n\t</script>";
			}
			$code .= "\n\t<iframe frameborder=\"0\" id=\"pageLock\" style=\"display:none;\"></iframe>";
			$code .= "\n\t<div id=\"pageLoader\" style=\"display:none\"></div>";
			$code .= "\n\t<div id=\"pageMessageBack\"></div>";
			$code .= "\n\t<div id=\"pageMessage\"></div>";
			$code .= "\n\t<div id=\"pageMessageGhost\"></div>";
		}

		//Code objects children
		foreach ($this->child as $obj) $code .= $obj->codeHTML($tab."\t");

		if ($this->isJampEngineActivated()) {
			//Code Javascript
			$code .= $system->getJs(array("lang/".LANG::$language.".js"));
			$code .= $system->getJs(array("system.event.js"));
			$code .= $system->getJs(array("system.browser.js"));
			$code .= $system->getJs(array("ajax.js"));
			if ($this->property["hideloader"]["value"] == "true") $code .= $system->setJs("AJAX.hideloader = true;");
			$code .= $system->getJs($this->requireJavaScript());
			$code .= $system->setEvent($this->getEvent());
			$code .= $system->setJs($this->getValidate());
			if ($this->property["debug"]["value"]=="true") $code .= $system->setJs("\nvar firebugcss = '".$system->dir_web_jamp.$system->dir_template."objcss/default/firebug.css'");
		}
		return $code;
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		global $system;

		$this->codejs = "";
		$this->setCSS();
		if ($this->isJampEngineActivated()) {
			$compressjs = $this->property["compressjs"]["value"];
			if (!empty($compressjs)) $system->compressjs = ($compressjs == "true") ? true : false; 
			if ($this->property["debug"]["value"] == "true") $this->property["onload"]["value"] = "AJAX.debugEnable(); ".$this->property["onload"]["value"];
			if ($this->property["loaddata"]["value"] == "true") 
			{
				$param = null;
				if ($this->property["forwardrequest"]["value"] == "true")
				{
					foreach($_REQUEST as $item => $value) $param .= "&".$item."=".$value;
				}
				$this->property["onload"]["value"] = "AJAX.loadAll('".$_SERVER['PHP_SELF']."', '$param'); ".$this->property["onload"]["value"];
			} else {
				$this->property["onload"]["value"] = "Resize(); ".$this->property["onload"]["value"];
			}
			if (!empty($this->property["keepalive"]["value"]))
			{
				$second = intVal($this->property["keepalive"]["value"]) * 1000;
				$this->setCodeJs("\t\tsetTimeout(function(){AJAX.keepAlive($second,'".$this->property["keepaliveurl"]["value"]."');}, $second);");
			}
			if (!empty($this->property["onmousemove"]["value"]))
			{
				$this->setCodeJs("\t\tdocument.onmousemove=function(event){".$this->property["onmousemove"]["value"]."}");
			}
		}
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		global $system;
		switch($name)
		{
			case "debug":
				if ($this->property["debug"]["value"] == "true")
				{
					$this->property["java"]["value"][] = "pi.js";
					$this->property["java"]["value"][] = "firebug-lite.js";
					$system->debug($this->property["id"]["value"], "<font color=\"blue\">LOAD<\/font> ".$_SERVER['PHP_SELF']);
					foreach ($_REQUEST as $key => $value)
					{
						if (is_array($value)) $value = implode(", ", $value);
						$system->debug($this->property["id"]["value"], "<font color=\"red\">POST<\/font> <b>[$key]<\/b> = $value");
					}
				}
			break;
			case "lang":
				LANG::$language = $this->property["lang"]["value"];
			break;
			case "hidehtmlerror":
			case "hidejserror":
			case "hidexmlerror":
				$this->propertyJS[$name] = $this->property[$name]["value"];
	  		break;
		}
	}
}
?>
