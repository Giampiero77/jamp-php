<?php
/**
* Class management ds SQLITE3
* @author	Fulvio Alessio <afulvio@gmail.com>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2012
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/
class sqlite3Ds extends iDS {

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
		$this->property["dsport"] 	= null;
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
	public function dsConnect()
	{
		if ($this->property["open"] == false)
		{
			$this->property["conn"] = new SQLite3($this->property["dshost"], SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, stripcslashes($this->property["dspwd"]));
			if(method_exists('SQLite3', 'busyTimeout')) $this->property["conn"]->busyTimeout(60000);
			$this->property["open"] = true;
		}
	}

	/**
	* Select the database
	* @param string $dsname Name of the database
	*/
	public function dsDBSelect($dsname=null)
	{
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

		if(!empty($_POST['remember_me']) && $_POST['remember_me']=="1")
		{
		    $expire = time() + 1728000; // Expire in 20 days		    
		    setcookie("user", $user, $expire);
		    setcookie("pwd", $pwd, $expire);
		} 
		$this->property["row"] = $this->property["result"]->fetchArray();
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
		$this->dsQuery("UPDATE ".$this->property["dstable"]." SET ".$post["itempwd"]." = '".$post["pwd"]."' WHERE ".$post["itemuser"]." = '".$post["user"]."' and ".$post["itemoldpwd"]." = '".$post["oldpwd"]."'");
		return $this->property["conn"]->changes();
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
				$ret[$i] = "$key[$i]='$value[$i]'";
			}
			return implode(" and",$ret);
		}

		if (strpos($value,",") === false) //Single Value
		{
			return "$key='$value'";
		}
		else	//Multiple Value
		{
			return "FIND_IN_SET($key, '$value')>0";
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
						if (is_array($store["condition"])) $condition = (isset($store["condition"][$k])) ? $store["condition"][$k] : "=";
						$this->property["where"][] = "".$store["keyname"][$k]." $condition '".$store["keyvalue"][$k]."'" ;
					}
				}
				else
				{
					$condition = (empty($store["condition"])) ? "=" : $store["condition"];
					$this->property["where"][] = "".$store["keyname"]." $condition '".$store["keyvalue"]."'" ;
				}
			}
		}
		else if (($where == null) && ($item != null) && ($value != null)) $this->property["where"][] = "$item = '$value'" ;
		else $this->property["where"] = $where;
		if(is_array($this->property["where"]))
		{
			foreach ($this->property["where"] as $k => $value)
			{
				if (stripos($value, "UNION") !== false) $value="";
				$this->property["where"][$k] = $value;
			}
		}else if (stripos($this->property["where"], "UNION") !== false) $this->property["where"]="";
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
		if (empty($qry))
		{
			$where = $this->property["where"];
			if (is_array($this->property["where"])) $where = implode(" and ", $this->property["where"]);
			$order = $this->property["order"];
			$alias = "*";
			if ($this->property["selecteditems"]!="*") $alias = $this->property["selecteditems"];
			if (isset($DS_ALIAS_ITEM[$this->property["dstable"]]))//Alias da impostare
			{
				foreach($DS_ALIAS_ITEM[$this->property["dstable"]] as $k => $item)
				{
					if ($k == "*") $alias = str_replace("*", $item, $alias);
					else $alias .= ", $item as $k";
				}
			}
// 			$table = "".str_replace(",",",", $this->property["dstable"])."";
			$table = '';
			$tablealias = explode(',', $this->property["dstable"]);
			foreach($tablealias as $s) 
			{
				  $s = str_replace('','',$s); // elimino gli apici inseriti su xml
				  $p = stripos($s, 'as ');
				  if ($p !== false)$table .= ''.substr($s,0,$p-1).''.substr($s,$p).',';
				  else $table .= ''.$s.',';
			 }
			 $table = substr($table,0,-1);
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
						$table .= ",";
					break;
				}
				$table .= "".$this->property["join"]."";
				$on = str_replace(".", ".", $this->property["joinrule"]);
				$on = str_replace(" ", "", $on);
				$on = " ON(".str_replace("=", "=", $on).")";
				$on = str_replace("AND", " AND ", $on);
				$table .= $on;
			}
			$limit = "";
			$qry = "SELECT $alias FROM $table";
			if ($this->property["limit"] > 0) 
			{
				$pos = stripos($qry.$where, 'FROM');
				if ($pos !== false) $this->property["qrycount"] = "SELECT COUNT(*) ".substr($qry.$where,$pos);
				$limit = " LIMIT ".$this->property["start"].", ".$this->property["limit"];
			}

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
						$where .= "".$tables[$k].".".$keys[$k]."='".$keyvalues[$k]."'";
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
						$where .= $this->where($tables[$i].".".$key[$k], $dsforeignkeyvalue);
						$i++;
					}
					else	$where .= $this->where($dsforeignkey[$k], $dsforeignkeyvalue);
				}
				$this->dsDBSelect();
			}
			if (!is_array($order))
			{
				$order = explode(",", $order);
				for($i = 0; $i < count($order); $i++) $order[$i] = str_replace("", "", trim($order[$i]));
			}
			$order = "".implode(", ", $order)."";
			$order = str_replace(" ASC"," ASC", $order);
			$order = str_replace(" DESC"," DESC", $order);
			$order = str_replace(".",".", $order);
		   if ($order == "") $order = "";
			
			if (!empty($order)) $order = " ORDER BY ".$order;
			if (!empty($where)) $where = " WHERE ".$where;

			$extra = "";
			if (isset($this->property["dsextraquery"])) $extra = " ".$this->property["dsextraquery"];
			$qry .= $where.$extra.$order.$limit;
		}
		else if (!empty($qry))
		{
		  if ((stripos($qry, "ORDER BY") === false) && (is_array($this->property["order"])))
		  {
				$order = "".implode(", ", $this->property["order"])."";
				$order = str_replace(" ASC"," ASC", $order);
				$order = str_replace(" DESC"," DESC", $order);
				$order = str_replace(".",".", $order);
            	if ($order != "") $qry .= " ORDER BY ".$order;
		  }
		  if ((stripos($qry, "LIMIT") === false) && ($this->property["limit"] > 0))
		  {
				$qry .= " LIMIT ".$this->property["start"].", ".$this->property["limit"];
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
				$qry .= "$k = '".$item."'";
			}
		}
		$table = $this->property["join"];
		if ($insert) $qry="INSERT INTO ".$table." SET $qry";
		else $qry="UPDATE ".$table." SET $qry  WHERE $where";
		$this->dsQuery($qry);
		if ($insert) 
		{
			$this->property["inslast"] = $this->property["conn"]->lastInsertRowID();
			if (($this->property["inslast"] == 0) && ($this->property["dssavetype"] == "row")) ClsError::showError("DS007");
		}
		else $this->property["inslast"] = 0;
		return $this->affected();
	}

	/**
	* Executes a update query
	* @param string $qry Query
	*/
	public function dsQueryUpdate($qry = null)
	{
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
				if ($item == null || $item == 'null') $qry .= "$k = null";
				else $qry .= "$k = '".$item."'";
			}   

			$where = "";
			if (is_array($this->property["where"])) $where = implode(" and ", $this->property["where"]);
			if (is_null($this->property["joinsave"])) $table = $this->property["dstable"];
			else $table = $this->property["joinsave"];
			$qry="UPDATE ".$table." SET $qry  WHERE ".$where;
		}
		$this->dsQuery($qry);
		$this->property["inslast"] = 0;
		return $this->affected();
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
			$qry = "TRUNCATE TABLE $table";
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
			$qry = "DELETE FROM $table WHERE ".$where;
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
				$keys = explode(",", $this->property["item"]["dsforeignkey"]);
				$keyvalues = explode(",", $this->property["item"]["dsforeignkeyvalue"]);
				unset($this->property["item"]["dsforeignkey"]);
				unset($this->property["item"]["dsforeignkeyvalue"]);
				foreach ($keys as	$k => $key)	$this->property["item"][$key] = $keyvalues[$k];
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
		  $items = implode(",", array_keys($this->property["item"]));
		  $values = implode("','", $this->property["item"]);
		  $values = "'".$values."'";
		  $values = str_replace ("'null'", "null", $values);
		  $values = str_replace ("'null", "null", $values);
		  if (is_null($this->property["joinsave"])) $table = $this->property["dstable"];
		  else $table = $this->property["joinsave"];
		  $qry = "INSERT INTO $table ($items) VALUES($values)";
		}
		$this->dsQuery($qry);
		$this->property["inslast"] = $this->property["conn"]->lastInsertRowID();
		if (($this->property["inslast"] == 0) && ($this->property["dssavetype"] == "row")) ClsError::showError("DS007");
		return $this->affected();
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
		$this->property["result"] = $this->property["conn"]->query($qry) or $this->ErrorDS003($errorsql);
		$this->property["qrylast"] = $qry;
		$this->property['tot'] = null;

		if (stripos($qry, 'SQL_CALC_FOUND_ROWS') !== false) $this->property["qrycount"] = "SELECT FOUND_ROWS();";
		if (!empty($this->property["qrycount"]))
		{
		    $res = $this->property["conn"]->query($this->property["qrycount"]) or $this->ErrorDS003($this->property["qrycount"]);
		    $row = $res->fetchArray();
		    $this->property['tot'] = $row[0];
		}
		$this->property["qrycount"] = "";
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
		if (!$this->property["result"]) return; 
		if ($row >= 0) $this->property["result"]->seek($row) or ClsError::showError("DS004");

		return $this->property["row"] = $this->property["result"]->fetchArray(SQLITE3_ASSOC);	
	}

	/**
	* Move the pointer of the results
	* @param string $row number line
	*/
	public function dsMoveRow($row)
	{
		if (empty($this->property["result"])) return;
		$this->property["result"]->seek($row) or ClsError::showError("DS004");
	}

	/**
	* Generates the necessary queries to the calculation of the lines
	* @return $qry SQL string
	*/
	public function setQryCount($qry)
	{
		$this->property["qrycount"] = "SELECT count(*) FROM ($qry) AS numrow";
	}

	/**
	* Returns the number of entries received
	* @return Number of rows
	*/
	public function dsCountRow()
	{
		if (empty($this->property["tot"]))
		{
			if (!empty($this->property["result"]) && !is_bool($this->property["result"])) return $this->property["result"]->numRows();
			return 0;
		}
		return $this->property["tot"];
	}

	/**
	* Returns all the databases
	*/  
	public function dsShowDatabases() { }

	/**
	* Returns all tables of a database
   * @param string $database name of the database
	*/  
	public function dsShowTables($database )
	{
		$this->dsQuery('SELECT name FROM sqlite_master WHERE type = "table"');
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
		$qry  = "CREATE TABLE $table (key INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ";
		$this->dsQuery($qry);
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
		$this->dsQuery("ALTER TABLE $table1 RENAME TO $table2;");
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
		$this->dsQuery("DROP TABLE $table;");
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
		$this->dsQuery("ALTER TABLE $table DROP $field;");
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
		switch($index)
		{
			case "primary":
				$index = "PRIMARY KEY";
			break;
			case "unique":
				$index = "UNIQUE";
			break;
			case "index":
				$index = "INDEX";
			break;
			case "fulltext":
				$index = "FULLTEXT";
			break;
		}
		$fields = "".implode(", ", $fields)."";
		$this->dsQuery("CREATE INDEX $table.$index ADD $index ($fields);");
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
		$action = ($keyname == "PRIMARY") ? "PRIMARY KEY" : "INDEX $keyname";
		$this->dsQuery("ALTER TABLE $table DROP $action;");
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
		$sql = "";
		$this->dsShowColumns($db, $table);
		while($row = $this->property["result"]->fetchArray()) 
		{
			$fields['Field1'][$row["Field"]] = "".$row["Field"]."";
			$fields['Field2'][$row["Field"]] = $row["Field"];
			$fields['Null'][$row["Field"]] = $row["Null"];
			$fields['Type'][$row["Field"]] = (preg_match("/^(TINYBLOB|BLOB|MEDIUMBLOB|LONGBLOB)/i", $row["Type"])) ? "BLOB" : strtoupper($row["Type"]);
		}
		$this->dsQuery("SELECT * FROM $table LIMIT $start,$maxrows");
		if ($this->dsCountRow()>0)
		{
			$sql .= "\r\n\r\nINSERT INTO $table (".implode(",", $fields['Field1']).") VALUES \r\n";
			while($row = $this->property["result"]->fetchArray()) 
			{
				$values = array();
				foreach ($fields['Field2'] as $key => $field) 
				{
					if (empty($row[$field]) && ($fields['Null'][$field]=="YES")) $values[] = "NULL";
					else if ($fields['Type'][$field]=="BLOB") $values[]  = '0x'.bin2hex($row[$field]);
					else $values[] = "'".str_replace("'", "''", $row[$field])."'";
				}
				$sql .= "(".implode(",", $values)."),\r\n";
			}
			$sql = substr($sql, 0, -3).";\r\n";
		}
		return $sql;
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
		$fields = array();
		$sql = "";
		if ($structure) 
		{
			$tmp = $this->dsShowTable($db, $table);
			$sql = $tmp['Code'];
			if (strpos($sql, "\r\n") === false) $sql = str_replace("\n", "\r\n", $sql).";";
		}
		if ($data) $sql .= $this->exportData($db, $table, $start, $maxrows);
		return $sql;
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

		$this->dsQuery("FLUSH PRIVILEGES;");
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
			$items_str = implode(",", $items);
			$values_str = implode("','", $values);
			$qry = "INSERT INTO $table ($items_str) VALUES('$values_str')";
			if ($method=="update")
			{ 
				$qry .= " ON DUPLICATE KEY UPDATE ";
				for ($i=0; $i<count($items); $i++) $qry .= "".$items[$i]."='".str_replace("'", "''", $values[$i])."',";
				$qry = substr($qry, 0, -1);
			}
			if ($method!="error") $this->dsQuery($qry);
			else $this->dsQuery($qry) or $this->ErrorDS003($qry);
			$this->property["inslast"] = $this->affected();
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
		ClsError::showError("DS003", $this->property["conn"], $qry);
	}

	/**
	* Get number of affected rows in previous sqlite operation
	* @return integer Returns the number of affected rows on success, and -1 if the last query failed.
	*/
	public function affected()
	{
		return $this->property["conn"]->changes();
	}
}
?>
