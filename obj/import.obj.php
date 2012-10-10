<?php
/**
* Object IMPORT
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_import extends ClsObject {
	/**
	* @var $child_property Properties of child objects
	*/
	private $child_property;
		
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
		$this->property["method"] 		= array("value" => "bypass", "inherit" => false, "html" => false);
 		$this->property["dsobj"]  		= array("value" => null, "inherit" => false, "html" => false);
 		$this->multiObj = true;
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
	* Set the properties of the object
	* @param string $name Name property
	* @param string $value 	Value of Property
	*/
	public function setProperty($name, $value)
	{
		if (is_array($value)) $this->child_property[$name] = $value;
		else parent::setProperty($name, $value);
	}
		
	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		global $xml, $system;
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
		if ($this->child_property["fieldFrom"]) 
		{		
			require_once($system->dir_real_jamp."/class/format.class.php");
			$format = new ClsFormat();
			$output=array();
			$i=0;			
			foreach ($input as $row) 
			{
				$index=0;				
				foreach ($this->child_property["fieldFrom"] as $fieldFrom)
				{
					$fieldTo = $this->child_property["fieldTo"][$index];
					$output[$i][$fieldTo] = $row[$fieldFrom];
					if (isset($this->child_property["format"][$index]))
						$output[$i][$fieldTo] = $format->Format($output[$i][$fieldTo], $this->child_property["format"][$index]);
					$index++;
				}
				$i++;
			}
		}
		$dsTo->ds->dsConnect();
		$dsTo->ds->dsImport($output, $this->property["method"]["value"]);
		$dsTo->callEvent("data_import_after", $dsTo);
		
		$code = "\n$tab<div ".$this->getProperty("html", true, false)." ></div>";
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