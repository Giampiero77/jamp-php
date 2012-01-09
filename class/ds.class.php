<?php
/**
* Class management datasource
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

abstract class iDS
{
	protected $result;
	protected $sort1;
	protected $sort2;
	protected $sort3;
	protected $sortname;
	protected $order;
	protected $recursive;
	protected $justthese;
	protected $filter;
	protected $alias;
	protected $scope;

	public $property = array(
		"open" 	  		=> null,	//State of the Database Connection
		"conn" 	  		=> null,	//Link Database Connection
		"dshost"		=> null, 	//Host name
		"dsport"		=> null, 	//Server port
		"dsuser"	  	=> null, 	//Username
		"dspwd"	  		=> null, 	//Password
		"start"	  		=> 0,		//Start - Limit
		"end"		  	=> 0,		//End - Limit
		"tot"		  	=> null,	//Total Result
		"limit"	  		=> 0,		//Limit
		"order"	  		=> array(),	//Itemt sorted
		"qrycount" 		=> null,	//Query for counting rows
		"result"	  	=> null,	//Result Query
		"row"		  	=> null,	//Current record
		"fetch"	  		=> null,	//Row type
		"where"	  		=> null,	//Query filter
		"item"	  		=> null,	//Managed fields
		"dsreferences" 		=> null,	//references DS
		"referenceskey"		=> null,	//references KEY
		"foreignkey" 		=> null,	//foreign KEY
		"qrylast"		=> null,	//Last queries executed
		"inslast"		=> -1,		//Last inserted primary key
		"encpwd"		=> null		//Type of encryption password login
	);

	/**
	* Connects to the database
	*/
	abstract public function dsConnect();

        /**
        * Close connects
        */
        public function dsClose()
        {
            unset($this->property["conn"]);
        }

	/**
	* 	Run the last query executed
	*/
	abstract public function dsQueryRefresh();

	/**
	* Executes a select query
	* @param string $qry Query
	*/
	abstract public function dsQuerySelect($qry = null);

	/**
	* Executes a insert query
	* @param string $qry Query
	*/
	abstract public function dsQueryInsert($qry = null);

	/**
	* Executes a update query
	* @param string $qry Query
	*/
	abstract public function dsQueryUpdate($qry = null);

	/**
	* Executes a delete query
	* @param string $qry Query
	*/
	abstract public function dsQueryDelete($qry = null);

	/**
	* Executes a truncate query
	* @param string $qry Query
	*/
	abstract public function dsQueryDeleteAll($qry = null);

	/**
	* Get the record from the results of the query and increases the pointer
	* @param string $row Number of line in the record to be removed. If value is 0 is the first record returned unread.
	*/
	public function dsGetRow($row = -1)
	{
		if (empty($this->property["result"])) return;
		if (!isset($this->property["result"][$this->property["poscount"]])) return; 
		if($row >= 0) return $this->property["result"][$row];
		return $this->property["row"] = $this->property["result"][$this->property["poscount"]++];	
	}


	/**
	* Move the pointer of the results
	* @param string $row number line
	*/
	abstract public function dsMoveRow($row);
	
	/**
	* Import data from another result
	* @param result $result Result to be imported
	* @param string $method Import method
	*/
	abstract public function dsImport($result, $method);

	/**
	* Returns the number of entries received
	* @return Number of rows
	*/
	public function dsCountRow()
	{
		return $this->property["qrycount"];
	}

	/**
	* Function ordering of results
	*/
 	protected function dsSort() 
 	{		
		if (count($this->sort1)>0)
		{
			$sort = ($this->order=="ASC") ? SORT_ASC : SORT_DESC;
			array_multisort($this->sort1, SORT_ASC, $this->sort2, SORT_DESC, $this->sort3, $sort, $this->property["result"]);
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
		if ($value!=null) $this->property["select"] = $value;
		else 
		{
			$ar = explode("=", $where);
			unset($ar[0]);
			$this->property["select"] = str_replace("'", "", implode("=", $ar));
		}
	}

	/**
	* Reads the property
	* @param string $name Name property
	* @param integer $index The array index
	* @return Array Property
	*/
	public function getProperty($name, $index)
	{
		if (is_array($this->property[$name])) return $this->property[$name][$index];
		else if(isset($this->property[$name])) return $this->property[$name];
	}
}
?>
