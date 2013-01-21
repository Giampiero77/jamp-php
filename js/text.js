/**
* Class TEXT
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsText()
{
	this.objposted = null;
}

clsText.prototype =
{
	setDsValue : function(obj, value)
	{
		var dsObj = $(obj.p.dsObj);
		if (value != undefined) obj.value = value;
		var pos = (obj.row == undefined) ? dsObj.DSpos : obj.row;
		if (pos == -1) dsObj.DSresult[pos][obj.p.dsItem] = Array();
		if (obj.p.format == undefined) dsObj.DSresult[pos][obj.p.dsItem] = obj.value;
		else
		{
			dsObj.DSresult[pos][obj.p.dsItem] = obj.value;
			FORMAT.unformat(obj, dsObj, pos);
 			FORMAT.format(obj, dsObj.DSresult[pos][obj.p.dsItem]);
		}
		dsObj.DSposalter = pos;
		DS.dschange(dsObj);
	},

	checkDsValue : function(obj)
	{
		if (obj.oldValue != obj.value) DS.dschangeNoLive($(obj.p.dsObj));
	},

	getDsValue : function(id)
	{
		var valueDs;
		var textObj = $(id);
		var dsObj = $(textObj.p.dsObj);
		if (textObj.p.InitreadOnly == undefined) textObj.p.InitreadOnly = textObj.readOnly;
		if (dsObj != null)
		{
			if (textObj.p.InitreadOnly == false) textObj.readOnly = (dsObj.DSpos != 0) ? false : true;
			if (dsObj.DSresult.length == 0) 
			{
				textObj.value = '';
				return;
			}
			var row = (textObj.row == undefined) ? dsObj.DSpos : textObj.row;
			if (row < 0 && textObj.p.defaultvalue != null) TEXT.setDsValue(textObj, textObj.p.defaultvalue);
			valueDs = (dsObj.DSresult[row][textObj.p.dsItem] == undefined) ? "" : dsObj.DSresult[row][textObj.p.dsItem];
		}
		else valueDs = textObj.value;
		if (textObj.p.format == undefined) textObj.value = valueDs;
		else FORMAT.format(textObj, valueDs);
		textObj.oldValue = textObj.value;
	},

	refreshObj : function(id)
	{
		var textObj = $(id);
		textObj.oldValue = "";
		this.getDsValue(id);
	},
	
 	setSelect : function(id, selectObj, valueStr)
	{
		selectObj.options.lenght = 0;
		if (valueStr == undefined) return;
		var arrayStr = valueStr.split(',');
		for (var i = 0; i < arrayStr.length; i++)
		{
		      selectObj.options[i] = new Option(arrayStr[i], arrayStr[i]);
		      selectObj.options[i].selected = true;
		}
	},

	postObj : function(id)
	{
		var filename = $(id+'_file').value;
		if (filename == '') return;
		AJAX.loader(true);
		TEXT.objposted = $(id);
		TEXT.dimension = $(id+'_dimension');
		var formObj = this.objposted.parentNode;
		if (TEXT.objposted.p.format != undefined)
		{
			 var posSlash = filename.lastIndexOf('/')+1; 		        		// client Windows  
			 if (posSlash==0) posSlash = filename.lastIndexOf('\\')+1;   	// client Unix/Linux  
			 filename = filename.substr(posSlash);
			 filename = filename.substr(0, filename.lastIndexOf('.'));
			 if (TEXT.objposted.p.format) formObj.forcename.value = FORMAT.Format(filename, TEXT.objposted.p.format);
		}
		TEXT.setSelect(id, TEXT.dimension, TEXT.objposted.p.dimension);
		formObj.submit();
	},

	AfterPost : function(ris, mes, newfilename)
	{
		if (ris) 
		{
			var filename = newfilename;
			if (newfilename == '')
			{
			    var posSlash = $(TEXT.objposted.id+'_file').value.lastIndexOf('/')+1; 		        	// client Windows  
			    if (posSlash==0) posSlash = $(TEXT.objposted.id+'_file').value.lastIndexOf('\\')+1;   	// client Unix/Linux  
			    filename = $(TEXT.objposted.id+'_file').value.substr(posSlash);
			}
			TEXT.objposted.value = filename;
			if (TEXT.objposted.p.dsObj != null) TEXT.setDsValue(TEXT.objposted);
		} 
		else SYSTEMEVENT.showMessage(mes, LANG.translate("TEXT001"), self.location.href, 0, 80);
		AJAX.loader(false);
	},

	AutoComplete : function(e, obj)
	{
		if (obj.readOnly || obj.oldValue == obj.value) return;

		var keynum = e.keyCode;
		if(window.event) keynum = e.keyCode;
		else if (e.which) keynum = e.which;
		var dsObj = $(obj.p.dsObjList);
		if (keynum == 27)
		{
			if (obj.p.focus == true) 
			{
				obj.value = obj.p.valueauto;
				this.hideValues(obj);
			}
			return;
		} else if (keynum == 37 || keynum == 39 || keynum == 36 || keynum == 35) return;
		if (dsObj.DSresult != undefined && dsObj.DSresult.length>0)
		{
			if (keynum == 38)
			{
				if (dsObj.DSpos == 0)
				{
					dsObj.DSpre = 1;
					dsObj.DSpos = 1;
					dsObj.handleResponse = "TEXT.showValues($('"+obj.id+"'));";
					eval(dsObj.id + "MovePrev();");
					this.selectRow(obj.id, 1, 0);
				}
				else if (dsObj.DSpos == 1)
				{
					this.selectRow(obj.id, 0, 0);
					dsObj.DSpre = 0;
					dsObj.DSpos = 0;
				}
				else 
				{
					dsObj.handleResponse = "TEXT.showValues($('"+obj.id+"'));";
					eval(dsObj.id + "MovePrev();");
					this.selectRow(obj.id);
				}
				return;
			}
			else if (keynum == 40)
			{
				if (dsObj.DSpos == 0)
				{
					this.selectRow(obj.id, 0, 1);
					dsObj.DSpre = 1;
					dsObj.DSpos = 1;
				}
				else 
				{
					dsObj.handleResponse = "TEXT.showValues($('"+obj.id+"'));";
					eval(dsObj.id + "MoveNext();");
					this.selectRow(obj.id);
				}
				return;
			}
			else if (keynum == 13)
			{
				this.selectValue(obj.id);
				return;
			}
			else if (keynum == 9)
			{
				return;
			}
		}
		var post = "data=load&dsobjname=" + obj.p.dsObjList;
		dsObj.DSsearch = obj.p.dsSearch.replaceAll('$$VALUE$$',obj.value.replace(/'/g,"\\\'"));
		do
		{
			var id = RegExp("\\$\\$ID_[a-zA-Z0-9]+").exec(dsObj.DSsearch);
			if (id != null) dsObj.DSsearch = dsObj.DSsearch.replace(id[0] + "$$", $(id[0].replace("$$ID_","")).value);
		}
		while (id != null);
		if (obj.p.minsearch == null || obj.value.length >= obj.p.minsearch)
		{
			if (obj.p.dsObj != undefined) $(obj.p.dsObj).lock = true;
			var hideloader = AJAX.hideloader;
			AJAX.hideloader = true;
			AJAX.dsmore(dsObj, post, false, "TEXT.showValues($('"+obj.id+"'));");
			AJAX.hideloader = hideloader;
			obj.p.valueauto = obj.value;
		}
	},

	selectRow : function(objname, pre, pos)
	{
		var obj = $(objname);
		if (obj.p.focus != true) return
		var dsObj = $(obj.p.dsObjList);
		var rows = $(obj.id + "_result_table");
		rows = rows.rows;
		pre = (pre == undefined) ? dsObj.DSpre : pre;
		pos = (pos == undefined) ? dsObj.DSpos : pos;
		if (pre > 0) rows[pre-1].className = "";
		if (pos > 0)
		{
			rows[pos-1].className = "autocomplete_tr";
			obj.value = dsObj.DSresult[pos][obj.p.dsItemList];
		}
		else obj.value = obj.p.valueauto;
	},
	
	hideValues : function(obj)
	{
		if (obj.p.focus == true)
		{
			var resultObj = obj.nextSibling;
			while(resultObj.tagName != "DIV")
			{
				resultObj = resultObj.nextSibling;
			}
			resultObj.style.display = "none";
		}
		if (obj.p.dsObj != undefined) $(obj.p.dsObj).lock = false;
		obj.p.focus = false;
	},

	lostFocus : function(e, obj)
	{
		if (obj.p.savesearch == true)
		{
			var dsObj = $(obj.p.dsObjList);
			if (obj.value!='' && dsObj.DSrow == 0 && (obj.p.minsearch == null || obj.value.length >= obj.p.minsearch))
			{
				var hideloader = AJAX.hideloader;
				AJAX.hideloader = true;
				var post = 'data=new_update&dsobjname=' + dsObj.id;
				post += '&keyname=' + encodeURIComponent(obj.p.dsItemList);
				post += '&keynamevalue=' + encodeURIComponent(obj.value);
				post += '&' + obj.p.dsItemList + "=" + encodeURIComponent(obj.value); 
				AJAX.request('POST', dsObj.p.DSaction, post, true, false);
				AJAX.hideloader = hideloader;
			}
		}
	},

	showValues : function(obj)
	{   
		obj.p.focus = true;  
		var dsObj = $(obj.p.dsObjList);
		var resultObj = obj.nextSibling;
		while(resultObj.tagName != "DIV")
		{
			resultObj = resultObj.nextSibling;
		}
		resultObj.innerHTML = "&nbsp;";
		if (dsObj.DSresult != undefined && dsObj.DSresult.length==0) 
		{
			TEXT.hideValues(obj);
			return;
		}

		var html = "<table width=\"100%\" id=\"" + obj.id + "_result_table\">";
		var length = dsObj.DSresult.length;
		for (var i = 1; i < length; i++)
		{
			var item = obj.p.dsItemList.split(",");
			var itemvalue = new Array();
			var itemlength = item.length;
			for (var ii = 0; ii<itemlength; ii++) itemvalue[ii] = dsObj.DSresult[i][item[ii]];
			var txtrow = itemvalue.join(" - ");
			var val = (obj.p.valueauto == undefined) ? obj.value : obj.p.valueauto;
			var find = RegExp(val, "i").exec(txtrow);
			html += "<tr onclick=\"javascript:TEXT.selectValue('" + obj.id + "', '"+ txtrow + "');\"><td nowrap>";
			html += txtrow.replace(find, find+"<b>") + "</b>";
			html += "</td></tr>";
		}
		html += "</table>";
		resultObj.innerHTML = html;
	
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
		resultObj.style.height = "200px";
		if (parseInt(resultObj.style.top) + 200  >=  window.innerHeight) resultObj.style.top = parseInt(resultObj.style.top) - 200 - obj.clientHeight + "px";
		resultObj.style.width = obj.clientWidth + "px";
		resultObj.style.display = "";
		dsObj.DSpos = 0;
		dsObj.handleResponse = undefined;
		this.selectRow(obj.id);
	},

	selectValue : function(id, value)
	{
		var obj = $(id);
		var dsObj = $(obj.p.dsObj);
		if (value != undefined) 
		{
			 if (dsObj != undefined) TEXT.setDsValue(obj, value);
			 else obj.value = value;   
		}
		TEXT.hideValues(obj);
	}
};

var TEXT = new clsText();