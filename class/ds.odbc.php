<?php
/**
* Class management ds ODBC
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/
class odbcDs extends iDS
{

	/**
	* Construct
	*/
	public function __construct()
	{
		$this->property["dstable"]	= null;
		$this->property["dsdefault"] 	= null;
		$this->property["join"]   	= null;
		$this->property["jointype"]	= null;
		$this->property["joinrule"]	= null;
		$this->property["joinsave"]	= null;

		$this->property["open"]		= false;
		$this->property["debug"] 	= false;
		$this->property["dsport"] 	= 0;
		$this->property["start"] 	= 0;
		$this->property["limit"]	= 0;
		$this->property["where"]	= array();
		$this->property["order"]	= array();
		$this->property["store"] 	= null;
		$this->property["fetch"] 	= "object";
		$this->property["selecteditems"] = "*";
		$this->property["dssavetype"] = "row";
	}

	/**
	* Connects to the database
	*/
	public function dsConnect($dsname=null)
	{
		if ($this->property["open"] == false)
		{
			 function_exists('odbc_connect') or ClsError::showError("DS00");
			 if (empty($dsname)) $dsname = $this->property["dsdefault"];
			 if (!empty($dsname)) $dsname = realpath($dsname);
			 $this->property["conn"] = odbc_connect($this->property["dshost"].$dsname, $this->property["dsuser"], $this->property["dspwd"]) or ClsError::showError("DS001");
			 $this->property["open"] = true;
		}
	}

	/**
	* Select the database
	* @param string $dsname Name of the database
	*/
	public function dsDBSelect($dsname=null)
	{
		if (!empty($dsname)) $this->dsConnect($dsname);
	}

	/**
	* Get Last insert id
	* @param string $dsname Name of the database
	*/
	public function odbc_insert_id($conn)
	{
		$result = odbc_exec($conn, "SELECT @@IDENTITY AS ID;");
		$row = odbc_fetch_object($result);
		return $row->ID;
	}

	/**
	* Get Number of affected rows
	* @param string $dsname Name of the database
	*/
	public function odbc_affected_rows($conn, $qry)
	{
		$result = odbc_exec($conn, $qry);
		$row = odbc_fetch_object($result);
		return $row->NUMBERS;
	}

	public function num_rows($result)
	{
		$rows = 0;
		while(odbc_fetch_into($result, $ar)) $rows++;
		if ($rows>0) odbc_fetch_row($result, 0);
		return $rows;
	}

	public function odbc_data_seek($row)
	{
		for($i=0; $i<$row; $i++) $this->dsGetRow();
	}

	/**
	* Login
	* @param post $post Post
	*/
	public function login($post, $chkpwd = true)
	{
		$user = $post["user"];
		$pwd = $post["pwd"];
		$itemuser = $post["itemuser"];
		$itempwd = $post["itempwd"];
		$where = $this->property["where"];

		switch($this->property["encpwd"])
		{
			case "md5":
				$pwd = md5($pwd);
			break;
			case "sha1":
				$pwd = sha1($pwd);
			break;
		}

		$this->property["where"] = null;
		$this->dsQueryFilter(null, $itemuser, $user);
		if ($chkpwd) $this->dsQueryFilter(null, $itempwd, $pwd);
		$this->dsConnect();
		$this->dsQuerySelect();

		$this->property["row"] = odbc_fetch_array($this->property["result"]);
		if (($this->property["row"][$itemuser] == $user) && ($this->property["row"][$itempwd] == $pwd))
		{
			unset($_SESSION["auth"]);
			$_SESSION["auth"]["data"] = @date("d/m/Y H:i:s");
			$_SESSION["auth"]["user"] = $user;
			if (isset($this->property["row"]["cn"])) $_SESSION["auth"]["cn"] = $this->property["row"]["cn"];
			$_SESSION["auth"]["info"] = $this->property["row"];
			$this->property["row"] = "";
		} else unset($_SESSION["auth"]);
		$this->property["where"] = $where;
	}

	/**
	* Make the change password
	* @param post $post Post
	*/
	public function changepasswd($post)
	{
		switch($this->property["encpwd"])
		{
			case "md5":
				$post["pwd"] = md5($post["pwd"]);
				$post["oldpwd"] = md5($post["oldpwd"]);
			break;
			case "sha1":
				$post["pwd"] = sha1($post["pwd"]);
				$post["oldpwd"] = sha1($post["oldpwd"]);
			break;
		}

		if (empty($post["user"]) || empty($post["pwd"]) || empty($post["oldpwd"])) return false;

		$this->dsConnect();
		$this->dsQuery("UPDATE `".$this->property["dstable"]."` SET `".$post["itempwd"]."` = '".$post["pwd"]."' WHERE `".$post["itemuser"]."` = '".$post["user"]."' and `".$post["itemoldpwd"]."` = '".$post["oldpwd"]."'");
		return $this->odbc_affected_rows($this->property["conn"], "SELECT COUNT(*) AS NUMBERS FROM `".$this->property["dstable"]."` WHERE `".$post["itemuser"]."` = '".$post["user"]."' and `".$post["itemoldpwd"]."` = '".$post["oldpwd"]."'");
	}

	/**
	* Returns the proper condition or WHERE FIND_IN_SET
	* @param string key
	* @param string value
	*/
	private function where($key, $value)
	{
		if (!strpos($key,",") === false) //Multiple Key
		{
			$key = explode(",", $key);
			$value = explode(",", $value);
			$ret = array();
			for($i=0; $i<count($key); $i++)
			{
				$ret[$i] = "`$key[$i]`='$value[$i]'";
			}
			return implode(" and",$ret);
		}

		if (strpos($value,",") === false) //Single Value
		{
			return "`$key`='$value'";
		}
		else	//Multiple Value
		{
			$vals = explode(",",$value);
			foreach ($vals as $val) $w[] = "(`$key`='$val')";
			$w = "(".implode(" OR ", $w).")";
			return $w;
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
		if (!is_numeric($value)) $value = "'$value'"; 
		if (!empty($this->property["where"]) && !is_array($this->property["where"])) $this->property["where"] = array($this->property["where"]);
		if (isset($_SESSION["store"][$this->property["store"]]))
		{
			$store = $_SESSION["store"][$this->property["store"]];
			if (!empty($store["dswhere"])) $this->property["where"][] = $store["dswhere"];
			if (!empty($store["keyname"]))
			{
				if (is_array($store["keyname"]))
				{
					foreach($store["keyname"] as $k => $value)
					{
						if (!is_numeric($store["keyvalue"][$k])) $store["keyvalue"][$k] = "'".$store["keyvalue"][$k]."'";
						if (is_array($store["condition"])) $condition = (isset($store["condition"][$k])) ? $store["condition"][$k] : "=";
						$this->property["where"][] = "`".$store["keyname"][$k]."` $condition ".$store["keyvalue"][$k];
					}
				}
				else
				{
					if (!is_numeric($store["keyname"])) $store["keyname"] = "'".$store["keyvalue"]."'";
					$condition = (empty($store["condition"])) ? "=" : $store["condition"];
					$this->property["where"][] = "`".$store["keyname"]."` $condition ".$store["keyvalue"];
				}
			}
		}
		else if (($where == null) && ($item != null) && ($value != null)) $this->property["where"][] = "`$item` = $value" ;
		else $this->property["where"] = $where;
	}

	/**
	* 	Run the last query executed
	*/
	public function dsQueryRefresh()
	{
		if (!empty($qry)) $this->dsQuery($this->property["qrylast"]);
	}

	/**
	* Executes a select query
	* @param string $qry Query
	*/
	public function dsQuerySelect($qry = null)
	{
		global $DS_ALIAS_ITEM;
//		if ((empty($qry)) && (isset($this->property["dsdefault"])))
		if (empty($qry))
		{
			$where = $this->property["where"];
			$order = $this->property["order"];
			$alias = "*";
			if ($this->property["selecteditems"]!="*") $alias = $this->property["selecteditems"];
			if (isset($DS_ALIAS_ITEM[$this->property["dstable"]]))//Alias da impostare
			{
				foreach($DS_ALIAS_ITEM[$this->property["dstable"]] as $k => $item)
				{
					if ($k == "*") $alias = str_replace("*", $item, $alias);
					else $alias .= ", $item as `$k`";
				}
			}
			$table = "`".str_replace(",","`,`", $this->property["dstable"])."`";
			if (isset($this->property["join"])) //JOIN
			{
				switch($this->property["jointype"])
				{
					case "left":
						$table .= " LEFT JOIN ";
					break;
					case "rigth":
						$table .= " RIGTH JOIN ";
					break;
					case "inner":
						$table .= " JOIN ";
					break;
				}
				$table .= "`".$this->property["join"]."`";
				$on = str_replace(".", "`.`", $this->property["joinrule"]);
				$on = str_replace(" ", "", $on);
				$on = " ON(`".str_replace("=", "`=`", $on)."`)";
				$table .= $on;
			}
			$qry = "SELECT $alias FROM $table";
			if ($this->property["limit"] > 0) $qry = "SELECT TOP ".($this->property["limit"]+$this->property["start"])." $alias FROM $table";

			if (is_array($this->property["where"])) $where = implode(" and ", $this->property["where"]);
			if (isset($_POST["dsforeignkey"]) && isset($_POST["dsforeignkeyvalue"]))
			{
				if (isset($this->property["join"])) //JOIN
				{
					$tables = explode(",", $this->property["referencestable"]);
					$keys = explode(",", $this->property["referenceskey"]);
					$keyvalues = explode(",", $_POST["dsforeignkeyvalue"]);
					for($k = 0; $k < count($tables); $k++)
					{
						if ($where != "") $where .= " and ";
						$where .= "`".$tables[$k]."`.`".$keys[$k]."`=`".$keyvalues[$k]."'";
					}
				}
				else
				{
					if ($where != "") $where .= " and ";
					if ($_POST["dsforeignkeyvalue"] != "" )$where .= $this->where($_POST["dsforeignkey"], $_POST["dsforeignkeyvalue"]);
				}
			} 
			else if (!empty($this->property["dsreferences"])) //Link DS
			{
				$key = explode(",",$this->property["referenceskey"]);
				$dsforeignkey = explode(",",$this->property["foreignkey"]);
				if (isset($this->property["referencestable"])) $tables = explode(",", $this->property["referencestable"]);
				$i = 0;
				global $xml;
				foreach(explode(",",$this->property["dsreferences"]) as $k => $objname)
				{
					$dsobj = $xml->getObjById($objname);
					if (is_null($dsobj)) continue;
					$dsobj->ds->dsConnect();
					$dsobj->ds->dsDBSelect();
					if ($dsobj->ds->dsCountRow() > 0)
					{
						$dsobj->ds->dsGetRow(0);
						switch($dsobj->ds->property["fetch"])
						{
							case "object":
								$dsforeignkeyvalue = @$dsobj->ds->property["row"]->$key[$k];
							break;
							case "row":
							case "assoc":
							case "array":
								$dsforeignkeyvalue = $dsobj->ds->property["row"][$key[$k]];
							break;
						}
					} else $dsforeignkeyvalue = "-1";
					if ($where != "") $where .= " and ";
					if (isset($this->property["join"]))
					{
						$where .= $this->where($tables[$i]."`.`".$key[$k], $dsforeignkeyvalue);
						$i++;
					}
					else	$where .= $this->where($dsforeignkey[$k], $dsforeignkeyvalue);
				}
				$this->dsDBSelect();
			}
			if (is_array($this->property["order"]))
			{
				$order = "`".implode("`, `", $this->property["order"])."`";
				$order = str_replace(" DESC`","` DESC", $order);
				if ($order == "``") $order = "";
			}
 			if (!empty($order)) $order = " ORDER BY ".$order;
			if (!empty($where)) $where = " WHERE ".$where;
			if ($this->property["limit"] > 0)
			{
				$pos = stripos($qry.$where, 'FROM');
				if ($pos !== false) $this->property["qrycount"] = "SELECT COUNT(*) AS NUMROWS ".substr($qry.$where,$pos);
			} 
			else $this->property["qrycount"] = "";

			$extra = "";
			if (isset($this->property["dsextraquery"])) $extra = " ".$this->property["dsextraquery"];
			$qry .= $where.$extra.$order;
		}
		else 
		{ 
			if (!empty($qry))
			{
				if ((stripos($qry, "ORDER BY") === false) && (is_array($this->property["order"])))
				{
					$order = "`".implode("`, `", $this->property["order"])."`";
					$order = str_replace(" DESC`","` DESC", $order);
					if ($order != "``") $qry .= " ORDER BY ".$order;
				}
				if ((stripos($qry, "LIMIT") === false) && ($this->property["limit"] > 0))
				{
					$qry .= " TOP ".$this->property["start"]." LIMIT ".$this->property["limit"];
				}
			}
		}
		if (!empty($qry)) $this->dsQuery($qry);
	}

	/**
	* Executes the query to update a JOIN
	*/
	public function dsQueryInsertUpdateJoin()
	{
		$insert = false;
		unset($this->property["item"]["dsforeignkey"]);
		unset($this->property["item"]["dsforeignkeyvalue"]);
		if (is_array($this->property["where"])) $where = implode(" and ", $this->property["where"]);
		else $where = $this->property["where"];
		if (empty($where)) $insert = true;
		$qry = "";
		foreach($this->property["item"] as $k => $item)
		{
			if (!$insert || ($insert && !empty($item)))
			{ 
				if (!empty($qry)) $qry .= ", ";
				$qry .= "`$k` = '".$item."'";
			}
		}
		$table = $this->property["join"];
		if ($insert) $qry="INSERT INTO `".$table."` SET $qry";
		else $qry="UPDATE `".$table."` SET $qry  WHERE $where";
		$this->dsQuery($qry);
		if ($insert) 
		{
			$this->property["inslast"] = $this->odbc_insert_id($this->property["conn"]);
			if (($this->property["inslast"] == 0) && ($this->property["dssavetype"] == "row")) ClsError::showError("DS007");
		}
		else $this->property["inslast"] = 0;
		return $this->odbc_affected_rows($this->property["conn"], "SELECT COUNT(*) AS NUMBERS FROM `".$table."` WHERE $where");
	}

	/**
	* Executes a update query
	* @param string $qry Query
	*/
	public function dsQueryUpdate($qry = null)
	{
		$num = 0;
		if (empty($qry))
		{
			if ($this->property["joinsave"] == "join")
			{
				$this->dsQueryInsertUpdateJoin();
				return;
			}
			unset($this->property["item"]["dsforeignkey"]);
			unset($this->property["item"]["dsforeignkeyvalue"]);
			global $DS_ALIAS_ITEM;
			if (is_array($DS_ALIAS_ITEM))
			{
				if (isset($DS_ALIAS_ITEM[$this->property["dstable"]]))
				{
					foreach(array_keys($DS_ALIAS_ITEM[$this->property["dstable"]]) as $alias)
					{
						unset($this->property["item"][$alias]);
					}
				}
			}
			$qry = "";
			foreach($this->property["item"] as $k => $item)
			{
				if (!empty($qry)) $qry .= ", ";
				if (!is_numeric($item)) $item = "'".$item."'";
				if ($_POST['keyname']!=$k) $qry .= "`$k` = ".$item;
			}
			$where = "";
			if (is_array($this->property["where"])) $where = implode(" and ", $this->property["where"]);
			if (is_null($this->property["joinsave"])) $table = $this->property["dstable"];
			else $table = $this->property["joinsave"];
			$qry="UPDATE `".$table."` SET $qry  WHERE ".$where;
			$num = $this->odbc_affected_rows($this->property["conn"], "SELECT COUNT(*) AS NUMBERS FROM `".$table."` WHERE $where"); 
		} 
		$this->dsQuery($qry);
		$this->property["inslast"] = 0;
		return $num;
	}

	/**
	* Executes a truncate query
	* @param string $qry Query
	*/
	public function dsQueryDeleteAll($qry = null)
	{
		if (empty($qry))
		{
			if (is_null($this->property["joinsave"])) $table = $this->property["dstable"];
			else $table = $this->property["joinsave"];
			$qry = "TRUNCATE TABLE `$table`";
		}
		$this->dsQuery($qry);
		$this->property["inslast"] = -1;
	}
		
	/**
	* Executes a delete query
	* @param string $qry Query
	*/
	public function dsQueryDelete($qry = null)
	{
		if (empty($qry))
		{
			$where = "";
			if (is_array($this->property["where"])) $where = implode(" and ", $this->property["where"]);
			if (is_null($this->property["joinsave"])) $table = $this->property["dstable"];
			else $table = $this->property["joinsave"];
			$qry = "DELETE FROM `$table` WHERE ".$where;
		}
		$this->dsQuery($qry);
		$this->property["inslast"] = -1;
	}
	
	/**
	* Executes a insert query
	* @param string $qry Query
	*/
	public function dsQueryInsert($qry = null)
	{
		if (empty($qry))
		{
			if (isset($this->property["item"]["dsforeignkey"]))
			{
				$key = $this->property["item"]["dsforeignkey"];
				$keyvalue = $this->property["item"]["dsforeignkeyvalue"];
				unset($this->property["item"]["dsforeignkey"]);
				unset($this->property["item"]["dsforeignkeyvalue"]);
				$this->property["item"][$key] = $keyvalue;
			}
			global $DS_ALIAS_ITEM;
			if (is_array($DS_ALIAS_ITEM))
			{
				if (isset($DS_ALIAS_ITEM[$this->property["dstable"]]))
				{
					foreach(array_keys($DS_ALIAS_ITEM[$this->property["dstable"]]) as $alias)
					{
						unset($this->property["item"][$alias]);
					}
				}
			}
			$items = implode("`,`", array_keys($this->property["item"]));
			$values = implode("','", $this->property["item"]);
			if (is_null($this->property["joinsave"])) $table = $this->property["dstable"];
			else $table = $this->property["joinsave"];
			$qry = "INSERT INTO `$table` (`$items`) VALUES('$values')";
		}
		$this->dsQuery($qry);
		$this->property["inslast"] = $this->odbc_insert_id($this->property["conn"]);
		if (($this->property["inslast"] == 0) && ($this->property["dssavetype"] == "row")) ClsError::showError("DS007");
		return 1;
	}

	/**
	* Executes a query
	* @param string $qry SQL string
	* @param string $errorsql displays the error message if the query goes wrong
	*/
	public function dsQuery($qry, $errorsql = false)
	{
		global $system;
		if (!isset($errorsql)) $errorsql = $qry;
		$this->property["result"] = odbc_exec($this->property["conn"], $qry) or $this->ErrorDS003($errorsql);
		if ($this->property["start"]>0) $this->odbc_data_seek($this->property["start"]);
		$this->property["qrylast"] = $qry;
		if ($this->property["debug"]=="true") $system->debug($this->property["id"], $qry);
	}

	/**
	* Performs the parsing of a SQL string
	* @param string $qryparsing SQL string
	* @param boolean $stripslashes Un-quotes SQL string
	* @return array $queries array of queries
	*/
	public function dsQueryParsing($qryparsing, $stripslashes = true)
	{
		$queries = array();
 		if (!is_array($qryparsing)) $qryparsing = explode("\n", $qryparsing);
		$i = $count = 0;
		foreach ($qryparsing as $line)
		{
			$line = ($stripslashes) ? trim(stripslashes($line)) : trim($line);
		   	if (substr($line, 0, 2) != "--" && $line!="") 
			{
				if (!isset($queries[$i])) $queries[$i] = $line;
				else $queries[$i] .= " ".$line;
				$count = substr_count($line, "'") + $count;
				if (preg_match("/(.*);$/", $line) && ($count % 2)==0) $i++;
			}
		}
		return $queries; 
	}

	/**
	* Get the record from the results of the query and increases the pointer
	* @param string $row Number of line in the record to be removed. If value is 0 is the first record returned unread.
	*/
	public function dsGetRow($row = -1)
	{
		//if ($this->dsCountRow()==0) return; 
		if ($row >= 0) $this->odbc_data_seek($row);
		switch($this->property["fetch"])
		{
			case "object":
				return $this->property["row"] = odbc_fetch_object($this->property["result"]);	
			break;
			case "array":
				return $this->property["row"] = odbc_fetch_array($this->property["result"]);	
			break;
			case "row":
				return $this->property["row"] = odbc_fetch_row($this->property["result"]);	
			break;
			case "assoc":
				return $this->property["row"] = odbc_fetch_assoc($this->property["result"]);	
			break;
		}
	}

	/**
	* Move the pointer of the results
	* @param string $row number line
	*/
	public function dsMoveRow($row)
	{
		if (empty($this->property["result"])) return;
		if ($this->num_rows($this->property["result"]) == 0) return false;
		odbc_data_seek($this->property["result"], $row) or ClsError::showError("DS004");
	}

	/**
	* Generates the necessary queries to the calculation of the lines
	* @return $qry SQL string
	*/
	public function setQryCount($qry)
	{
		$this->property["qrycount"] = "SELECT count(*) AS NUMROWS FROM ($qry)";
	}

	/**
	* Returns the number of entries received
	* @return Number of rows
	*/
	public function dsCountRow()
	{
		if (empty($this->property["qrycount"]))
		{
			if (!empty($this->property["result"]) && !is_bool($this->property["result"])) return $this->num_rows($this->property["result"]);
			return 0;
		} else {
 			$res = odbc_exec($this->property["conn"], $this->property["qrycount"]) or $this->ErrorDS003($this->property["qrycount"]);
			$row = odbc_fetch_array($res);
			return $row['NUMROWS'];
		}
	}

	/**
	* Returns all the databases
	*/  
    	public function dsShowDatabases()
	{
	}

	/**
	* Returns all tables of a database
   	* @param string $database name of the database
	*/  
	public function dsShowTables($database )
	{
	}

	/**
	* Returns all views of a database
	* @param string $database name of the database
	*/  
	public function dsShowViews($database)
	{
	}

	/**
	* Returns all functions of a database
	* @param string $database name of the database
	*/  
	public function dsShowFunctions($database)
	{
	}

	/**
	* Returns all procedures of a database
	* @param string $database name of the database
	*/  
	public function dsShowProcedures($database)
	{
	}

	/**
	* Returns the contents of a table
	* @param string $database name of the database
	* @param string $table name of the table
	*/  
	public function dsShowTable($database, $table)
	{
	}

	/**
	* Returns the contents of a view
	* @param string $database name of the database
	* @param string $view name of the view
	*/  
	public function dsShowView($database, $view)
	{
	}

	/**
	* Returns the contents of a view
	* @param string $database name of the database
	* @param string $view name of the view
	*/  
	public function dsShowProcedure($database, $procedure)
	{
	}

	/**
	* Returns the contents of a function
	* @param string $database name of the database
	* @param string $view name of the function
	*/  
	public function dsShowFunction($database, $function)
	{
	}

	/**
	* Collation List
	*/  
	public function dsShowCollation()
	{
	}

	/**
	* Returns the list of users and privileges
	* @param string $database name of the database
	* @param string $table name of the table
	*/  
	public function dsShowUsers($database = false, $table = false)
	{
	}

	/**
	* Create a database 
	* @param string $db name of the database
	*/  
	public function CreateDatabase($db)
	{
	}  

	/**
	* Create a table
	* @param string $db1 name of the database
	* @param string $table1 name of the table
	* @param string $engine engine
	* @param string $collation collation
	*/  
	public function CreateTable($db, $table, $engine, $collation)
	{
	} 

	/**
	* Create a view
	* @param string $db name of the database
	* @param string $view name of the view
	* @param string $sql SQL syntax
	* @param string $user Username
	* @param string $host Enabled host
	*/  
	public function CreateView($db, $view, $sql, $user = false, $host = false)
	{
	}  

	/**
	* Save a view
	* @param string $db name of the database
	* @param string $view name of the view
	* @param string $sql SQL syntax
	* @param string $user Username
	* @param string $host Enabled host
	*/  
	public function SaveView($db, $view, $sql, $user = null, $host = null)
	{
	}  

	/**
	* Create a Function
	* @param string $db name of the database
	* @param string $func name of the function
	* @param string $sql SQL syntax
	* @param string $user Username
	* @param string $host Enabled host
	*/  
	public function CreateFunction($db, $func, $sql, $user = null, $host = null)
	{
	}  

	/**
	* Save a Function
	* @param string $db name of the database
	* @param string $func name of the function
	* @param string $sql SQL syntax
	* @param string $user Username
	* @param string $host Enabled host
	*/  
	public function SaveFunction($db, $func, $sql, $user = null, $host = null)
	{
	}

	/**
	* Create a Procedure
	* @param string $db name of the database
	* @param string $proc name of the procedure
	* @param string $sql SQL syntax
	* @param string $user Username
	* @param string $host Enabled host
	*/  
	public function CreateProcedure($db, $proc, $sql, $user = null, $host = null)
	{
	}  

	/**
	* Salve a view
	* @param string $db name of the database
	* @param string $proc name of the procedure
	* @param string $sql SQL syntax
	* @param string $user Username
	* @param string $host Enabled host
	*/
	public function SaveProcedure($db, $proc, $sql, $user = null, $host = null)
	{
	}

	/**
	* Rename the table may also be used to move a table from one database to another
	* @param string $db1 source database
	* @param string $table1 name of table
	* @param string $db2 destination database
	* @param string $table2 new name of table
	*/  
	public function RenameTable($db1, $table1, $db2, $table2)
	{
	}

	/**
	* Rename the view may also be used to move a view from one database to another
	* @param string $db1 source database
   	* @param string $view1 name of view
	* @param string $db2 destination database
	* @param string $view2 new name of view
	*/  
	public function RenameView($db1, $view1, $db2, $view2)
	{
	} 

	/**
	* Rename the function may also be used to move a function from one database to another
	* @param string $db1 source database
   	* @param string $func1 name of function
	* @param string $db2 destination database
	* @param string $func2 new name of function
	*/  
	public function RenameFunction($db1, $func1, $db2, $func2)
	{
	} 

	/**
	* Rename the procedure may also be used to move a procedure from one database to another
	* @param string $db1 source database
   	* @param string $proc1 name of procedure
	* @param string $db2 destination database
	* @param string $proc2 new name of procedure
	*/  
	public function RenameProcedure($db1, $proc1, $db2, $proc2)
	{
	} 

	/**
	* Drop a database
	* @param string $db name of the database
	*/  
	public function DropDatabase($db)
	{
	}

	/**
	* Drop a table
	* @param string $db name of the database
	* @param string $table name of the table
	*/  
	public function DropTable($db, $table)
	{
	}

	/**
	* Drop a view
	* @param string $db name of the database
	* @param string $view name of the view
	*/  
	public function DropView($db, $view)
	{
	}

	/**
	* Drop a Function
	* @param string $db name of the database
	* @param string $func name of the function
	*/  
	public function DropFunction($db, $func)
	{
	}

	/**
	* Drop a procedure
	* @param string $db name of the database
	* @param string $proc name of the procedure
	*/
	public function DropProcedure($db, $proc)
	{
	} 

	/**
	* Returns all the columns of a table
	* @param string $db name of the database
	* @param string $table name of the table
	*/  
	public function dsShowColumns($db, $table)
	{
	}

    	/**
	* Returns the structure of a table, view or query
	* @param string $table name of the table
	*/  
	public function dsShowColumnsResult($table = null)
	{
	}

	/**
	* Returns all indexes of a table
	* @param string $db name of the database
	* @param string $table name of the table
	*/  
	public function dsShowIndex($db, $table)
	{
	}  

	/**
	* Returns all the foreign keys of a table
	* @param string $db name of the database
	* @param string $table name of the table
	*/  
	public function dsShowForeignKey($db, $table)
	{
	}  

	/**
	* Editing or adding a column of a table
	* @param string $db name of the database
	* @param string $table name of the table
	* @param string $value value to insert/update
	* @param string $type ADD/CHANGE
	*/  
	public function AlterTable($db, $table, $value, $type = "CHANGE")
	{
	}  

	/**
	* Delete the field in a table
	* @param string $db name of the database
	* @param string $table name of the table
	* @param string $field name of the field
	*/  
	public function DropField($db, $table, $field)
	{
	}  

	/**
	* Create a index
	* @param string $db name of the database
	* @param string $table name of the table
	* @param string $field type of the index
	* @param string $field name of the fields
	*/  
	public function AddIndex($db, $table, $index, $fields)
	{
	}  

	/**
	* Editing or adding a column of a table
	* @param string $fkdb name of local database
	* @param string $fktable behalf of the local table
	* @param string $fkfields foreign key/keys
	* @param string $rfdb name of the remote database
	* @param string $rftable name of remote table
	* @param string $rffields remote field/fields
	* @param string $ondelete options canceled 	
	* @param string $onupdate update options
	*/  
	public function AddForeignKey($fkdb, $fktable, $fkfields, $rfdb, $rftable, $rffields, $ondelete, $onupdate)
	{
	}  

	/**
	* Delete a index
	* @param string $db name of the database
	* @param string $table name of the table
	* @param string $keyname name of the key
	*/  
	public function DropIndex($db, $table, $keyname)
	{
	}  

	/**
	* Delete a foreign key
	* @param string $db name of the database
	* @param string $table name of the table
	* @param string $keyname name of the key
	*/  
	public function DropForeignKey($db, $table, $keyname)
	{
	}  

	/**
	* Export data
	* @param string $db name of the database
	* @param string $table name of the table
	* @param string $start starting row
	* @param string $maxrows maximum rows
	*/  
	public function exportData($db, $table, $start, $maxrows)
	{
	}

	/**
	* Export database
	* @param string $db database name
	*/  
	public function exportDatabase($db)
	{
	}

	/**
	* Export table
	* @param string $db name of the database
	* @param string $table name of the table
	* @param boolean $structure returns of the structured table in SQL format
	* @param boolean $data returns the data in SQL format
	* @param string $start starting row
	* @param string $maxrows maximum rows
	*/  
	public function exportTable($db, $table, $structure = false, $data = false, $start = 0, $maxrows = 60000)
	{
	}

	/**
	* Export view
	* @param string $db name of the database
	* @param string $view name of the view
	* @param boolean $structure returns of the structured view in SQL format
	* @param boolean $data returns the data in SQL format
	* @param string $start starting row
	* @param string $maxrows maximum rows
	*/  
	public function exportView($db, $view, $structure = false, $data = false, $start = 0, $maxrows = 60000)
	{
	} 

	/**
	* Export function
	* @param string $db name of the database
	* @param string $function name of the view
	*/  
	public function exportFunction($db, $function)
	{
	} 

	/**
	* Export procedure
	* @param string $db name of the database
	* @param string $procedure name of the procedure
	*/  
	public function exportProcedure($db, $procedure)
	{
	}

	/**
	* 	Reload the user privileges
	*/  
	public function reloadPrivileges()
	{
	}

	/**
	* Modify the privileges of the global
	* @param array $value privileges
	*/  
	public function dsModUser($value)
	{
	}  

	/**
	* Modify the privileges of the global
	* @param array $value privileges
	*/  
	public function dsModDbGrant($value)
	{
	}  

	/**
	* Insert the privileges of the global
	* @param array $value privileges
	*/  
	public function dsAddDbGrant($value)
	{
	}  

	/**
	* Delete user privileges associated with the db
	* @param string $db name of the database
	* @param string $user privileges of the user
	* @param string $host name of the host
	*/  
	public function DropGrantDb($db, $user, $host)
	{
	}  

	/**
	* Import data from another result
	* @param result $result Result to be imported
	* @param string $method Import method
	*/
	public function dsImport($result, $method)
	{
		global $system;
		$table = $this->property["dstable"];
		$conn =  $this->property["conn"];
		foreach ($result as $row) 
		{
			$items = array();
			$values = array();
			foreach ($row as $item => $value)	
			{
				$items[] = $item;
				$values[] = str_replace("'", "''", $value);
			}
			$items_str = implode("`,`", $items);
			$values_str = implode("','", $values);
			$qry = "INSERT INTO `$table` (`$items_str`) VALUES('$values_str')";
			if ($method=="update")
			{ 
				$qry .= " ON DUPLICATE KEY UPDATE ";
				for ($i=0; $i<count($items); $i++) $qry .= "`".$items[$i]."`='".str_replace("'", "''", $values[$i])."',";
				$qry = substr($qry, 0, -1);
			}
			if ($method!="error") odbc_exec($conn, $qry);
			else odbc_exec($conn, $qry) or $this->ErrorDS003($qry);
			$this->property["inslast"] = $this->odbc_insert_id($conn);
			if (($this->property["inslast"] == 0) && ($this->property["dssavetype"] == "row")) ClsError::showError("DS007");
	 		if ($this->property["debug"]=="true") $system->debug($this->property["id"], $qry);
		}
	}

	/**
	* Error Handling
	* @param string $qry SQL Query
	*/
	private function ErrorDS003($qry)
	{
		if (mysql_errno($this->property["conn"]) == 1062)
		{
			$err = odbc_error($this->property["conn"]);
			$item = explode("'", $err);
			$item = explode("-", $item[1]);
			ClsError::showError("DS006", $item[0]);
		}
		else ClsError::showError("DS003", $this->property["conn"], $qry);
	}
}
?>