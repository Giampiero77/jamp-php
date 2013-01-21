/**
* Class SPLITBAR
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsSplitbar()
{
	this.obj = null;
	this.objSplit = null;
	this.beforeObj = null;
	this.afterObj = null;
	this.start = 0;
	this.X = 0;
	this.Y = 0;
	this.deltaX = 0;
	this.deltaY = 0;
	this.blockBefore = 0;
	this.blockAfter = 0;
}

clsSplitbar.prototype =
{
	getAroundObj : function()
	{
		this.beforeObj = this.obj;
		do{
			this.beforeObj = (this.beforeObj.previousSibling == undefined) ? this.beforeObj.parentNode.previousSibling : this.beforeObj.previousSibling;
		}
		while (this.beforeObj.nodeType != 1);

		this.afterObj = this.obj;
		do
			this.afterObj = (this.afterObj.nextSibling == undefined) ? this.afterObj.parentNode.nextSibling : this.afterObj.nextSibling;
		while (this.afterObj.nodeType != 1);

		this.beforeObj.style.overflow = "hidden";
		this.afterObj.style.overflow = "hidden";
	},

	getIframe : function()
	{
		var iframe1 = document.getElementsByTagName("iframe");
		this.iframe = new Array();
		var length1 = iframe1.length;
		for (var i = 0; i < length1; i++)
		{
			this.iframe.push(iframe1[i]);
			var iframe2 = iframe1[i].contentWindow.document.getElementsByTagName("iframe");
			var length2 = iframe2.length;
			for (var ii = 0; ii < length2; ii++)
			{
				this.iframe.push(iframe2[ii]);
			}
		}
	},

	beginDragHorizontal : function(obj, event)
	{
		if (event == undefined) event = window.event;
		this.obj = obj;
		this.start = event.clientY;
		this.deltaY = event.screenY - this.obj.offsetTop;
		this.obj.style.zIndex = "998";

		this.getAroundObj();
		this.blockBefore = this.beforeObj.offsetTop;
		this.blockAfter = this.afterObj.offsetTop + this.afterObj.clientHeight - this.obj.clientHeight;

		SYSTEMEVENT.addEventListener(document, "mousemove", SPLITBAR.moveHandlerHorizontal);
		SYSTEMEVENT.addEventListener(document, "mouseup", SPLITBAR.upHandlerHorizontal);

		this.getIframe();
		var length = this.iframe.length;
		for (var i = 0; i < length; i++)
		{
			SYSTEMEVENT.addEventListener(this.iframe[i].contentWindow.document, "mousemove", SPLITBAR.moveHandlerHorizontal);
			SYSTEMEVENT.addEventListener(this.iframe[i].contentWindow.document, "mouseup", SPLITBAR.upHandlerHorizontal);
		}	

		SYSTEMEVENT.stopPropagation(event);
		SYSTEMEVENT.preventDefault(event);
	},

	beginDragVertical : function(obj, event)
	{
		if (event == undefined) event = window.event;
		this.obj = obj;
		this.start = event.clientX;
		this.deltaX = event.screenX - this.obj.offsetLeft;
		this.obj.style.zIndex = "998";

		this.getAroundObj();
		this.blockBefore = this.beforeObj.offsetLeft ;
		this.blockAfter = this.afterObj.offsetLeft + this.afterObj.clientWidth - this.obj.clientWidth;

		SYSTEMEVENT.addEventListener(document, "mousemove", SPLITBAR.moveHandlerVertical);
		SYSTEMEVENT.addEventListener(document, "mouseup", SPLITBAR.upHandlerVertical);

		this.getIframe();
		var length = this.iframe.length; 
		for (var i = 0; i < length; i++)
		{
			SYSTEMEVENT.addEventListener(this.iframe[i].contentWindow.document, "mousemove", SPLITBAR.moveHandlerVertical);
			SYSTEMEVENT.addEventListener(this.iframe[i].contentWindow.document, "mouseup", SPLITBAR.upHandlerVertical);
		}	

		SYSTEMEVENT.stopPropagation(event);
		SYSTEMEVENT.preventDefault(event);
	},

	moveHandlerHorizontal : function (event)
	{
		SYSTEMEVENT.stopPropagation(event);
		SYSTEMEVENT.preventDefault(event);
		var pos = event.screenY - SPLITBAR.deltaY;
		if (pos > SPLITBAR.blockBefore && pos < SPLITBAR.blockAfter) SPLITBAR.Y = pos;
		SPLITBAR.obj.style.top =  SPLITBAR.Y + "px";
	},

	moveHandlerVertical : function (event)
	{
		SYSTEMEVENT.stopPropagation(event);
		SYSTEMEVENT.preventDefault(event);
		var pos = (event.screenX - SPLITBAR.deltaX);
		if (pos > SPLITBAR.blockBefore && pos < SPLITBAR.blockAfter) SPLITBAR.X = pos;
 		SPLITBAR.obj.style.left =  SPLITBAR.X  + "px";
	},

	upHandlerHorizontal : function (event)
	{
		SYSTEMEVENT.removeEventListener(document, "mouseup", SPLITBAR.upHandlerHorizontal);
		SYSTEMEVENT.removeEventListener(document, "mousemove", SPLITBAR.moveHandlerHorizontal);
		var length = SPLITBAR.iframe.length; 
		for (var i = 0; i < length; i++)
		{
			SYSTEMEVENT.removeEventListener(SPLITBAR.iframe[i].contentWindow.document, "mousemove", SPLITBAR.moveHandlerHorizontal);
			SYSTEMEVENT.removeEventListener(SPLITBAR.iframe[i].contentWindow.document, "mouseup", SPLITBAR.upHandlerHorizontal);
		}	

		if (SPLITBAR.beforeObj.style.display != "none" && SPLITBAR.Y != 0)
		{
			if (SPLITBAR.beforeObj.style.height.indexOf("%") != -1) SPLITBAR.beforeObj.style.height = SPLITBAR.beforeObj.clientHeight + "px";
			if (SPLITBAR.afterObj.style.height.indexOf("%") != -1) SPLITBAR.afterObj.style.height = SPLITBAR.afterObj.clientHeight + "px";
			var delta = SPLITBAR.start - SPLITBAR.Y;
			var h = (SPLITBAR.beforeObj.style.height == "") ? SPLITBAR.beforeObj.clientHeight - delta : parseInt(SPLITBAR.beforeObj.style.height) - delta;
			SPLITBAR.beforeObj.style.height = (h < 0) ? "0px" :  h + "px";
			if (h < 0) delta = delta + h;
			h = (SPLITBAR.afterObj.style.height == "") ? SPLITBAR.afterObj.clientHeight + delta : parseInt(SPLITBAR.afterObj.style.height) + delta;
			SPLITBAR.afterObj.style.height = (h < 0) ? "0px" :  h + "px";
		}
		SYSTEMEVENT.stopPropagation(event);
		SPLITBAR.obj.style.zIndex = "997";
		Resize();
	},

	upHandlerVertical : function (event)
	{
		SYSTEMEVENT.removeEventListener(document, "mouseup", SPLITBAR.upHandlerVertical);
		SYSTEMEVENT.removeEventListener(document, "mousemove", SPLITBAR.moveHandlerVertical);
		var length = SPLITBAR.iframe.length;
		for (var i = 0; i < length; i++)
		{
			SYSTEMEVENT.removeEventListener(SPLITBAR.iframe[i].contentWindow.document, "mousemove", SPLITBAR.moveHandlerVertical);
			SYSTEMEVENT.removeEventListener(SPLITBAR.iframe[i].contentWindow.document, "mouseup", SPLITBAR.upHandlerVertical);
		}	

		if (SPLITBAR.beforeObj.style.display != "none" && SPLITBAR.X != 0)
		{
			if (SPLITBAR.beforeObj.style.width.indexOf("%") != -1) SPLITBAR.beforeObj.style.width = SPLITBAR.beforeObj.clientWidth + "px";
			if (SPLITBAR.afterObj.style.width.indexOf("%") != -1) SPLITBAR.afterObj.style.width = SPLITBAR.afterObj.clientWidth + "px";
			var delta = SPLITBAR.start - SPLITBAR.X;
			var w = (SPLITBAR.beforeObj.style.width == "") ? SPLITBAR.beforeObj.clientWidth - delta : parseInt(SPLITBAR.beforeObj.style.width) - delta;
			SPLITBAR.beforeObj.style.width = (w < 0) ? "0px" :  w + "px";
			if (w < 0) delta = delta + w;
			w = (SPLITBAR.afterObj.style.width == "") ? SPLITBAR.afterObj.clientWidth + delta : parseInt(SPLITBAR.afterObj.style.width) + delta;
			SPLITBAR.afterObj.style.width = (w < 0) ? "0px" :  w + "px";
		}
		SYSTEMEVENT.stopPropagation(event);
		SPLITBAR.obj.style.zIndex = "997";
		Resize();
	},

	initHeight : function (objname)
	{
		var delta = 0;
		SPLITBAR.obj = $(objname);
		SPLITBAR.getAroundObj();
		SPLITBAR.afterObj.style.marginLeft = "11px";
		SPLITBAR.obj.style.height = (SPLITBAR.beforeObj.clientHeight > SPLITBAR.afterObj.clientHeight) ? SPLITBAR.beforeObj.clientHeight + "px" : SPLITBAR.afterObj.clientHeight + "px";
		if ((SYSTEMBROWSER.isIE() && SYSTEMBROWSER.getVersion() < 8) || SYSTEMBROWSER.isFirefox()) delta = 8;
		else delta = 10;
  		SPLITBAR.obj.style.left = (SPLITBAR.afterObj.offsetLeft - delta) + "px";
		SPLITBAR.obj.style.display = "block";
		SPLITBAR.obj.style.paddingTop = (SPLITBAR.obj.parentNode.clientHeight / 2) + "px";
	},

	initWidth : function (objname)
	{
		var delta = 0;
		SPLITBAR.obj = $(objname);
		SPLITBAR.getAroundObj();
		SPLITBAR.afterObj.style.marginTop = "11px";
		SPLITBAR.obj.style.width = (SPLITBAR.beforeObj.clientWidth > SPLITBAR.afterObj.clientWidth) ? SPLITBAR.beforeObj.clientWidth + "px" : SPLITBAR.afterObj.clientWidth + "px";
		if ((SYSTEMBROWSER.isIE() && SYSTEMBROWSER.getVersion() < 8) || SYSTEMBROWSER.isFirefox()) delta = 8;
		else delta = 10;
  		SPLITBAR.obj.style.top = (SPLITBAR.afterObj.offsetTop - delta) + "px";
		SPLITBAR.obj.style.display = "block";
	}
};

var SPLITBAR = new clsSplitbar();
