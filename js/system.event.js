/**
* Class SYSTEMEVENT
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsSystemEvent()
{
	this.messagebox			= $("pageMessage");
	this.messagebox_back		= $("pageMessageBack");
	this.messagebox_ghost 	= $("pageMessageGhost");
	this.loaderbox 			= $("pageLoader");
	this.pagelock 		 		= $("pageLock");
}

clsSystemEvent.prototype =
{
	errorJAVASCRIPT : function(message, url, row)
	{
		if (SYSTEMEVENT.messagebox.style.display!="block") SYSTEMEVENT.showMessage(message, LANG.translate("EVENT001"), url, row);
	},

	errorXML : function(message)
	{
 		if (SYSTEMEVENT.messagebox.style.display!="block") SYSTEMEVENT.showMessage(message, LANG.translate("EVENT002"), self.location.href, 0);
	},

	errorHTML : function(message)
	{
 		if (SYSTEMEVENT.messagebox.style.display!="block") SYSTEMEVENT.showMessage(message, LANG.translate("EVENT003"), self.location.href, 0);
	},

	show : function(html) 
	{
		SYSTEMEVENT.messagebox.innerHTML = html;
		SYSTEMEVENT.loaderbox.style.display = "none";
		SYSTEMEVENT.pagelock.style.display = "block";
		SYSTEMEVENT.messagebox_back.style.display = "block";
		SYSTEMEVENT.messagebox.style.display = "block";
		ANIMATE.animate(SYSTEMEVENT.messagebox_back, 'opacity:0.8', '500','', 'default');
		ANIMATE.animate(SYSTEMEVENT.messagebox, 'opacity:1', '500','', 'default');
		SYSTEMEVENT.addEventListener(window, "resize", SYSTEMEVENT.sizeMessage);
		SYSTEMEVENT.sizeMessage();
	},

	showMessageGhost : function(message, note, custom)
	{
		if (custom == undefined)
		{
		  var msg = '';
		  if (note && note != '') msg = "<br><font color='gray'>"+note+"</font>";
		  SYSTEMEVENT.messagebox_ghost.innerHTML = "<p style='width:350;margin-left:50px'><b>"+message+"</b>"+msg;
		} 
		else SYSTEMEVENT.messagebox_ghost.innerHTML = custom;
		ANIMATE.ghost(SYSTEMEVENT.messagebox_ghost.id);
	},

	showMessage : function(message, title, url, row) 
	{
		var errortxt =  '<h2 class="dialog_title"><span>'+title+'</span></h2>';
 		errortxt += '<div id="pageMessageContent" class="dialog_content">';
 		errortxt += '	<div class="dialog_body">';
		errortxt += '		<div class="clearfix">';
		var start = message.indexOf('<div class="dialog_content_img"></div>',0);
		if (start>-1) 
		{
			var end = message.indexOf('<div class="dialog_buttons">',0);
			errortxt += message.substr(start, end-start);
		}
		else 
		{
	 		errortxt += '			<div class="dialog_content_img"></div>';
			errortxt += '			<div class="dialog_content_txt">';
			errortxt += '				<div><br><b>'+LANG.translate("EVENT004")+url+'</b></div>';
			errortxt += '				<div><br><b>'+LANG.translate("EVENT005")+row+'</b></div>';
			errortxt += '				<div style="margin-top: 10px;"><b>'+LANG.translate("EVENT006")+'</b> '+message+'</data></div>';
			errortxt += '			</div>';
			errortxt += '		</div>';
			errortxt += '	</div>';
			errortxt += '</div>'; 
		}
		errortxt += '<div class="dialog_buttons">';
	  	errortxt += '	<input type="button" onclick="SYSTEMEVENT.Close();" value="Chiudi">';
		errortxt += '</div>';
		SYSTEMEVENT.show(errortxt);
	},

	showHTML : function(title, html) 
	{
 		var div = '<h2 class="dialog_title"><span>'+title+'</span></h2>';
  		div += '<div id="pageMessageContent" class="dialog_content">';
  		div += '	<div class="dialog_body">';
 		div += '		<div class="clearfix">';
 		div += '			<div class="dialog_content_txt">'+html+'</div>';
 		div += '		</div>';
 		div += '	</div>';
 		div += '</div>'; 
		div += '<div class="dialog_buttons">';
	  	div += '	<input type="button" onclick="SYSTEMEVENT.Close();" value="Chiudi">';
		div += '</div>';
		SYSTEMEVENT.show(div);
	},

	sizeMessage : function()
	{
		var obj = $('pageMessageContent');
		obj.style.height = (obj.offsetParent.offsetHeight - 64)+ 'px'
	},

	Close : function() 
	{
		SYSTEMEVENT.messagebox.style.display = "none";
		SYSTEMEVENT.loaderbox.style.display = "none";
		SYSTEMEVENT.pagelock.style.display = "none";
		SYSTEMEVENT.removeEventListener(window, "resize", SYSTEMEVENT.sizeMessage);
		ANIMATE.animate(SYSTEMEVENT.messagebox_back, 'opacity:0', '500', function(){SYSTEMEVENT.messagebox_back.style.display = "none";}, 'default');
		ANIMATE.animate(SYSTEMEVENT.messagebox, 'opacity:0', '500', function(){SYSTEMEVENT.messagebox.style.display = "none";}, 'default');

	},

	stopPropagation : function (event)
	{
		if (!event) event = window.event;
		if (event.stopPropagation) event.stopPropagation(); //DOM level 2
		else event.cancelBubble = true; //IE
	},

	preventDefault : function (event)
	{
		if (!event) event = window.event;
		if (event.preventDefault) event.preventDefault(); //DOM level 2
		else event.returnValue = false; //IE
	},

	addEventListener : function (obj, eventname, fnz)
	{
		if (document.addEventListener) obj.addEventListener(eventname, fnz, true); //DOM level 2
		else if (document.attachEvent) obj.attachEvent("on" + eventname, fnz); //IE
	},

	removeEventListener : function (obj, eventname, fnz)
	{
		if (document.removeEventListener) obj.removeEventListener(eventname, fnz, true); //DOM level 2
		else if (document.detachEvent) obj.detachEvent("on" + eventname, fnz); //IE
	},

	addAfterCustomFunction : function (classname, fnz, customfnz)
	{
		eval(classname + ".__" + fnz + " = " + classname + "." + fnz + ";");
		eval(classname + "." + fnz + " = function(){" + classname + ".__" + fnz + ".apply(this, arguments); " + customfnz + ".apply(this, arguments);}");
	},

	addBeforeCustomFunction : function (classname, fnz, customfnz)
	{
		eval(classname + ".__" + fnz + " = " + classname + "." + fnz + ";");
		eval(classname + "." + fnz + " = function(){ var ris = " + customfnz + ".apply(this, arguments); if(ris!=undefined && !ris) return; " + classname + ".__" + fnz + ".apply(this, arguments);}");
	},

	removeCustomFunction : function (classname, fnz)
	{
		eval(classname + "." + fnz + " = " + classname + ".__" + fnz + ";");
	},

	setFocus : function (obj)
	{
		setTimeout(function(){try {obj.focus();} catch (e) {}},0);
	}
}

var SYSTEMEVENT = new clsSystemEvent();