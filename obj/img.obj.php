<?php
/**
* Object IMG
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/
class ClsObj_img extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property["id"] 		 	= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["width"] 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["height"] 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["align"] 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["src"] 		 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["alt"] 		 	= array("value" => "", "inherit" => false, "html" => true);
		$this->property["hspace"] 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["vspace"] 	 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["border"] 	 	= array("value" => "0",  "inherit" => false, "html" => true);
		$this->property["href"] 	 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["target"] 	 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["ismap"] 	 	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["longdesc"] 	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["name"] 	 	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["usemap"] 	 	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["label"]  	 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelalign"]	= array("value" => "left",  "inherit" => false, "html" => false);
		$this->property["labelstyle"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["labelwidth"] 	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["size"]			= array("value" => null, "inherit" => false, "html" => false);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		if (empty($this->property["href"]["value"])) $code = "\n$tab<img ".$this->getProperty("html", true, false).">";
		else 
		{
			$code = "\n$tab<a href=\"".$this->property["href"]["value"]."\" target=\"".$this->property["target"]["value"]."\">";
			$code .= "<img ".$this->getProperty("html", true, false)."></a>";
		}
		$code = $this->getCodeLabelAlign($code, $tab);
		return $code;
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		$pdf->CellObj($this);
	}

	/**
	* Generate the code text
	*/
	public function codeTXT()
	{
		foreach ($this->child as $obj) $obj->codeTXT();
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