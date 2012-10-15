<?php
/**
* Class management ds Oracle 8i
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/
class oracle8Ds extends iDS
{
	private $_row_number = 'OCI8_ROWNUM';		// Simulazione LIMIT
	private $_debug	= false;

	/**
	* Construct
	*/
	public function __construct()
	{
		if ($this->_debug) openlog("oracle8Ds", LOG_PID | LOG_PERROR, LOG_LOCAL0);

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
		$this->property["querynolimit"] = null;
	}

	/**
	* Connects to the database
	*/
	public function dsConnect()
	{
		if ($this->property["open"] == false)
		{
			function_exists('oci_connect') or ClsError::showError("DS00", "MYSQL");

			$this->dsDBSelect($this->property["dsdefault"]);
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
		if (!empty($dsname)) {
			putenv("NLS_LANG=AMERICAN_AMERICA.AL32UTF8");
			putenv("NLS_DATE_FORMAT=YYYY-MM-DD HH24:MI:SS");
			$this->property["conn"] = oci_connect(
				$this->property["dsuser"],
				stripcslashes($this->property["dspwd"]),
				$dsname
			) or ClsError::showError("DS001");
		}
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
		
		$this->property["row"] = TO_REIMPLEMENT_fetch_array($this->property["result"]);
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
		return TO_REIMPLEMENT_affected_rows();
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
		if ((empty($qry)) && (isset($this->property["dsdefault"])))
		{
			if (isset($this->property["join"])) //JOIN
			{
				if (! is_array($this->property["where"])) {
					$this->property["where"] = $this->property["join"]." AND (".$this->property["where"].")";
				} else {
					$this->property["where"][] = $this->property["join"];
				}
			}
			$where = $this->property["where"];
			$order = $this->property["order"];
			$alias = "*";
			if ($this->property["selecteditems"]!="*") $alias = $this->property["selecteditems"];
			if ($alias == '*') {
				die("Devi specificare [selecteditems] nel datasource [".$this->property['id']."]");
			}

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
			$limit = "";
			if ($table == "") return false;
			$qry = "SELECT $alias FROM $table";
			if (is_array($this->property["where"])) $where = implode(" and ", $this->property["where"]);

			if ($this->property["limit"] > 0) 
			{
				if (empty($where)) $where="1=1";
				$pos = stripos($qry.$where, 'FROM');
				if ($pos !== false) $this->property["qrycount"] = "SELECT COUNT(*) as tot ".substr($qry.' where '.$where,$pos);
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
//		  if ((stripos($qry, "LIMIT") === false) && ($this->property["limit"] > 0))
//		  {
//				$qry .= " LIMIT ".$this->property["start"].", ".$this->property["limit"];
//		  }
		}
		$this->property["querynolimit"] = $qry;
		if ($this->property["limit"] > 0) {
			$arr = explode(',',$this->property["selecteditems"]);
			$s = array();
			foreach($arr as $field) {
				$p = strpos($field, ' as ');
				if ($p !== false) {
					$s[] = substr($field, $p+4);
				} else {
					$p = strpos($field, '.');
					if ($p !== false) {
						$s[] = substr($field, $p+1);
					} else {
						$s[] = $field;
					}
				}
			}
			$fields = implode(',', $s);
			$qry = "select * from
(select ROWNUM as {$this->_row_number},$fields from ($qry))
where {$this->_row_number} between ".$this->property["start"]." and ".($this->property["start"]+$this->property["limit"]);
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
			$this->property["inslast"] = TO_REIMPLEMENT_insert_id($this->property["conn"]);
			if (($this->property["inslast"] == 0) && ($this->property["dssavetype"] == "row")) ClsError::showError("DS007");
		}
		else $this->property["inslast"] = 0;
		return 0;
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
				if ($k == $this->_row_number) continue;
				$date_parts=null;
				preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/',$item,$date_parts);
				$isDateTime = (is_array($date_parts) && Count($date_parts) == 7);
				if ($isDateTime) {
					// Presuppongo che sia un campo data, devo utilizzare la sintassi oracle8
					$item = "to_date('$item', 'yyyy-mm-dd hh24:mi:ss')";
				}

				if (!empty($qry)) $qry .= ", ";
				if ($item == null || $item == 'null') $qry .= "$k = null";
				else {
					if ($isDateTime) $qry .= "$k = $item";
								else $qry .= "$k = '".$this->oci_escape($item)."'";
				}
			}

			$where = "";
			if (is_array($this->property["where"])) $where = implode(" and ", $this->property["where"]);
			if (is_null($this->property["joinsave"])) $table = $this->property["dstable"];
			else $table = $this->property["joinsave"];
			$qry="UPDATE ".$table." SET $qry  WHERE ".$where;
		}
		$result=$this->dsQuery($qry);

		$this->property["inslast"] = 0;
		return;
	}

	function oci_escape($s) {
		return str_replace(
			array("\'",	"\\\\"	),
			array("''",	"\\"	),
			$s
		);
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
		$result=$this->dsQuery($qry);
		if (empty($result)) $result=0;

		$this->property["inslast"] = $result;
		if (($this->property["inslast"] == 0) && ($this->property["dssavetype"] == "row")) ClsError::showError("DS007");
		return $result;
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
		if ($this->_debug) syslog(LOG_WARNING, "dsQuery($qry, $errorsql)");

		if (empty($qry)) return;

		$this->property["result"] = oci_parse($this->property["conn"], $qry );
//		die($qry);
		try {
			$result=oci_execute($this->property["result"], OCI_COMMIT_ON_SUCCESS);
		} catch (Exception $e) {
			$this->ErrorDS003($errorsql." ".$e->getMessage());
			$result=false;
		}
		$this->property["qrylast"] = $qry;
		$this->property['tot'] = null;

		if ($this->property["debug"]=="true") $system->debug($this->property["id"], $qry);
		return $result;
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
		if ($row >= 0) {die("REIMPLEMENT_data_seek"); TO_REIMPLEMENT_data_seek($this->property["result"], $row) or ClsError::showError("DS004"); }
		switch($this->property["fetch"])
		{
			case "array":
			case "row":
			case "assoc":
			case "object":
				$row = null;
				ocifetchinto($this->property["result"], $row, OCI_ASSOC+OCI_RETURN_NULLS);
				if (empty($row)) return;
				$result = array();
				foreach($row as $key => $value) {
					$result[$key] = $value; // iconv("UTF-8", "ISO-8859-1//IGNORE", $value);
				}
				$this->property["row"] = $result;
				return $result;
				break;
			default:
				// non supportato
				ClsError::showError("DS001");
				break;
			case "array":
		}
	}

	/**
	* Move the pointer of the results
	* @param string $row number line
	*/
	public function dsMoveRow($row)
	{
		if (empty($this->property["result"])) return;
		if (TO_REIMPLEMENT_num_rows($this->property["result"]) == 0) return false;
		TO_REIMPLEMENT_data_seek($this->property["result"], $row) or ClsError::showError("DS004");
	}

	/**
	* Generates the necessary queries to the calculation of the lines
	* @return $qry SQL string
	*/
	public function setQryCount($qry)
	{
//		$this->property["qrycount"] = "SELECT count(*) AS numrow FROM (".$this->property["querynolimit"].")";
	}

	/**
	* Returns the number of entries received
	* @return Number of rows
	*/
	public function dsCountRow()
	{
		if (empty($this->property["qrycount"])) $this->setQryCount($this->property["qrylast"]);

		if (empty($this->property["qrycount"])) return -1;

		$res = oci_parse($this->property["conn"], $this->property["qrycount"] );
//		echo $this->property["qrycount"];
		try {
			if ($this->_debug) syslog(LOG_WARNING, $this->property["qrycount"]);
			oci_execute($res, OCI_DEFAULT);
		} catch (Exception $e) {
			$this->ErrorDS003("err dsCountRow() ".$e->getMessage());
		}
		$row = null;
		ocifetchinto($res, $row, OCI_ASSOC+OCI_RETURN_NULLS);
		$this->property["tot"] = is_array($row) && array_key_exists('TOT', $row) ? $row['TOT'] : -1;
		return $this->property["tot"];
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
		$this->dsQuery("DROP PROCEDURE IF EXISTS $db.$proc;");
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
		die("TODO");
	}

	/**
	* Returns all indexes of a table
	* @param string $db name of the database
	* @param string $table name of the table
	*/  
	public function dsShowIndex($db, $table)
	{
		$this->dsQuery("SHOW INDEX FROM $table FROM $db");
    }  

	/**
	* Returns all the foreign keys of a table
	* @param string $db name of the database
	* @param string $table name of the table
	*/  
	public function dsShowForeignKey($db, $table)
	{
		die("TODO");
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
		$qry = "ALTER TABLE $db.$table $type";
		if ($type=="CHANGE") $qry .= " ".$value['keynamevalue']." ".$value['Field']."";
		else $qry .= " ".$value['Field']."";
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
		$this->dsQuery("ALTER TABLE $db.$table DROP $field;");
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
		$this->dsQuery("ALTER TABLE $db.$table ADD $index ($fields);");
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
		$qry = "ALTER TABLE $fkdb.$fktable ADD FOREIGN KEY ($fkfields) REFERENCES $rfdb.$rftable ($rffields)";
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
		$action = ($keyname == "PRIMARY") ? "PRIMARY KEY" : "INDEX $keyname";
		$this->dsQuery("ALTER TABLE $db.$table DROP $action;");
	}  

	/**
	* Delete a foreign key
	* @param string $db name of the database
	* @param string $table name of the table
	* @param string $keyname name of the key
	*/  
	public function DropForeignKey($db, $table, $keyname)
	{
		$this->dsQuery("ALTER TABLE $db.$table DROP FOREIGN KEY ".$keyname.";");
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
		die("TODO");
	}

	/**
	* Export database
	* @param string $db database name
	*/  
	public function exportDatabase($db)
	{
		die("TODO");
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
		die("TODO");
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
		die("TODO");
	} 

	/**
	* Export function
	* @param string $db name of the database
	* @param string $function name of the view
	*/  
	public function exportFunction($db, $function)
	{
		die("TODO");
	} 

	/**
	* Export procedure
	* @param string $db name of the database
	* @param string $procedure name of the procedure
	*/  
	public function exportProcedure($db, $procedure)
	{
		die("TODO");
	}

	/**
	* 	Reload the user privileges
	*/  
	public function reloadPrivileges()
	{
		die("TODO");
	}

	/**
	* Modify the privileges of the global
	* @param array $value privileges
	*/  
	public function dsModUser($value)
	{
		die("TODO");
    }  

	/**
	* Modify the privileges of the global
	* @param array $value privileges
	*/  
	public function dsModDbGrant($value)
	{
		die("TODO");
    }  

	/**
	* Insert the privileges of the global
	* @param array $value privileges
	*/  
	public function dsAddDbGrant($value)
	{
		die("TODO");
	}  

	/**
	* Delete user privileges associated with the db
	* @param string $db name of the database
	* @param string $user privileges of the user
	* @param string $host name of the host
	*/  
	public function DropGrantDb($db, $user, $host)
	{
		die("TODO");
	}  

	/**
	* Import data from another result
	* @param result $result Result to be imported
	* @param string $method Import method
	*/
	public function dsImport($result, $method)
	{
		die("TODO");
  	}

	/**
	* Error Handling
	* @param string $qry SQL Query
	*/
	private function ErrorDS003($qry)
	{
		if (TO_REIMPLEMENT_errno($this->property["conn"]) == 1062)
		{
			$err = TO_REIMPLEMENT_error($this->property["conn"]);
			$item = explode("'", $err);
			$item = explode("-", $item[1]);
			ClsError::showError("DS006", $item[0]);
		}
		else ClsError::showError("DS003", $this->property["conn"], $qry);
	}
	
	/**
	* Get number of affected rows in previous MySQL operation
	* @return integer Returns the number of affected rows on success, and -1 if the last query failed.
	*/
	public function affected()
	{
		return TO_REIMPLEMENT_affected_rows();
	}
}
?>
