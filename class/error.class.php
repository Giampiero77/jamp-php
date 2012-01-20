<?php
/**
* Class management handling errors
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsError
{
	public static $param = array();

	/**
	* Displays the error page
	* @param integer $e Error code
	* @param integer $obj Object that generated the error
	* @param integer $extra Additional information about
	*/
	public static function showError($e, $obj = null, $extra = null)
	{
		global $system;
		$return = true;
		self::$param = array();
		self::$param["errno"] = $e;
		self::$param["obj"]   = $obj;
		self::$param["extra"] = $extra;
		self::$param['message'] = "";
		self::$param['title'] = "Errore: $e";

		switch ($e)
		{
			case "DS003": 
				self::$param['message'] .= LANG::translate($e, array($extra, mysql_errno($obj), mysql_error($obj)));
			break;
			default:
				self::$param['message'] .= LANG::translate($e, array($obj, $extra));
			break;
		}
		$return = userEvent::call("before_error");
		if (is_null($return) || ($return==true))
		{
			if(!isset($_POST["data"]))
			{
				$time = (NOCACHECSS) ? "?".NOCACHECSS : "";
				print "<html>";
				print "\n<head>";
				print "\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".$system->dir_web_jamp.$system->dir_template."objcss/default/page.css".$time."\">";
				print "\n</head>";
				print "\n<body onload=\"javascript: SizeError();\">";

				print "\n<div style=\"display: block;-moz-opacity: 0.8;filter:alpha(opacity: 80);opacity: 0.8;\" id=\"pageMessageBack\"></div>";
				print "\n<div style=\"display: block;-moz-opacity: 1;filter:alpha(opacity: 100);opacity: 1;\" id=\"pageMessage\">";
				print "\n<h2 class=\"dialog_title\"><span>".self::$param['title']."</span></h2>";
				print "\n<div id=\"pageMessageContent\" class=\"dialog_content\">";
				print "\n<div class=\"dialog_body\">";
				print "\n<div class=\"clearfix\">";
				print "\n<div class=\"dialog_content_img\"></div>";
				print "\n<div class=\"dialog_content_txt\">\n";
				print "\n<div style=\"margin-top: 10px;\">".self::$param['message']."</div>";
				print "\n</div></div>";
				if(ERROR_LEVEL == 1)
				{
					print "\n<b>Backtrace: </b>\n";
					print "<table class=\"dialog_backtrace\" width=\"100%\">";
					print "<tr><td><b>Line</b></td><td><b>Class</b></td><td><b>Function</b></td><td><b>Type</b></td><td><b>Args</b></td><td><b>File</b></td></tr>";
					foreach(debug_backtrace() as $row)
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
				$return = true;
			}
			else
			{
				print "\n".self::$param['message']."<br>";
				if(ERROR_LEVEL == 1)
				{
					print "<pre>";
					debug_print_backtrace();
					print "</pre>";
				}
				print "\n<br>";
			}
		}
		$return = userEvent::call("after_error");
		if (is_null($return) || ($return==true)) die();
	}
}


?>
