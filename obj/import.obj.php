<?php
/**
* Object IMPORT
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_import extends ClsObject {
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
 		$ds = $xml->getObjByType("ds");	

		$dsFrom = $ds[$this->property["from"]["value"]];
		$dsTo = $ds[$this->property["to"]["value"]];  

  		$dsFrom->ds->dsConnect();
		$dsFrom->ds->dsQuerySelect($dsFrom->property["dsquery_select"]["value"]);	
		$dsFrom->callEvent("data_import_before", $dsFrom);
		
		$input[]= $output[] = array();
		if (!is_array($dsFrom->ds->property["result"]))
		{
			 $i=0;
			 $dsFrom->ds->property["fetch"] = "array";
			 while ($row = $dsFrom->ds->dsGetRow()) $input[$i++] = $output[$i++] = $row;  
		}
		else $input = $output = $dsFrom->ds->property["result"];

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
		$dsTo->ds->dsConnect();
		$dsTo->ds->dsImport($output, $this->property["method"]["value"]);
		$dsTo->callEvent("data_import_after", $dsTo);
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