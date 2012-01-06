/**
* Class NOTE
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsNote()
{
	this.obj = undefined;
	this.deltaX = 0;
	this.deltaY = 0;
}

clsNote.prototype = 
{
	initObj : function(id)
	{
		var noteObj = $(id);
		var noteContainer = $(id + "_container");
		var noteImg = $(id + "_img");
		var widthOBJ = noteObj.clientWidth;
		var heightOBJ = noteObj.clientHeight;
		noteContainer.style.display = "none";
		noteContainer.style.paddingLeft = (widthOBJ * 18 / 322) + "px";
		noteContainer.style.width = (widthOBJ - (widthOBJ * 20 / 322)) + "px";
		noteContainer.style.height = (heightOBJ - (heightOBJ * 25 / 322)) + "px";
		noteContainer.style.paddingLeft = (widthOBJ * 18 / 322) + "px";
		noteContainer.style.display = "block";
	},

	startMove : function(obj, event)
	{
		NOTE.obj = obj;
		if (!event) event = window.event;
		NOTE.deltaX = event.clientX - obj.offsetLeft;
		NOTE.deltaY = event.clientY - obj.offsetTop;
		SYSTEMEVENT.addEventListener(document, "mousemove", NOTE.Move);
		SYSTEMEVENT.addEventListener(document, "mouseup", NOTE.MoveUP);
		SYSTEMEVENT.stopPropagation(event);
		SYSTEMEVENT.preventDefault(event);
	},

	Move : function(event)
	{
		if (!event) event = window.event;
		NOTE.obj.style.left = event.clientX - NOTE.deltaX +  "px";
		NOTE.obj.style.top = event.clientY - NOTE.deltaY +"px";
		SYSTEMEVENT.stopPropagation(event);
	},

	MoveUP : function(event)
	{
		SYSTEMEVENT.removeEventListener(document, "mousemove", NOTE.Move);
		SYSTEMEVENT.removeEventListener(document, "mouseup", NOTE.MoveUP);
		SYSTEMEVENT.stopPropagation(event);
	}
}

var NOTE = new clsNote();