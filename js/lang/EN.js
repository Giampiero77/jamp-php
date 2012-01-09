/**
* Class Language EN
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsLANG()
{
	this.package = {
		"CAL000"	: "CLOSE",
		"CAL001"	: "TODAY",
		"CAL002"	: "CANCEL",
		"CAL003"	: "CONFIRM",
		"JDS000"	: "Do you want to delete the data?",
		"JDS001"	: "CAUTION Do you want to delete ALL the data?",
		"JDS002"	: "Are you sure you want to delete ALL the data?",
		"JDS003"	: "Want to save those changes?",
		"JDS004"	: "Want to save the new data?",
		"JAJAX001"	: "AJAX functions are not supported by your browser, we recommend that you use the latest version of Firefox!",
		"EVENT001"	: "JavaScript Error",
		"EVENT002"	: "XML Transaction Error",
		"EVENT003"	: "HTML Error",
		"EVENT004"	: "File:",
		"EVENT005"	: "Row:",
		"EVENT006"	: "Message:",
		"DSNAV001"	: "NEW",
		"IMAGE001"	: "Error while uploading image",
		"TEXT001"	: "Error while uploading file",
		"DOTPAD"	: "."
	}
}

clsLANG.prototype =
{
	translate : function (code)
	{
		if (this.package[code] == undefined) SYSTEMEVENT.errorJAVASCRIPT("Error Code: <b>"+ code + "</b> undefined!", "EN.js", "");
		else return this.package[code];
	}
}
var LANG = new clsLANG();