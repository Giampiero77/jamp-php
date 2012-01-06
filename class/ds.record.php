<?php
/**
* Class management ds RECORD
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/
class recordDs extends iDS
{	

	/**
	* Construct
	*/
	public function __construct()
	{
 		require_once("format.class.php");
		$this->format = new ClsFormat();
		$this->property["dsport"] = "r";	//read/write
		$this->property["dsuser"] = 0;
		$this->property["poscount"] = 0;
		$this->property["open"]  = false;
		$this->property["fetch"] = "object";
		$this->property["reclength"] = array();
		$this->property["limit"] = 0;
		$this->property["start"] = 0;
		$this->property["debug"] = false;

		$this->filesize = 0;
		$this->recordsize = 0;
		$this->records = array();
	}

	/**
	* Destruct
	*/
	public function __destruct()
	{
	    $this->dsClose();
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
				foreach ($this->property["reclength"] as $reclength) $this->recordsize += $reclength;
				$this->filesize = filesize($this->property["dshost"]);
				$this->property["open"] = true;
			}  
			catch (Exception $e) 
			{  
				ClsError::showError("FILE001", $this->property["dshost"]);
			} 
		}
	}

	/**
	* Close ftp connection
	*/
	public function dsClose()
	{
	    if ($this->property["open"]) fclose($this->property["handle"]);
	}

	/**
	* Executes a select query
	* @param string $qry Query
	*/
	public function dsQuerySelect($qry = null)
	{
		global $system;
		if (!$this->property["open"]) return false;
		$i=0;	
		if (isset($this->property["start"])) fseek($this->property["handle"], $this->recordsize*$this->property["start"]);
		while (!feof($this->property["handle"]))  
		{
			if (($this->property["limit"]>0) && ($i>$this->property["limit"]-1)) break;	
			$y=0;
			foreach ($this->property["recname"] as $recname) 
			{
				$value = fread($this->property["handle"], $this->property["reclength"][$y]);
				if ($recname!="") 
				{
					if (!empty($this->property["format"][$y])) $value = $this->format->Format($value, $this->property["format"][$y]);
					$this->property["result"][$i][$recname] = $value; 
				}
				$y++;
			}
			$i++;
		}
		if($this->property["debug"]=="true") $system->debug($this->property["id"], "Selected file: ".$this->property["dshost"]);
	}

	/**
	* Set filters
	* @param array $where Array of filters
	* @param string $item Name of the field to filter
	* @param strinf $value Filtered value
	*/
	public function dsQueryFilter($where = array(), $item = null, $value = null)
	{
	}

	/**
	* Executes a update query
	* @param string $qry Query
	*/
	public function dsQueryUpdate($qry = null)
	{
	}

	/**
	* Executes a delete query
	* @param string $qry Query
	*/
	public function dsQueryDelete($qry = null)
	{
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
		global $system;
		$this->filesize = filesize($this->property["dshost"]);
		return ceil($this->filesize / $this->recordsize);
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