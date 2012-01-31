/**
* Class REGEXP
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsRegExp()
{
	this.dgt = Array();
	this.dgt['number'] = "\\d";				// Only numeric characters
	this.dgt['nonumber'] = "\\D";				// Excluding digits
	this.dgt['alphanumeric'] = "\\w";		// Only characters alphanumeric
	this.dgt['noalphanumeric'] = "\\W";		// Excluding characters alphanumeric
	this.dgt['alphabetic'] = "[a-zA-Z]";	// Only alphabetic characters
	this.dgt['loweralphabetic'] = "[a-z]";	// Only lowercase alphabetic characters
	this.dgt['upperalphabetic'] = "[A-Z]";	// Only uppercase alphabetic characters
	this.dgt['decimal'] = "[0-9\.,]";		// Only decimal character
	this.dgt['permission'] = "[0-7]";		// Permission

	this.word = Array();
	this.word['number'] = "^[0-9\.]{0,}$";		// Only numeric characters
	this.word['decimal'] = "^[0-9\.,]{0,}$";	// Only decimal character
	this.word['alphanumeric'] = "^[a-zA-Z0-9_]$";	// Only characters alphanumerici
	this.word['ip'] = "^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$";	// IP

	this.word['email'] = "^[a-zA-Z0-9_\.]+@[a-zA-Z0-9-]{1}([a-zA-Z0-9-]+\.)+[a-zA-Z]{1,4}$";	// MAIL
	this.word['cf'] = "^[a-zA-Z]{6}[0-9lmnpqrstuvLMNPQRSTUV]{2}[abcdehlmprstABCDEHLMPRST]{1}[0-9lmnpqrstuvLMNPQRSTUV]{2}([a-zA-Z]{1}[0-9]{3})[a-zA-Z]{1}$"; 	// Fiscal Code
	// PIVA = CF
	this.word['picf'] = "^[0-9]{11}$|^[a-zA-Z]{6}[0-9lmnpqrstuvLMNPQRSTUV]{2}[abcdehlmprstABCDEHLMPRST]{1}[0-9lmnpqrstuvLMNPQRSTUV]{2}([a-zA-Z]{1}[0-9]{3})[a-zA-Z]{1}$";    // Piva+Fiscal Code 
	//IBAN ITALIA CON E SENZA SPAZI CASE INSENSITIVE
   this.word['iban_it'] = "^((IT|it)[ ][0-9]{2}[ ][a-zA-Z][ ][0-9]{5}[ ][0-9]{5}[ ][0-9a-zA-z]{12})|((IT|it)[0-9]{2}[a-zA-Z][0-9]{5}[0-9]{5}[0-9a-zA-z]{12})$"; //IBAN ITALIA  	
	
	this.word['piva'] = "^[0-9]{11}$"; 	// VAT
	this.word['permission'] = "^[0-7]{3}$"; //Permission
	this.error = false;
	this.errorpage = false;
}

clsRegExp.prototype =
{
	setError : function(obj) 
	{
		setTimeout(function(){REGEXP.error=false;},1);
		SYSTEMEVENT.setFocus(obj);
		if (obj.p.dsObj)
		{
			$(obj.p.dsObj).DSchange = false;
			eval(obj.p.dsObj + 'ChangeItem()');
		}
		this.error = true;
		this.errorpage = true;
		obj.predClass = obj.className;
		obj.className = 'regerror';
		return false;
	},

	checkWord : function(obj, type) 
	{
		if (obj.readOnly || this.error) return true;
		obj.className = (obj.predClass != undefined) ? obj.predClass : '';
		this.errorpage = false;
		if (obj.p.minlength!=undefined && obj.value.length<obj.p.minlength) return REGEXP.setError(obj);
		if (obj.value == undefined || obj.value == "") return true;
		if (type == undefined || type == "") return true;
		var myRegex = (REGEXP.word[type] == undefined) ? type : REGEXP.word[type];
		if (new RegExp(myRegex).exec(obj.value)==null) return REGEXP.setError(obj);
		return true;
	},

	checkForm : function(obj, type) 
	{
		if (obj.readOnly) return true;
		obj.className = (obj.predClass != undefined) ? obj.predClass : '';
		if (type == undefined || type == "") return true;
		var myRegex = (REGEXP.word[type] == undefined) ? type : REGEXP.word[type];
		if (new RegExp(myRegex).exec(obj.value)==null) 
		{
			 this.errorpage = true;
			 obj.predClass = obj.className;
			 obj.className = 'regerror';
			 return false;
		}
		return true;
	},

	checkDigit : function(e, type) 
	{
		var oTarget = (e.target) ? e.target : e.srcElement;
		var keynum = (window.event) ? e.keyCode : e.which;
		switch (keynum) 
		{
			case 0:
			case 8:  // Back space
			case 9:  // Tab
			case 13: // Enter
			case 37: // Left arrow
			case 39: // Right arrow 
				return true;
			break;
			case 46:
				if (type == "decimal")
				{
					var pos = e.target.selectionStart;
					var out = e.target.value.substr(0, pos);
					out += LANG.package["DOTPAD"];
					out += e.target.value.substr(pos);
					e.target.value = out;
					e.target.selectionStart = pos+1;
					e.target.selectionEnd = pos+1;
					SYSTEMEVENT.preventDefault(e);
				}
			default:
				var keychar = String.fromCharCode(keynum);
				var myRegex = (REGEXP.dgt[type] == undefined) ? type : REGEXP.dgt[type];
				if (new RegExp(myRegex).exec(keychar)==null) SYSTEMEVENT.preventDefault(e);
		}
		if(oTarget.type=="textarea") 
		{						
			var obj = $(oTarget.id);
    		if (obj.p.maxlength != undefined && obj.value.length >= obj.p.maxlength) SYSTEMEVENT.preventDefault(e);
		}
		return true;
	},

	 doKeyUp : function(obj)
	 {
		if (obj.p.maxlength!=undefined) obj.value = obj.value.substr(0,obj.p.maxlength);
	 },

	nextFocus : function(e, id, specialkey) 
	{
		var oTarget = (e.target) ? e.target : e.srcElement;
		var keynum = (window.event) ? e.keyCode : e.which;
		if (keynum == specialkey) $(id).focus();
	},

	actionClick : function(e, id, specialkey) 
	{
		var oTarget = (e.target) ? e.target : e.srcElement;
		var keynum = (window.event) ? e.keyCode : e.which;
		if (keynum == specialkey) $(id).click();
	}
}

var REGEXP = new clsRegExp();