/**
* Class CALENDAR
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsCalendar()
{
	this.arr_months = null;
	this.arr_days = null;
	this.weekstart = null;
	this.html = "";
	this.dsObj = null;
	this.obj = null;
	this.day = null;
	this.div = null;
	this.day = null;
	this.time = null;
	this.datetime = null;
	this.className = null;
	this.testdatatime = "[hHisKk]";
	if (SYSTEMBROWSER.isIE() && SYSTEMBROWSER.getVersion() <= 6)
 	{
		this.iframe = document.createElement('iframe');
		this.iframe.id = "frame-calendar";
 	}
}

clsCalendar.prototype = 
{
	getFormat : function()
	{
		var reg = this.obj.p.format.split("|");
		var lang = reg[3];
		this.arr_months = FORMAT.intSetting[lang]['month'];
		this.arr_days = FORMAT.intSetting[lang]['day'];
		this.weekstart = FORMAT.intSetting[lang]['weekstart'];
	},

	getWeekDay : function()
	{
		this.html += "\n\t\t<thead><td></td>";
		for (var n=0; n<7; n++) this.html += "\n\t\t\t<td class=\"weekday\">"+this.arr_days[(this.weekstart+n)%7]+"</td>";
		this.html += "\n\t\t</thead>";
	},

	getDsDay : function()
	{
		this.day = new Date();
		var reg = this.obj.p.format.split("|");
		reg[1]=reg[3]; reg[2]=reg[4];
		reg[3]="EN";  reg[4]="yyyy-mm-dd";
		this.datetime = (new RegExp(CALENDAR.testdatatime).exec(reg[2])!=null);
		var data = FORMAT.formatDate(this.obj.value, reg, false);
		if (data != "") 
		{
			var value = data.split("-");
			this.day = new Date(value[0], value[1]-1, value[2]);
			if (this.datetime)
			{
			    reg[4]="yyyy-mm-dd HH:ii:ss";
			    var time = FORMAT.formatDate(this.obj.value, reg, false);
			    this.time = time.split(" ")[1];
			}
		}
	},

	getMonthYear : function()
	{
		this.html += "\n\t<select onchange=\"javascript:CALENDAR.setMonth(this.value);\">";
		for(var i = 0; i < 12; i++)
		{
			this.html += (i == this.day.getMonth()) ? "\n\t\t<option value=\"" + i + "\" selected>" + this.arr_months[i] + "</option>" : "\n\t\t<option value=\"" + i + "\">" + this.arr_months[i] + "</option>";
		}
		this.html += "\n\t</select>";
		this.html += "\n\t<select onchange=\"javascript:CALENDAR.setYear(this.value);\">";
		for(var i = this.day.getFullYear() + 10; i > this.day.getFullYear() - 20; i--)
		{
			this.html += (i == this.day.getFullYear()) ? "\n\t\t<option value=\"" + i + "\" selected>" + i + "</option>" : "\n\t\t<option value=\"" + i + "\">" + i + "</option>";
		}
		this.html += "\n\t</select>";
	},
	
	createSelect : function(id, max, sel)
	{
	  var html = '<select id="'+id+'" onchange="CALENDAR.setTime();">';
	  for (var i=0; i<max; i++) 
	  {                         
	      var si = (i<10) ? '0'+i : i;
	      if (sel==i) html += '<option value="'+si+'" selected>'+si+'</option>';
	      else html += '<option value="'+si+'">'+si+'</option>';           
	  }
	  html += '</select>';
	  return html;  
	},
	
	getCalendar : function()
	{
		var start = new Date(this.day);
		var end = new Date(this.day);
		end.setDate(31);

		var month = start.getMonth();
		start.setDate(1);
		start.setDate(1 + this.weekstart - start.getDay());
		if (start.getMonth() == month && start.getDate()>1) start.setDate(-5);
		this.html += "\n\t\t<tbody>";
		while (start.getTime() <= end.getTime())
		{
			this.html += "\n\t\t\t<tr>";
			this.html += "<td class=\"week\">" + start.getWeek() + "</td>";
			for (var n=0; n<7; n++)
			{
				var sel = (this.day.getDate() == start.getDate() && start.getMonth() == month) ? true : false;
				this.html += (sel) ? "\n\t\t\t<td class=\"dayselected\">" : "\n\t\t\t<td>";
				var outmonth = (start.getMonth() < 9) ? "0" + (start.getMonth()+1) : (start.getMonth()+1);
				var outday = (start.getDate() < 10) ? "0" + start.getDate() : start.getDate();
				if (this.datetime) this.html += (start.getMonth() == month) ? "<span "+ " onclick=\"javascript:CALENDAR.setDay(this, '" + start.getFullYear() + "-" + outmonth + "-" + outday + "');\">" +  start.getDate() + "</span>" : start.getDate();
				else this.html += (start.getMonth() == month) ? "<span "+ " onclick=\"javascript:CALENDAR.setDate('" + start.getFullYear() + "-" + outmonth + "-" + outday + "');\">" +  start.getDate() + "</span>" : start.getDate();
				this.html += "</td>";
				if (sel) this.seldate = (start.getFullYear() + "-" + outmonth + "-" + outday);
				start.setDate(start.getDate() + 1);
			}
			this.html += "\n\t\t\t</tr>";
		}
		if (this.seldate == undefined) this.seldate = start.getDate();
		this.html += "\n\t\t</tbody>";
	},

	show_picker : function(obj)
	{
		if (obj.p.dsObj!=undefined && $(obj.p.dsObj).DSpos == 0) return;
		if (this.div != null) this.div.parentNode.removeChild(this.div);
		this.obj = obj;
		this.getDsDay();
		this.getFormat();
		this.div = document.createElement("DIV");
		this.className = (this.obj.p.classcalendar == null) ? "calendar" : this.obj.p.classcalendar;
		this.div.className = this.className;
		this.div.style.position = "absolute";
		this.div.style.zIndex = "900";

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
		var scrollLeft = obj.scrollLeft;
		while (parentObj.offsetParent != undefined && parentObj.scrollTop == 0)
		{
			parentObj = parentObj.parentNode;
			scrollTop += parentObj.scrollTop;
			scrollLeft += parentObj.scrollLeft;
		}
		scrollTop = isNaN(scrollTop) ? 0 : scrollTop;
		scrollLeft = isNaN(scrollLeft) ? 0 : scrollLeft;
		this.div.style.left = (offLeft - scrollLeft) + "px";
		this.div.style.top = offTop + obj.offsetHeight - scrollTop + "px";
// 		this.div.style.minWidth = obj.clientWidth + "px";
		if (parseInt(this.div.style.top) + 210  >=  SYSTEMBROWSER.winHeight()) this.div.style.top = parseInt(this.div.style.top) - 210  + "px";
		this.show();

		this.obj.parentNode.insertBefore(this.div, obj);
		if (this.iframe != undefined) 
		{
			this.iframe.style.left   = this.div.style.left;
			this.iframe.style.left   = this.div.style.left;
			this.iframe.style.top    = this.div.style.top;
			this.iframe.style.width  = this.div.clientWidth + "px";
			this.iframe.style.height = this.div.clientHeight + "px";
			this.obj.parentNode.insertBefore(this.iframe, obj);
		}
	},

	show : function()
	{
		this.html = "\n\t<table>";
		this.html += "\n\t<tr><td>";
		this.html += "<a "+ " href=\"javascript:CALENDAR.hide();\"><span class=\"" + this.className + "_close\" title=\"" + LANG.translate("CAL000") + "\">&nbsp;</span></a>";
		this.html += "\n\t</td><td>";
		this.getMonthYear();
		this.html += "\n\t</td></tr>";
		this.html += "\n\t</table>";
		this.html += "\n\t<table class=\"cal\" align=\"center\">";
		this.getWeekDay();
		this.getCalendar();
		this.html += "\n\t</table>";

		if (this.datetime)
		{
			 this.html += "\n\t<div style=\"text-align:left;float:left\">";
			 var reg = this.obj.p.format.split("|");
			 var time = (this.time != null) ? this.time : "12:00:00";
			 this.html += CALENDAR.createSelect('calendar_hours', 24, time.split(":")[0]);
			 this.html += CALENDAR.createSelect('calendar_minutes', 60, time.split(":")[1]);
			 if (reg[4].split(":")[2] != undefined) this.html += CALENDAR.createSelect('calendar_seconds', 60, time.split(":")[2]);
			 this.html += "\n\t</div>";
		}

		this.html += "\n\t<div>";
		var today = new Date();
		var month = today.getMonth() + 1;
		month = (month < 10) ? "0"+month : month;
		var outday = (today.getDate() < 10) ? "0" + today.getDate() : today.getDate();
		this.html += "<a href=\"javascript:CALENDAR.setDate('" + today.getFullYear() + "-"  + month  + "-" + outday + "');\"><span class=\"" + this.className + "_today\" title=\"" + LANG.translate("CAL001") + "\">&nbsp;</span></a>";
		this.html += "<a href=\"javascript:CALENDAR.setDate('');\"><span class=\"" + this.className + "_cancel\" title=\"" + LANG.translate("CAL002") + "\">&nbsp;</span></a>";
		this.html += "<a href=\"javascript:CALENDAR.confirmDate();\"><span class=\"" + this.className + "_confirm\" title=\"" + LANG.translate("CAL003") + "\">&nbsp;</span></a></div>";
		this.html += "\n\t</div>";
		this.div.innerHTML = this.html;
	},

	hide : function()
	{
		this.div.parentNode.removeChild(this.div);
		this.div = null;
		if (this.iframe != undefined) this.iframe.parentNode.removeChild(this.iframe);
	},

	setYear : function(year)
	{
		this.day.setFullYear(year);
		this.show();
	},

	setMonth : function(month)
	{
		this.day.setMonth(month);
		this.show();
	},

	setDay : function(sel, value)
	{
	    var tbody = sel.parentNode.parentNode.parentNode;
	    var span = tbody.getElementsByTagName('span');
	    for (var i=0; i<span.length; i++) span[i].parentNode.className = '';
	    sel.parentNode.className = 'dayselected';
	    this.seldate = value;
	    var daysel = value.split("-");
	    this.day = new Date(daysel[0], daysel[1], daysel[2]);
	},

	setTime : function()
	{
		this.time = ($('calendar_hours')!=undefined) ? $('calendar_hours').value : '12';
		this.time += ':' + (($('calendar_minutes') != undefined) ? $('calendar_minutes').value : '00');
		this.times += ':' + (($('calendar_seconds') != undefined) ? $('calendar_seconds').value : '00');
	},

	confirmDate : function()
	{
	    this.setDate(this.seldate);
	},
	
	setDate : function(value)
	{
		var reg = this.obj.p.format.split("|");
		reg[1]="EN";
		if (new RegExp(CALENDAR.testdatatime).exec(reg[2])!=null)
		{
			reg[2] = "yyyy-mm-dd HH:ii:ss";
			value += ' ' + (($('calendar_hours') != undefined) ? $('calendar_hours').value : '12');
			value += ':' + (($('calendar_minutes') != undefined) ? $('calendar_minutes').value : '00');
			value += ':' + (($('calendar_seconds') != undefined) ? $('calendar_seconds').value : '00');
		}
		else reg[2]="yyyy-mm-dd";
		this.obj.value = FORMAT.formatDate(value, reg, false);
		if (this.obj.p.dsObj != undefined) TEXT.setDsValue(this.obj, FORMAT.formatDate(value, reg, false));
		this.hide();
	}
};
Date.prototype.getWeek = function() { var onejan = new Date(this.getFullYear(),0,1); return Math.ceil((((this - onejan) / 86400000) + onejan.getDay()+1)/7);};
var CALENDAR = new clsCalendar();