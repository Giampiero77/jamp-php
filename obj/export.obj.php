<?php
/**
* Object EXPORT
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_export extends ClsObject {
	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)
	{
		$this->property = array(); //Cancello le proprietà di default
		$this->property["id"]    		= array("value" => null,  "inherit" => false, "html" => true);
		$this->property["debug"] 		= array("value" => "false", "inherit" => true, "html" => false);
		$this->property["from"] 		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["to"] 			= array("value" => null, "inherit" => false, "html" => false);
		$this->property["fieldFrom"]	= array("value" => false, "inherit" => false, "html" => false);
		$this->property["fieldTo"] 	= array("value" => null, "inherit" => false, "html" => false);
 		$this->property["format"] 		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["method"] 		= array("value" => "bypass", "inherit" => false, "html" => false);
 		$this->property["dsobj"]  		= array("value" => null, "inherit" => false, "html" => false);
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
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		global $xml;
 		$xml->getElementsByTagName("ds");	
		$ds = $xml->pageObj->getChilden();

		$dsFrom = $ds[$this->property["from"]["value"]];
  		$dsFrom->ds->dsConnect();
  		$dsFrom->ds->dsQuerySelect();
  		$input = $output = $dsFrom->ds->property["result"];

		// Mapping and format
		if ($this->property["fieldFrom"]["value"]) 
		{
			global $system;
			require_once($system->dir_real_jamp."/class/format.class.php");
			$format = new ClsFormat();
			$output=array();
			$i=0;
			foreach ($input as $row) 
			{
				$index=0;
				foreach ($this->property["fieldFrom"]["value"] as $fieldFrom)
				{
					$fieldTo = $this->property["fieldTo"]["value"][$index];
					$output[$i][$fieldTo] = $row[$fieldFrom];
					if (isset($this->property["format"]["value"][$index]))
						$output[$i][$fieldTo] = $format->Format($output[$i][$fieldTo], $this->property["format"]["value"][$index]);
					$index++;
				}
				$i++;
			}
		}
		$dsTo = $ds[$this->property["to"]["value"]];  
		$dsTo->ds->dsConnect();
		$dsTo->ds->dsImport($output, $this->property["method"]["value"]);

		userEvent::call("data_import_after", $dsFrom, $dsTo);
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