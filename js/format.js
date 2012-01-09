/**
* Class FORMAT
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
* Much of the code on the formatting of dates was made available by
* Author: Matt Kruse <matt@mattkruse.com>
* WWW: http://www.mattkruse.com/
*/

function clsFormat()
{
	this.STR_PAD_RIGHT = 0;
	this.STR_PAD_LEFT = 1;

	this.intSetting = Array();
	this.intSetting['IT'] = Array();
	this.intSetting['EN'] = Array();

	this.intSetting['EN']['month'] = Array('January','February','March','April','May','June','July','August','September','October','November','December');
	this.intSetting['EN']['mon'] = Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	this.intSetting['EN']['weekstart'] = 0;

	this.intSetting['EN']['weekday'] = Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	this.intSetting['EN']['day'] = Array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');

	this.intSetting['IT']['month'] =  Array('Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre');
	this.intSetting['IT']['mon'] = Array('Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic');

	this.intSetting['IT']['weekday'] = Array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato','Dom','Lun','Mar','Mer','Gio','Ven','Sab');
	this.intSetting['IT']['day'] = Array('Dom','Lun','Mar','Mer','Gio','Ven','Sab');
	this.intSetting['IT']['weekstart'] = 1;

	this.intSetting['IT']['thousand'] = ".";
	this.intSetting['IT']['decimal']  = ",";

	this.intSetting['EN']['thousand'] = ",";
	this.intSetting['EN']['decimal']  = ".";
}

clsFormat.prototype =
{
	LZ : function(x) {return(x<0||x>9?"":"0")+x},

	unformatReg : function(Obj)
	{
		var reg = Obj.p.format.split("|");
		if (reg[0]!="string") 
		{
			var temp = reg[3]; reg[3] = reg[1]; reg[1] = temp;
			temp = reg[2]; reg[2] = reg[4]; reg[4] = temp;
		}
		return reg;
	},

	unformat : function(Obj, dsObj, pos)
	{
		var format = this.unformatReg(Obj);
		var valueDs = dsObj.DSresult[pos][Obj.p.dsItem];
		if (format[0]=="date") valueDs = this.formatDate(valueDs, format);
		else if (format[0]=="number") valueDs = this.formatNumber(valueDs, format);
		else valueDs = this.formatString(valueDs, format);
		dsObj.DSresult[pos][Obj.p.dsItem] = valueDs;
	},

	format : function(Obj, valueDs)
	{
		valueDs = this.Format(valueDs, Obj.p.format);
		if (Obj.value != undefined) Obj.value = valueDs;
		else Obj.innerHTML = valueDs;
	},

	Format : function(text, format)
	{
		var reg = format.split("|");
		if (reg[0] == "date") return this.formatDate(text, reg, false);
		if (reg[0] == "datefixed")	return this.formatDate(text, reg, true);
		else if (reg[0] == "number") return this.formatNumber(text, reg);
		else return this.formatString(text, reg);
	},

	setDate : function(s, input, addYear, addMonth, addDay, addHours, addMinutes, addSeconds)
	{
		var y = parseInt(FORMAT.Format(s, 'date|'+input+'|EN|yyyy'),10);
		var m = parseInt(FORMAT.Format(s, 'date|'+input+'|EN|m'),10);
		var d = parseInt(FORMAT.Format(s, 'date|'+input+'|EN|d'),10);
		var h = parseInt(FORMAT.Format(s, 'date|'+input+'|EN|H'),10);
		var i = parseInt(FORMAT.Format(s, 'date|'+input+'|EN|i'),10);
		var s = parseInt(FORMAT.Format(s, 'date|'+input+'|EN|s'),10);
		var dt = new Date();
		dt.setFullYear(y+parseInt(addYear,10));
		dt.setMonth(m+parseInt(addMonth,10));
		dt.setDate(d+parseInt(addDay,10));
		dt.setHours(h+parseInt(addHours,10));
		dt.setMinutes(i+parseInt(addMinutes,10));
		dt.setSeconds(s+parseInt(addSeconds,10));
		return dt;
	 },

	getDate : function(d, output, addYear, addMonth, addDay, addHours, addMinutes, addSeconds)
	{
		  d.setFullYear(d.getFullYear()+parseInt(addYear,10));
		  d.setMonth(d.getMonth()+parseInt(addMonth,10));
		  d.setDate(d.getDate()+parseInt(addDay,10));
		  d.setHours(d.getHours()+parseInt(addHours,10));
		  d.setMinutes(d.getMinutes()+parseInt(addMinutes,10));
		  d.setSeconds(d.getSeconds()+parseInt(addSeconds,10));

		  var p = [];
		  p[0] = d.getFullYear();		// year
		  p[1] = d.getMonth()+1;		// month
		  p[2] = d.getDate();			// day
		  p[3] = d.getHours();			// hours
		  p[4] = d.getMinutes();		// minutes
		  p[5] = d.getSeconds();		// seconds
		  return FORMAT.Format(p[0]+'-'+p[1]+'-'+p[2]+' '+p[3]+':'+p[4]+':'+p[5], 'date|EN|yyyy-m-d H:i:s|'+output);
	},
	 
	formatDate : function(date, reg, fixed)
	{
		var i_format=0;
		var format=reg[4]+"";
		var result = c = token = "";
		var timestamp = this.getDateFromFormat(date, reg);
		if (!fixed && timestamp==0) 
		{
			if (!fixed) return "";
			else
			{
				value["yyyy"]='0000';
				value["yy"]='00';
				value["m"]='0';
				value["mm"]='00';
				value["mmmm"]='';
				value["mmm"]='';
				value["d"]='0';
				value["dd"]='00';
				value["ddd"]='';
				value["dddd"]='';
				value["H"]='0';
				value["HH"]='00';
				value["h"]='0';
				value["hh"]='00';
				value["K"]='0';
				value["k"]='0';
				value["KK"]='00';
				value["kk"]='00';
				value["a"]='';
				value["i"]='0';
				value["ii"]='00';
				value["s"]='0';
				value["ss"]='00';
			}
		}
		else
		{
			if (reg[4] == "TIMESTAMP") return (timestamp/1000).toString();
			var date = new Date(timestamp);
			var y=date.getYear()+"";
			var M=date.getMonth()+1;
			var d=date.getDate();
			var E=date.getDay();
			var H=date.getHours();
			var i=date.getMinutes();
			var s=date.getSeconds();
			var yyyy,yy,mmmm,mm,dd,hh,h,ii,ss,ampm,HH,H,KK,K,kk,k;
			var value=new Object();
			if (y.length < 4) {y=""+(y-0+1900);}
			value["yyyy"]=+y;
			value["yy"]=y.substring(2,4);
			value["m"]=M;
			value["mm"]=this.LZ(M);
			value["mmmm"]=this.intSetting[reg[3]]['month'][M-1];
			value["mmm"]=this.intSetting[reg[3]]['mon'][M-1];
			value["d"]=d;
			value["dd"]=this.LZ(d);
			value["ddd"]=this.intSetting[reg[3]]['day'][E];
			value["dddd"]=this.intSetting[reg[3]]['weekday'][E];
			value["H"]=H;
			value["HH"]=this.LZ(H);
			if (H==0) value["h"]=12;
			else if (H>12) value["h"]=H-12;
			else value["h"]=H;
			value["hh"]=this.LZ(value["h"]);
			if (H>11) value["K"]=H-12;
			else value["K"]=H;
			value["k"]=H+1;
			value["KK"]=this.LZ(value["K"]);
			value["kk"]=this.LZ(value["k"]);
			if (H > 11) value["a"]="PM";
			else { value["a"]="AM"; }
			value["i"]=i;
			value["ii"]=this.LZ(i);
			value["s"]=s;
			value["ss"]=this.LZ(s);
		}
		while (i_format < format.length) 
		{
			c=format.charAt(i_format);
			token="";
			while ((format.charAt(i_format)==c) && (i_format < format.length)) token += format.charAt(i_format++);
			if (value[token] != null) result=result + value[token];
			else result=result + token;
		}
		return result;
	},
	
	_isInteger : function(val)
	{
		var digits="1234567890";
		var length = val.length;
		for (var i=0; i < length; i++) 
		{
			if (digits.indexOf(val.charAt(i))==-1) return false;
		}
		return true;
	},

	_getInt : function(str,i,minlength,maxlength) 
	{
		for (var x=maxlength; x>=minlength; x--) 
		{
			var token=str.substring(i,i+x);
			if (token.length < minlength) { return null; }
			if (this._isInteger(token)) { return token; }
		}
		return null;
	},

	getValue : function(val, start, arr)
	{
		var length = arr.length;
		for (var i=0; i<length; i++) 
		{
			if (val.substring(start, start+arr[i].length).toLowerCase()==arr[i].toLowerCase()) return i;
		}
		return 0;
	},
	
	getDateFromFormat : function(val, reg)
	{
		if (reg[2] == "TIMESTAMP") return parseInt(val*1000);
		val=val+"";
		format=reg[2]+"";
		var i_val=i_format=0;
		var c=token=token2=ampm="";
		var x,y;
		var day=1;
		var now=new Date();
		var year=now.getYear();
		var month=now.getMonth()+1;
		var hh=now.getHours();
		var ii=now.getMinutes();
		var ss=now.getSeconds();
		while (i_format < format.length) 
		{
			c=format.charAt(i_format);
			token="";
			while ((format.charAt(i_format)==c) && (i_format < format.length)) token += format.charAt(i_format++);
			if (token=="yyyy" || token=="yy") 
			{ 
				if (token=="yyyy") { x=4;y=4; }
				if (token=="yy")   { x=2;y=2; }
				year=this._getInt(val,i_val,x,y);
				if (year==null) return 0;
				i_val += year.length;
				if (year.length==2) 
				{
					if (year > 70) year=1900+(year-0);
					else year=2000+(year-0);
				}
			}
			else if (token=="mmmm")
			{
				month = this.getValue(val, i_val, this.intSetting[reg[1]]['month'])+1;
				i_val += (this.intSetting[reg[1]]['month'][month-1]).length;
			}
			else if (token=="mmm")
			{
				month = this.getValue(val, i_val, this.intSetting[reg[1]]['mon'])+1;
				i_val += (this.intSetting[reg[1]]['mon'][month-1]).length;
			}
			else if (token=="mm"|| token=="m") 
			{
				month=this._getInt(val,i_val,token.length,2);
				if(month==null||(month<1)||(month>12)) return 0;
				i_val+=month.length;
			}
			else if (token=="dddd")
			{
				day = this.getValue(val, i_val, this.intSetting[reg[1]]['weekday']);
				i_val += (this.intSetting[reg[1]]['weekday'][day]).length;
			}
			else if (token=="ddd")
			{
				day = this.getValue(val, i_val, this.intSetting[reg[1]]['day']);
				i_val += (this.intSetting[reg[1]]['day'][day]).length;
			}
			else if (token=="dd"||token=="d") 
			{
				day=this._getInt(val,i_val,token.length,2);
				if(day==null||(day<1)||(day>31)) return 0;
				i_val+=day.length;
			}
			else if (token=="hh"||token=="h") 
			{
				hh=this._getInt(val,i_val,token.length,2);
				if(hh==null||(hh<1)||(hh>12)) return 0;
				i_val+=hh.length;
			}
			else if (token=="HH"||token=="H") 
			{
				hh=this._getInt(val,i_val,token.length,2);
				if(hh==null||(hh<0)||(hh>23)) return 0;
				i_val+=hh.length;
			}
			else if (token=="KK"||token=="K") 
			{
				hh=this._getInt(val,i_val,token.length,2);
				if(hh==null||(hh<0)||(hh>11)) return 0;
				i_val+=hh.length;
			}
			else if (token=="kk"||token=="k") 
			{
				hh=this._getInt(val,i_val,token.length,2);
				if(hh==null||(hh<1)||(hh>24)) return 0;
				i_val+=hh.length;hh--;
			}
			else if (token=="ii"||token=="i") 
			{
				ii=this._getInt(val,i_val,token.length,2);
				if(ii==null||(ii<0)||(ii>59)) return 0;
				i_val+=ii.length;
			}
			else if (token=="ss"||token=="s") 
			{
				ss=this._getInt(val,i_val,token.length,2);
				if(ss==null||(ss<0)||(ss>59)) return 0;
				i_val+=ss.length;
			}
			else if (token=="a") 
			{
				if (val.substring(i_val,i_val+2).toLowerCase()=="am") ampm="AM";
				else if (val.substring(i_val,i_val+2).toLowerCase()=="pm") ampm="PM";
				else return 0;
				i_val+=2;
			}
			else 
			{
				if (val.substring(i_val,i_val+token.length)!=token) return 0;
				else i_val+=token.length;
			}
		}
		if (i_val != val.length) return 0;
		if (month==2 && day > 28) 
		{
			 if  (((year%4==0) && (year%100 != 0)) || (year%400==0))
			 { 
				  if (day > 29) return 0;
			 }
			 else return 0;
		}
		if (((month==4)||(month==6)||(month==9)||(month==11)) && (day > 30)) return 0;
		if (hh<12 && ampm=="PM") hh=hh-0+12;
		else if (hh>11 && ampm=="AM") hh-=12;
		var newdate=new Date(year,month-1,day,hh,ii,ss);
		return newdate.getTime();
	},

	number_format : function(number, decimals, dec_point, thousands_sep) 
	{
		var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
		var d = dec_point == undefined ? "." : dec_point;
		var t = thousands_sep == undefined ? "," : thousands_sep, s = n < 0 ? "-" : "";
		var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0; 
	   return s + (j ? i.substr(0, j) + t : "")+i.substr(j).replace(/(\d{3})(?=\d)/g, "$1"+t) + (c ? d + Math.abs(n-i).toFixed(c).slice(2) : "");
	},

 	formatNumber : function(text, format)
 	{
		//number|EN|,0.00|IT|.0,00
		text = text.toString();
		var decimal = ((pos = format[4].indexOf(this.intSetting[format[3]]['decimal']))>-1) ? (format[4].length-pos-1) : 0;
 		var thousands = format[4].replace(/[.,]/g,"").length - decimal;
  		text = text.replaceAll(this.intSetting[format[1]]['thousand'], "");
  		text = text.replaceAll(this.intSetting[format[1]]['decimal'], this.intSetting['EN']['decimal']);
 		var outsepthousands = (format[4].indexOf(this.intSetting[format[3]]['thousand'])>-1) ? this.intSetting[format[3]]['thousand'] : "";
  		text = this.number_format(text, decimal, this.intSetting[format[3]]['decimal'], outsepthousands);
   		outthousands = text.replace(/[,.]/g,"").length - decimal; 
   		for (var y=outthousands; y<thousands; y++) text = "0"+text;
 		return text;
 	},

	countChar : function(string, char)
	{
		var count = 0;
		var length = string.length;
		for (var i = 0; i < length; i++) if (string[i]==char) count++;
		return count; 
	},

	formatString : function(text, format)
	{
 		if (format[1].indexOf('trim')>-1)  text = text.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
 		if (format[1].indexOf('upper')>-1) text = text.toUpperCase();
 		if (format[1].indexOf('lower')>-1) text = text.toLowerCase();
		var length = this.countChar(format[1], "@");
		if (length==0) length = text.length;
		return text.pad(length, " ", (format[1].indexOf('!')>-1) ? this.STR_PAD_LEFT : this.STR_PAD_RIGHT);;
	}
}
var FORMAT = new clsFormat();
