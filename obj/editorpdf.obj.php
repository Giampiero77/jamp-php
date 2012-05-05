<?php
/**
* Object Editor Pdf
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_editorpdf extends ClsObject {
	/**
	* @var $container Array containing the child objects
	*/
	var $child = array();

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)	
	{
		$this->property["id"]		= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["dsobj"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["class"] 	= array("value" => "editorpdf", "inherit" => false, "html" => true);
		$this->property["cssfile"]  = array("value" => "objcss/default/editorpdf.css", "inherit" => false, "html" => false);
		$this->property["java"]		= array("value" => "editorpdf.js", "inherit" => false, "html" => false);
		
		$this->property["pageformat"]	= array("value" => "A4", "inherit" => false, "html" => false);
		$this->property["orientation"] 	= array("value" => "P", "inherit" => false, "html" => false);

		$this->property["dsitemkey"]		= array("value" => "key", "inherit" => false, "html" => false);
		$this->property["dsitemobject"]		= array("value" => "object", "inherit" => false, "html" => false);
		$this->property["dsitemx"] 			= array("value" => "x", "inherit" => false, "html" => false);
		$this->property["dsitemy"] 			= array("value" => "y", "inherit" => false, "html" => false);
		$this->property["dsitemwidth"] 		= array("value" => "width", "inherit" => false, "html" => false);
		$this->property["dsitemheight"]		= array("value" => "height", "inherit" => false, "html" => false);
		$this->property["dsitemtext"]		= array("value" => "text", "inherit" => false, "html" => false);
		$this->property["dsitemborder"]		= array("value" => "border", "inherit" => false, "html" => false);
		$this->property["dsitemalign"] 		= array("value" => "align", "inherit" => false, "html" => false);
		$this->property["dsitemfont"] 		= array("value" => "font", "inherit" => false, "html" => false);
		$this->property["dsitemfontsize"] 	= array("value" => "fontsize", "inherit" => false, "html" => false);
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
		if (!empty($this->property["dsobj"]["value"]))
		{
			global $xml;
			$pdf->print_header=false;
			$pdf->print_footer=false;
			$dsObj = $xml->getObjById($this->property["dsobj"]["value"]);
			$dsObj->ds->dsMoveRow(0);
			$pdf->SetMargins(10,10,10,10);
			$pdf->SetAutoPageBreak(false);
			$align = $this->property["dsitemalign"]["value"];
			$object = $this->property["dsitemobject"]["value"];
			$font = $this->property["dsitemfont"]["value"];
			$fontsize = $this->property["dsitemfontsize"]["value"];
			$x = $this->property["dsitemx"]["value"];
			$y = $this->property["dsitemy"]["value"];
			$width = $this->property["dsitemwidth"]["value"];
			$height = $this->property["dsitemheight"]["value"];
			$text = $this->property["dsitemtext"]["value"];
			$border = $this->property["dsitemborder"]["value"];
			while($row = $dsObj->ds->dsGetRow())
			{
				if ($row->$align=="1") $align = "L";
				switch($row->$object)
				{
					case 1:
						$pdf->SetFont($row->$font, "" , $row->fontsize);
						$pdf->setXY($row->$x,$row->$y);
						$pdf->MultiCell($row->$width,$row->$height,$row->$text,$row->$border,$align);
					break;
				}
			}
		}
	}

	/**
	* Generate the code text
	*/
	public function codeTXT()
	{
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$code = "\n$tab<div ".$this->getProperty("html", true, false).">";
		$code .= "\n$tab</div>";
		return $code;
	}

	/**
	* Builds the object
	*/
	public function BuildObj()
	{
		$id = $this->property["id"]["value"];
		$this->addEventListener("window", "resize", "Resize");
		$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
		$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
		$this->propertyJS["pageformat"] = $this->property["pageformat"]["value"];
		$this->propertyJS["orientation"] = $this->property["orientation"]["value"];
		if (!empty($this->property["dsobj"]["value"]))
		{
			$dsobj = $this->property["dsobj"]["value"];
			$this->addEvent($id, $dsobj."Refresh", "EDITORPDF.refreshObj(\"$id\");");
			$this->propertyJS["dsitemkey"] = $this->property["dsitemkey"]["value"];
			$this->propertyJS["dsitemobject"] = $this->property["dsitemobject"]["value"];
			$this->propertyJS["dsitemx"] = $this->property["dsitemx"]["value"];
			$this->propertyJS["dsitemy"] = $this->property["dsitemy"]["value"];
			$this->propertyJS["dsitemwidth"] = $this->property["dsitemwidth"]["value"];
			$this->propertyJS["dsitemheight"] = $this->property["dsitemheight"]["value"];
			$this->propertyJS["dsitemtext"] = $this->property["dsitemtext"]["value"];
			$this->propertyJS["dsitemborder"] = $this->property["dsitemborder"]["value"];
			$this->propertyJS["dsitemalign"] = $this->property["dsitemalign"]["value"];
			$this->propertyJS["dsitemfont"] = $this->property["dsitemfont"]["value"];
			$this->propertyJS["dsitemfontsize"] = $this->property["dsitemfontsize"]["value"];
		}
	}
	
	/**
	 * The function is called after each setting of a property
	 * @param string $name Name property
	 */
	protected function setPropertyAfter($name)
	{
	}
}
?>
