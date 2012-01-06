/**
* Class SYSTEMBROWSER
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsSystemBrowser()
{
}

clsSystemBrowser.prototype =
{
	winWidth : function()
	{
		if (document.layers) return	window.Width;
		if (document.all) return document.body.offsetWidth;
		return window.innerWidth;
	},

	winHeight : function()
	{
		if (document.layers) return window.Height;
		if (document.all) return document.body.offsetHeight;
		return window.innerHeight;
	},

	getCSSProp : function(obj, styleProp) 
	{
		if (obj.currentStyle)
		{
			styleProp = styleProp.replace("-t", "T");
			styleProp = styleProp.replace("-w", "W");
			styleProp = styleProp.replace("-b", "B");
			styleProp = styleProp.replace("-l" ,"L");
			styleProp = styleProp.replace("-r" ,"R");
  			return obj.currentStyle[styleProp];
		}
		else return document.defaultView.getComputedStyle(obj,null).getPropertyValue(styleProp);
	},

	getOffSetY : function(el)
	{
		var topY = 0;
		while (el) {
			topY+=el.offsetTop;
			el=el.offsetParent;
		}
		return topY;
	},

	getOffSetX : function(el)
	{
		var topX = 0;
		while (el) {
			topX+=el.offsetLeft;
			el=el.offsetParent;
		}
		return topX;
	},

	fixPage : function()
	{
 		document.body.parentNode.style.overflow = "hidden";
 		document.body.parentNode.style.width = "100%";
 		document.body.parentNode.style.height = "100%";
		document.body.style.overflow = "hidden";
		document.body.style.width = document.body.parentNode.clientWidth - 4 + "px";
		document.body.style.height = document.body.parentNode.clientHeight - 4 + "px";
	},

	getNextV : function(obj)
	{
		var delta = 0;
		if (obj.nextSibling == null) return 0;
		if (obj.nextSibling.nextSibling == null) return 0;
 		var afterObj = (obj.nextSibling.nodeType == 1) ? obj.nextSibling : obj.nextSibling.nextSibling;
		if (afterObj.p != undefined && afterObj.p.typeObj == "splitbar")
		{
			var afterObj = (afterObj.nextSibling.nodeType == 1) ? afterObj.nextSibling : afterObj.nextSibling.nextSibling;
			delta = 11;
		}
		if (afterObj.parentNode == obj.parentNode)
		{
			var float = (afterObj.style.styleFloat == undefined) ? afterObj.style.cssFloat : afterObj.style.styleFloat;
			if (float == "") float = this.getCSSProp(afterObj,"float");
			if (float == "none" || float == undefined) return afterObj.clientHeight + delta;
		}
		return 0;
	},

	borderHeight : function(obj, isParent)
	{
 		if (obj == document.body) return 0;
 		var borderTop = (obj.style.borderTopWidth != "") ? obj.style.borderTopWidth : this.getCSSProp(obj,"border-top-width");
 		var borderBottom = (obj.style.borderBottomWidth != "") ? obj.style.borderBottomWidth : this.getCSSProp(obj,"border-bottom-width");
		var paddingBottom = (obj.style.paddingBottom != "") ? obj.style.paddingBottom : this.getCSSProp(obj,"padding-bottom");
		var paddingTop = (obj.style.paddingTop != "") ? obj.style.paddingTop : this.getCSSProp(obj,"padding-top");
		var heightVal = 0;
		heightVal = (borderTop.indexOf("px") == -1) ? heightVal : heightVal + parseFloat(borderTop);
		heightVal = (borderBottom.indexOf("px") == -1) ? heightVal : heightVal + parseFloat(borderBottom);
		heightVal = (paddingBottom == undefined) ? heightVal : heightVal + parseFloat(paddingBottom);
		if(!isParent) heightVal = (paddingTop == undefined) ? heightVal : heightVal + parseFloat(paddingTop);
		return heightVal;
	},

	autoHeight : function(objname)
	{
		if(AJAX.loadall) return;
 		this.fixPage();

		var obj = $(objname);
		var parentObj = (obj.parentNode.id == objname + "_container") ? obj.parentNode.parentNode : obj.parentNode;

		var heightVal = parentObj.clientHeight - this.borderHeight(parentObj, true) - this.borderHeight(obj) - (this.getOffSetY(obj) - this.getOffSetY(parentObj));

  		heightVal = heightVal - this.getNextV(obj);

 		obj.style.height = (heightVal > 0) ? heightVal + "px" : "0px";
	},

	borderWidth : function(obj, isParent)
	{
 		if (obj == document.body) return 0;
 		var borderLeft = (obj.style.borderLeftWidth != "") ? obj.style.borderLeftWidth : this.getCSSProp(obj,"border-left-width");
 		var borderRight = (obj.style.borderRightWidth != "") ? obj.style.borderRightWidth : this.getCSSProp(obj,"border-right-width");
		var paddingRight = (obj.style.paddingRight != "") ? obj.style.paddingRight : this.getCSSProp(obj,"padding-right");
		var paddingLeft = (obj.style.paddingLeft != "") ? obj.style.paddingLeft : this.getCSSProp(obj,"padding-left");
		var widthVal = 0;
		widthVal = (borderLeft.indexOf("px") == -1) ? widthVal : widthVal + parseFloat(borderLeft);
		widthVal = (borderRight.indexOf("px") == -1) ? widthVal : widthVal + parseFloat(borderRight);
 		widthVal = (paddingLeft == undefined) ? widthVal : widthVal + parseFloat(paddingLeft);
		if(!isParent) widthVal = (paddingRight == undefined) ? widthVal : widthVal + parseFloat(paddingRight);
		return widthVal;
	},

	autoWidth : function(objname)
	{
		if(AJAX.loadall) return;
		this.fixPage();

		var obj = $(objname);
		var parentObj = (obj.parentNode.id == objname + "_container") ? obj.parentNode.parentNode : obj.parentNode;
		
		var widthVal = parentObj.clientWidth - this.borderWidth(parentObj, true) - this.borderWidth(obj) - (this.getOffSetX(obj) - this.getOffSetX(parentObj));
 		obj.style.width = (widthVal > 0) ? widthVal + "px" : "0px";
	},

	autoWidthCenter : function(objname)
	{
		var obj = $(objname);

		var afterObj = obj;
		var offsetWidth = 0;
		do
		{
			afterObj = (afterObj.nextSibling == undefined) ? afterObj.parentNode.nextSibling : afterObj.nextSibling;
			if (afterObj.p != undefined && afterObj.p.typeObj == "splitbar")
			{
				afterObj.style.display = "block";
				offsetWidth = afterObj.clientWidth;
			}
		}
		while (afterObj.nodeType != 1 || afterObj.p != undefined && afterObj.p.typeObj == "splitbar");
		offsetWidth = afterObj.offsetLeft - obj.offsetLeft - offsetWidth - 2;
		obj.style.width = (offsetWidth < 0) ? "0px" : offsetWidth + "px";
	},

	printCode : function(obj)
	{
 		newwin = window.open(obj.src,'printwin','left=100,top=100,width=400,height=400,scrollbars=1');
		if(window.attachEvent) newwin.attachEvent('onload', function(){ newwin.document.getElementsByTagName('body')[0].innerHTML=obj.editor.body.innerHTML; newwin.print(); });
		else newwin.addEventListener('DOMContentLoaded', function(){newwin.document.getElementsByTagName('body')[0].innerHTML=obj.editor.body.innerHTML; newwin.print();}, false);
	},

	printContent : function(id)
	{
		var obj = $(id);
 		if (obj == undefined) //Print Page
		{
			window.print();
			return;
		}
		if (obj.options != undefined) if (obj.options.indexOf("codepress") != -1)
		{
			this.printCode(obj);
			return;
		}
		var ie = this.isIE();
  		var head = document.getElementsByTagName("head")[0].cloneNode(true);
 		for (var i=0; i<head.childNodes.length; i++)
 			if (head.childNodes[i].tagName==undefined || head.childNodes[i].tagName!="LINK") head.removeChild(head.childNodes[i]);
		if (obj.p != undefined && obj.p.typeObj == "tabs") obj = obj.SelectedBox;
		newwin = window.open('','printwin','left=100,top=100,width=400,height=400,scrollbars=1');
		newwin.document.write('<hmtl>\n<head>\n');
 		if (ie) newwin.document.write(head.innerHTML+'\n');
		newwin.document.write('<title>Print Page</title>\n');
		newwin.document.write('<style type="text/css">\n');
		newwin.document.write('input, select, textarea, div, span, div:hover, span:hover {\n');
		newwin.document.write('background-color: #FFF;\n');
		newwin.document.write('}\n');
		newwin.document.write('th,tr,td,table {\n');
		newwin.document.write('border: 1px solid;\n');
		newwin.document.write('border-collapse: collapse;\n');
		newwin.document.write('}\n');
		newwin.document.write('</style>\n');
		newwin.document.write('<script>\n');
		newwin.document.write('function chkstate(){\n');
		newwin.document.write('if(document.readyState=="complete"){\n');
		newwin.document.write('window.close()\n');
		newwin.document.write('}\n');
		newwin.document.write('else{\n');
		newwin.document.write('setTimeout("chkstate()",2000)\n');
		newwin.document.write('}\n');
		newwin.document.write('}\n');
		newwin.document.write('function print_win(){\n');
		newwin.document.write('window.print();\n');
		newwin.document.write('chkstate();\n');
		newwin.document.write('}\n');
		newwin.document.write('<\/script>\n');
		newwin.document.write('</head>\n');
		newwin.document.write('<body onload="print_win()">\n');
		newwin.document.write('<div style="position: absolute;height : 99%;width : 99%; z-index: 1005;">\n');
		newwin.document.write('</body>\n');
		newwin.document.write('</html>\n');
  		var body = obj.cloneNode(true);
		if (!ie)
 		{
   		newwin.document.body.appendChild(body);
			var newhead = newwin.document.getElementsByTagName("head")[0];
			newhead.innerHTML = head.innerHTML + newhead.innerHTML;
		}
		else
 		{
   		newwin.document.body.innerHTML = body.innerHTML;
 			window.print();
		}

 		var div = newwin.document.body.getElementsByTagName('div');
		var length = div.length;
 		for (var i=0; i<div.length; i++) div[i].style.backgroundColor = 'transparent';
 		var span = newwin.document.body.getElementsByTagName('span');
		var length = span.length;
 		for (var i=0; i<length; i++) span[i].style.backgroundColor = 'transparent';
		newwin.document.close();
	},

	removeCSS : function(href1)
	{
		href1 = href1.split("?")[0];
		var curcss = document.getElementsByTagName('LINK');
		for (var i=0; i<curcss.length; i++) 
		{
			var href = curcss[i].getAttribute('href').split("?")[0];
			if (href == href1) curcss[i].parentNode.removeChild(curcss[i]);
		}
	},

	addCSS : function(href)
	{
		var head = document.getElementsByTagName('head')[0];
		var newhead = document.createElement('link');
		newhead.setAttribute("href", href);
		newhead.setAttribute("rel", "stylesheet");
		newhead.setAttribute("type", "text/css");
		head.appendChild(newhead);
	},

	removeJS : function(src1)
	{
		src1 = src1.split("?")[0];
		var scripts = document.getElementsByTagName('script');
		for (var i=0; i<scripts.length; i++) 
		{
			if (scripts[i].getAttribute('src')!=undefined) 
			{
				var src = scripts[i].getAttribute('src').split("?")[0];
				if (src == src1) scripts[i].parentNode.removeChild(scripts[i]);
			}
		}
	},

	addJS : function(src)
	{
		var newjs = document.createElement('script');
		newjs.setAttribute("type", "text/javascript");
		newjs.setAttribute("language", "JavaScript1.5");
		newjs.setAttribute("src", src);
		document.body.appendChild(newjs);
 	},

	manageCSS : function(css, time, path)
	{
		var curcss = document.getElementsByTagName('LINK');
		var newcss = new Array();
		for (var k = 0; k < css.length; k++)
		{
			var addcss = true;
			var href1 = (css[k].indexOf("://") < 0) ? (path + css[k]) : css[k];
			for (var i = 0; i < curcss.length; i++) 
			{
				var href = curcss[i].getAttribute('href').split("?")[0];
				if (href == href1) {addcss=false;break;}
			}
			if (addcss) newcss[newcss.length] = href1+time;
		}
		for (var k = 0; k < newcss.length; k++) this.addCSS(newcss[k]);
	},

	manageJS : function(js, time, path)
	{
		var curjs = document.getElementsByTagName('SCRIPT');
		var newjs = new Array();
		for (var k = 0; k < js.length; k++)
		{
			var addjs = true;
			var src1 = (js[k].indexOf("://") < 0) ? (path + js[k]) : js[k];
			for (var i = 0; i < curjs.length; i++) 
			{
				if (curjs[i].getAttribute('src')!=undefined)
				{
					var src = curjs[i].getAttribute('src').split("?")[0];
					if (src == src1) {addjs=false;break;}
				}
			}
			if (addjs) newjs[newjs.length] = src1+time;
		}
		for (var k = 0; k < newjs.length; k++) this.addJS(newjs[k]);
	},

	isOpera : function()
	{
		return /Opera/.test(navigator.userAgent);
	},

	isSafari : function()
	{
		return /Safari/.test(navigator.userAgent) && !this.isChrome();
	},

	isFirefox : function()
	{
		return /Firefox/.test(navigator.userAgent);
	},

	isChrome : function()
	{
		return /Chrome/.test(navigator.userAgent);
	},

	isKHTML : function()
	{
		return /KHTML/.test(navigator.userAgent) && !this.isChrome;
	},

	isGecko : function()
	{
		return navigator.product == "Gecko" &&
			! ( this.isOpera() || this.isSafari() || this.isFirefox() || this.isChrome());
	},

	isIE : function()
	{
		return /MSIE/.test(navigator.userAgent);
	},

	clone : function (obj) 
	{
		var o = new this.constructor(); 
		for (var p in obj) 
		{
			 if (typeof obj[p] === 'object') 
			 o[p] = obj[p].clone(); 
			 else o[p] = obj[p];
		}	
		return o;
	},

	getVersion : function()
	{
		if( this.isIE() )
		{
			return Number(navigator.userAgent.match(/MSIE ([0-9.]+)/)[1]);
		}
		else if( this.isSafari() || this.isChrome())
		{
			return Number(navigator.userAgent.match(/[0-9.]+$/));
		}
		else if( this.isFirefox() )
		{
			return parseInt(navigator.userAgent.match(/Firefox\/([0-9.]+)/)[1]);
		}
		else if(this.isKHTML() )
		{
			return parseInt(navigator.userAgent.match(/KHTML\/([0-9.]+)/)[1]);
		}
		else if( this.isGecko() )
		{
			var n = navigator.userAgent.match(/rv:([0-9.]+)/)[1];
	
			var ar = n.split(".");

			var s = ar[0] + ".";
			var length = ar.length;
			for(var i = 1; i < length; ++i)
			{
			s += ("0" + ar[i]).match(/.{2}$/)[0];
			}	
			return Number(s);
		}
		else if( this.isOpera() )
		{
			return Number(navigator.userAgent.match(/Opera.([0-9.]+)/)[1]);
		}
		else
		{
			return null;
		}
	}
}
var SYSTEMBROWSER = new clsSystemBrowser();

Array.prototype.removeDuplicates = function () { for (var i = 1; i < this.length; i++) { if (this[i][0] == this[i-1][0]) this.splice(i,1); }}
Array.prototype.empty = function () { for (var i = 0; i <= this.length; i++) this.shift(); }
String.prototype.trim = function () { return this.replace(/^\s+|\s+$/g, ''); }
String.prototype.pad = function(l, s, t) 
{
	var result = (t==0) ? ((l>this.length) ? this.substr(0, l) + (s = new Array(Math.ceil((l-s.length)/s.length) + 1).join(s)) : this.substr(0, l)) : ((l>this.length) ? (s = new Array(Math.ceil((l-s.length)/s.length) + 1).join(s)) + this.substr(0, l) : this.substr(0, l));
	return s || (s=" "), result;
};
String.prototype.replaceAll=function(s1, s2) {return this.split(s1).join(s2)}