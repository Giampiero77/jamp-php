/**
* Class RADIO
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsRadio()
{
}

clsRadio.prototype =
{
	toogle : function(obj)
	{
		if (obj.readOnly || obj.disabled) return;
		this.uncheck(obj);
		obj.className = obj.p.classradio + "_check";
		obj.p.checked = true;
	},

	setDsValue : function(obj, value)
	{
		if (obj.readOnly || obj.disabled) return;
		var dsObj = $(obj.p.dsObj);
		if (value != undefined) obj.value = value;
		var pos = (obj.row == undefined) ? dsObj.DSpos : obj.row;
		if (pos == -1) dsObj.DSresult[pos][obj.p.dsItem] = Array();
		if (dsObj.DSresult[pos]!=undefined)
		{
			 this.toogle(obj);
			 dsObj.DSresult[pos][obj.p.dsItem] = obj.value;
			 dsObj.DSposalter = pos;
			 DS.dschange(dsObj);
		}
	},

	getDsValue : function(id)
	{
		var radioObj = $(id);
		var dsObj = $(radioObj.p.dsObj);
		var className = radioObj.p.classradio;
		radioObj.p.value = (radioObj.p.value==undefined) ? 1 : radioObj.p.value;
		if (dsObj != null)
		{
			if (radioObj.p.name==undefined && radioObj.row!=undefined) radioObj.p.name = radioObj.p.dsItem + "_" + radioObj.row; 
			if (dsObj.DSpos != 0 && dsObj.p.Readonly!=true) radioObj.readOnly = false;
			if (dsObj.DSresult.length == 0) return;
			var row = (radioObj.row == undefined) ? dsObj.DSpos : radioObj.row;
			var valueDs = (dsObj.DSresult[row][radioObj.p.dsItem] == undefined) ? "" : dsObj.DSresult[row][radioObj.p.dsItem];
			radioObj.p.checked = (radioObj.p.value == valueDs);
		}
		else radioObj.readOnly = (radioObj.p.Readonly) ? true : false;
		radioObj.className = (radioObj.p.checked) ? className + "_check" : className + "_uncheck";
		radioObj.disabled = (radioObj.p.Disabled) ? true : false;
		radioObj.value = radioObj.p.value;
	},

	uncheck : function(obj)
	{
		var objname = obj.p.name;
		if (objname == null) return;
		var classObj = document.getElementsByTagName('span');
		var length = classObj.length;
		var dsObj = $(obj.p.dsObj);
		for (var i = 0; i < length; i++)
		{
			if (classObj[i].p && classObj[i].p.typeObj=='radio' && classObj[i].p.name == objname) 
			{
				classObj[i].className = classObj[i].p.classradio + "_uncheck";
				classObj[i].p.checked = false;
				if (dsObj != null)
				{
					 var pos = (classObj[i].row == undefined) ? dsObj.DSpos : classObj[i].row;
					 if (pos == -1) dsObj.DSresult[pos][obj.p.dsItem] = Array();
					 dsObj.DSresult[pos][classObj[i].p.dsItem] = 0;
					 dsObj.DSposalter = pos;
					 DS.dschange(dsObj);
				}
			}
		}
	},

	getCheckedObj: function(objname)
	{
		var classObj = document.getElementsByTagName('span');
		var length = classObj.length;
		for (var i = 0; i < length; i++)
		{
			if (classObj[i].p && classObj[i].p.typeObj=='radio' && classObj[i].p.name == objname)
			{
				if (classObj[i].className == classObj[i].p.classradio + "_check") return classObj[i];
			}
		}
	},
	
	refreshObj : function(id)
	{
		this.getDsValue(id);
	}
}

var RADIO = new clsRadio();