<?php
/**
* Object LIGHTBOX
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @package	Object
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsObj_lightbox extends ClsObject {
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
		$this->property["align"]  = array("value" => null, "inherit" => false, "html" => true);
		$this->property["id"] 	  = array("value" => $id,  "inherit" => false, "html" => true);
		$this->property["value"]  = array("value" => null, "inherit" => false, "html" => false);
		$this->property["size"]   = array("value" => null, "inherit" => false, "html" => false);
		$this->property["width"]  = array("value" => null, "inherit" => false, "html" => false);
		$this->property["height"] = array("value" => null, "inherit" => false, "html" => false);
		$this->property["modal"]  = array("value" => false, "inherit" => false, "html" => false);

		// themes: grey (default), red, green, blue, gold
		$this->property["theme"] = array("value" => null, "inherit" => false, "html" => false);

		// controls whether or not Flash objects should be hidden
		$this->property["hideflash"] = array("value" => null, "inherit" => false, "html" => false);

 		//controls whether to show the outer grey (or theme) border
		$this->property["outerborder"] = array("value" => null, "inherit" => false, "html" => false);

		// controls the speed of the image resizing (1=slowest and 10=fastest)
		$this->property["resizespeed"] = array("value" => null, "inherit" => false, "html" => false);

		// higher opacity = darker overlay, lower opacity = lighter overlay
		$this->property["maxopacity"] = array("value" => null, "inherit" => false, "html" => false);

		// 1 = "Prev/Next" buttons on top left and left (default), 2 = "<< prev | next >>" links next to image number
		$this->property["navtype"] = array("value" => null, "inherit" => false, "html" => false);

		// controls whether or not images should be resized if larger than the browser window dimensions
		$this->property["autoresize"] = array("value" => null, "inherit" => false, "html" => false);

		// controls whether or not "animate" Lightbox, i.e. resize transition between images, fade in/out effects, etc.
		$this->property["doanimations"] = array("value" => null, "inherit" => false, "html" => false);		

		// if you adjust the padding in the CSS, you will need to update this variable - otherwise, leave this alone.
		$this->property["bordersize"] = array("value" => null, "inherit" => false, "html" => false);
	
		// Change value (milliseconds) to increase/decrease the time between "slides" (10000 = 10 seconds)
		$this->property["slideinterval"] = array("value" => null, "inherit" => false, "html" => false);

		// true to display Next/Prev buttons/text during slideshow, false to hide
		$this->property["shownavigation"] = array("value" => null, "inherit" => false, "html" => false);

		// true to display the Close button, false to hide
		$this->property["showclose"] = array("value" => null, "inherit" => false, "html" => false);

		// true to display image details (caption, count), false to hide
		$this->property["showdetails"] = array("value" => null, "inherit" => false, "html" => false);

		// true to display pause/play buttons next to close button, false to hide
		$this->property["showplaypause"] = array("value" => null, "inherit" => false, "html" => false);

		// true to automatically close Lightbox after the last image is reached, false to keep open
		$this->property["autoend"] = array("value" => null, "inherit" => false, "html" => false);

		// true to pause the slideshow when the "Next" button is clicked
		$this->property["pauseonnextclick"] = array("value" => null, "inherit" => false, "html" => false);

		// true to pause the slideshow when the "Prev" button is clicked
		$this->property["pauseonprevclick"] = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["java"] = array("value" => "lightbox.js", "inherit" => false, "html" => false);
		$this->property["cssfile"] = array("value" => "objcss/default/lightbox.css", "inherit" => false, "html" => false);
 		$this->property["dsobj"] = array("value" => null, "inherit" => false, "html" => false);
 		$this->property["dsitem"] = array("value" => null, "inherit" => false, "html" => false);
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
	* Builds the object
	*/
	public function BuildObj()
	{
		$this->codejs = "";
 		$this->setCodeJs("\t\tLIGHTBOX.updateLightboxItems(\"".$this->property["id"]["value"]."\");");
	}

	/**
	* Generate the code html
	* @param string $tab Tabs
	*/
	public function codeHTML($tab = "")
	{
		if (!empty($this->property["width"]["value"])) $this->property["style"]["value"] .= "width:".$this->property["width"]["value"].";";
		if (!empty($this->property["height"]["value"])) $this->property["style"]["value"] .= "height:".$this->property["height"]["value"].";";
		$code = "\n$tab<div ".$this->getProperty("html", true, false).">";
		if (!empty($this->property["value"]["value"])) $code .= "\n\t".$tab.$this->property["value"]["value"];
 		foreach ($this->child as $obj) $code .= "\n".$obj->codeHTML($tab);
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
		switch($name)
		{
			case "modal":
				$this->propertyJS[$name] = ($this->property[$name]["value"] == "true") ? true : false;
			break;	
			case "theme":
			case "hideflash":
			case "outerborder":
			case "resizespeed":
			case "maxopacity":
			case "navtype":
			case "autoresize":
			case "doanimations":
			case "bordersize":
			case "slideinterval":
			case "shownavigation":
			case "showclose":
			case "showdetails":
			case "showplaypause":
			case "autoend":
			case "pauseonnextclick":
			case "pauseonprevclick":
				$this->propertyJS[$name] = $this->property[$name]["value"];
			break;	
		}
	}
}
?>
