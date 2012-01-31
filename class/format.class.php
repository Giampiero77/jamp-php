<?php
/**
* Class FORMAT
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/
class ClsFormat
{
  	private $formatData = array();
	private $intSetting = array();
	
	/**
	* Construct
	*/
	public function __construct()
	{
		$this->field = array();
		$this->intSetting['EN']['month'] = array('January','February','March','April','May','June','July','August','September','October','November','December');
		$this->intSetting['EN']['mon'] = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$this->intSetting['EN']['weekday'] = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
		$this->intSetting['EN']['day'] = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
		$this->intSetting['EN']['weekstart'] = 0;

		$this->intSetting['IT']['month'] = array('Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre');
		$this->intSetting['IT']['mon'] = array('Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic');
		$this->intSetting['IT']['weekday'] = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');
		$this->intSetting['IT']['day'] = array('Dom','Lun','Mar','Mer','Gio','Ven','Sab');
		$this->intSetting['IT']['weekstart'] = 1;

		$this->intSetting['IT']['thousand'] = ".";
		$this->intSetting['IT']['decimal']  = ",";

		$this->intSetting['EN']['thousand'] = ",";
		$this->intSetting['EN']['decimal']  = ".";
	}

	/**
	* Report the value in the original formatting
	* @param string $text Value
	* @param string $format Formatting
	* @return string Formatted string
	*/   
	public function unFormat($text, $format)
	{
		$format = explode("|", $format);
		if ($format[0]!="string") 
		{
			$temp = $format[3]; $format[3] = $format[1]; $format[1] = $temp;
			$temp = $format[2]; $format[2] = $format[4]; $format[4] = $temp;
		}
		if ($format[0]=="date") return $this->formatDate($text, $format);
		else if ($format[0]=="number") return $this->formatNumber($text, $format);
		else return $this->formatString($text, $format);
	}

	/**
	* Format value
	* @param string $text Value
	* @param string $format Formatting
	* @return string Formatted string
	*/
	public function Format($text, $format)
	{
		$format = explode("|", $format);
		if ($format[0] == "date") return $this->formatDate($text, $format, false);
		else if ($format[0] == "datefixed") return $this->formatDate($text, $format, true);
		else if ($format[0] == "number") return $this->formatNumber($text, $format);
		else return $this->formatString($text, $format);
	}

	private function LZ($x) {return ($x<0||$x>9?"":"0").$x; }

	private function formatDate($date, $reg, $fixed)
	{
		$i_format=0;
		$format=$reg[4];
		$result = $c = $token= "";
		$timestamp = $this->getDateFromFormat($date, $reg);
		if ($timestamp==0) 
		{
			if (!$fixed) return "";
			else 
			{
				$value["yyyy"] = "0000";
				$value["yy"] = "00";
				$value["m"] = "0";
				$value["mm"] = "00";
				$value["mmmm"] = "";
				$value["mmm"] = "";
				$value["d"]="0";
				$value["dd"]="00";
				$value["ddd"]="";
				$value["dddd"]="";
				$value["H"]="0";
				$value["HH"]="00";
				$value["h"]="0";
				$value["hh"]="00";
				$value["K"]="0";
				$value["k"]="0";
				$value["KK"]="00";
				$value["kk"]="00";
				$value["a"]="";
				$value["i"]="";
				$value["ii"]="00";
				$value["s"]="0";
				$value["ss"]="00";
			}
		}
		else 
		{
			if ($format == "TIMESTAMP") return (string)$timestamp;
			$date = getdate($this->getDateFromFormat($date, $reg));
			$y = $date["year"];
			$M = $date['mon'];
			$d = $date['mday'];
			$E = $date['wday'];
			$H = $date['hours'];
			$i = $date['minutes'];
			$s = $date['seconds'];
			if (strlen($y) < 4) {$y=$y+1900;}
			$value["yyyy"] = $y;
			$value["yy"] = substr($y,2,2);
			$value["m"] = $M;
			$value["mm"] = $this->LZ($M);
			$value["mmmm"]=$this->intSetting[$reg[3]]['month'][$M-1];
			$value["mmm"]=$this->intSetting[$reg[3]]['mon'][$M-1];
			$value["d"]=$d;
			$value["dd"]=$this->LZ($d);
			$value["ddd"]=$this->intSetting[$reg[3]]['day'][$E];
			$value["dddd"]=$this->intSetting[$reg[3]]['weekday'][$E];
			$value["H"]=$H;
			$value["HH"]=$this->LZ($H);
			if ($H==0) $value["h"]=12;
			else if ($H>12) $value["h"]=$H-12;
			else $value["h"]=$H;
			$value["hh"]=$this->LZ($value["h"]);
			if ($H>11) $value["K"]=$H-12;
			else $value["K"]=$H;
			$value["k"]=$H+1;
			$value["KK"]=$this->LZ($value["K"]);
			$value["kk"]=$this->LZ($value["k"]);
			if ($H > 11) $value["a"]="PM";
			else { $value["a"]="AM"; }
			$value["i"]=$i;
			$value["ii"]=$this->LZ($i);
			$value["s"]=$s;
			$value["ss"]=$this->LZ($s);
		}
		while ($i_format < strlen($format)) 
		{
			$c = $format[$i_format];
			$token="";
			while (($i_format < strlen($format) && ($format[$i_format]==$c))) $token .= $format[$i_format++];
			if (isset($value[$token])) $result = $result.$value[$token];
			else $result = $result.$token;
		}
		return $result;
	}

	private function _getInt($str, $i, $minlength, $maxlength) 
	{		
		for ($x=$maxlength; $x>=$minlength; $x--) 
		{
			$token = substr($str, $i, $x);
			if (strlen($token) < $minlength) { return null; }
			if (is_numeric($token)) { return $token; }
		}
		return null;
	}

	private function getValue($val, $start, $arr)
	{
		for ($i=0; $i<count($arr); $i++) 
		{
			if (strtolower(substr($val, $start, strlen($val)))==strtolower($arr[$i])) return $i;
		}
		return 0;
	}

	private function getDateFromFormat($val, $reg)
	{	
		if ($reg[2] == "TIMESTAMP") return (int)$val;	
		$format=$reg[2];
		$i_val=$i_format=0;
		$c=$token=$token2=$ampm="";
		$day = 1;
		$now = getdate();
		$year = $now["year"];
		$month = $now['mon'];
		$hh = $now['hours'];
		$ii = $now['minutes'];
		$ss = $now['seconds'];
		while ($i_format < strlen($format)) 
		{
			$c = $format[$i_format];
			$token="";
 			while (($i_format < strlen($format)) && ($format[$i_format]==$c)) $token .= $format[$i_format++]; 
			if ($token=="yyyy" || $token=="yy") 
 			{ 
 				if ($token=="yyyy") { $x=4;$y=4; }
 				if ($token=="yy")   { $x=2;$y=2; }
 				$year=$this->_getInt($val, $i_val, $x, $y);
 				if (!isset($year)) return 0;
 				$i_val += strlen($year);
 				if (strlen($year)==2) 
 				{
 					if ($year > 70) $year=1900+($year-0);
 					else $year=2000+($year-0);
 				}
 			}
 			else if ($token=="mmmm")
 			{
				$month = $this->getValue($val, $i_val, $this->intSetting[$reg[1]]['month'])+1;
				$i_val += strlen($this->intSetting[$reg[1]]['month'][$month-1]);
			}
 			else if ($token=="mmm")
 			{
				$month = $this->getValue($val, $i_val, $this->intSetting[$reg[1]]['mon'])+1;
				$i_val += strlen($this->intSetting[$reg[1]]['mon'][$month-1]);
 			}
 			else if ($token=="mm"|| $token=="m") 
 			{
 				$month = $this->_getInt($val,$i_val,strlen($token),2);
 				if(!isset($month)||($month<1)||($month>12)) return 0;
 				$i_val+=strlen($month);
 			}
 			else if ($token=="dddd")
 			{
				$day = $this->getValue($val, $i_val, $this->intSetting[$reg[1]]['weekday']);
				$i_val += strlen($this->intSetting[$reg[1]]['weekday'][$day]);
 			}
 			else if ($token=="ddd")
 			{
				$day = $this->getValue($val, $i_val, $this->intSetting[$reg[1]]['day']);
				$i_val += strlen($this->intSetting[$reg[1]]['day'][$day]);
 			}
 			else if ($token=="dd" || $token=="d") 
 			{
 				$day=$this->_getInt($val,$i_val, strlen($token), 2);
 				if (!isset($day)||($day<1)||($day>31)) return 0;
 				$i_val+=strlen($day);
 			}
 			else if ($token=="hh" || $token=="h") 
 			{
 				$hh=$this->_getInt($val,$i_val,strlen($token),2);
 				if(!isset($hh)||($hh<1)||($hh>12)) return 0;
 				$i_val+=strlen($hh);
 			}
 			else if ($token=="HH" || $token=="H") 
 			{
 				$hh=$this->_getInt($val,$i_val,strlen($token),2);
 				if(!isset($hh)||($hh<0)||($hh>23)) return 0;
 				$i_val+=strlen($hh);
 			}
 			else if ($token=="KK" || $token=="K") 
 			{
 				$hh=$this->_getInt($val,$i_val,strlen($token),2);
				if(!isset($hh)||($hh<0)||($hh>11)) return 0;
 				$i_val+=strlen($hh);
 			}
 			else if ($token=="kk"|| $token=="k") 
			{
 				$hh=$this->_getInt($val,$i_val,strlen(token),2);
 				if(!isset($hh)||($hh<1)||($hh>24)) return 0;
 				$i_val+=strlen($hh);$hh--;
 			}
 			else if ($token=="ii" || $token=="i") 
 			{
 				$ii=$this->_getInt($val,$i_val,strlen($token),2);
 				if(!isset($ii)||($ii<0)||($ii>59)) return 0;
 				$i_val+=strlen($ii);
 			}
 			else if ($token=="ss" || $token=="s") 
 			{
 				$ss=$this->_getInt($val,$i_val,strlen($token),2);
 				if(!isset($ss)||($ss<0)||($ss>59)) return 0;
 				$i_val+=strlen($ss);
 			}
 			else if ($token=="a") 
 			{
 				if (strtolower(substr($val,$i_val,2))=="am") $ampm="AM";
 				else if (strtolower(substr($val,$i_val,2))=="pm") $ampm="PM";
 				else return 0;
 				$i_val+=2;
 			}
 			else 
 			{
 				if (substr($val, $i_val, strlen($token)) != $token) return 0;
 				else $i_val+=strlen($token);
 			}
		}
		if ($i_val != strlen($val)) return 0;
		if ($month==2 && $day>28) 
		{
			 if  ((($year%4==0) && ($year%100 != 0)) || ($year%400==0))
			 { 
				  if (day > 29) return 0;
			 }
			else return 0;
		}
		if ((($month==4)||($month==6)||($month==9)||($month==11)) && ($day > 30)) return 0;
		if ($hh<12 && $ampm=="PM") $hh=$hh-0+12;
		else if ($hh>11 && $ampm=="AM") $hh-=12;
		return mktime($hh, $ii, $ss, $month, $day, $year);
	}

	/**
	* Formatting numbers
	* @param string $text Value
	* @param string $format Formatting
	* @return string Formatted string
	*/
	private function formatNumber($text, $format)
	{
		$decimal = (($pos = strpos($format[4], $this->intSetting[$format[3]]['decimal'])) !== false) ? strlen($format[4])-$pos-1 : 0;
		$thousands = strlen(preg_replace("/[.,]/", "", $format[4])) - $decimal;
		$text = str_replace($this->intSetting[$format[1]]['thousand'], "", $text);
		$text = str_replace($this->intSetting[$format[1]]['decimal'], $this->intSetting['EN']['decimal'], $text);
		$outsepthousands = strpos($format[4], $this->intSetting[$format[3]]['thousand'])!==false ? $this->intSetting[$format[3]]['thousand'] : "";
		if ($text=="") $text = 0;
		$text = number_format($text, $decimal, $this->intSetting[$format[3]]['decimal'], $outsepthousands);
		$outthousands = strlen(preg_replace("/[.,]/", "", $text)) - $decimal;
		for ($y=$outthousands; $y<$thousands; $y++) $text = "0".$text;
		return $text;
	}

	/**
	* Formatting strings
	* @param string $text Value
	* @param string $format Formatting
	* @return string Formatted string
	*/
	private function formatString($text, $format)
	{
		if (strpos($format[1],"trim") !== false) $text = trim($text);
		if (strpos($format[1],"upper") !== false) $text = strtoupper($text);
		if (strpos($format[1],"lower") !== false) $text = strtolower($text);
		$fixed = count(explode("@", $format[1]))-1;
		if ($fixed > 0) 
		{
			$reverse = (strpos($format[1],"!") !== false) ? STR_PAD_LEFT : STR_PAD_RIGHT;
			$length = strlen($text);				
			if ($fixed > $length) $text = str_pad($text, $fixed, " ", $reverse);
			else if ($fixed < $length) 
			{
				if ($reverse == STR_PAD_LEFT) $text = substr($text, -$fixed);
				else $text = substr($text, 0, $fixed);
			}
		}
		return $text;
	}
}
?>
