/**************************************************
* Class DRAG
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
* Much of the code was made available by www.youngpup.net
* Script featured on Dynamic Drive (http://www.dynamicdrive.com) 12.08.2005
**************************************************
* 10.28.2001 - fixed minor bug where events
* sometimes fired off the handle, not the root.
**************************************************/

function clsDrag()
{
	this.obj = null;
}

clsDrag.prototype =
{
	init : function(id, oRoot, minX, maxX, minY, maxY, bSwapHorzRef, bSwapVertRef, fXMapper, fYMapper)
	{
		o = $(id);
		o.onmousedown	= DRAG.start;
		o.hmode			= bSwapHorzRef ? false : true ;
		o.vmode			= bSwapVertRef ? false : true ;
		o.root = oRoot && oRoot != null ? oRoot : o ;
		/*
		if (o.hmode)  o.root.style.left = parseInt(ANIMATE.getProp(o.root, 'left'))+"px";
		if (o.vmode)  o.root.style.top  = parseInt(ANIMATE.getProp(o.root, 'top'))+"px";
		if (!o.hmode) o.root.style.right = parseInt(ANIMATE.getProp(o.root, 'right'))+"px";
		if (!o.vmode) o.root.style.bottom = parseInt(ANIMATE.getProp(o.root, 'bottom'))+"px";
		*/
		if (o.hmode  && isNaN(parseInt(o.root.style.left  ))) o.root.style.left   = "0px";
		if (o.vmode  && isNaN(parseInt(o.root.style.top   ))) o.root.style.top    = "0px";
		if (!o.hmode && isNaN(parseInt(o.root.style.right ))) o.root.style.right  = "0px";
		if (!o.vmode && isNaN(parseInt(o.root.style.bottom))) o.root.style.bottom = "0px";

		o.minX	= typeof minX != 'undefined' ? minX : null;
		o.minY	= typeof minY != 'undefined' ? minY : null;
		o.maxX	= typeof maxX != 'undefined' ? maxX : null;
		o.maxY	= typeof maxY != 'undefined' ? maxY : null;

		o.xMapper = fXMapper ? fXMapper : null;
		o.yMapper = fYMapper ? fYMapper : null;

		o.root.onDragStart	= new Function();
		o.root.onDragEnd	= new Function();
		o.root.onDrag		= new Function();
	},

	start : function(e)
	{
		var o = DRAG.obj = this;
		e = DRAG.fixE(e);

		var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
		var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right);
		o.root.onDragStart(x, y);

		o.lastMouseX	= e.clientX;
		o.lastMouseY	= e.clientY;

		if (o.hmode) {
			if (o.minX != null)	o.minMouseX	= e.clientX - x + o.minX;
			if (o.maxX != null)	o.maxMouseX	= o.minMouseX + o.maxX - o.minX;
		} else {
			if (o.minX != null) o.maxMouseX = -o.minX + e.clientX + x;
			if (o.maxX != null) o.minMouseX = -o.maxX + e.clientX + x;
		}

		if (o.vmode) {
			if (o.minY != null)	o.minMouseY	= e.clientY - y + o.minY;
			if (o.maxY != null)	o.maxMouseY	= o.minMouseY + o.maxY - o.minY;
		} else {
			if (o.minY != null) o.maxMouseY = -o.minY + e.clientY + y;
			if (o.maxY != null) o.minMouseY = -o.maxY + e.clientY + y;
		}

		document.onmousemove	= DRAG.drag;
		document.onmouseup	= DRAG.end;
	},

	drag : function(e)
	{
		e = DRAG.fixE(e);
		var o = DRAG.obj;
		var ey	= e.clientY;
		var ex	= e.clientX;

		var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
		var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right);
		var nx, ny;

		if (o.minX != null) ex = o.hmode ? Math.max(ex, o.minMouseX) : Math.min(ex, o.maxMouseX);
		if (o.maxX != null) ex = o.hmode ? Math.min(ex, o.maxMouseX) : Math.max(ex, o.minMouseX);
		if (o.minY != null) ey = o.vmode ? Math.max(ey, o.minMouseY) : Math.min(ey, o.maxMouseY);
		if (o.maxY != null) ey = o.vmode ? Math.min(ey, o.maxMouseY) : Math.max(ey, o.minMouseY);

		nx = x + ((ex - o.lastMouseX) * (o.hmode ? 1 : -1));
		ny = y + ((ey - o.lastMouseY) * (o.vmode ? 1 : -1));

		if (o.xMapper)		nx = o.xMapper(y)
		else if (o.yMapper)	ny = o.yMapper(x)

		DRAG.obj.root.style[o.hmode ? "left" : "right"] = nx + "px";
		DRAG.obj.root.style[o.vmode ? "top" : "bottom"] = ny + "px";
		DRAG.obj.lastMouseX	= ex;
		DRAG.obj.lastMouseY	= ey;

		DRAG.obj.root.onDrag(nx, ny);
		return false;
	},

	end : function()
	{
		document.onmousemove = null;
		document.onmouseup   = null;
		DRAG.obj.root.onDragEnd(parseInt(DRAG.obj.root.style[DRAG.obj.hmode ? "left" : "right"]), 
								parseInt(DRAG.obj.root.style[DRAG.obj.vmode ? "top" : "bottom"]));
		DRAG.obj = null;
	},

	fixE : function(e)
	{
		if (typeof e == 'undefined') e = window.event;
		if (typeof e.layerX == 'undefined') e.layerX = e.offsetX;
		if (typeof e.layerY == 'undefined') e.layerY = e.offsetY;
		return e;
	}
}

var DRAG = new clsDrag();