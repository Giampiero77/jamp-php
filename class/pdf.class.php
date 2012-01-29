<?php
/**
* Class ClsPDF
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsPDF extends FPDF {
	private $format;
	private $breakpage;
	private $storerow;
	public $storerow_enable;
	private $storerow_height;

	public function __construct($orientation,$unit,$pageformat)
	{
 		require_once("format.class.php");
		parent::FPDF($orientation,$unit,$pageformat);
		$this->format = new ClsFormat();
		$this->property = array();
		$this->storerow = null;
		$this->storerow_enable = false;
		$this->breakpage = false;
		$this->top = 0;
		$this->maxheight = 0;
		$this->maxsize = 0;
		$this->headrow["data"] = null;
		$this->headrow["row"] = 0;
	}

	/**
	* Convert the style to array
	* @return array
	*/
	public function StyleToArray($txtstyles)
	{
		$array = explode(";", $txtstyles);
		foreach($array as $property)
		{
			$prop = explode(":", $property);
			if (!empty($prop[1])) $this->property[trim($prop[0])] = trim($prop[1]);
		}
		if ($this->property["align"] == "center") $this->property["align"] = "C";
		else if ($this->property["align"] == "right") $this->property["align"] = "R";
		if ($this->property["font-weight"] == "bold") $this->property["font-weight"] = "B";
	}

	/**
	* Convert the style to array
	* @return array
	*/
	public function SetStyle($txtstyles)
	{
		global $system;
		if ($this->top < $this->getY()) $this->top = $this->getY();
		if(!empty($txtstyles))
		{
   			$this->StyleToArray($txtstyles);
 			$this->property["border-width"] = (!empty($this->property["border"])) ? intval($this->property["border"]) : 0;
 			if(isset($this->property["float"])) 
 			{
 				if ($this->property["float"]=="none") $this->property["ln"] = 1;
 				if ($this->property["overflow"]=="auto") $this->property["multi"] = true;
 			}
 			if(empty($this->property["color"])) $this->SetTextColor(0, 0, 0);
 			else 
 			{
 				$color = $system->HEXtoRGB($this->property["color"]);
 				$this->SetTextColor($color["R"], $color["G"], $color["B"]);
 			}
 			if(!empty($this->property["border-color"]))
 			{
 				$color = $system->HEXtoRGB($this->property["border-color"]);
				$this->SetDrawColor($color["R"], $color["G"], $color["B"]);
 			}			
            if (!empty($this->property["background-color"]))
    		{
    			$bgcolor = $system->HEXtoRGB($this->property["background-color"]);
    			$this->SetFillColor($bgcolor["R"], $bgcolor["G"], $bgcolor["B"]);
    		}
		}
		$this->property["font-size"] = (isset($this->property["font-size"])) ? intval($this->property["font-size"]) : 12;
		$this->property["border-width"] = (isset($this->property["border-width"])) ? intval($this->property["border-width"]) : 0;
		
		
		$this->property["ln"] = (isset($this->property["ln"])) ? intval($this->property["ln"]) : 0;
		if (isset($this->property["width"])) $this->property["width"] = intval($this->property["width"]);
		$this->property["height"] = (isset($this->property["height"])) ? intval($this->property["height"]) : $this->property["font-size"]/2;
 		$this->SetFont($this->property["font-family"], $this->property["font-weight"], $this->property["font-size"]);       			                }

	/**
	* Inserts a cell by reading the property
	* @param object Object
	*/
	function printValue($text)
	{
		if (isset($this->property["top"])) $this->SetY($this->property["top"]);
		if (isset($this->property["left"])) 
        {
            $this->SetX($this->property["left"]);
            $left = $this->property["left"];
        }                                 
        else $left = $this->getX(); 
		$this->property["width"] = (isset($this->property["width"])) ? intval($this->property["width"]) : ($this->GetStringWidth($text) + 2);
		$text = explode('\n', $text);
		foreach($text as $k => $outtext)
		{
			$outtext = utf8_decode($outtext);
 			if ($k>0) $this->Ln();
			if (isset($outtext) && $outtext!="")
			{
			    $bg = (empty($this->property["background-color"])) ? 0 : 1; 
	  			if (!empty($this->property["wrap"]) && $outtext!="") $this->Write($this->property["wrap"], $left,$outtext);
	  			else if ($this->property["multi"]) $this->MultiCell($this->property["width"],$this->property["height"], $outtext, $this->property["border-width"], $this->property["align"]);
				else $this->Cell($this->property["width"], $this->property["height"], $outtext, $this->property["border-width"], $this->property["ln"], $this->property["align"],$bg);
			}
		}
	}

	function printImage()
	{
		$this->SetStyle($this->property["style"]);
		$imgwidth = isset($this->property["width"]) ? intval($this->property["width"]) : 0;
		$imgheight = isset($this->property["height"]) ? intval($this->property["height"]) : 0;
		if (!file_exists($this->property["src"])) return false;
		if (empty($imgwidth) || empty($imgheight)) 
		{
			list($imgwidth, $imgheight) = getimagesize($this->property["src"]);
			$imgwidth = $imgwidth/4;
			$imgheight = $imgheight/4; 
		}
		$top = (isset($this->property["top"])) ? intval($this->property["top"]) : $this->getY();
		$left = (isset($this->property["left"])) ? intval($this->property["left"]) : $this->getX();

		if (empty($this->property["label"]))
		{
			$this->Image($this->property["src"], $left, $top, $imgwidth, $imgheight);
		}
		else if ($this->property["labelalign"]=="left")
		{
			if (!empty($this->property["labelstyle"]))
			{
				$this->SetStyle($this->property["labelstyle"]);
				$this->printValue($this->property["label"]);
				$this->Image($this->property["src"], $left, $top, $imgwidth, $imgheight);
			}
			else 
			{
				$this->property["width"] = 0;
				$this->property["height"] = $this->property["font-size"]/2;
				if ($this->property["height"] > $imgheight) $top += (($this->property["height"] - $imgheight)/2);
 				else $this->property["top"] += (($imgheight - $this->property["height"])/2);
				if (!empty($this->property["left"])) $left = intval($this->property["left"]);
 				$left += $this->GetStringWidth($this->property["label"])+2;
				$this->printValue($this->property["label"]);
				$this->Image($this->property["src"], $left, $top, $imgwidth, $imgheight);
			}
		}
		else
		{
			if (!empty($this->property["labelstyle"]))
			{
				$this->Image($this->property["src"], $left, $top, $imgwidth, $imgheight);
				$this->SetStyle($this->property["labelstyle"]);
				$this->printValue($this->property["label"]);
			}
			else 
			{
				$this->property["left"] = $left + $imgwidth;
				$this->property["width"] = 0;
				$this->property["height"] = $this->property["font-size"]/2;
				if ($this->property["height"] > $imgheight) $top += (($this->property["height"] - $imgheight)/2);
  				else $this->property["top"] += $imgheight/2;
				$this->printValue($this->property["label"]);
				$this->Image($this->property["src"], $left, $top, $imgwidth, $imgheight);
			}
		}
	}

	/**
	* Inserts a cell by reading the property
	* @param object Object
	*/
	function printText()
	{
		if (empty($this->property["label"]))
		{
			$this->SetStyle($this->property["style"]);
			$this->printValue($this->property["text"]);
		}
		else if ($this->property["labelalign"]=="left")
		{
			if (!empty($this->property["labelstyle"]))
			{
				$this->SetStyle($this->property["labelstyle"]);
				$this->printValue($this->property["label"]);
				$this->SetStyle($this->property["style"]);
 				$this->printValue($this->property["text"]);
			}
			else 
			{
				$this->SetStyle($this->property["style"]);
 				$this->printValue($this->property["label"].$this->property["text"]);
			}
		}
		else
		{
			if (!empty($this->property["labelstyle"]))
			{
				$this->SetStyle($this->property["style"]);
 				$this->printValue($this->property["text"]);
				$this->SetStyle($this->property["labelstyle"]);
				$this->printValue($this->property["label"]);
			}
			else 
			{
				$this->SetStyle($this->property["style"]);
 				$this->printValue($this->property["text"].$this->property["label"]);
			}
		}
	}

	/**
	* Inserts a cell by reading the property
	* @param object Object
	*/
	function CellObj($obj)
	{	
		$this->property = array();
		$this->property["text"] = $obj->getPropertyName("value", false);
		if ($this->property["text"] == "null") $this->property["text"] = "";
		$this->property["format"] = $obj->getPropertyName("format", false);
		if($this->property["text"] == '$$PDF-BREAK-PAGE$$')
		{
 			$this->breakpage = true;
			return;
		}
		if($this->breakpage == true) 
		{
			$this->AddPage($this->CurOrientation);
			$this->breakpage = false;
		}
		if (!isset($this->property["text"])) $this->property["text"] = $obj->getPropertyName("defaultvalue", false);
 		if (!empty($this->property["format"])) $this->property["text"] = $this->format->Format($this->property["text"], $this->property["format"]);
		$ds = $obj->getPropertyName("dsobj", false);
		if (!empty($ds)) $this->property["ln"] = 1;
		$this->property["label"] = $obj->getPropertyName("label", false);
		$this->property["style"] = strtolower($obj->getPropertyName("style", false));
		$this->property["labelstyle"] = strtolower($obj->getPropertyName("labelstyle", false));
		$this->property["labelalign"] = $obj->getPropertyName("labelalign", false);
		$this->property["src"] = $obj->getPropertyName("src", false);
		$this->property["align"] = $obj->getPropertyName("align", false);
		$this->property["wrap"] = $obj->getPropertyName("wrap", false);
		$this->property["multi"] = false;
		$this->property["font-family"] = "Arial";
		$this->property["font-weight"] = "";
		//Store Cell
		if($this->storerow_enable) 
		{
			$this->property["border-width"] = 1;
			$this->property["top"] = 0;
			$this->StyleToArray($this->property["style"]);
			if (empty($this->property["font-size"])) $this->property["font-size"] = 10;
			$this->property["text"] = utf8_decode($this->property["label"].$this->property["text"]);
	 		$width = $obj->getPropertyName("size", false);
			$this->property["colwidth"] = (!empty($width)) ? intval($width) : 30;
			$this->SetFont($this->property["font-family"], $this->property["font-weight"], $this->property["font-size"]);
			$this->maxheight = max($this->maxheight, $this->NbLines($this->property["colwidth"], $this->property["text"]));
			$this->maxsize = max($this->maxsize, $this->property["font-size"]/2);
			if (!empty($this->property["src"]))
			{
				$this->property["width"] = isset($this->property["width"]) ? intval($this->property["width"]) : 0;
				$this->property["height"] = isset($this->property["height"]) ? intval($this->property["height"]) : 0;
				if (empty($this->property["width"]) || empty($this->property["height"])) 
				{
					list($this->property["width"], $this->property["height"]) = getimagesize($this->property["src"]);
					$this->property["width"] = $this->property["width"]/4;
					$this->property["height"] = $this->property["height"]/4;
				}
 				if (empty($this->property["left"])) $this->property["left"] = ($this->property["colwidth"]/2)-($this->property["width"]/2);
   				$this->maxheight = max($this->maxheight, $this->property["height"]/4);
  				if (empty($this->property["top"])) $this->property["top"] = (($this->maxheight*$this->maxsize)/2)-($this->property["height"]/2);
			}
			$this->storerow[] = $this->property;
		}
		else if (empty($this->property["src"])) $this->printText();
		else $this->printImage();
	}

	/**
	* Print stored row
	*/
	function Print_Store_Row()
	{
		global $system;
		$height = ($this->maxsize * $this->maxheight);
		$newpage = $this->CheckPageBreak($height);
		if ($this->headrow["row"] > 0) 
		{
			$this->headrow["row"]--;
			$this->headrow["data"][] = array("cell"=>$this->storerow, "height"=>$height);
		}
		if ($this->top > $this->getY()) $this->SetY($this->top+2);
		if ($newpage && !is_null($this->headrow["data"])) 
		{
			foreach($this->headrow["data"] as $row)
			{
				foreach($row["cell"] as $cell) $y = $this->Print_Store_Cell($cell, $row["height"]);
			}
			$this->setY($y + $row["height"]);
		}
		foreach($this->storerow as $cell) $y = $this->Print_Store_Cell($cell, $height);
		$this->setY($y + $height - $this->maxsize);
		$this->storerow = null;
		$this->maxheight = $this->maxsize = 0;
	}

	/**
	* Print stored cell
	*/
	function Print_Store_Cell($cell, $height)
	{
		global $system;
		if ($cell["font-weight"] == "bold") $cell["font-weight"] = "B";
		$this->SetFont($cell["font-family"], $cell["font-weight"], $cell["font-size"]);
		$x = $this->getX();
		$y = $this->getY();
		if(empty($cell["color"])) $this->SetTextColor(0, 0, 0);
		else 
		{
			$color = $system->HEXtoRGB($cell["color"]);
			$this->SetTextColor($color["R"], $color["G"], $color["B"]);
		}
		if(!empty($cell["border-color"]))
		{
			$color = $system->HEXtoRGB($cell["border-color"]);
			$this->SetDrawColor($color["R"], $color["G"], $color["B"]);
		}
		if ($cell["border-width"]>0) $this->Rect($x, $y, $cell["colwidth"], $height);
		if (!empty($cell["src"])) $this->Image($cell["src"], $cell["left"]+$x, $cell["top"]+$y, $cell["width"], $cell["height"]);
		else $this->MultiCell($cell["colwidth"], $cell["font-size"]/2, $cell["text"], 0, $cell["align"]);
		$this->setXY($x + $cell["colwidth"], $y);
		return $y;
	}

	/**
	* Create new page
	*/
	function CheckPageBreak($height)
	{
		if($this->GetY() + $height + $this->topFooter > $this->PageBreakTrigger)	
		{
			$this->AddPage($this->CurOrientation);
			return true;
		}
		return false;
	}

	/**
	* Calculate number line
	*/
	function NbLines($w,$txt)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0) $w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n") $nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
					$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j) $i++;
				}
				else $i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else $i++;
		}
		return $nl;
	}

	/**
	* Header
	*/
	function Header()
	{
		//Custom Header
		global $xml;
		$this->SetFont('Arial','I',8);
		$this->SetTextColor(0);
		$header = @$xml->getObjById("header");
		if($header != null) 
		{
			$this->storerow_enable = false;
			$header->headerPDF($this); 
			$this->Ln();
			$this->top = $this->getY();
		}
		//Default Header
	}

	/**
	* Footer
	*/
	function Footer()
	{
		//Custom Footer
		global $xml;
		$footer = @$xml->getObjById("footer");
		if($footer != null) 
		{
			$this->storerow_enable = false;
			$this->SetY(intval($footer->getPropertyName("top", true, false)));
 			$footer->footerPDF($this);
			return;
		}

		//Default Footer
		//Va a 1.5 cm dal fondo della pagina
		$this->SetY(-15);
		//Seleziona Arial corsivo 8
		$this->SetFont('Arial','I',8);
		$this->SetTextColor(0);
		//Stampa il numero di pagina centrato
		$time = date("d/m/Y H:m:i");
		$this->Cell($this->GetStringWidth($time),10,$time,0,0,'L');
		$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
	}

	/**
	* Barcode39
	* @param integer abscissa of barcode
	* @param integer ordinate of barcode
	* @param string value of barcode
	* @param integer corresponds to the width of a wide bar 
	* @param integer bar height
	* @param string Font
	* @param integer Size
	*/
	function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5, $font='Arial', $size=10){
	
		$wide = $baseline;
		$narrow = $baseline / 3 ; 
		$gap = $narrow;
	
		$barChar['0'] = 'nnnwwnwnn';
		$barChar['1'] = 'wnnwnnnnw';
		$barChar['2'] = 'nnwwnnnnw';
		$barChar['3'] = 'wnwwnnnnn';
		$barChar['4'] = 'nnnwwnnnw';
		$barChar['5'] = 'wnnwwnnnn';
		$barChar['6'] = 'nnwwwnnnn';
		$barChar['7'] = 'nnnwnnwnw';
		$barChar['8'] = 'wnnwnnwnn';
		$barChar['9'] = 'nnwwnnwnn';
		$barChar['A'] = 'wnnnnwnnw';
		$barChar['B'] = 'nnwnnwnnw';
		$barChar['C'] = 'wnwnnwnnn';
		$barChar['D'] = 'nnnnwwnnw';
		$barChar['E'] = 'wnnnwwnnn';
		$barChar['F'] = 'nnwnwwnnn';
		$barChar['G'] = 'nnnnnwwnw';
		$barChar['H'] = 'wnnnnwwnn';
		$barChar['I'] = 'nnwnnwwnn';
		$barChar['J'] = 'nnnnwwwnn';
		$barChar['K'] = 'wnnnnnnww';
		$barChar['L'] = 'nnwnnnnww';
		$barChar['M'] = 'wnwnnnnwn';
		$barChar['N'] = 'nnnnwnnww';
		$barChar['O'] = 'wnnnwnnwn'; 
		$barChar['P'] = 'nnwnwnnwn';
		$barChar['Q'] = 'nnnnnnwww';
		$barChar['R'] = 'wnnnnnwwn';
		$barChar['S'] = 'nnwnnnwwn';
		$barChar['T'] = 'nnnnwnwwn';
		$barChar['U'] = 'wwnnnnnnw';
		$barChar['V'] = 'nwwnnnnnw';
		$barChar['W'] = 'wwwnnnnnn';
		$barChar['X'] = 'nwnnwnnnw';
		$barChar['Y'] = 'wwnnwnnnn';
		$barChar['Z'] = 'nwwnwnnnn';
		$barChar['-'] = 'nwnnnnwnw';
		$barChar['.'] = 'wwnnnnwnn';
		$barChar[' '] = 'nwwnnnwnn';
		$barChar['*'] = 'nwnnwnwnn';
		$barChar['$'] = 'nwnwnwnnn';
		$barChar['/'] = 'nwnwnnnwn';
		$barChar['+'] = 'nwnnnwnwn';
		$barChar['%'] = 'nnnwnwnwn';
	
		$this->SetFont($font,'',$size);
		$this->Text($xpos, $ypos + $height + 4, $code);
		$this->SetFillColor(0);
	
		$code = '*'.strtoupper($code).'*';
		for($i=0; $i<strlen($code); $i++){
			$char = $code{$i};
			if(!isset($barChar[$char])){
					$this->Error('Invalid character in barcode: '.$char);
			}
			$seq = $barChar[$char];
			for($bar=0; $bar<9; $bar++){
					if($seq{$bar} == 'n'){
						$lineWidth = $narrow;
					}else{
						$lineWidth = $wide;
					}
					if($bar % 2 == 0){
						$this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
					}
					$xpos += $lineWidth;
			}
			$xpos += $gap;
		}
	}

	/**
	* EAN13
	* @param integer abscissa of barcode
	* @param integer ordinate of barcode
	* @param string value of barcode
	* @param integer bar height
	* @param integer corresponds to the width of a wide bar 
	* @param string Font
	* @param integer Size
	*/
	function EAN13($x, $y, $barcode, $baseline =.35, $h=16, $font='Arial', $size=10)
	{
		//Padding
		$barcode=str_pad($barcode, 12,'0', STR_PAD_LEFT);
		if(strlen($barcode) == 12) //Get Check Digit
		{ 
			$sum=0;
			for ($i=1; $i<=11; $i+=2)
				$sum += 3*$barcode[$i];
			for ($i=0; $i<=10; $i+=2)
				$sum += $barcode[$i];
			$r = $sum%10;
			if ($r>0) $r = 10-$r;
			$barcode .=  $r;
		}
		else
		{
			//Check Digit
			$sum=0;
			for ($i=1; $i<=11; $i+=2)
				$sum += 3 * $barcode[$i];
			for ($i = 0; $i <= 10; $i += 2)
				$sum += $barcode[$i];
			if (!(($sum + $barcode[12])%10 == 0)) $this->Error('EAN13 - Incorrect check digit');
		}
		//Convert digits to bars
		$codes = array(
			'A'=>array(
					'0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
					'5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'),
			'B'=>array(
					'0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
					'5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'),
			'C'=>array(
					'0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
					'5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100')
			);
		$parities=array(
			'0'=>array('A','A','A','A','A','A'),
			'1'=>array('A','A','B','A','B','B'),
			'2'=>array('A','A','B','B','A','B'),
			'3'=>array('A','A','B','B','B','A'),
			'4'=>array('A','B','A','A','B','B'),
			'5'=>array('A','B','B','A','A','B'),
			'6'=>array('A','B','B','B','A','A'),
			'7'=>array('A','B','A','B','A','B'),
			'8'=>array('A','B','A','B','B','A'),
			'9'=>array('A','B','B','A','B','A')
			);
		$code = '101';
		$p = $parities[$barcode[0]];
		for ($i = 1; $i <= 6; $i++)
			$code .= $codes[$p[$i-1]][$barcode[$i]];
		$code .='01010';
		for ($i=7; $i<=12; $i++)
			$code .= $codes['C'][$barcode[$i]];
		$code .= '101';
		//Draw bars
		for($i=0; $i<strlen($code); $i++)
		{
			if($code[$i] == '1')
					$this->Rect($x + $i * $baseline, $y, $baseline, $h, 'F');
		}
		//Print text uder barcode
		$this->SetFont($font, '', $size);
		$this->Text($x,$y+$h+11/$this->k,substr($barcode,-13));
	}

	function AddPage($orientation='')
	{
		userEvent::call("pdf_before_addpage", $this);
		parent::AddPage($orientation);
		userEvent::call("pdf_after_addpage", $this);
	}



    function Write($h,$left,$txt,$link='')
    {
    	$cw=&$this->CurrentFont['cw'];
    	$w=$this->w-$this->rMargin-$this->x;
    	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    	$s=str_replace("\r",'',$txt);
    	$nb=strlen($s);
    	$sep=-1;
    	$i=$j=$l=0;
    	$nl=1;
    	while($i<$nb)
    	{
    		//Get next character
    		$c=$s{$i};
    		if($c=="\n")
    		{
    			//Explicit line break   
                $this->SetX($left);   
    			$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
    			$i++;
    			$sep=-1;
    			$j=$i;
    			$l=0;
    			if($nl==1)
    			{
    				$this->x=$this->lMargin;
    				$w=$this->w-$this->rMargin-$this->x;
    				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    			}
    			$nl++;
    			continue;
    		}
    		if($c==' ') $sep=$i;
    		$l+=$cw[$c];
    		if($l>$wmax)
    		{
    			//Automatic line break
    			if($sep==-1)
    			{
    				if($this->x>$this->lMargin)
    				{
    					//Move to next line
    					$this->x=$this->lMargin;
    					$this->y+=$h;
    					$w=$this->w-$this->rMargin-$this->x;
    					$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    					$i++;
    					$nl++;
    					continue;
    				}
    				if($i==$j) $i++; 
                    $this->SetX($left);   
    				$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
    			}
    			else
    			{
                    $this->SetX($left);   
   				$this->Cell($w,$h,substr($s,$j,$sep-$j),0,2,'',0,$link);
    				$i=$sep+1;
    			}
    			$sep=-1;
    			$j=$i;
    			$l=0;
    			if($nl==1)
    			{
    				$this->x=$this->lMargin;
    				$w=$this->w-$this->rMargin-$this->x;
    				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    			}
    			$nl++;
    		}
    		else 
    			$i++;
    	}
    	//Last chunk
    	if($i!=$j) {
            $this->SetX($left);   
    		$this->Cell($l/1000*$this->FontSize,$h,substr($s,$j),0,0,'',0,$link);
        }
    }
}
?>
