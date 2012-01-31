<?php
/**
* Class management ds SMS
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class smsDs extends iDS
{	
	/**
	* Construct
	*/
	public function __construct()
	{
		$this->property["poscount"]	= 0;
		$this->property["open"]  		= false;
		$this->property["prefix"] 		= "+39";
		$this->property["verbose"] 	= "1";
		$this->store = "";
	}

	/**
	* Connects to the ftp
	*/
	public function dsConnect()
	{
		$this->property["open"]  = true;
	}

	public function GetUserStatus($user, $password)
	{
		$qry  = "http://151.1.233.1/ProgettoSMS/HttpGateway/GetUserStatus.aspx?";
		$qry .= "user=".urlencode($user);
		$qry .= "&password=".urlencode($password);
		return $this->dsQuery($qry);
	}

	public function GetMessageStatus($user, $password, $idmsg)
	{
		$qry  = "http://151.1.233.1/ProgettoSMS/HttpGateway/GetMessageStatus.aspx?";
		$qry .= "user=".urlencode($user);
		$qry .= "&password=".urlencode($password);
		$qry .= "&MessageID=".urlencode($idmsg);
		if (!empty($this->property["answerrecipients"])) $qry .= "&AnswerRecipients=".$this->property["answerrecipients"];
		return $this->dsQuery($qry);
	}

	public function GetGateways($user, $password)
	{
		$qry  = "http://151.1.233.1/ProgettoSMS/HttpGateway/GetGateways.aspx?";
		$qry .= "user=".urlencode($user);
		$qry .= "&password=".urlencode($password);
		return $this->dsQuery($qry);
	}

	public function GetCarriers($user, $password)
	{
		$qry  = "http://151.1.233.1/ProgettoSMS/HttpGateway/GetCarriers.aspx?";
		$qry .= "user=".urlencode($user);
		$qry .= "&password=".urlencode($password);
		return $this->dsQuery($qry);
	}

	public function SendMessage($user, $password, $param)
	{
		unset($param["data"]);
		unset($param["dsobjname"]);
		unset($param["start"]);
		if (isset($param["AdCs"]))
		{
			$dest= explode("\n", $param["AdCs"]);
			for($i = 0; $i < count($dest); $i++)
			{
				$dest[$i] = trim($dest[$i]);
				$dest[$i] = str_replace(" ", "", $dest[$i]);
				if (substr($dest[$i], 0, 1) != "+") $dest[$i] = $this->property["prefix"].$dest[$i];
				$dest[$i] = urlencode($dest[$i]);
			}
			$param["AdCs"] = implode(";", $dest);
		} 
		$qry  = "http://151.1.233.1/ProgettoSMS/HttpGateway/SendMessage.aspx?";
		$qry .= "user=".urlencode($user);
		$qry .= "&password=".urlencode($password);
		if (isset($param["OAdC"])) $qry .= "&OAdC=".urlencode(trim($param["OAdC"]));
		if (isset($param["AdCs"])) $qry .= "&AdCs=".urlencode(trim($param["AdCs"]));
		if (isset($param["Message"])) $qry .= "&Message=".urlencode(trim($param["Message"]));
		if (isset($param["DDT"])) $qry .= "&DDT=".urlencode(trim($param["DDT"]));
		if ($this->property["verbose"] == 1) $qry .= "&Verbose=1";
		if (!empty($this->property["gateway"])) $qry .= "&Gateway=".$this->property["gateway"];
		if (!empty($this->property["carriergateways"])) $qry .= "&CarrierGateways=".$this->property["carriergateways"];
		if (!empty($this->property["answerrecipients"])) $qry .= "&AnswerRecipients=".$this->property["answerrecipients"];
		return $this->dsQuery($qry);
	}

	public function dsQuery($qry)
	{
		global $system;
		if($this->property["debug"]=="true") $system->debug($this->property["id"], "Request: ".$qry);

		$this->property["conn"] = fopen($qry, "rb");
		$result = fread($this->property["conn"],1000);
		fclose($this->property["conn"]);
		return $result;
	}

	function txt2result($txt, $insertkey = false)
	{
		$txt = split("\n", $txt);
		$lastkey = "";
		$lastvalue = "";
		$txt[0] = "Status=".$txt[0];
		foreach($txt as $row)
		{
			$arr = explode("=", $row);
			if(count($arr) == 2)
			{ 
				$lastkey = trim($arr[0]);
				$lastvalue = trim($arr[1]);
			}
			else if ($arr[0] != ".") $lastvalue .= $arr[0];
			$this->property["result"][0][$lastkey] = $lastvalue;
		}
		if ($insertkey)
		{
			$this->property["result"][0]["key"] = time();
			$this->property["result"][0]["keyname"] = "key";
			$this->property["result"][0]["keynamevalue"] = $this->property["result"][0]["key"];
		}
	}

	private function pushresult()
	{
		$this->store = $this->property["result"];
	}

	private function popresult()
	{
		if ($this->store == "") return true;
		else
		{
			$this->property["result"] = $this->store;
			$this->store = "";
			return false;
		}
	}

	/**
	* Executes a select query
	* @param string $qry Query
	*/
	public function dsQuerySelect($qry = null)
	{
		$this->property["poscount"] = 0;	
		if ($this->popresult())
		{
			if(empty($qry))
			{
				$info = isset($_POST["info"]) ? $_POST["info"] : "GetUserStatus";
				switch($info)
				{
					case "GetMessageStatus":
					default: 
						$result = $this->GetMessageStatus($this->property["dsuser"], $this->property["dspwd"], $_POST["MessageID"]);
					break;

					case "GetGateways":
						$result = $this->GetGateways($this->property["dsuser"], $this->property["dspwd"]);
					break;

					case "GetCarriers":
						$result = $this->GetCarriers($this->property["dsuser"], $this->property["dspwd"]);
					break;

					case "GetUserStatus":
					default: 
						$result = $this->GetUserStatus($this->property["dsuser"], $this->property["dspwd"]);
					break;
				}
			}
			else $result = $this->dsQuery($qry);
			$this->txt2result($result, true);
		}
		$this->property["tot"] = 1;	
	}

	/**
	* Executes a update query
	* @param string $qry Query
	*/
	public function dsQueryUpdate($qry = null)
	{
		$this->dsQueryInsert($qry);
	}

	/**
	* Executes a truncate query
	* @param string $qry Query
	*/
	public function dsQueryDeleteAll($qry = null)
	{
		$this->dsQueryInsert($qry);
	}

	/**
	* Executes a delete query
	* @param string $qry Query
	*/
	public function dsQueryDelete($qry = null)
	{
		$this->dsQueryInsert($qry);
	}
	
	/**
	* Executes a insert query
	* @param string $qry Query
	*/
	public function dsQueryInsert($qry = null)
	{
		$this->property["poscount"] = 0;	
		if(empty($qry)) $result = $this->SendMessage($this->property["dsuser"], $this->property["dspwd"], $_POST);
		else $result = $this->dsQuery($qry);
		$this->txt2result($result, true);
		$this->property["tot"] = 1;	
		$this->pushresult();
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
