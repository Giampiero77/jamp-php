<?php
/**
* Object TBODY
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_tbody extends ClsObject {
	/**
	* @var $container Array containing the child objects
	*/
	var $child = array();

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id){
		$this->property["id"] 		= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["align"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["bgcolor"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["char"] 	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["charoff"] 	= array("value" => null, "inherit" => false, "html" => true);
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		if (is_array($this->child)) 
		{
			$pdf->SetFont('');
			foreach ($this->child as $obj) $obj->codePDF($pdf);
		}
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
		$code = "\n$tab<tbody ".$this->getProperty("html", true, false).">";
		foreach ($this->child as $obj) $code .= $tab.$obj->codeHTML($tab."\t");
		$code .= "\n$tab</tbody>";
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