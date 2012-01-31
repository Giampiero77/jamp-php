<?php
/**
* Object INCLUDE
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_include extends ClsObject {
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
		unset($this->property);
		$this->property["id"]		= array("value" => null, "inherit" => false, "html" => true);
		$this->property["src"]		= array("value" => null, "inherit" => false, "html" => false);
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
	public function codeHEAD($page)
	{
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		global $system;

		$code = file_get_contents($this->parseValue($this->property["src"]["value"]));
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