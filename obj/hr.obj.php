<?php
/**
* Object HR
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_hr extends ClsObject {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property = array(); //Cancello le proprietà di default
		$this->property["id"]  			= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["align"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["class"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["dir"]	 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["size"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["lang"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["style"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["title"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["width"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["noshade"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["debug"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onclick"] 		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["ondblclick"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onmousedown"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onmouseup"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onmouseover"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onmousemove"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onmouseout"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onkeypress"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onkeydown"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["onkeyup"] 		= array("value" => null, "inherit" => false, "html" => true);
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		$w=$pdf->w-$pdf->rMargin;
		$pdf->Line($pdf->GetX(), $pdf->GetY(), $w, $pdf->GetY());
		$pdf->SetY($pdf->GetY()+1);
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
		return "\n$tab<hr ".$this->getProperty("html", true, false)." />";
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