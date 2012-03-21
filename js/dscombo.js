/**
* Class DSCOMBO
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsDscombo()
{
	this.lastds	= Array();
}

clsDscombo.prototype =
{
	getDsValue : function(id)
	{
		var dscomboObj = $(id);
		var dsObjRow = $(dscomboObj.p.dsObj);
		if (this.lastds[dscomboObj.p.dsObj] == undefined) this.lastds[dscomboObj.p.dsObj] = Array();
		if (this.lastds[dscomboObj.p.dsObj][dscomboObj.p.dsItem] == undefined) this.lastds[dscomboObj.p.dsObj][dscomboObj.p.dsItem] = Array("", "");

		dscomboObj.readOnly = (dsObjRow.DSpos != 0) ? false : true;
		if (dsObjRow.DSresult.length == 0)
		{
			dscomboObj.value = "";
			return;
		}
		var row = (dscomboObj.row == undefined) ? dsObjRow.DSpos : dscomboObj.row;
 		var valueDs = (dsObjRow.DSresult[row][dscomboObj.p.dsItem] == undefined) ? "" : dsObjRow.DSresult[row][dscomboObj.p.dsItem];
		if (row < 0) dscomboObj.value = "";
 		var change = (dscomboObj.valuekey == valueDs) ? false : true;
		if (change == false) 
		{
			if (dscomboObj.valuekey == this.lastds[dscomboObj.p.dsObj][dscomboObj.p.dsItem][0]) dscomboObj.value = this.lastds[dscomboObj.p.dsObj][dscomboObj.p.dsItem][1];
			return;
		}
		dscomboObj.valuekey = valueDs;
		
		var dsObj = $(dscomboObj.p.dsObjList);
		if (dscomboObj.valuekey == this.lastds[dscomboObj.p.dsObj][dscomboObj.p.dsItem][0]) dscomboObj.value = this.lastds[dscomboObj.p.dsObj][dscomboObj.p.dsItem][1];
		else
		{
			if (dscomboObj.valuekey == "" || dsObjRow.DSpos < 0) 
			{
			    dscomboObj.value = '';
			    return;
			}
			var post = "data=load&dsobjname=" + dscomboObj.p.dsObjList;
			post += "&dsforeignkey=" + encodeURIComponent(dscomboObj.p.dsItemKeyList) + "&dsforeignkeyvalue=" + encodeURIComponent(dscomboObj.valuekey);
			var loadall = AJAX.loadall;
			AJAX.request("POST", dsObj.p.DSaction, post, true, true);
			AJAX.loadall = loadall;
			if (dsObj.DSresult.length == 0) var valueDs = "";
			else 
			{
				var valueDs = new Array();
				var item = dscomboObj.p.dsItemList.split(",");
				var itemlength = item.length;
				for (var i = 0; i < itemlength; i++) valueDs[i] = dsObj.DSresult[1][item[i]];
				valueDs = valueDs.join(" - ");
			}
 			dscomboObj.value = valueDs;
		}

		this.lastds[dscomboObj.p.dsObj][dscomboObj.p.dsItem][0] = dscomboObj.valuekey;
		this.lastds[dscomboObj.p.dsObj][dscomboObj.p.dsItem][1] = dscomboObj.value;
		if (dscomboObj.p.outlabel != null) dscomboObj.innerHTML = dscomboObj.value;
	},

	getDsValueLabel : function(id)
	{
		var dscomboObj = $(id);
		var dsObjRow = $(dscomboObj.p.dsObj);
		dscomboObj.value = "";
		dscomboObj.readOnly = (dsObjRow.DSpos != 0) ? false : true;
		if (dsObjRow.DSresult.length == 0) return;
		var row = (dscomboObj.row == undefined) ? dsObjRow.DSpos : dscomboObj.row;
		var item = dscomboObj.p.dsItemLabel.split(",");
		var valueDs = new Array();
		var itemlength = item.length;
		for (var i = 0; i < itemlength; i++) valueDs[i] = (dsObjRow.DSresult[row][item[i]] == undefined) ? "" : dsObjRow.DSresult[row][item[i]];
		valueDs = valueDs.join(" - ");
		dscomboObj.value = valueDs;
	},

	refreshObj : function(id)
	{
		this.getDsValue(id);
	},

	delaySearch : function(e, obj)
	{
		if (obj.readOnly) return;
		var keynum = e.keyCode;
		if(window.event) keynum = e.keyCode;
		else if(e.which) keynum = e.which;
		if (keynum == 13) this.searchDsValue(obj);
		else obj.searchchange = true;
		if (obj.value == "")
		{
			 obj.valuekey = "";
			 var dsObj = $(obj.p.dsObj);
			 if (dsObj != undefined) 
			 {
				dsObj.DSresult[dsObj.DSpos][obj.p.dsItem] = "";
				this.lastds[obj.p.dsObj][obj.p.dsItem] = Array("", "");
				DS.dschange(dsObj);
			 }
		}
	},

	setDsValue : function(dscomboObj, dsObj, dsObjPos)
	{
		var item = dscomboObj.p.dsItemList.split(",");
		var itemvalue = new Array();
		var itemlength = item.length;
		for (var i = 0; i<itemlength; i++) itemvalue[i] = dsObj.DSresult[dsObjPos][item[i]];
		dscomboObj.value = (dscomboObj.p.format == undefined) ? itemvalue.join(" - ") : FORMAT.Format(itemvalue.join(" - "), dscomboObj.p.format);
		dscomboObj.valuekey = dsObj.DSresult[dsObjPos][dscomboObj.p.dsItemKeyList];
		var dsObjRow = $(dscomboObj.p.dsObj);
		if (dsObjRow != undefined)
		{
			if (dscomboObj.p.dsItemLabel == undefined) this.lastds[dscomboObj.p.dsObj][dscomboObj.p.dsItem] = Array(dscomboObj.valuekey, dscomboObj.value);
			var row = (dscomboObj.row == undefined) ? dsObjRow.DSpos : dscomboObj.row;
			dsObjRow.DSresult[row][dscomboObj.p.dsItem] = dscomboObj.valuekey;
			dsObj.DSposalter = row;
			DS.dschange(dsObjRow);
			if (dscomboObj.p.dsItemLabel != undefined)
			{
				var itemlabel = dscomboObj.p.dsItemLabel.split(",");
				var itemlength = itemlabel.length;
				for (var i = 0; i < itemlength; i++) dsObjRow.DSresult[row][itemlabel[i]] = (itemvalue[i] == undefined) ? "" : itemvalue[i];
			}
		}
	},

	searchDsValue : function(obj)
	{
		if (obj.searchchange == false) return;
		obj.searchchange = false;
		var dsObj = $(obj.p.dsObjList);
		var post = "data=load&dsobjname=" + obj.p.dsObjList;
		var dssearch = obj.p.dsSearch.replaceAll('$$VALUE$$',obj.value.replace(/'/g,"\\\'"));
		do
		{
			var id = RegExp("\\$\\$ID_[a-zA-Z0-9]+").exec(dssearch);
			if (id != null) dssearch = dssearch.replace(id[0] + "$$", $(id[0].replace("$$ID_","")).value);
		}
		while (id != null);

		post += "&" + obj.p.dsObjList + "where=" + encodeURIComponent(dssearch);

		if(dsObj.p.DSreferences==undefined) AJAX.request("POST", dsObj.p.DSaction, post, true, true);
		else
		{
			var old = obj.DSsearch;
			dsObj.DSsearch = dssearch;
			AJAX.dslink(dsObj.id)
			obj.DSsearch = old;
		}

		if (dsObj.DSresult.length == 2)
		{
			this.setDsValue(obj, dsObj, 1);
			obj.select();
		}
		if (obj.expanded == true) this.showValues(obj);
		else if (dsObj.DSresult.length > 2) this.showValues(obj);
	},

	selectDsValue : function(id, pos, event)
	{
		SYSTEMEVENT.preventDefault(event);
		SYSTEMEVENT.stopPropagation(event);

		var dscomboObj = $(id);
		var dsObj = $(dscomboObj.p.dsObjList);
		dsObj.DSpos = pos;
		if (dsObj.DSresult.length==0) return;
		this.setDsValue(dscomboObj, dsObj, pos);
		this.collapse(dscomboObj);
	},

	expand : function(dscomboObj)
	{
		while (dscomboObj.tagName != "INPUT")
		{
			dscomboObj = dscomboObj.previousSibling;
		}
		if (dscomboObj.readOnly) return;
		if (dscomboObj.expanded == true) this.collapse(dscomboObj);
		else
		{
			 this.searchDsValue(dscomboObj);
			 this.showValues(dscomboObj);
		}
	},

	collapse : function(obj)
	{
		var resultObj = obj.nextSibling;
		while(resultObj.tagName != "DIV")
		{
			resultObj = resultObj.nextSibling;
		}
		resultObj.style.display = "none";
		obj.expanded = false;
	},

	showValues : function(obj)
	{
		var resultObj = obj.nextSibling;
		while (resultObj.tagName != "DIV")
		{
			resultObj = resultObj.nextSibling;
		}
		resultObj.innerHTML = "&nbsp;";
		var dsObj = $(obj.p.dsObjList);

		if (dsObj.DSresult != undefined)
		{
			var html = "<table width=\"100%\">";
			var length = dsObj.DSresult.length;
			for (var i = 1; i < length; i++)
			{
				html += "<tr onclick=\"javascript:DSCOMBO.selectDsValue('" + obj.id + "', '" + i + "', event);\"><td nowrap>";
				var item = obj.p.dsItemList.split(",");
				var itemvalue = new Array();
				var itemlength = item.length;
				for (var ii = 0; ii<itemlength; ii++) 
				{
					itemvalue[ii] = (obj.p.format == undefined) ? dsObj.DSresult[i][item[ii]] : FORMAT.Format(dsObj.DSresult[i][item[ii]], obj.p.format);
				}
 				html += itemvalue.join(" - ");
				html += "</td></tr>";
			}
			html += "</table>";
			resultObj.innerHTML = html;
		}
		
		var parentObj = obj;
		var offLeft = obj.offsetLeft;
		var offTop = obj.offsetTop;
		while (parentObj.offsetParent != undefined)
		{
			parentObj = parentObj.offsetParent;
			if (parentObj.style.position != "relative" && parentObj.style.position != "absolute")
			{
				offLeft += parentObj.offsetLeft;
				offTop += parentObj.offsetTop;
			}
		}
		var parentObj = obj;
		var scrollTop = obj.scrollTop;
		while (parentObj.offsetParent != undefined && parentObj.scrollTop == 0)
		{
			parentObj = parentObj.parentNode;
			scrollTop += parentObj.scrollTop;
		}
		resultObj.style.left = offLeft + "px";
		scrollTop = isNaN(scrollTop) ? 0 : scrollTop;
		resultObj.style.top = offTop + obj.offsetHeight - scrollTop + "px";
		resultObj.style.height = "200px" 
		if (parseInt(resultObj.style.top) + 200  >=  window.innerHeight) resultObj.style.top = parseInt(resultObj.style.top) - 200 - obj.clientHeight + "px";
		resultObj.style.width = obj.clientWidth + "px";
		resultObj.style.display = "";
		obj.expanded = true;
	}
}

var DSCOMBO = new clsDscombo();