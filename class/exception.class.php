<?php
/**
* Class management handling exceptions
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsException
{
	public $param = array();

	/**
	* Construct
	*/
	public function __construct() 
	{
		set_exception_handler(array($this, 'ExceptionHandler'));
		set_error_handler(array($this, 'ErrorHandler'));
	}

	public function ExceptionHandler($e)
	{
		$this->param["errno"] = get_class($e);
		$this->param["message"] = $e->getMessage();
		$this->param["errfile"] = $e->getFile();
		$this->param["errline"] = $e->getLine();
		$this->param["title"] = $this->param["errno"];
		$return = userEvent::call("before_exception_error", $this);
		if (is_null($return) || ($return==true))
		{
			global $system;
			$time = (NOCACHECSS) ? "?".NOCACHECSS : "";
			print "<html>";
			print "\n<head>";
			print "\n\t<link rel=\"stylesheet\" type=\"text/css\"  href=\"".$system->dir_web_jamp.$system->dir_template."objcss/page/page.css".$time."\">";
			print "\n</head>";
			print "\n<body onload=\"javascript: SizeError();\">";
			print "\n<div style=\"display: block;-moz-opacity: 0.8;filter:alpha(opacity: 80);opacity: 0.8;\" id=\"pageMessageBack\"></div>";
			print "\n<div style=\"display: block;-moz-opacity: 1;filter:alpha(opacity: 100);opacity: 1;\" id=\"pageMessage\">";
			print "\n<h2 class=\"dialog_title\"><span>".$this->param["title"]."</span></h2>";
			print "\n<div id=\"pageMessageContent\" class=\"dialog_content\">";
			print "\n<div class=\"dialog_body\">";
			print "\n<div class=\"clearfix\">";
			print "\n<div class=\"dialog_content_img\"></div>";
			print "\n<div class=\"dialog_content_txt\"><b>Pagina: </b>".$this->param["errfile"]."\n";
			print "\n<div><br><b>Riga:</b> ".$this->param["errline"]."</div>";
			print "\n<div style=\"margin-top: 10px;\">".$this->param["message"]."</div>";
			print "\n</div></div>";
			if (ERROR_LEVEL == 1)
			{
				print "\n<b>Backtrace: </b>\n";
				print "<table class=\"dialog_backtrace\" width=\"100%\">";
				print "<tr><td><b>Line</b></td><td><b>Class</b></td><td><b>Function</b></td><td><b>Type</b></td><td><b>Args</b></td><td><b>File</b></td></tr>";
				foreach (debug_backtrace() as $row)
				{
					$line = isset($row["line"]) ? $row["line"] : "-";
					$class = isset($row["class"]) ? $row["class"] : "-";
					$function = isset($row["function"]) ? $row["function"] : "-";
					$type = isset($row["type"]) ? $row["type"] : "-";
					$file = isset($row["file"]) ? $row["file"] : "-";
					print "<tr>";
					print "<td valign=\"top\">$line</td>";
					print "<td valign=\"top\">$class</td>";
					print "<td valign=\"top\">$function</td>";
					print "<td valign=\"top\">$type</td>";
					print "<td valign=\"top\">";
					var_dump($row["args"]);
					print "</td>";
					print "<td valign=\"top\">$file</td>";
					print "<tr>";
				}
				print "</table>";
			}
			print "\n</div></div>";
			print "\n<div class=\"dialog_buttons\">";
			print "\n<input onclick=\"document.getElementById('pageMessageBack').style.display='none'; document.getElementById('pageMessage').style.display='none';\" value=\"Chiudi\" type=\"button\">";
			print "\n</div></div>";

			print "\n<script type=\"text/javascript\" language=\"JavaScript1.5\">";
			print "\nfunction SizeError(){";
			print "\nvar obj = document.getElementById('pageMessageContent');";
			print "\nobj.style.height = (obj.offsetParent.offsetHeight - 64)+ 'px';";
			print "\n}";
			print "\nif(window.attachEvent) window.attachEvent('onresize',SizeError);";
			print "\nelse window.addEventListener('resize',SizeError,false);";
			print "\n</script>";
			print "\n</body>";
			print "\n</html>";
		}
		$return = userEvent::call("after_exception_error", $this);
	}

	/**
	* Error handling
	* @param string $errno Error number
	* @param string $errstr Error description
	* @param string $errfile File that generated the error
	* @param string $errline Error line
	*/
	public function ErrorHandler($errno, $errstr, $errfile, $errline)
	{	
		$error = array (
				E_ERROR     		=> 'ERROR',
				E_WARNING			=> 'WARNING',
				E_NOTICE			=> 'NOTICE',
				E_PARSE          	=> 'PARSING ERROR',
				E_CORE_ERROR     	=> 'CORE ERROR',
				E_CORE_WARNING   	=> 'CORE WARNING ERROR',
				E_COMPILE_ERROR  	=> 'COMPILE ERROR',
				E_COMPILE_WARNING 	=> 'COMPILE WARNING ERROR',
				E_USER_ERROR     	=> 'USER ERROR',
				E_USER_WARNING   	=> 'USER WARNING ERROR',
				E_USER_NOTICE    	=> 'USER NOTICE ERROR',
				E_STRICT			=> 'STRICT NOTICE ERROR');
		$this->param["errno"] = $errno;
		$this->param["message"] = $errstr;
		$this->param["errfile"] = $errfile;
		$this->param["errline"] = $errline;
		$this->param["title"] = (isset($error[$errno])) ? $error[$errno] : $errno;
		$return = userEvent::call("before_exception_error", $this);
		if (is_null($return) || ($return==true))
		{
			global $system;
			$time = (NOCACHECSS) ? "?".NOCACHECSS : "";
			print "<html>";
			print "\n<head>";
			print "\n\t<link rel=\"stylesheet\" type=\"text/css\"  href=\"".$system->dir_web_jamp.$system->dir_template."objcss/default/page.css".$time."\">";
			print "\n</head>";
			print "\n<body onload=\"javascript: SizeError();\">";
			print "\n<div style=\"display: block;-moz-opacity: 0.8;filter:alpha(opacity: 80);opacity: 0.8;\" id=\"pageMessageBack\"></div>";
			print "\n<div style=\"display: block;-moz-opacity: 1;filter:alpha(opacity: 100);opacity: 1;\" id=\"pageMessage\">";
			print "\n<h2 class=\"dialog_title\"><span>".$this->param["title"]."</span></h2>";
			print "\n<div id=\"pageMessageContent\" class=\"dialog_content\">";
			print "\n<div class=\"dialog_body\">";
			print "\n<div class=\"clearfix\">";
			print "\n<div class=\"dialog_content_img\"></div>";
			print "\n<div class=\"dialog_content_txt\"><b>Pagina: </b>".$this->param["errfile"]."\n";
			print "\n<div><br><b>Riga:</b> ".$this->param["errline"]."</div>";
			print "\n<div style=\"margin-top: 10px;\">".$this->param["message"]."</div>";
			print "\n</div></div>";
			if (ERROR_LEVEL == 1)
			{
				print "\n<b>Backtrace: </b>\n";
				print "<table class=\"dialog_backtrace\" width=\"100%\">";
				print "<tr><td><b>Line</b></td><td><b>Class</b></td><td><b>Function</b></td><td><b>Type</b></td><td><b>Args</b></td><td><b>File</b></td></tr>";
				foreach (debug_backtrace() as $row)
				{
					$line = isset($row["line"]) ? $row["line"] : "-";
					$class = isset($row["class"]) ? $row["class"] : "-";
					$function = isset($row["function"]) ? $row["function"] : "-";
					$type = isset($row["type"]) ? $row["type"] : "-";
					$file = isset($row["file"]) ? $row["file"] : "-";
					print "<tr>";
					print "<td valign=\"top\">$line</td>";
					print "<td valign=\"top\">$class</td>";
					print "<td valign=\"top\">$function</td>";
					print "<td valign=\"top\">$type</td>";
					print "<td valign=\"top\">";
					if (isset($row["args"])) var_dump($row["args"]);
					print "</td>";
					print "<td valign=\"top\">$file</td>";
					print "<tr>";
				}
				print "</table>";
			}
			print "\n</div></div>";
			print "\n<div class=\"dialog_buttons\">";
			print "\n<input onclick=\"document.getElementById('pageMessageBack').style.display='none'; document.getElementById('pageMessage').style.display='none';\" value=\"Chiudi\" type=\"button\">";
			print "\n</div></div>";

			print "\n<script type=\"text/javascript\" language=\"JavaScript1.5\">";
			print "\nfunction SizeError(){";
			print "\nvar obj = document.getElementById('pageMessageContent');";
			print "\nobj.style.height = (obj.offsetParent.offsetHeight - 64)+ 'px';";
			print "\n}";
			print "\nif(window.attachEvent) window.attachEvent('onresize',SizeError);";
			print "\nelse window.addEventListener('resize',SizeError,false);";
			print "\n</script>";
			print "\n</body>";
			print "\n</html>";
		}
		$return = userEvent::call("after_exception_error", $this);
		if (isset($error[$errno]) && (is_null($return) || ($return==true))) die();
	}
}
?>