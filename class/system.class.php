<?php
/**
* Class ClsSystem
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsSystem 
{
	public $dir_real_web;	
	public $dir_real_jamp;	
	public $dir_web_jamp;	
	public $dir_class;	
	public $dir_conf;
	public $dir_develop;
	public $dir_doc;
	public $dir_js;
	public $dir_frm;
	public $dir_obj;
	public $dir_plugin;
	public $dir_template;
	public $dir_tmp;
	public $filedebug;
	public $compressjs;

	/**
	* constructor
	*/
	public function __construct($loadConfig = false)
	{
		$this->dir_real_web  = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF'])));
		$this->dir_real_jamp = realpath(dirname(__FILE__).'/../');
		$this->dir_web_jamp  = str_replace('\\','/', substr($this->dir_real_jamp, strlen($this->dir_real_web)))."/";
		$this->version	= "3.0.0_beta";	
		$this->dir_class	= "class/";	
		$this->dir_conf	= "conf/";
		$this->dir_develop = "develop/";
		$this->dir_doc		= "doc/";
		$this->dir_js		= "js/";
		$this->dir_frm		= "frm/";
		$this->dir_obj		= "obj/";
		$this->dir_plugin	= "plugin/";
		$this->dir_template	= "template/";
		$this->dir_tmp	 	= "tmp/";
		if ($loadConfig) $this->loadConfig();
	}

	/**
	* Load system configuration
	*/
	public function loadConfig()
	{
		require_once($this->dir_real_jamp."/class/lang.class.php");
		if (defined('_JAMP_SETTING_')) {
			require_once(_JAMP_SETTING_);
		} else {
			require_once($this->dir_real_jamp."/conf/setting.inc.php");
		}
		$this->compressjs = COMPRESSJS;
		require_once($this->dir_real_jamp."/".$this->dir_plugin.'packer-php/class.JavaScriptPacker.php');
		error_reporting(ERROR_REPORTING);
		date_default_timezone_set(TIMEZONE);
		require_once($this->dir_real_jamp."/class/exception.class.php");
		require_once($this->dir_real_jamp."/class/error.class.php");
		require_once($this->dir_real_jamp."/class/object.class.php");
		require_once($this->dir_real_jamp."/class/xml.class.php");
		require_once($this->dir_real_jamp."/class/userevent.class.php");
		require_once($this->dir_real_jamp."/class/event.class.php");
		LANG::$language = LANGUAGE;
		new ClsException();
		session_cache_limiter('private'); 
		if (!isset($_SESSION)) session_start();
	}

	/** 
	* Debug on file
	* @return string debug date
	*/
	public function debug($id, $log)
	{
		if(!isset($this->filedebug)) $this->filedebug = array();
		$this->filedebug[] = "<font color=\"green\">".@date("H:i:s d/m/Y")."<\/font> - <b>[$id]<\/b> ".$log;
	}
	
	/** 
	* Send date to Debug console
	* @return string debug date
	*/
	public function sendDebug()
	{
		$out = "\n";
		foreach($this->filedebug as $row)
		{
			$row = str_replace("\r\n","",$row);
			$row = str_replace("\t"," ",$row);
			$row = str_replace("\n","",$row);
			$row = str_replace("\'","'",$row);
			$row = str_replace("'","\'",$row);
			if(!empty($row)) $out .= "\n\t\twindow.consoleJAMP.log('".$row."');";
		}
		unset($this->filedebug);
		return $out;
	}

	/** 
	* Include link to JS files
	* @param array original path of file
	* @return string path js file
	*/
	public function getPathJS($filejs, $time = "")
	{
		if (strpos($filejs, "://")===false) return $this->dir_web_jamp.$this->dir_js.$filejs.$time;
		return $filejs;
	}

	/** 
	* Include link to CSS files
	* @param array original path of file
	* @return string path css file
	*/
	public function getPathCSS($filecss, $time = "")
	{
		if (strpos($filecss, ".menu")===false && strpos($filecss, "://")===false) return $this->dir_web_jamp.$this->dir_template.$filecss.$time;
		return $filecss;
	}

	/** 
	* Include link to CSS files
	* @param array names of files
	* @return string code html
	*/
	public function getCSS($filescss)
	{
		$out = "";
		$time = (NOCACHECSS) ? "?".NOCACHECSS : "";
		$filescss = array_unique($filescss);
		foreach($filescss as $filecss)
		{	
			if($filecss != "") $out .="\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".$this->getPathCSS($filecss, $time)."\">";
		}
		return $out;
	}

	/** 
	* Include link to javascript files
	* @param array $scripts names of files
	* @return string code html
	*/
	public function getJs($scripts)
	{
		$out = "";
		$time = (NOCACHEJS) ? "?".NOCACHEJS : "";
		$scripts = array_unique($scripts);
		foreach($scripts as $script)
		{	
			if($script != "") 
			{
				if (strpos($script, ".menu")===false && strpos($script, "://")===false) 
				{
					$out .="\n\t<script type=\"text/javascript\" language=\"JavaScript1.5\" src=\"".$this->dir_web_jamp.$this->dir_js.$script.$time."\"></script>";
				}
				else $out .="\n\t<script type=\"text/javascript\" language=\"JavaScript1.5\" src=\"".$script."\"></script>";
			}
		}
		return $out;
	}

	/**
	* Insert tag <script> and javascript code
	* @param string $code
	* @return string code html
	*/
	public function setJs($code)
	{
		if (empty($code)) return;
		$code = $this->packJS($code);
		return "\n\t<script type=\"text/javascript\" language=\"JavaScript1.5\">$code</script>";
	}

	/**
	* Instantiate an object
	* @param string $id Unique name given to object
	* @param string $classname class name
	*/
	public function newObj($id, $classname)
	{
		if(empty($classname)) ClsError::showError("OBJ003");
		if(!include_once($this->dir_real_jamp."/".$this->dir_obj."$classname.obj.php")) ClsError::showError("OBJ004",$classname);
		$classname = "ClsObj_".$classname;
		$obj = new $classname($id);
		return $obj;
	}

	/**
	* Istanzia ds object
	* @param string $ds Name ds class name
	*/
	public function newDs($ds)
	{
		require_once($this->dir_real_jamp."/".$this->dir_class."/ds.class.php");
		require_once($this->dir_real_jamp."/".$this->dir_class."/ds.$ds.php");
		$clsds = $ds."Ds";
		return new $clsds;
	}

	/**
	* Create a new datasource
	* @param string $id Object Name
	* @param string $conn Connection Name	
	*/
	public function newConnect($id, $conn)
	{
		$ds = $this->newObj($id, "ds");
		$ds->setProperty("conn", $conn);
		$ds->ds->dsConnect();
		return $ds;
	}
		
	/**
	* Enable the use of plugins
	* @param name
	*/
	public function loadclass($name)
	{
		require_once($this->dir_real_jamp."/class/$name.class.php");
	}

	/**
	* Enable the use of plugins
	* @param name
	*/
	public function plugin($name)
	{
		switch($name)
		{
			case "pdf": //FPDF
				define('FPDF_FONTPATH', $this->dir_real_jamp."/".$this->dir_plugin.'fpdf'.JAMP_FPDF_VERSION.'/font/');
				require_once($this->dir_real_jamp."/".$this->dir_plugin.'fpdf'.JAMP_FPDF_VERSION.'/fpdf.php');
				require_once($this->dir_real_jamp."/class/pdf.class.php");
			break;

			case "excel": //Excel
				require_once($this->dir_real_jamp."/".$this->dir_plugin.'excel/excel.class.php');
			break;
		}	
	}

	/**
	* Returns all the objects instantiated
	* @return $array
	*/
	public function allObj()
	{
		return scandir($this->dir_real_jamp."/".$this->dir_obj);
	}

	/**
	* set the custom event
	* @param string $event event name
	* @return string code html
	*/
	public function setEvent($event){
		$out = "";
		foreach($event as $k => $e)
		{
			if((isset($e["before"])) || (isset($e["function"])) || (isset($e["after"])))
			{ 
				if(empty($k)) $out .= "\n\t\t/* FREE CODE ON LOAD */";
				else
				{
					$param = "";
					if(!empty($e["param"])) $param = is_array($e["param"]) ? $e["param"][0] : $e["param"];
					$out .= "\n\t\tfunction $k($param)\n\t\t{";
				}
				if(isset($e["before"]))
				{
					$out .= "\n\t\t\t/* Before */";
					foreach($e["before"] as $fnz) $out .= "\n\t\t\t".$fnz;			
				}
				if(isset($e["function"])){
					$out .= "\n\t\t\t/* Event */";
					foreach($e["function"] as $fnz) $out .= "\n\t\t\t".$fnz;			
				}
				if(isset($e["after"]))
				{
					$out .= "\n\t\t\t/* After */";
					foreach($e["after"] as $fnz) $out .= "\n\t\t\t".$fnz;			
				}
				if(empty($k))	$out .= "\n\t\t/* END FREE CODE ON LOAD */";
				else $out .= "\n\t\t}";
				$found_event = true;
			}
			if(isset($e["event"]))
			{
				$out .= "\n\t\t/* Listener */";
				$e["event"] = array_unique($e["event"]); 
				foreach($e["event"] as $evn) $out .= "\n\t\t$evn";
			}
			$out .= "\n\n\t";
		}
		$out = $this->packJS($out);
		return "\n\t<script type=\"text/javascript\" language=\"JavaScript1.5\">$out</script>";
	}

	/**
		Convert RGB color in HEX
		* @param string $color RGB color
		* @return string $col HEX color
	*/
	public function HEXtoRGB($color)
	{
		$red   = 100;
		$green = 100;
		$blue  = 100;
		$col=array();
		if(preg_match("/[#]?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i", $color, $ret))
		{
			$red = hexdec( $ret[1] );
			$green = hexdec( $ret[2] );
			$blue = hexdec( $ret[3] );
		}
		$col["R"]=$red;
		$col["G"]=$green;
		$col["B"]=$blue;
		return($col);
	}

	public function packJS($code) 
	{
		if (!$this->compressjs) return $code;
		$packer = new JavaScriptPacker($code, 'Normal', true, false);
		return $packer->pack();
	}
}
?>
