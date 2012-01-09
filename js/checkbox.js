/**
* Class CHECKBOX
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsCheckbox()
{
}

clsCheckbox.prototype =
{
	toogle : function(obj)
	{
		if (obj.readOnly || obj.disabled) return;
 		if (obj.className == obj.p.classcheckbox + "_check") 
 		{
 			obj.value = (obj.p.uncheck != null) ? obj.p.uncheck : 0;
 			obj.className = obj.p.classcheckbox + "_uncheck";
         if (obj.p.uncheckImage) obj.style.backgroundImage = "url('"+obj.p.uncheckImage+"')";
 		}		
 		else if (obj.className == obj.p.classcheckbox + "_uncheck") 
 		{
 			if (obj.p.tristate != null) 
 			{
 				obj.value = (obj.p.tristate != null) ? obj.p.tristate : -1;
 				obj.className = obj.p.classcheckbox + "_undefined";
            if (obj.p.tristateImage) obj.style.backgroundImage = "url('"+obj.p.tristateImage+"')";
 			}
 			else
 			{
 				obj.value = (obj.p.check != null) ? obj.p.check : 1;
 				obj.className = obj.p.classcheckbox + "_check";
            if (obj.p.checkImage) obj.style.backgroundImage = "url('"+obj.p.checkImage+"')";
 			}
 		}
 		else
 		{
 			obj.value = (obj.p.check != null) ? obj.p.check : 1;
 			obj.className = obj.p.classcheckbox + "_check";
         if (obj.p.checkImage) obj.style.backgroundImage = "url('"+obj.p.checkImage+"')";
 		}
	},	

	setDsValue : function(obj, value)
	{
		if (obj.readOnly || obj.disabled) return;
		if (value != undefined) obj.value = value;
		this.toogle(obj);
		var dsObj = $(obj.p.dsObj);
		var pos = (obj.row == undefined) ? dsObj.DSpos : obj.row;
		if (pos == -1) dsObj.DSresult[pos][obj.p.dsItem] = Array();
		dsObj.DSresult[pos][obj.p.dsItem] = obj.value;
		dsObj.DSposalter = pos;
		DS.dschange(dsObj);	
	},

	getDsValue : function(id)
	{
		var checkboxObj = $(id);
		var classcheckbox = checkboxObj.p.classcheckbox;
		var check = (checkboxObj.p.check != undefined) ? checkboxObj.p.check : 1;
		var uncheck = (checkboxObj.p.uncheck != undefined) ? checkboxObj.p.uncheck : 0;
		var valueDs = checkboxObj.p.value;
		checkboxObj.readOnly = (checkboxObj.p.Readonly == true) ? true : false;
		checkboxObj.disabled = (checkboxObj.p.Disabled == true) ? true : false;
		checkboxObj.className = classcheckbox + "_undefined";
		if (checkboxObj.p.tristateImage) checkboxObj.style.backgroundImage = "url('"+checkboxObj.p.tristateImage+"')";
		if (checkboxObj.p.dsObj != null)
		{
			var dsObj = $(checkboxObj.p.dsObj);
			if (dsObj.DSpos == 0) checkboxObj.readOnly = true;
			if (dsObj.DSresult.length == 0) return;
			var row = (checkboxObj.row == undefined) ? dsObj.DSpos : checkboxObj.row;
			valueDs = (dsObj.DSresult[row][checkboxObj.p.dsItem] == undefined) ? "" : dsObj.DSresult[row][checkboxObj.p.dsItem]; 
		}
		if (valueDs == check) 
		{
			 checkboxObj.className = classcheckbox + "_check";
			 if (checkboxObj.p.checkImage) checkboxObj.style.backgroundImage = "url('"+checkboxObj.p.checkImage+"')";
		}
		else if (valueDs == uncheck) 
		{
			 checkboxObj.className = classcheckbox + "_uncheck";
			 if (checkboxObj.p.uncheckImage) checkboxObj.style.backgroundImage = "url('"+checkboxObj.p.uncheckImage+"')";
		}
		checkboxObj.value = valueDs;
	},

	refreshObj : function(id)
	{
		this.getDsValue(id);
	}
}

var CHECKBOX = new clsCheckbox();