<?php
/**
* Object VIEWXLS
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_viewxls extends ClsObject {
	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id)	
	{
		global $system;
		require_once($system->dir_real_jamp."/".$system->dir_plugin.'excel/excel_reader.php');
		$this->property["align"]	= array("value" => null, "inherit" => false, "html" => true);
		$this->property["id"]		= array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["value"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["size"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["width"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["src"]		= array("value" => null, "inherit" => false, "html" => false);
		$this->property["height"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["cssfile"] 	= array("value" => "objcss/default/viewxls.css", "inherit" => false, "html" => false);
	}

	/**
	* Generate the code pdf
	* @param string $pdf Class PDF
	*/
	public function codePDF($pdf)
	{
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
		$this->setCSS();
		if (!empty($this->property["width"]["value"])) $this->property["style"]["value"] .= "width:".$this->property["width"]["value"].";";
		if (!empty($this->property["height"]["value"])) $this->property["style"]["value"] .= "height:".$this->property["height"]["value"].";";
		$code = "";
		if (!empty($this->property["label"]["value"])) $code .= $this->property["label"]["value"];
		$code .= "\n$tab<div ".$this->getProperty("html", true, false).">";
		$data = new Spreadsheet_Excel_Reader($this->property["src"]["value"]);
 		$code .= $data->dump(true,true);
		$code .= "\n$tab</div>";
		return $code;
	}

	/**
	* The function is called after each setting of a property
	* @param string $name Name property
	*/
	protected function setPropertyAfter($name)
	{
		$id = $this->property["id"]["value"];
		switch($name){
			case "height":
				if ($this->property["height"]["value"] == "autosize")
				{
					$this->property["height"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoHeight('$id');");
				}
			break;
			case "width":
				if ($this->property["width"]["value"] == "autosize")
				{
					$this->property["width"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoWidth('$id');");
				}
				if ($this->property["width"]["value"] == "autosize_center")
				{
					$this->property["width"]["value"] = "0";
					$this->addEventListener("window", "resize", "Resize");
					$this->addEventBefore($id, "Resize", "SYSTEMBROWSER.autoWidthCenter('$id');");
				}
			break;
		}
	}
}
?>