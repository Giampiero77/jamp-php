<?php
/**
* Class management ds MYSQL
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/
class mysqlDs extends iDS
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
		$this->property["dsport"] 	= 3306;
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
			function_exists('mysql_connect') or ClsError::showError("DS00", "MYSQL");
			$this->property["conn"] = mysql_connect($this->property["dshost"].":".$this->property["dsport"], $this->property["dsuser"], stripcslashes($this->property["dspwd"])) or ClsError::showError("DS001");
			mysql_query("SET NAMES 'utf8'", $this->property["conn"]);
			mysql_query("SET CHARACTER SET 'utf8'", $this->property["conn"]);
			mysql_query("SET character_set_connection = 'utf8';", $this->property["conn"]);
			mysql_query("SET character_set_database = 'utf8';", $this->property["conn"]);
			if (!empty($this->property["dsdefault"])) $this->dsDBSelect();
			$this->property["open"] = true;
		}
	}

	/**
	* Select the database
	* @param string $dsname Name of the database
	*/
	public function dsDBSelect($dsname=null)
	{
		if (empty($dsname)) $dsname = $this->property["dsdefault"];
		if (!empty($dsname)) mysql_select_db($dsname, $this->property["conn"]) or ClsError::showError("DS002");
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

		$this->property["row"] = mysql_fetch_array($this->property["result"]);
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
		return mysql_affected_rows();
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
			return "FIND_IN_SET(`$key`, '$value')>0";
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
						$this->property["where"][] = "`".$store["keyname"][$k]."` $condition '".$store["keyvalue"][$k]."'" ;
					}
				}
				else
				{
					$condition = (empty($store["condition"])) ? "=" : $store["condition"];
					$this->property["where"][] = "`".$store["keyname"]."` $condition '".$store["keyvalue"]."'" ;
				}
			}
		}
		else if (($where == null) && ($item != null) && ($value != null)) $this->property["where"][] = "`$item` = '$value'" ;
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
		if ((empty($qry)) && (isset($this->property["dsdefault"])))
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
				$on = str_replace("AND", "` AND `", $on);
				$table .= $on;
			}
			 
			$limit = "";
			$qry = "SELECT $alias FROM $table";
			if ($this->property["limit"] > 0) 
			{
				if (stripos($qry, 'SQL_CALC_FOUND_ROWS') === false) 
				{
				  $qry = "SELECT SQL_CALC_FOUND_ROWS $alias FROM $table";
				}
				else 
				{
					 $pos = stripos($qry.$where, 'FROM');
					 if ($pos !== false) $this->property["qrycount"] = "SELECT COUNT(*) ".substr($qry.$where,$pos);
				}
				$limit = " LIMIT ".$this->property["start"].", ".$this->property["limit"];
			}

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
						$where .= "`".$tables[$k]."`.`".$keys[$k]."`='".$keyvalues[$k]."'";
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
			if (!is_array($order))
			{
				$order = explode(",", $order);
				for($i = 0; $i < count($order); $i++) $order[$i] = str_replace("`", "", trim($order[$i]));
			}
			$order = "`".implode("`, `", $order)."`";
			$order = str_replace(" DESC`","` DESC", $order);
			$order = str_replace(".","`.`", $order);
            if ($order == "``") $order = "";
			
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
				$order = "`".implode("`, `", $this->property["order"])."`";
				$order = str_replace(" DESC`","` DESC", $order);
				$order = str_replace(".","`.`", $order);
            	if ($order != "``") $qry .= " ORDER BY ".$order;
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
				$qry .= "`$k` = '".$item."'";
			}
		}
		$table = $this->property["join"];
		if ($insert) $qry="INSERT INTO `".$table."` SET $qry";
		else $qry="UPDATE `".$table."` SET $qry  WHERE $where";
		$this->dsQuery($qry);
		if ($insert) 
		{
			$this->property["inslast"] = mysql_insert_id($this->property["conn"]);
			if (($this->property["inslast"] == 0) && ($this->property["dssavetype"] == "row")) ClsError::showError("DS007");
		}
		else $this->property["inslast"] = 0;
		return mysql_affected_rows();
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
				if ($item == null || $item == 'null') $qry .= "`$k` = null";
				else $qry .= "`$k` = '".$item."'";
			}   

			$where = "";
			if (is_array($this->property["where"])) $where = implode(" and ", $this->property["where"]);
			if (is_null($this->property["joinsave"])) $table = $this->property["dstable"];
			else $table = $this->property["joinsave"];
			$qry="UPDATE `".$table."` SET $qry  WHERE ".$where;
		} 
		$this->dsQuery($qry);
		$this->property["inslast"] = 0;
		return mysql_affected_rows();
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
		  $items = implode("`,`", array_keys($this->property["item"]));
		  $values = implode("','", $this->property["item"]);
		  $values = "'".$values."'";
		  $values = str_replace ("'null'", "null", $values);
		  $values = str_replace ("'null", "null", $values);
		  if (is_null($this->property["joinsave"])) $table = $this->property["dstable"];
		  else $table = $this->property["joinsave"];
		  $qry = "INSERT INTO `$table` (`$items`) VALUES($values)";
		}
		$this->dsQuery($qry);
		$this->property["inslast"] = mysql_insert_id($this->property["conn"]);
		if (($this->property["inslast"] == 0) && ($this->property["dssavetype"] == "row")) ClsError::showError("DS007");
		return mysql_affected_rows();
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
		$this->property["result"] = mysql_query($qry, $this->property["conn"]) or $this->ErrorDS003($errorsql);
		$this->property["qrylast"] = $qry;
		$this->property['tot'] = null;

		if (stripos($qry, 'SQL_CALC_FOUND_ROWS') !== false) $this->property["qrycount"] = "SELECT FOUND_ROWS();";
		if (!empty($this->property["qrycount"]))
		{
		    $res = mysql_query($this->property["qrycount"], $this->property["conn"]) or $this->ErrorDS003($this->property["qrycount"]);
		    $row = mysql_fetch_row($res);
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
		if ($row >= 0) mysql_data_seek($this->property["result"], $row) or ClsError::showError("DS004");
		switch($this->property["fetch"])
		{
			case "object":
				return $this->property["row"] = mysql_fetch_object($this->property["result"]);	
			break;
			case "array":
				return $this->property["row"] = mysql_fetch_array($this->property["result"]);	
			break;
			case "row":
				return $this->property["row"] = mysql_fetch_row($this->property["result"]);	
			break;
			case "assoc":
				return $this->property["row"] = mysql_fetch_assoc($this->property["result"]);	
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
		if (mysql_num_rows($this->property["result"]) == 0) return false;
		mysql_data_seek($this->property["result"], $row) or ClsError::showError("DS004");
	}

	/**
	* Generates the necessary queries to the calculation of the lines
	* @return $qry SQL string
	*/
	public function setQryCount($qry)
	{
		$this->property["qrycount"] = "SELECT count(*) FROM ($qry) AS `numrow`";
	}

	/**
	* Returns the number of entries received
	* @return Number of rows
	*/
	public function dsCountRow()
	{
		if (empty($this->property["tot"]))
		{
			if (!empty($this->property["result"]) && !is_bool($this->property["result"])) return mysql_num_rows($this->property["result"]);
			return 0;
		}
		return $this->property["tot"];
	}

	/**
	* Returns all the databases
	*/  
    public function dsShowDatabases()
	{
		$this->dsQuery("SHOW DATABASES;");
 		$this->dsGetRow(); // Salta la riga d'intestazione
		$this->property['tot'] = mysql_num_rows($this->property["result"])-1;
	}

	/**
	* Returns all tables of a database
   * @param string $database name of the database
	*/  
	public function dsShowTables($database )
	{
		$this->dsQuery("SHOW TABLE STATUS FROM `$database` WHERE (`Engine` IS NOT NULL);");
	}

	/**
	* Returns all views of a database
	* @param string $database name of the database
	*/  
	public function dsShowViews($database)
	{
		$this->dsQuery("SHOW TABLE STATUS FROM `$database` WHERE (`Engine` IS NULL);");
	}

	/**
	* Returns all functions of a database
	* @param string $database name of the database
	*/  
	public function dsShowFunctions($database)
	{
		$qry = "SELECT * FROM `mysql`.`proc` WHERE `type`='FUNCTION'";
		if ($database!=false) $qry .= " AND `db`='".$database."'";
		$this->dsQuery($qry);
	}

	/**
	* Returns all procedures of a database
	* @param string $database name of the database
	*/  
	public function dsShowProcedures($database)
	{
		$qry = "SELECT * FROM `mysql`.`proc` WHERE `type`='PROCEDURE'";
		if ($database!=false) $qry .= " AND `db`='".$database."'";
		$this->dsQuery($qry);
	}

	/**
	* Returns the contents of a table
	* @param string $database name of the database
	* @param string $table name of the table
	*/  
	public function dsShowTable($database, $table)
	{
		$this->dsQuery("SHOW CREATE TABLE `$database`.`$table`;");
		$row = mysql_fetch_array($this->property["result"]); 
		$result["Name"] = $row["Table"];
		$result["Code"] = $row["Create Table"];
 		return $result;
	}

	/**
	* Returns the contents of a view
	* @param string $database name of the database
	* @param string $view name of the view
	*/  
	public function dsShowView($database, $view)
	{
		$this->dsDBSelect($database);
		$this->dsQuery("SHOW CREATE VIEW `$view`;");
		$row = mysql_fetch_array($this->property["result"]); 
		$result["Name"] = $row["View"];
		list($null, $result["User"], $null, $result["Host"]) = explode("`", $row["Create View"]);
		$result["Code"] = substr($row["Create View"],stripos($row["Create View"], "SELECT"));
		$result["Code"] = preg_replace("/`,/","`,\n\t", $result["Code"]);
		$result["Code"] = preg_replace("/( from | group by | order by | where | having | union )/","\n$0", $result["Code"]);
		$result["Code"] = preg_replace("/( left join | right join | join | \(| or | and )/","\n\t$0", $result["Code"]);
 		return $result;
	}

	/**
	* Returns the contents of a view
	* @param string $database name of the database
	* @param string $view name of the view
	*/  
	public function dsShowProcedure($database, $procedure)
	{
		$this->dsDBSelect($database);
		$this->dsQuery("SHOW CREATE PROCEDURE `$procedure`;");
		$row = mysql_fetch_array($this->property["result"]); 
		$result["Name"] = $row["Procedure"];
		$result["Code"] = $row["Create Procedure"];
		list($null, $result["User"], $null, $result["Host"]) = explode("`", $result["Code"]);
		$result["Code"] = substr($result["Code"],strpos($result["Code"], "`$procedure`")+strlen($procedure)+2);
 		return $result;	
	}

	/**
	* Returns the contents of a function
	* @param string $database name of the database
	* @param string $view name of the function
	*/  
	public function dsShowFunction($database, $function)
	{
		$this->dsDBSelect($database);
		$this->dsQuery("SHOW CREATE FUNCTION `$function`;");
		$row = mysql_fetch_array($this->property["result"]); 
		$result["Name"] = $row["Function"];
		$result["Code"] = $row["Create Function"];
		list($null, $result["User"], $null, $result["Host"]) = explode("`", $result["Code"]);
		$result["Code"] = substr($result["Code"],strpos($result["Code"], "`$function`")+strlen($function)+2);
		return $result;	
	}

   /**
	* Collation List
	*/  
	public function dsShowCollation()
	{
		$this->dsQuery("SHOW COLLATION;");
	}

	/**
	* Returns the list of users and privileges
	* @param string $database name of the database
	* @param string $table name of the table
	*/  
	public function dsShowUsers($database = false, $table = false)
	{
		$this->dsDBSelect('mysql');
		if ($database != false && $table != false) $qry = "SELECT * FROM `tables_priv` WHERE `db`='".$database."' AND `Table_name`='".$table."'";
		else if ($database!=false) $qry = "SELECT *, `Host` AS `Host1` FROM `db` WHERE `Db`='".$database."'";
		else $qry = "SELECT *, `Password` AS `Pwd` FROM `user`;";
		$this->dsQuery($qry);
	}

	/**
	* Create a database 
	* @param string $db name of the database
	*/  
	public function CreateDatabase($db)
	{
		$this->dsQuery("CREATE DATABASE `$db`");
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
		if (isset($engine) || ($engine=="")) $engine = "MYISAM";
		if (isset($collation) || ($collation=="")) $collation = "utf8_general_ci";
		list($coll) = explode("_", $collation);
		$qry  = "CREATE TABLE `$db`.`$table` (`key` INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ";
		$qry .= "ENGINE = $engine ";
		$qry .= "CHARACTER SET $coll COLLATE $collation;";
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
		$this->dsDBSelect($db);
		if ($user && $host) $qry = "CREATE DEFINER=`$user`@`$host` VIEW `$view` AS ".stripslashes($sql);
		else $qry = "CREATE VIEW `$view` AS ".stripslashes($sql);
		$this->dsQuery($qry);
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
		$this->dsDBSelect($db);		
		if (isset($user) && isset($host)) $qry = "CREATE OR REPLACE DEFINER=`$user`@`$host` VIEW `$view` AS ".stripslashes($sql);
		else $qry = "CREATE OR REPLACE VIEW `$view` AS ".stripslashes($sql);
		$this->dsQuery($qry);
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
		if (isset($user) && isset($host)) $qry = "CREATE DEFINER=`$user`@`$host` FUNCTION `$db`.`$func` ".stripslashes($sql);
		else $qry = "CREATE FUNCTION `$func` ".stripslashes($sql);
		$this->dsQuery($qry);
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
		$this->DropFunction($db, $func);
		$this->CreateFunction($db, $func, $sql, $user, $host);
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
		if (isset($user) && isset($host)) $qry = "CREATE DEFINER=`$user`@`$host` PROCEDURE `$db`.`$proc` ".stripslashes($sql);
		else $qry = "CREATE PROCEDURE `$proc` ".stripslashes($sql);
		$this->dsQuery($qry);
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
		$this->DropProcedure($db, $proc);
		$this->CreateProcedure($db, $func, $sql, $user, $host);
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
		$this->dsQuery("RENAME TABLE `$db1`.`$table1` TO `$db2`.`$table2`;");
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
		$sql = $this->dsShowView($db1, $view1);
		$this->CreateView($db2, $view2, $sql["Code"], $sql["User"], $sql["Host"]);
		$this->DropView($db1, $view1);
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
		$sql = $this->dsShowFunction($db1, $func1);
		$this->CreateFunction($db2, $func2, $sql['Code'], $sql["User"], $sql["Host"]);
		$this->DropFunction($db1, $func1);
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
		$sql = $this->dsShowProcedure($db1, $proc1);
		$this->CreateProcedure($db2, $proc2, $sql['Code'], $sql["User"], $sql["Host"]);
		$this->DropProcedure($db1, $proc1);
	} 

	/**
	* Drop a database
	* @param string $db name of the database
	*/  
	public function DropDatabase($db)
	{
		$this->dsQuery("DROP DATABASE `$db`");
	}  

	/**
	* Drop a table
	* @param string $db name of the database
	* @param string $table name of the table
	*/  
	public function DropTable($db, $table)
	{
		$this->dsQuery("DROP TABLE `$db`.`$table`;");
    }  

	/**
	* Drop a view
	* @param string $db name of the database
	* @param string $view name of the view
	*/  
	public function DropView($db, $view)
	{
		$this->dsQuery("DROP VIEW `$db`.`$view`;");
	}  

	/**
	* Drop a Function
	* @param string $db name of the database
	* @param string $func name of the function
	*/  
	public function DropFunction($db, $func)
	{
		$this->dsQuery("DROP FUNCTION IF EXISTS `$db`.`$func`;");
	} 

	/**
	* Drop a procedure
	* @param string $db name of the database
	* @param string $proc name of the procedure
	*/  
	public function DropProcedure($db, $proc)
	{
		$this->dsQuery("DROP PROCEDURE IF EXISTS `$db`.`$proc`;");
	} 

	/**
	* Returns all the columns of a table
	* @param string $db name of the database
	* @param string $table name of the table
	*/  
	public function dsShowColumns($db, $table)
	{
		$this->dsQuery("SHOW FULL COLUMNS FROM `$table` FROM `$db`;");
	}  

    /**
	* Returns the structure of a table, view or query
	* @param string $table name of the table
	*/  
	public function dsShowColumnsResult($table = null)
	{
		global $system;
		if (isset($table)) 
		{
			$table = trim($table);
			if (preg_match("/^(CREATE|ALTER|DROP|SHOW|RENAME|INSERT|UPDATE) /i", $table)) return array();
			if (preg_match("/^SELECT /i", $table)) $qry="SELECT * FROM ($table) AS `struct` LIMIT 0;";
			else $qry="SELECT * FROM (`$table`) LIMIT 0;";
			$this->dsQuery($qry, $table);
		}
		$out = array();
		for($i = 0; $i < mysql_num_fields($this->property["result"]); $i++)
		{
			$out[] = mysql_fetch_field($this->property["result"], $i);
		}
		return $out;
	}

	/**
	* Returns all indexes of a table
	* @param string $db name of the database
	* @param string $table name of the table
	*/  
	public function dsShowIndex($db, $table)
	{
		$this->dsQuery("SHOW INDEX FROM `$table` FROM `$db`");
    }  

	/**
	* Returns all the foreign keys of a table
	* @param string $db name of the database
	* @param string $table name of the table
	*/  
	public function dsShowForeignKey($db, $table)
	{
		$regEx  = "/^CONSTRAINT ";
		$regEx .= "`((?:[^`])+)` ";                  			  // constraint name
		$regEx .= "FOREIGN KEY +\(((?:`(?:[^`])+`,?\s*)+)\) +";   // local columns
		$regEx .= "REFERENCES\s*(?:`((?:[^`])+)`\.)?";            // foreign db
		$regEx .= "`((?:[^`])+)` +";                              // foreign table
		$regEx .= "\(((?:`(?:[^`])+`,?\s*)+)\)";                  // foreign columns
		$regEx .= "(?: ON DELETE((?: RESTRICT| CASCADE| SET NULL| NO ACTION)))?";
		$regEx .= "(?: ON UPDATE((?: RESTRICT| CASCADE| SET NULL| NO ACTION)))?/";
		$this->dsQuery("SHOW CREATE TABLE `$db`.`$table`;");
		$result = array();
		while ($row = mysql_fetch_array($this->property["result"]))
		{
			$rows = explode("\n", $row["Create Table"]);
			foreach ($rows as $i => $row) 
			{
				$split = array();
				$row = trim($row);
				if (preg_match($regEx,$row,$split))
				{
					$result[$i+1]["constraint"] = $split[1];
					$result[$i+1]["foreignkey"] = str_replace("`", "", $split[2]);
					$result[$i+1]["referencedb"] = ($split[3]=="") ? $db : $split[3];

					$result[$i+1]["referencetable"] = $split[4];
					$result[$i+1]["referencekey"] = str_replace("`", "", $split[5]);
					$result[$i+1]["ondelete"] = (isset($split[6])) ? trim($split[6]) : "";
					$result[$i+1]["onupdate"] = (isset($split[7])) ? trim($split[7]) : "";;
				}		
			}		
		}		
		return $result;
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
		$qry = "ALTER TABLE `$db`.`$table` $type";
		if ($type=="CHANGE") $qry .= " `".$value['keynamevalue']."` `".$value['Field']."`";
		else $qry .= " `".$value['Field']."`";
		if (isset($value['Lenght']) && ($value['Lenght']!="")) $value['Type'] = $value['Type']."(".$value['Lenght'].")";
		if (isset($value['Type']) && ($value['Type'])!="") $qry .= " ".$value['Type'];
		if (isset($value['Collation']) && ($value['Collation']!="")) 
		{
			list($coll) = explode("_", $value['Collation']);
			if ($coll!="null") $qry .= " CHARACTER SET $coll COLLATE ".$value['Collation'];
		}  
		if (isset($value['Null']) && ($value['Null'])=="YES") $qry .= " NULL";
		else $qry .= " NOT NULL";
		if (isset($value['Extra']) && ($value['Extra']!="")) $qry .= " AUTO_INCREMENT";
		if (isset($value['Default']) && ($value['Default']!="") && ($value['Default']!='null')) $qry .= " DEFAULT '".$value['Default']."'";
		if (isset($value['Comment']) && ($value['Comment']!="") && ($value['Comment']!='null')) $qry .= " COMMENT '".$_POST['Comment']."'";
		$this->dsQuery($qry);
    }  

	/**
	* Delete the field in a table
	* @param string $db name of the database
	* @param string $table name of the table
	* @param string $field name of the field
	*/  
	public function DropField($db, $table, $field)
	{
		$this->dsQuery("ALTER TABLE `$db`.`$table` DROP `$field`;");
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
		$fields = "`".implode("`, `", $fields)."`";
		$this->dsQuery("ALTER TABLE `$db`.`$table` ADD $index ($fields);");
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
		$qry = "ALTER TABLE `$fkdb`.`$fktable` ADD FOREIGN KEY ($fkfields) REFERENCES `$rfdb`.`$rftable` ($rffields)";
		if ($ondelete!="") $qry .= " ON DELETE $ondelete";
		if ($onupdate!="") $qry .= " ON UPDATE $onupdate";
		$this->dsQuery($qry);
	}  

	/**
	* Delete a index
	* @param string $db name of the database
	* @param string $table name of the table
	* @param string $keyname name of the key
	*/  
	public function DropIndex($db, $table, $keyname)
	{
		$action = ($keyname == "PRIMARY") ? "PRIMARY KEY" : "INDEX `$keyname`";
		$this->dsQuery("ALTER TABLE `$db`.`$table` DROP $action;");
	}  

	/**
	* Delete a foreign key
	* @param string $db name of the database
	* @param string $table name of the table
	* @param string $keyname name of the key
	*/  
	public function DropForeignKey($db, $table, $keyname)
	{
		$this->dsQuery("ALTER TABLE `$db`.`$table` DROP FOREIGN KEY `".$keyname."`;");
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
		while($row = mysql_fetch_array($this->property["result"])) 
		{
			$fields['Field1'][$row["Field"]] = "`".$row["Field"]."`";
			$fields['Field2'][$row["Field"]] = $row["Field"];
			$fields['Null'][$row["Field"]] = $row["Null"];
			$fields['Type'][$row["Field"]] = (preg_match("/^(TINYBLOB|BLOB|MEDIUMBLOB|LONGBLOB)/i", $row["Type"])) ? "BLOB" : strtoupper($row["Type"]);
		}
		$this->dsQuery("SELECT * FROM `$db`.`$table` LIMIT $start,$maxrows");
		if ($this->dsCountRow()>0)
		{
			$sql .= "\r\n\r\nINSERT INTO `$table` (".implode(",", $fields['Field1']).") VALUES \r\n";
			while($row = mysql_fetch_array($this->property["result"])) 
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
		$sql = "CREATE DATABASE `$db`;\r\n";
		$sql .= "USE `$db`;";
		return $sql;
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
		$fields = array();
		$sql = "";
		if ($structure) 
		{
			$tmp = $this->dsShowView($db, $view);
			$sql = "CREATE OR REPLACE VIEW `".$db."`.`$view` AS \r\n".$tmp["Code"].";";
			if (strpos($sql, "\r\n") === false) $sql = str_replace("\n", "\r\n", $sql).";";
		}
		if ($data) $sql .= $this->exportData($db, $view, $start, $maxrows);
		return $sql;
	} 

	/**
	* Export function
	* @param string $db name of the database
	* @param string $function name of the view
	*/  
	public function exportFunction($db, $function)
	{
    	$tmp = $this->dsShowFunction($db, $function);
		$sql = "CREATE FUNCTION `$function` ".$tmp["Code"].";";
		if (strpos($sql, "\r\n") === false) $sql = str_replace("\n", "\r\n", $sql).";";
    	return $sql;
	} 

	/**
	* Export procedure
	* @param string $db name of the database
	* @param string $procedure name of the procedure
	*/  
	public function exportProcedure($db, $procedure)
	{
		$tmp = $this->dsShowFunction($db, $procedure);
		$sql = "CREATE PROCEDURE `$procedure` ".$tmp["Code"].";";
		if (strpos($sql, "\r\n") === false) $sql = str_replace("\n", "\r\n", $sql).";";
		return $sql;
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
 		$qry  = "UPDATE `mysql`.`user` SET ";
 		$qry .= "`Host` = '".$value['Host']."', ";
		$qry .= "`User` = '".$value['User']."', ";
	 	if ($value['Password']=='') $qry .= "`Password` = '', ";
		$qry .= "`Select_priv` = '".$value['Select_priv']."', ";
		$qry .= "`Insert_priv` = '".$value['Insert_priv']."', ";
		$qry .= "`Update_priv` = '".$value['Update_priv']."', ";
		$qry .= "`Delete_priv` = '".$value['Delete_priv']."', ";
		$qry .= "`Create_priv` = '".$value['Create_priv']."', ";
		$qry .= "`Drop_priv` = '".$value['Drop_priv']."', ";
		$qry .= "`Reload_priv` = '".$value['Reload_priv']."', ";
		$qry .= "`Shutdown_priv` = '".$value['Shutdown_priv']."', ";
		$qry .= "`Process_priv` = '".$value['Process_priv']."', ";
		$qry .= "`File_priv` = '".$value['File_priv']."', ";
		$qry .= "`Grant_priv` = '".$value['Grant_priv']."', ";
		$qry .= "`References_priv` = '".$value['References_priv']."', ";
		$qry .= "`Index_priv` = '".$value['Index_priv']."', ";
		$qry .= "`Alter_priv` = '".$value['Alter_priv']."', ";
		$qry .= "`Show_db_priv` = '".$value['Show_db_priv']."', ";
		$qry .= "`Super_priv` = '".$value['Super_priv']."', ";
		$qry .= "`Create_tmp_table_priv` = '".$value['Create_tmp_table_priv']."', ";
		$qry .= "`Lock_tables_priv` = '".$value['Lock_tables_priv']."', ";
		$qry .= "`Execute_priv` = '".$value['Execute_priv']."', ";
		$qry .= "`Repl_slave_priv` = '".$value['Repl_slave_priv']."', "; 
		$qry .= "`Repl_client_priv` = '".$value['Repl_client_priv']."', "; 
		$qry .= "`Create_view_priv` = '".$value['Create_view_priv']."', "; 
		$qry .= "`Show_view_priv` = '".$value['Show_view_priv']."', "; 
		$qry .= "`Create_routine_priv` = '".$value['Create_routine_priv']."', "; 
		$qry .= "`Alter_routine_priv` = '".$value['Alter_routine_priv']."', "; 
		$qry .= "`Create_user_priv` = '".$value['Create_user_priv']."', "; 
		$qry .= "`ssl_type` = '".$value['ssl_type']."', "; 
		$qry .= "`max_questions` = '".$value['max_questions']."', ";
		$qry .= "`max_updates` = '".$value['max_updates']."', ";
		$qry .= "`max_connections` = '".$value['max_connections']."', "; 
		$qry .= "`max_user_connections` = '".$value['max_user_connections']."' "; 
		$qry .= "WHERE `User` = '".$value['keynamevalue']."' LIMIT 1;";
		$this->dsQuery($query);
		if ($value['Password']!=$value['Pwd']) 
 		{
			$this->dsQuery("SET PASSWORD FOR '".$value['User']."'@'".$value['Host']."' = PASSWORD('".$value['Password']."');");
		}
    }  

	/**
	* Modify the privileges of the global
	* @param array $value privileges
	*/  
	public function dsModDbGrant($value)
	{
 		$qry  = "UPDATE `mysql`.`db` SET ";
 		$qry .= "`User` = '".$value['User']."', ";
 		$qry .= "`Host` = '".$value['Host']."', ";
		$qry .= "`Select_priv` = '".$value['Select_priv']."', ";
		$qry .= "`Insert_priv` = '".$value['Insert_priv']."', ";
		$qry .= "`Update_priv` = '".$value['Update_priv']."', ";
		$qry .= "`Delete_priv` = '".$value['Delete_priv']."', ";
		$qry .= "`Create_priv` = '".$value['Create_priv']."', ";
		$qry .= "`Drop_priv` = '".$value['Drop_priv']."', ";
		$qry .= "`Grant_priv` = '".$value['Grant_priv']."', ";
		$qry .= "`References_priv` = '".$value['References_priv']."', ";
		$qry .= "`Index_priv` = '".$value['Index_priv']."', ";
		$qry .= "`Alter_priv` = '".$value['Alter_priv']."', ";
		$qry .= "`Create_tmp_table_priv` = '".$value['Create_tmp_table_priv']."', ";
		$qry .= "`Lock_tables_priv` = '".$value['Lock_tables_priv']."', ";
		$qry .= "`Execute_priv` = '".$value['Execute_priv']."', ";
		$qry .= "`Create_view_priv` = '".$value['Create_view_priv']."', "; 
		$qry .= "`Show_view_priv` = '".$value['Show_view_priv']."', "; 
		$qry .= "`Create_routine_priv` = '".$value['Create_routine_priv']."', "; 
		$qry .= "`Alter_routine_priv` = '".$value['Alter_routine_priv']."' "; 
		$qry .= "WHERE `User`='".$value['keynamevalue']."' AND `Host`='".$value['Host1']."' AND `Db`='".$value['Db']."' LIMIT 1;";
		$this->dsQuery($qry);
    }  

	/**
	* Insert the privileges of the global
	* @param array $value privileges
	*/  
	public function dsAddDbGrant($value)
	{
		if (!isset($value['Select_priv'])) $value['Select_priv'] = "N";
		if (!isset($value['Insert_priv'])) $value['Insert_priv'] = "N";
		if (!isset($value['Update_priv'])) $value['Update_priv'] = "N";
		if (!isset($value['Delete_priv'])) $value['Delete_priv'] = "N";
		if (!isset($value['Create_priv'])) $value['Create_priv'] = "N";
		if (!isset($value['Drop_priv'])) $value['Drop_priv'] = "N";
		if (!isset($value['Grant_priv'])) $value['Grant_priv'] = "N";
		if (!isset($value['References_priv'])) $value['References_priv'] = "N";
		if (!isset($value['Index_priv'])) $value['Index_priv'] = "N";
		if (!isset($value['Alter_priv'])) $value['Alter_priv'] = "N";
		if (!isset($value['Create_tmp_table_priv'])) $value['Create_tmp_table_priv'] = "N";
		if (!isset($value['Lock_tables_priv'])) $value['Lock_tables_priv'] = "N";
		if (!isset($value['Create_view_priv'])) $value['Create_view_priv'] = "N";
		if (!isset($value['Show_view_priv'])) $value['Show_view_priv'] = "N";
		if (!isset($value['Create_routine_priv'])) $value['Create_routine_priv'] = "N"; 
		if (!isset($value['Alter_routine_priv'])) $value['Alter_routine_priv'] = "N";
		if (!isset($value['Execute_priv'])) $value['Execute_priv'] = "N";
 		$qry  = "INSERT INTO `mysql`.`db` ";
		$qry .= "(`Host`,`Db`,`User`,`Select_priv`,`Insert_priv`,`Update_priv`,`Delete_priv`,`Create_priv`,`Drop_priv`,";
		$qry .= "`Grant_priv`,`References_priv`,`Index_priv`,`Alter_priv`,`Create_tmp_table_priv`,`Lock_tables_priv`,";
		$qry .= "`Create_view_priv`,`Show_view_priv`,`Create_routine_priv`,`Alter_routine_priv`,`Execute_priv`) ";
		$qry .= "VALUES ('".$value['Host']."','".$value['Db']."','".$value['User']."','".$value['Select_priv']."','".$value['Insert_priv']."',";
		$qry .= "'".$value['Update_priv']."','".$value['Delete_priv']."','".$value['Create_priv']."','".$value['Drop_priv']."',";
		$qry .= "'".$value['Grant_priv']."','".$value['References_priv']."','".$value['Index_priv']."','".$value['Alter_priv']."',";
		$qry .="'".$value['Create_tmp_table_priv']."','".$value['Lock_tables_priv']."','".$value['Create_view_priv']."',";
		$qry .="'".$value['Show_view_priv']."','".$value['Create_routine_priv']."',"; 
		$qry .= "'".$value['Alter_routine_priv']."','".$value['`Execute_priv`']."');"; 
		$this->dsQuery($qry);
	}  

	/**
	* Delete user privileges associated with the db
	* @param string $db name of the database
	* @param string $user privileges of the user
	* @param string $host name of the host
	*/  
	public function DropGrantDb($db, $user, $host)
	{
		$this->dsQuery("DELETE FROM `mysql`.`db` WHERE `Host`='".$host."' AND `Db`= '".$db."' AND `User`='".$user."' LIMIT 1");
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
			if ($method!="error") mysql_query($qry, $conn);
			else mysql_query($qry, $conn) or $this->ErrorDS003($qry);
			$this->property["inslast"] = mysql_insert_id($conn);
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
			$err = mysql_error($this->property["conn"]);
			$item = explode("'", $err);
			$item = explode("-", $item[1]);
			ClsError::showError("DS006", $item[0]);
		}
		else ClsError::showError("DS003", $this->property["conn"], $qry);
	}
}
?>
