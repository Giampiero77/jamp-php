/**
* Class DSSELECTURL
* @author	Alyx Association <info@alyx.it> modified by Bertoli Stefano
* @version	2.0.0
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsDsSelectUrl()
{
}

clsDsSelectUrl.prototype =
{
	setDsValue : function(obj, value)
	{
		if (value != undefined) obj.value = value;
		if (obj.p.dsObj != undefined)
		{
			var dsObj = $(obj.p.dsObj);
			var pos = (obj.row == undefined) ? dsObj.DSpos : obj.row;
			var a = 0;
			if (pos < 0) dsObj.DSresult[pos][obj.p.dsItem] = Array();
			var itemvalue = Array();
			var length = obj.options.length;
			for (var i = 0; i < length; i++) if (obj.options[i].selected) itemvalue[a++] = obj.options[i].value;
			dsObj.DSresult[pos][obj.p.dsItem] = itemvalue.join(",");
			dsObj.DSposalter = pos;
			DS.dschange(dsObj);
		}
	},

	getDsValue : function(id)
	{
		var dsselectObj = $(id);
		if(dsselectObj.lock == true) return;
		var dsObjName = (dsselectObj.p.dsObj == undefined) ? dsselectObj.p.dsObjList : dsselectObj.p.dsObj;
		var dsObj = $(dsObjName);
		if (dsObj.DSposMemory > 0)
		{
			dsselectObj.selectedIndex = dsObj.DSposMemory - 1;
			return;
		} 
		else dsselectObj.selectedIndex = -1;
		if (dsselectObj.p.disabled != true && dsselectObj.p.disabled != 'true')
		{
			dsselectObj.disabled = (dsObj.DSpos != 0) ? false : true;
			if (dsselectObj.p.dsObj == undefined) dsselectObj.disabled = false;
		}
		if (dsObj.DSresult == undefined) return;
		if (dsObj.DSresult.length == 0) return;
		var itemrow = (dsselectObj.p.dsItem == undefined) ? dsselectObj.p.dsItemList : dsselectObj.p.dsItem;
		var row = (dsselectObj.row == undefined) ? dsObj.DSpos : dsselectObj.row;
		if (dsObj.DSresult[row] == undefined) return;
		var itemvalue = (dsObj.DSresult[row][itemrow] == undefined) ? "" : dsObj.DSresult[row][itemrow];

		if (dsselectObj.p.dsObj == undefined) itemvalue = dsselectObj.p.value;
		dsselectObj.value = itemvalue;

		if (dsselectObj.multiple == true && itemvalue != null)
		{
			itemvalue = itemvalue.split(",");
			var length = dsselectObj.options.length;
			for (var i = 0; i < length; i++)
			{
				dsselectObj.options[i].selected = false;
				var length1 = itemvalue.length;
				for (var ii = 0; ii < length1; ii++)
				{
					if (dsselectObj.options[i].value == itemvalue[ii]) 
					{
						dsselectObj.options[i].selected = true;
						//Icon
						if (dsselectObj.p.directory != undefined) 
						{
							dsselectObj.style.background = dsselectObj.options[i].style.background;	
							dsselectObj.style.backgroundRepeat = "no-repeat";
						}
						break;
					}
				}
			}
		}

		//Icon
		if (dsselectObj.p.directory != undefined) 
		{
			var idx = dsselectObj.selectedIndex;
 			dsselectObj.style.backgroundImage = (idx>-1) ? dsselectObj.options[idx].style.backgroundImage : "";
			if (this.imgIcon) this.imgIcon.src = (idx>-1) ? dsselectObj.options[idx].filepath : "";
		}

		if (dsselectObj.p.outlabel == 'label') 
		{
			var label = dsselectObj.nextSibling;
 			label.innerHTML = ((dsselectObj.selectedIndex + 1) > 0) ? dsselectObj.options[dsselectObj.selectedIndex].text : "";
		}
		
		if (dsselectObj.p.outlabel == 'url') 
		{
			var label = dsselectObj.nextSibling;
 			label.innerHTML = ((dsselectObj.selectedIndex + 1) > 0) ? "<a href=\"" + dsselectObj.p.action +"?"+ dsselectObj.p.actionparam+"="+dsselectObj.value+"\" target=\"" + dsselectObj.p.target + "\">" + dsselectObj.options[dsselectObj.selectedIndex].text + "</a>" : "";
		}
		
	if (dsselectObj.p.dsObjList!=undefined && dsselectObj.p.dsNav) DSSELECT.setPosDSList(dsselectObj);
	},

	refreshObj : function(id)
	{
		var dsselectObj = $(id);
		var row = (dsselectObj.row==undefined) ? 0 : dsselectObj.row;
		if (dsselectObj.p.disabled == undefined) dsselectObj.p.disabled = dsselectObj.disabled;
		if (dsselectObj.p.dsObjList != undefined && dsselectObj.p.customvalue == null && row<2)
		{
			dsselectObj.lock = false;
			var dsObj = $(dsselectObj.p.dsObjList);
			if (dsselectObj.p.dsObj != undefined ) var dsObjRow = $(dsselectObj.p.dsObj);
			var dsitem = dsselectObj.p.dsItemList;
			var dsitemkey = (dsselectObj.p.dsItemKeyList == undefined) ? dsselectObj.p.dsItemList : dsselectObj.p.dsItemKeyList;
			dsselectObj.options.length = 0;
			if (dsObj.DSresult.length > 1) dsselectObj.options.length = dsObj.DSresult.length - 1;
			if (dsselectObj.p.directory != undefined) //Icon
			{
				var directory = dsselectObj.p.directory;
				if (navigator.userAgent.indexOf("MSIE")>-1) 
				{
					if (!this.imgIcon) 
					{
						this.imgIcon = document.createElement('img');
						dsselectObj.parentNode.insertBefore(this.imgIcon, dsselectObj);
					}
					var img = "";	
					this.imgIcon.src = directory+"/"+img;	
				}
				for (var i = 1; i <= dsObj.DSrow; i++)
				{
					dsselectObj.options[i-1] = new Option(dsObj.DSresult[i][dsitem],dsObj.DSresult[i][dsitemkey]);
					dsselectObj.options[i-1].className = dsselectObj.className;
					dsselectObj.options[i-1].filepath = directory+"/"+dsObj.DSresult[i]["img"];
					dsselectObj.options[i-1].style.backgroundImage = "url('"+dsselectObj.options[i-1].filepath+"')";	
				}
			}
			else 
			{
				dsselectObj.options.length = 0;
				var items = dsitem.split(",");
 				if (dsselectObj.p.allselect != undefined) dsselectObj.options[dsselectObj.options.length] = new Option(dsselectObj.p.allselect,"");
				if (dsselectObj.p.valuezero != undefined) dsselectObj.options[dsselectObj.options.length] = new Option(dsselectObj.p.valuezero, 0);
				var len = dsObj.DSresult.length;
				for (var i = 1; i < len; i++)
				{
					var itemvalue = Array();
					for (var k in items)
					{
						if(items.hasOwnProperty(k)) //Bug Fix
						{
							itemvalue[k] = (dsselectObj.p.format == undefined) ? dsObj.DSresult[i][items[k]] : FORMAT.Format(dsObj.DSresult[i][items[k]], dsselectObj.p.format);
						}
					}
 					dsselectObj.options[dsselectObj.options.length] = new Option(itemvalue.join(" - "),dsObj.DSresult[i][dsitemkey]);
					var option = new Option(itemvalue.join(" - "),dsObj.DSresult[i][dsitemkey]);
				}
			}
		}
		this.getDsValue(id);
		dsselectObj.lock = false;
	},

	setPosDSList : function(obj)
	{
		var dsObj = $(obj.p.dsObjList);
		var old = obj.lock;
		var i = 1;
		if (obj.p.allselect != undefined) i--;
		if (obj.p.valuezero != undefined) i--;
		if ((obj.selectedIndex + i)>0)
		{
			if (i>1 && (dsObj.DSpos == parseInt(obj.selectedIndex + i))) return;
			obj.lock = true;
			dsObj.DSpre = dsObj.DSpos;
			dsObj.DSpos = parseInt(obj.selectedIndex + i);
			if (obj.p.memory != null) dsObj.DSposMemory = dsObj.DSpos;
			eval(obj.p.dsObjList + "Move()");
			obj.lock = old;
		}
	},

	change : function(obj)
	{
		if (obj.p.dsNav == true) this.setPosDSList(obj);
		this.setDsValue(obj);
	}
}

var DSSELECTURL = new clsDsSelectUrl();
