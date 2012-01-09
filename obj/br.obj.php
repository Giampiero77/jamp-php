<?php
/**
* Object BR
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_br extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property = array();
		$this->property["id"] 	 = array("value" => $id, "inherit" => false, "html" => false);
		$this->property["row"] 	 = array("value" => 1, 	  "inherit" => false, "html" => false);
		$this->property["class"] = array("value" => null, "inherit" => false, "html" => true);
		$this->property["style"] = array("value" => null, "inherit" => false, "html" => true);
		$this->property["title"] = array("value" => null, "inherit" => false, "html" => true);
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		for($i = 0; $i < intval($this->property["row"]["value"]); $i++) $pdf->Ln();
	}

	/**
	* Generate the code text
	*/
	public function codeTXT()
	{
		return "\n";
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$code = "\n$tab<span id=\"".$this->property["id"]["value"]."\">";
		for($i = 0; $i < intval($this->property["row"]["value"]); $i++)
		{
			$code .= "\n$tab\t<br id=\"".$this->property["id"]["value"]."_$i\" ".$this->getProperty("html", true, false).">";
		}
		$code .= "\n$tab</span>";
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
