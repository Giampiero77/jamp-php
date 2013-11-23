<?php

require_once('div.obj.php');
class ClsObj_html5video extends ClsObj_div {

	/**
	* Construct
	* @param string $id ID object
	*/
	public function __construct($id) {
		parent::__construct($id);
		$this->property["canvas"]	= array("value" => 'canvas', "inherit" => false, "html" => false);
		$this->property["autoplay"]	= array("value" => null, "inherit" => false, "html" => false);
		$this->property["poster"]	= array("value" => null, "inherit" => false, "html" => false);
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		$code = '<video '.$this->getProperty("html", true, false).
			(!empty($this->property["autoplay"]["value"]) ? ' autoplay' : '').
			(!empty($this->property["poster"]["value"]) ? ' poster="'.$this->property["poster"]["value"].'"' : '').
			(!empty($this->property["width"]["value"]) ? ' width="'.$this->property["width"]["value"].'"' : '').
			(!empty($this->property["height"]["value"]) ? ' height="'.$this->property["height"]["value"].'"' : '').
			'></video>';

		$code .= '<canvas id="'.$this->property["canvas"]["value"].'"'.
			(!empty($this->property["width"]["value"]) ? ' width="'.$this->property["width"]["value"].'"' : '').
			(!empty($this->property["height"]["value"]) ? ' height="'.$this->property["height"]["value"].'"' : '').
			'></canvas>';
		return $code;
	}

}
?>
