<?php
/**
* Object HTABLE
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_thead extends ClsObject {
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
		$this->property["id"] 		= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["align"] 	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["valign"] 	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["bgcolor"]	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["char"] 	= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["charoff"] = array("value" => null,  "inherit" => false, "html" => true);
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		$pdf->SetFillColor(107,107,107);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetTextColor(0);
    	$pdf->SetFont('');
    	$pdf->SetLineWidth(.3);
    	$pdf->SetFont('','B');
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
		$code = "\n$tab<thead ".$this->getProperty("html", true, false).">";
		foreach ($this->child as $obj) $code .= $tab.$obj->codeHTML($tab."\t");
		$code .= "\n$tab</thead>";
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