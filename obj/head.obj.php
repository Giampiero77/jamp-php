<?php
/**
* Object HEAD
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_head extends ClsObject {
	/**
	* @var $container Array contenente gli oggetti gestiti
	*/
	var $child = array();

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"] 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["icon"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["title"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["debug"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["html5"] 	= array("value" => null, "inherit" => false, "html" => false);
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
	* Generate the header (pdf)
	* @param string $pdf Class PDF
	*/
	public function headerPDF($pdf)
	{
		foreach ($this->child as $obj) $obj->codePDF($pdf);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHEAD($page, $ajax)
	{
		global $system;
		if (!isset($this->property["icon"]["value"])) $this->setProperty("icon", $page->getPropertyName("icon"));
		if (!isset($this->property["title"]["value"])) $this->setProperty("title", $page->getPropertyName("title"));
		if (!isset($this->property["debug"]["value"])) $this->setProperty("debug", $page->getPropertyName("debug"));

		$code = "\n<head>";
		$code .= "\n\t<title>".$this->property["title"]["value"]."</title>";
		if ($ajax) {
			$code .= "\n\t<meta name=\"GENERATOR\" content=\"JAMP\" />";
			$code .= "\n\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />";
			$code .= "\n\t<script type=\"text/javascript\" language=\"JavaScript1.5\">";
			$code .= "\n\t\tfunction $(id) { return document.getElementById(id); }";
			$code .= "\n\t</script>";
			$code .= $system->getCSS($page->requireCSS());
		}
		if ($this->property["debug"]["value"]=="true") $code .= $system->getCSS(array("objcss/default/firebug.css"));
		if (!empty($this->property["icon"]["value"])) $code .= "\n\t<link rel=\"shortcut icon\" href=\"".$this->property["icon"]["value"]."\">";
		foreach ($this->child as $obj) $code .= $obj->codeHTML("\t");
		if ($this->property["html5"]["value"]=="true") 
		{
			 $code .= "\n\t<!--[if lt IE 9]>";
			 $code .= "\n\t<script src=\"".$system->dir_web_jamp.$system->dir_js."html5.js\"></script>";
			 $code .= "\n\t<![endif]-->";
		}
		$code .= "\n</head>";
		return $code;
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		unset($this->property["id"]["value"]);
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