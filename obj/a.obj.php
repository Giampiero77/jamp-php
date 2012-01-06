<?php
/**
* Object A
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_a extends ClsObject {
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
		$this->property["id"] 	 	  = array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["charset"] 	  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["coords"] 	  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["href"] 	  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["hreflang"]   = array("value" => null, "inherit" => false, "html" => true);
		$this->property["name"]   	  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["rel"]   	  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["rev"]   	  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["shape"]   	  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["target"]     = array("value" => null, "inherit" => false, "html" => true);
		$this->property["type"]   	  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["text"]   	  = array("value" => null, "inherit" => false, "html" => false);
		$this->property["label"]   	  = array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelalign"]	= array("value" => "left",	"inherit" => false, "html" => false);
		$this->property["labelstyle"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelwidth"] 	= array("value" => null, "inherit" => false, "html" => false);
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		$this->property["value"]["value"] = $this->property["text"]["value"];
		$pdf->CellObj($this);
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
		$code = $tab."<a ".$this->getProperty("html", true, false).">".str_replace('\n',"<br>",$this->property["text"]["value"]);
		foreach ($this->child as $obj) $code .= $obj->codeHTML($tab."\t");
		$code .= "</a>";
		$code = $this->getCodeLabelAlign($code, $tab);
		return $code;
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