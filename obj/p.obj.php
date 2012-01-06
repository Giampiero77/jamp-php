<?php
/**
* Object P
* @author	Alyx Association <info@alyx.it>
* @version	2.0.2
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_p extends ClsObject {
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
		$this->property["text"]   	  = array("value" => null, "inherit" => false, "html" => false);
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
		$code = $tab."<p ".$this->getProperty("html", true, false).">".str_replace('\n',"<br>",$this->property["text"]["value"]);
		foreach ($this->child as $obj) $code .= $obj->codeHTML($tab."\t");
		$code .= "</p>";
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