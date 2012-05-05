/**
* Class LABEL
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsEditorPdf()
{
}

clsEditorPdf.prototype = 
{
	makepage : function(obj)
	{
		obj.sheet = document.createElement("div");
		obj.sheet.w = 219;
		obj.sheet.h = 297;

		/*if (obj.p.pageformat=="A3")
		{
			var w = 0;
			var h = 0;
		}*/

		if (obj.p.orientation=="L")
		{
			var hw = obj.sheet.w;
			var hh = obj.sheet.h;
			obj.sheet.h = hw;
			obj.sheet.w = hh;
		}
 
		obj.sheet.setAttribute("class", "sheet");
		obj.sheet.style.width = obj.sheet.w-10+"mm";
		obj.sheet.style.height = obj.sheet.h-10+"mm";
		obj.appendChild(obj.sheet);
		
		obj.sheet.margin = document.createElement("div");
		obj.sheet.margin.setAttribute("class", "margin");
		obj.sheet.margin.style.width = obj.sheet.w-20+"mm";
		obj.sheet.margin.style.height = obj.sheet.h-20+"mm";
		obj.sheet.appendChild(obj.sheet.margin);

		obj.setting = document.createElement("div");
		obj.setting.setAttribute("class", "setting");
		obj.setting.style.top = obj.offsetTop+10+"px";
		obj.setting.style.left = obj.offsetLeft+10+"px";
		obj.appendChild(obj.setting);

		obj.sheet.setAttribute("onclick", "EDITORPDF.setting('','"+obj.id+"',event);");

		this.centerSheet(obj);
		this.writeRuler(obj);
		this.loadData(obj);
	},

	loadData : function(obj)
	{
		var dsObj = $(obj.p.dsObj);
		for (var i in dsObj.DSresult) if (dsObj.DSresult.hasOwnProperty(i)) this.addObj(obj, dsObj.DSresult[i]);
	},
	
	addObj : function(obj, data)
	{
		var typeObj = "";
/*		switch(parseInt(data['dsitemobject']))
		{
			case 1: //Cella
				typeObj = "div";
			break;
		}
		*/
		
		var objnew = document.createElement("div");
		objnew.setAttribute("class", "multicell");
		if(data[obj.p.dsitemborder]=="1") objnew.style.border="0.2mm solid black";
		objnew.innerHTML = data[obj.p.dsitemtext];
		objnew.style.left = (data[obj.p.dsitemx]-5)+"mm";
		objnew.style.top = (data[obj.p.dsitemy]-10)+"mm";
		objnew.style.paddingLeft = "1mm";
		objnew.style.paddingRight = "1mm";
		objnew.style.width = data[obj.p.dsitemwidth]-2+"mm";
		var align = "";
		if (data[obj.p.dsitemalign]=="1") align="left";
		objnew.style.textAlign = align;
		objnew.style.fontFamily = data[obj.p.dsitemfont];
		objnew.style.fontSize = data[obj.p.dsitemfontsize]+"pt";
		var height = (data[obj.p.dsitemheight]==0) ? data[obj.p.dsitemfontsize]+"pt" : data[obj.p.dsitemheight]+"mm";
		objnew.style.minHeight = height;
		objnew.key = data[obj.p.dsitemkey];
		objnew.setAttribute("onclick","EDITORPDF.setting(this,'',event);");
		obj.sheet.margin.appendChild(objnew);
	},
	
	writeRuler : function(obj)
	{
		obj.rulerX = document.createElement("div");
		obj.rulerX.setAttribute("class", "rulerX");
		obj.rulerX.style.height = "1px"; 
		obj.appendChild(obj.rulerX);

		obj.rulerY = document.createElement("div");
		obj.rulerY.setAttribute("class", "rulerY");
		obj.rulerY.style.width = "1px";
		obj.rulerY.style.top = obj.offsetTop+"px";
		obj.rulerY.style.height = obj.offsetHeight+"px";
		obj.appendChild(obj.rulerY);

		obj.setAttribute("onmousemove", "EDITORPDF.RulerXY(this, event);");
	},
	
	createOBJ : function(id, type, x, y)
	{
		var obj = $(id);
		var dsObj = $(obj.p.dsObj);
		DS.dsnew(obj.p.dsObj);
		var row = dsObj.DSresult[dsObj.DSpos];
		row[obj.p.dsitemobject]=type.value.toString;
		row[obj.p.dsitemtext]='Testo';
		row[obj.p.dsitemwidth]='100';
		//Da convertire in mm nella posizione giusta
		x=10;
		y=10;
		row[obj.p.dsitemx]=x;
		row[obj.p.dsitemy]=y;
		row[obj.p.dsitemfont]="Arial";
		row[obj.p.dsitemfontsize]="10";
		DS.dschange(dsObj);
		this.addObj(obj, dsObj.DSresult[dsObj.DSpos]);
	},
	
	setting : function(obj,objE, event)
	{
		if (obj == '')
		{
			var editor = $(objE);
			var code = "<h1>OGGETTO?</h1>";
			code += "<select onchange=\"EDITORPDF.createOBJ('"+objE+"', this, "+event.clientX+","+event.clientY+");\"><option checked=\"true\"></option><option value=\"1\">Multi Cell</select>";
			editor.setting.innerHTML = code;
			editor.setting.style.display = "block";
		} 
		else 
		{
			var setting = obj.parentElement.parentElement.parentElement.setting;
			var h = parseFloat(obj.style.height);
			var w = parseFloat(obj.style.width);
			if(isNaN(h)) h="";
			if(isNaN(w)) w="";
			var code = "<h1>OGGETTO: "+obj.className+"</h1>";
			code += "<table width=\"223\">";
			code += "<tr><td>Y:</td><td width=\"223px\" align=\"right\"><button onclick=\"EDITORPDF.settingTOP(this,'-');\">-</button><input type=\"text\" size=\"3\" maxlength=\"3\" id=\"setting-top\" onchange=\"EDITORPDF.settingTOP(this,'');\" value=\""+parseFloat(obj.style.top)+"\">mm<button onclick=\"EDITORPDF.settingTOP(this,'+');\">+</button></td></tr>";
			code += "<tr><td>X:</td><td align=\"right\"><button onclick=\"EDITORPDF.settingLEFT(this,'-');\">-</button><input type=\"text\" size=\"3\" maxlength=\"3\" id=\"setting-left\" onchange=\"EDITORPDF.settingLEFT(this,'');\" value=\""+parseFloat(obj.style.left)+"\">mm<button onclick=\"EDITORPDF.settingLEFT(this,'+');\">+</button></td></tr>";
			code += "<tr><td>W:</td><td width=\"223px\" align=\"right\"><button onclick=\"EDITORPDF.settingWIDTH(this,'-');\">-</button><input type=\"text\" size=\"3\" maxlength=\"3\" id=\"setting-width\" onchange=\"EDITORPDF.settingWIDTH(this,'');\" value=\""+w+"\">mm<button onclick=\"EDITORPDF.settingWIDTH(this,'+');\">+</button></td></tr>";
			code += "<tr><td>H:</td><td align=\"right\"><button onclick=\"EDITORPDF.settingHEIGHT(this,'-');\">-</button><input type=\"text\" size=\"3\" maxlength=\"3\" id=\"setting-height\" onchange=\"EDITORPDF.settingHEIGHT(this,'');\" value=\""+h+"\">mm<button onclick=\"EDITORPDF.settingHEIGHT(this,'+');\">+</button></td></tr>";
			code += "<tr><td>Font:</td><td align=\"right\"><select style=\"width:147px\" id=\"setting-font\" onchange=\"EDITORPDF.settingFONT(this);\"style=\"width:162px;\"><option>Arial</option><option>Courier</option><option>Times</option><option>Symbol</option><option>ZapfDingbats</option></select></td></tr>";
			code += "<tr><td>Pt:</td><td align=\"right\"><button onclick=\"EDITORPDF.settingSIZE(this,'-');\">-</button><input type=\"text\" size=\"3\" maxlength=\"3\" id=\"setting-size\" onchange=\"EDITORPDF.settingSIZE(this,'');\" value=\""+parseFloat(obj.style.fontSize)+"\">mm<button onclick=\"EDITORPDF.settingSIZE(this,'+');\">+</button></td></tr>";
			code += "<tr><td>Testo:</td><td align=\"right\"><textarea style=\"width:138px;\" onkeyup=\"EDITORPDF.settingTEXT(this);\">"+obj.innerHTML+"</textarea></td></tr>";
			code += "<tr><td>Bordo:</td><td align=\"right\"><input type=\"checkbox\" maxlength=\"3\" id=\"setting-border\" onchange=\"EDITORPDF.settingBORDER(this);\"></td></tr>";
			code += "<tr><td></td><td align=\"right\"><input type=\"button\" maxlength=\"3\" onclick=\"EDITORPDF.settingDELETE(this);\" value=\"Elimina!\"></td></tr>";
			code += "</table>";
			setting.innerHTML = code;
			$('setting-font').value = obj.style.fontFamily;
			if(obj.style.border != '') $('setting-border').checked = true;
			setting.style.display = "block";
			setting.obj = obj;
		}
		if (!event) event = window.event;
		SYSTEMEVENT.stopPropagation(event);
	},
	
	settingDELETE : function(obj, oper)
	{
		var setting = obj.parentElement.parentElement.parentElement.parentElement.parentElement;
		var obj = setting.parentElement;
		DS.dsdelete(obj.p.dsObj);
		obj.sheet.margin.removeChild(setting.obj);
		setting.style.display = "none";
	},
	
	settingWIDTH : function(obj, oper)
	{
		var setting = obj.parentElement.parentElement.parentElement.parentElement.parentElement;
		var sheet = setting.parentElement.sheet;
		var val = parseFloat(setting.obj.style.width);
		if (oper=="-") val--;
		else if (oper=="+") val++;
		else val = parseFloat(obj.value);
		
		if(isNaN(val)) val="";
		else
		{
			if (val <0 ) val = 0;
			if ((val+20) > sheet.h) val = sheet.h-20; 
			setting.obj.style.width = val+"mm";
		}
		$('setting-width').value = val;
		var objE = sheet.parentNode;
		this.setDsValue(objE, objE.p.dsitemwidth, val);
	},

	settingHEIGHT : function(obj, oper)
	{
		var setting = obj.parentElement.parentElement.parentElement.parentElement.parentElement;
		var sheet = setting.parentElement.sheet;
		var val = parseFloat(setting.obj.style.height);
		if (oper=="-") val--;
		else if (oper=="+") val++;
		else val = parseFloat(obj.value);
		if(isNaN(val)) val="";
		else
		{
			if (val <0 ) val = 0;
			if ((val+20) > sheet.w) val = sheet.w-20; 
			setting.obj.style.height = val+"mm";
		}
		$('setting-height').value = val; 
		var objE = sheet.parentNode;
		this.setDsValue(objE, objE.p.dsitemheight, val);
	},

	settingTOP : function(obj, oper)
	{
		var setting = obj.parentElement.parentElement.parentElement.parentElement.parentElement;
		var sheet = setting.parentElement.sheet;
		var val = parseFloat(setting.obj.style.top);
		if (oper=="-") val--;
		else if (oper=="+") val++;
		else val = parseFloat(obj.value);
		if (val <0 ) val = 0;
		if ((val+20) > sheet.h) val = sheet.h-20; 
		setting.obj.style.top = val+"mm";
		$('setting-top').value = val; 
		var objE = sheet.parentNode;
		this.setDsValue(objE, objE.p.dsitemy, val+10);
	},

	settingLEFT : function(obj, oper)
	{
		var setting = obj.parentElement.parentElement.parentElement.parentElement.parentElement;
		var sheet = setting.parentElement.sheet;
		var val = parseFloat(setting.obj.style.left);
		if (oper=="-") val--;
		else if (oper=="+") val++;
		else val = parseFloat(obj.value);
		if (val <0 ) val = 0;
		if ((val+20) > sheet.w) val = sheet.w-20; 
		setting.obj.style.left = val+"mm";
		$('setting-left').value = val; 
		var objE = sheet.parentNode;
		this.setDsValue(objE, objE.p.dsitemx, val+5);
	},

	settingSIZE : function(obj, oper)
	{
		var setting = obj.parentElement.parentElement.parentElement.parentElement.parentElement;
		var sheet = setting.parentElement.sheet;
		var val = parseFloat(setting.obj.style.fontSize);
		if (oper=="-") val--;
		else if (oper=="+") val++;
		else val = parseFloat(obj.value);
		setting.obj.style.fontSize = val+"pt";
		$('setting-size').value = val; 
		var objE = sheet.parentNode;
		this.setDsValue(objE, objE.p.dsitemfontsize, val);
	},

	settingTEXT : function(obj)
	{
		var setting = obj.parentElement.parentElement.parentElement.parentElement.parentElement;
		var sheet = setting.parentElement.sheet;
		setting.obj.innerHTML = obj.value;
		var objE = sheet.parentNode;
		this.setDsValue(objE, objE.p.dsitemtext, obj.value);
	},

	settingFONT : function(obj)
	{
		var setting = obj.parentElement.parentElement.parentElement.parentElement.parentElement;
		var sheet = setting.parentElement.sheet;
		setting.obj.style.fontFamily = obj.value;
		var objE = sheet.parentNode;
		this.setDsValue(objE, objE.p.dsitemfont, val);
	},

	settingBORDER : function(obj)
	{
		var setting = obj.parentElement.parentElement.parentElement.parentElement.parentElement;
		var sheet = setting.parentElement.sheet;
		var val = 0;
		if (obj.checked == false) setting.obj.style.border = "";
		else 
		{
			setting.obj.style.border = "0.2mm solid black";
			val = 1;
		}
		var objE = sheet.parentNode;
		this.setDsValue(objE, objE.p.dsitemborder, val);
	},

	setDsValue : function (obj, dsitem, val)
	{
		var dsObj = $(obj.p.dsObj);
		dsObj.DSresult[dsObj.DSpos][dsitem] = val.toString();
		DS.dschange(dsObj);
	},
		
	RulerXY : function (obj, event)
	{
		if (!event) event = window.event;
		if (event.ctrlKey==true)
		{
			obj.rulerX.style.display="block";
			obj.rulerY.style.display="block";
			obj.rulerX.style.top = (event.clientY) - 1 + "px";
			obj.rulerY.style.left = (event.clientX) - 1 + "px";
		} 
		else
		{
			obj.rulerX.style.display="none";
			obj.rulerY.style.display="none";			
		}
			
	},
	
	centerSheet : function(obj)
	{
		var paddingLeft = (obj.offsetWidth-obj.sheet.offsetWidth)/2;
		if (paddingLeft>0) obj.style.paddingLeft = paddingLeft + "px";
		else obj.sheet.style.marginLeft = "10mm";
	},
	
	refreshObj : function(id)
	{
		var obj = $(id);
		this.makepage(obj);
	}
}

var EDITORPDF = new clsEditorPdf();