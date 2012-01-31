<?php
/**
* Class management ds RECORD
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/
class csvDs extends iDS
{	

	/**
	* Construct
	*/
	public function __construct()
	{
 		require_once("format.class.php");
		$this->format = new ClsFormat();
		$this->property["dsuser"] = 0;
		$this->property["poscount"] = 0;
		$this->property["open"] = false;
		$this->property["fetch"] = "object";
		$this->property["limit"] = 0;
		$this->property["start"] = 0;
		$this->property["debug"] = false;
		$this->property["fieldseparator"] = ",";
		$this->property["fieldencloser"] = '"';
		$this->property["fieldescape"] = "\\";
		$this->property["info"] = "";
	}

	/**
	* Destruct
	*/
	public function __destruct()
	{
	  $this->dsClose();
	}

        /**
        * Close connects
        */
        public function dsClose()
        {
	    if ($this->property["open"] == true) fclose($this->property["handle"]);
        }


	/**
	* Connects to the file
	*/
	public function dsConnect()
	{
		if($this->property["open"] == false)
		{
			global $system;
			if (strpos($this->property["dshost"], "://")===false)
			      $this->property["dshost"] = realpath($this->property["dshost"]);
			try 
			{
				$this->property["handle"] = fopen($this->property["dshost"], $this->property["dsport"]);
				$this->property["info"] = pathinfo($this->property["dshost"]);
				$this->property["open"] = true;
			}  
			catch (Exception $e) 
			{  
				ClsError::showError("FILE001", $this->property["dshost"]);
			} 
		}
	}

	/**
	* Executes a select query
	* @param string $qry Query
	*/
	public function dsQuerySelect($qry = null)
	{
		if (!$this->property["open"]) return false;
		$this->property["result"] = array();
		$this->property['tot'] = 0;
		$i = 0;
		$start = 0;
		$find = empty($this->property["where"]) ? "all" : true;
		$begin = ($this->property['start'] == 0) ? 0 : $this->property['start'] + 1;
      if (isset($this->property["info"]["extension"]) && $this->property["info"]["extension"] == "php") fgets($this->property["handle"]);
		while (($fields = fgetcsv($this->property["handle"], 0, $this->property["fieldseparator"], $this->property["fieldencloser"])) !== FALSE) 
		{
			$this->property['tot']++;
			if ($find == "all") $start = $this->property['tot'];
			if ($start > $begin && ($i < $this->property['limit'] || empty($this->property['limit'])))
			{
				if ($find === true) $find = false;
				foreach ($this->property["fieldname"] as $key => $name)
				{
					$value = (isset($fields[$key])) ? $fields[$key] : "";
					if ($find == false && isset($this->property["where"][$name]) && $this->property["where"][$name] == $value) $find = true;
					$this->property["result"][$i][$name] = $value;
				}
				if ($find === false)
				{
					unset($this->property["result"][$i]);
					$this->property['tot']--;
				}
				else
				{
					$i++;
					$start++;		
				}
			}
		}	
		if($this->property["debug"]=="true") 
		{
		  global $system;
		  $system->debug($this->property["id"], "Selected file: ".$this->property["dshost"]);
		}
	}

	/**
	* Set filters
	* @param array $where Array of filters
	* @param string $item Name of the field to filter
	* @param strinf $value Filtered value
	*/
	public function dsQueryFilter($where = array(), $item = null, $value = null)
	{
		if (is_array($where)) foreach($where as $key => $val) $this->property["where"][$key] = $val;
		$this->property["where"][$item] = $value;
	}

	private function writeFile()
	{
		fclose($this->property["handle"]);
		$this->property["handle"] = fopen($this->property["dshost"], "w");
		if ($this->property["info"]["extension"] == "php") fputs($this->property["handle"], "<?php die();?>".PHP_EOL);
		foreach($this->property["result"] as $row)
		{
			fputcsv($this->property["handle"], $row, $this->property["fieldseparator"], $this->property["fieldencloser"]);
		}	
		fclose($this->property["handle"]);
		$this->property["handle"] = fopen($this->property["dshost"], "r");
	}

	/**
	* Executes a update query
	* @param string $qry Query
	*/
	public function dsQueryUpdate($qry = null)
	{
		if (!$this->property["open"]) return false;
		$this->property["result"] = array();
		$i = 0;
		$find = false;
		if ($this->property["info"]["extension"] == "php") fgets($this->property["handle"]);
		while (($fields = fgetcsv($this->property["handle"], 0, $this->property["fieldseparator"], $this->property["fieldencloser"])) !== FALSE) 
		{
			foreach ($this->property["fieldname"] as $key => $name)
			{
				$value = (isset($fields[$key])) ? $fields[$key] : "";
				if (!$find && isset($this->property["where"][$name]) && $this->property["where"][$name] == $value) $find = true;
				$this->property["result"][$i][$name] = $value;
			}
			if ($find)
			{
				$this->property["result"][$i] = $this->property["item"];
				$find = false;
			}
			$i++;
		}	
		$this->writeFile();
	}

	/**
	* Executes a delete query
	* @param string $qry Query
	*/
	public function dsQueryDelete($qry = null)
	{
		if (!$this->property["open"]) return false;
		$this->property["result"] = array();
		$i = 0;
		$find = false;
		if ($this->property["info"]["extension"] == "php") fgets($this->property["handle"]);
		while (($fields = fgetcsv($this->property["handle"], 0, $this->property["fieldseparator"], $this->property["fieldencloser"])) !== FALSE) 
		{
			foreach ($this->property["fieldname"] as $key => $name)
			{
				$value = (isset($fields[$key])) ? $fields[$key] : "";
				if (!$find && isset($this->property["where"][$name]) && $this->property["where"][$name] == $value) $find = true;
				$this->property["result"][$i][$name] = $value;
			}
			if ($find)
			{
				unset($this->property["result"][$i]);
				$find = false;
			}
			else $i++;
		}	
		$this->writeFile();
	}

	/**
	* Executes a truncate query
	* @param string $qry Query
	*/
	public function dsQueryDeleteAll($qry = null)
	{
	}

	/**
	* Executes a insert query
	* @param string $qry Query
	*/
	public function dsQueryInsert($qry = null)
	{
		$key = $this->property["dskey"];
		$this->dsQueryFilter(null, $key, $this->property["item"][$key]);
		$this->dsQuerySelect();
		if (count($this->property["result"]) > 0) ClsError::showError("DS006", $_POST[$key].":".count($this->property["result"]));
		fclose($this->property["handle"]);
		$this->property["handle"] = fopen($this->property["dshost"], "a");
		fputcsv($this->property["handle"], $this->property["item"], $this->property["fieldseparator"], $this->property["fieldencloser"]);
		fclose($this->property["handle"]);
		$this->property["handle"] = fopen($this->property["dshost"], "r");
	}

	/**
	* Move the pointer of the results
	* @param string $row number line
	*/
	public function dsMoveRow($row)
	{
		$this->property["poscount"] = $row;
	}

	/**
	* Returns the number of entries received
	* @return Number of rows
	*/
	public function dsCountRow()
	{
		if (!empty($this->property["where"])) count($this->property["result"]);
		else return $this->property['tot'];
	}

	/**
	* 	Run the last query executed
	*/
	public function dsQueryRefresh()
	{
	}

	/**
	* Import data from another result
	* @param result $result Result to be imported
	* @param string $method Import method
	*/
	public function dsImport($result, $method)
	{
  	}
}
?>
