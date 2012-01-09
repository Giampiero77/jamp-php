/**
* Class GRIDDS
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsGridds()
{
}

clsGridds.prototype = 
{
	getAllCol : function(obj)
	{
		//col
		obj.colObj.cols = new Array();
		obj.colObj.width = 0;
		var length = obj.colObj.childNodes.length; 
		for (var i = 0; i < length; i++) if (obj.colObj.childNodes[i].nodeType == 1)
		{
			obj.colObj.childNodes[i].col = obj.colObj.cols.length;
			obj.colObj.cols[obj.colObj.cols.length] = obj.colObj.childNodes[i];
			if (obj.colObj.childNodes[i].className == obj.className + "_colDOWN")
			{
				obj.order = true;
				obj.colObj.childNodes[i].defaultCursor = "pointer";
				obj.colObj.childNodes[i].order = true;
			} else obj.colObj.childNodes[i].defaultCursor = "default";
			if (obj.colObj.childNodes[i].style.width == "") obj.colObj.childNodes[i].style.width = obj.colObj.childNodes[i].clientWidth + "px";
			obj.colObj.width += obj.colObj.childNodes[i].clientWidth + 1;
		}
		obj.colObj.style.width = obj.colObj.width + "px";
		
		//head
		if (obj.headObj != undefined)
		{
			obj.headObj.cols = new Array();
			var length = obj.headObj.childNodes.length;
			for (var i = 0; i < length; i++) if (obj.headObj.childNodes[i].nodeType == 1)
			{
				obj.headObj.childNodes[i].span = parseInt(obj.headObj.childNodes[i].style.width);
				obj.headObj.cols[obj.headObj.cols.length] = obj.headObj.childNodes[i];
			}
			obj.headObj.style.width = obj.colObj.width + "px";
		}

		//row0
		obj.rowZero.cols = new Array();
		var length = obj.rowZero.childNodes.length;
		for (var i = 0; i < length; i++) if (obj.rowZero.childNodes[i].nodeType == 1) obj.rowZero.cols[obj.rowZero.cols.length] = obj.rowZero.childNodes[i];
	},

	setWidthHead : function(obj)
	{
		if (obj.headObj != undefined)
		{
			if (obj.colObj.cols[0] == undefined)
			{
				this.setWidthHeadNoCols(obj);
				return;
			}
			var pos = 0;
			var tot = 0;
			var offset = obj.colObj.cols[0].clientWidth - parseInt(obj.colObj.cols[0].style.width) + 1;
			var length = obj.headObj.cols.length;
			for (var i = 0; i < length; i++)
			{
				var span = obj.headObj.cols[i].span;
				var endpos = (pos+span <= obj.colObj.cols.length) ? pos+span : obj.colObj.cols.length;
				for (var ii = pos; ii < endpos; ii++) tot += parseInt(obj.colObj.cols[ii].style.width);
				obj.headObj.cols[i].style.width = tot + offset * (span - 1) + "px";
				pos += span;
				tot = 0;
			}
		}
	},

	setWidthHeadNoCols : function(obj)
	{
		if (obj.headObj != undefined)
		{
			var length = obj.headObj.cols.length;
			for (var i = 0; i < length; i++)
			{
				obj.headObj.cols[i].style.width = "100px";
			}
			obj.headObj.style.width = "auto";
		}
	},

	setWidthRowZero : function(obj)
	{
		obj.rowZero.refreshROW = "";
		//Row0
		var length = obj.rowZero.cols.length;
		for (var i = 0; i < length; i++)
		{
			var labelwidth = 4;
			obj.rowZero.cols[i].style.width = obj.colObj.cols[i].clientWidth + "px";
			var length1 = obj.rowZero.cols[i].childNodes.length;
			for (var ii = 0; ii < length1; ii++)
			{
				var objSRC = obj.rowZero.cols[i].childNodes[ii];
				if (objSRC.p != undefined)
				{
					var testcode = objSRC.p.typeObj.toUpperCase() + ".refreshObj(\"" + objSRC.id + "\"); ";
					try { eval(testcode); }
					catch(e)	{ testcode = ""; };
					obj.rowZero.refreshROW += testcode;
					obj.rowZero.cols[i].colOBJ = objSRC;
				} 
				else if (objSRC.nodeType == 1) labelwidth += objSRC.offsetWidth;
			}
			obj.rowZero.cols[i].colOBJ.style.width = (parseInt(obj.rowZero.cols[i].style.width) - labelwidth) + "px";
		}
	},

	setColOrder : function(obj)
	{
		var orders = obj.DS.p.DSorder.split(",");
		var order = new Array();
		var length = orders.length;
		for (var i = 0; i < length; i++)
		{
			var check = orders[i].split(" DESC");
			if (check.length > 1) order[check[0]] = true;
			else order[check[0]] = false;
		}
		var length = obj.colObj.cols.length;
		for (var i = 0; i < length; i++)
		{
			if (obj.colObj.cols[i].order != undefined)
			{
				var check = order[obj.rowZero.cols[i].colOBJ.p.dsItem];
				if (check == true)
				{
					obj.colObj.cols[i].className = obj.className + "_colUP";
					obj.colObj.cols[i].order= obj.rowZero.cols[i].colOBJ.p.dsItem;
				}
				if (check == false)
				{
					obj.colObj.cols[i].className = obj.className + "_colDOWN";
					obj.colObj.cols[i].order= obj.rowZero.cols[i].colOBJ.p.dsItem + " DESC";
				}
				if (check == undefined)
				{
					obj.colObj.cols[i].className = obj.className + "_colNONE";
					obj.colObj.cols[i].order= obj.rowZero.cols[i].colOBJ.p.dsItem + " DESC";
				}
			}
		}
	},

	addRowTimeout : function(obj, i, numrows)
	{
		if (i<numrows) 
		{
			 AJAX.loader(true);
			 this.addROW(obj, i);
			 GRIDDS.obj = obj;
			 GRIDDS.i = i;
			 GRIDDS.numrows = numrows;
			 setTimeout(function(){GRIDDS.addRowTimeout(GRIDDS.obj, GRIDDS.i+1, GRIDDS.numrows);},0);
		}
		else 
		{
			 this.getDSpos(obj); //Set current row
			 obj.bodyObj.style.display = "";
			 AJAX.loader(false);
		}
	},

	showROW : function(obj)
	{  
		obj.bodyObj.rows = Array();
		obj.bodyObj.innerHTML = "";
		obj.startROW = 0;
		obj.bodyObj.style.display = "none";
		var length = obj.DS.DSresult.length;
		if (obj.DS != undefined) 
		{
		    if (length < 50) 
		    {
			  for (var i=1; i < (length); i++) this.addROW(obj, i);	
			  this.getDSpos(obj); //Set current row
			  obj.bodyObj.style.display = "";
		    }  
		    else 
		    {
			  this.addRowTimeout(obj, 1, length);	
		    }
		}
	},
	
	addCOL : function(obj, newRow, i, row, col, numobj)
	{
		for (var ii = 0; ii < numobj; ii++) //OBJ
		{
			var objSRC = obj.rowZero.childNodes[i].childNodes[ii];
			var objDES = newRow.childNodes[i].childNodes[ii];
			if (objSRC.p != undefined)
			{
				objDES.id = obj.id + "_" + col + "_" + row;
				objDES.row = row;
				objDES.col = col;
				objDES.p = SYSTEMBROWSER.clone(objSRC.p);
				newRow.childNodes[i].colOBJ = objDES;
				newRow.cols[col] = objDES;
				var testcode = objDES.p.typeObj.toUpperCase() + ".getDsValue(\"" + objDES.id + "\"); ";
				try { eval(testcode); }
				catch(e){ testcode = ""; };
				newRow.refreshROW += testcode;
			}
		}
	},

	addROW : function(obj, row)
	{
		var col = 0;
		var newRow = obj.bodyObj.appendChild(obj.rowZero.cloneNode(true));
		var refreshCode = "";
		newRow.className = newRow.className.replace("_row0", "_row");
		newRow.id = obj.id + "_row" + row;
		newRow.row = row;
		if (row % 2 == 0) newRow.className = newRow.className + "_even"; 
		newRow.style.width = obj.colObj.width + "px";
		newRow.cols = new Array();
		newRow.refreshROW = '';
		var length = newRow.childNodes.length;
		for (var i = 0; i < length; i++)
		{
			if (newRow.childNodes[i].nodeType == 1) //COL
			{
				newRow.cols[col] = newRow.childNodes[i];
				col++;
				newRow.childNodes[i].id = obj.id + "_row" + row + "_col" + col;
				var length1 = newRow.childNodes[i].childNodes.length;
				this.addCOL(obj, newRow, i, row, col, length1);
			}
		}
		obj.bodyObj.rows[row] = newRow;
	},

	 changeColor : function(obj, row, color)
	 {
		  var newRow = obj.bodyObj.rows[row];
		  var length = newRow.childNodes.length;
		  for (var i = 0; i < length; i++)
		  {
			 if (newRow.childNodes[i].nodeType == 1)
			 {
				  newRow.childNodes[i].style.backgroundColor = color;
				  var length1 = newRow.childNodes[i].childNodes.length;
				  for (var ii = 0; ii < length1; ii++)
				  {
					 var node = newRow.childNodes[i].childNodes[ii]; 
					 if (node.nodeType == 1) node.style.backgroundColor = color;
					 if (node.tagName == 'SPAN' && (node.innerHTML == '' || node.innerHTML == undefined)) node.innerHTML = '&nbsp';
				  }   
			 }   
		  }
	 },

	 changeColorCol : function(newRow, i, numobj, color)
	 {
	      for (var ii = 0; ii < numobj; ii++) //OBJ
	      {
		  var node = newRow.childNodes[i].childNodes[ii]; 
		  if (node.nodeType == 1) node.style.backgroundColor = color;
		  if (node.tagName == 'SPAN' && node.innerHTML == '') node.innerHTML = '&nbsp';
	      }
	 },

	getDSpos : function(obj)
	{
		if (obj.DS == undefined) return;
 		if (obj.DS.DSpre > 0) obj.bodyObj.rows[obj.DS.DSpre].className = (obj.bodyObj.rows[obj.DS.DSpre].row % 2 == 0) ? obj.className + "_row_even" : obj.className + "_row";
 		if (obj.DS.DSpos > 0 && obj.bodyObj.rows[obj.DS.DSpos]!=undefined)
		{
			obj.bodyObj.rows[obj.DS.DSpos].className = obj.className + "_row_pos";
			if (obj.p.autoscroll == true)
			{
				var topscroll = -(obj.bodyObj.clientHeight - obj.bodyObj.rows[obj.DS.DSpos].offsetTop + obj.bodyObj.offsetTop - obj.bodyObj.rows[obj.DS.DSpos].clientHeight );
				obj.bodyObj.scrollTop = (topscroll < 0) ? 0 : topscroll;
			}
		}
		if (obj.DS.DSpos < 0 && obj.p.insertNew == true) this.addROW(obj, obj.DS.DSpos);
	},

	unselected : function(obj)
	{
		for (var i in obj.DS.DSmultipos)
		{
			if (obj.DS.DSmultipos.hasOwnProperty(i)) obj.bodyObj.rows[i].className = (obj.bodyObj.rows[i].row % 2 == 0) ? obj.className + "_row_even" : obj.className + "_row";
		}
		obj.DS.DSmultipos = Array();
		obj.UpROW = undefined;
		obj.DownROW = undefined;
	},

	clickROW : function(obj, row, event)
	{
		if (!event) event = window.event;
		if (event.ctrlKey == false && event.shiftKey == false)
		{
			if (obj.DS.DSmultipos.length > 0) this.unselected(obj);
			DS.moveRow(obj.p.dsObj, row);
		}
		else this.selectedROW(obj, row, event.shiftKey, event.ctrlKey);
	},

	selectedROW : function(obj, row, shiftKey, ctrlKey)
	{
		var start = (shiftKey) ? obj.startROW : row;
		start = (start == 0) ? row : start;
		var stop = row;
		if (start > row)
		{
			stop = start;
			start = row;
		}
		for (var i = start; i <= stop; i++)
		{
			if (shiftKey == false && ctrlKey && obj.bodyObj.rows[i].className == obj.className + "_row_selected") 
			{
				obj.bodyObj.rows[i].className = (obj.bodyObj.rows[i].row % 2 == 0) ? obj.className + "_row_even" : obj.className + "_row";
				delete obj.DS.DSmultipos[i];
			}
			else 
			{
				obj.bodyObj.rows[i].className = obj.className + "_row_selected";
				obj.DS.DSmultipos[i] = "";
			}
		}
		obj.startROW = row;
	},

	refreshObj : function(id)
	{
		var obj = $(id);
		if (obj.init == undefined) return;
		obj.DS = $(obj.p.dsObj);
		if (obj.DS == undefined || obj.DS.DSresult == undefined) return;
		if (obj.rowZero.refreshROW == undefined) this.setWidthRowZero(obj); //set width Row Zero
		else eval(obj.rowZero.refreshROW);
		if (obj.order) this.setColOrder(obj); //Set col's order
		this.showROW(obj); //Show row ds
	},

	initObj : function(id)
	{
		var obj = $(id);
		obj.init = true;
		obj.labelObj = $(id + "_label");
		obj.headObj = $(id + "_head");
		obj.headsObj = $(id + "_heads");
		obj.headsObj.style.display = "";
		obj.colObj = $(id + "_col");
		obj.bodyObj = $(id + "_body");	
		obj.rowZero = $(id + "_row0");
     	if (obj.clientHeight>0) obj.bodyObj.style.height = (obj.labelObj == undefined) ? obj.clientHeight - obj.headsObj.offsetHeight + "px" : obj.clientHeight - obj.headsObj.offsetHeight - obj.labelObj.offsetHeight + "px";
		if (obj.headObj != undefined) obj.headObj.style.left = "0px";
		if (obj.colObj != undefined)
		{
			obj.colObj.style.left = "0px";
			this.getAllCol(obj); //init head, col, rowzero
			this.setWidthHead(obj); //set width Head
			SYSTEMEVENT.addEventListener(obj.bodyObj, "scroll", function(event) { GRIDDS.scrollBody(id); });
		} 
		else obj.init = undefined;
	},

	displayObj : function(id)
	{
		var obj = $(id);
		if (obj.init) return;
		this.initObj(id);
 		if (obj.bodyObj != undefined) obj.bodyObj.style.width = obj.offsetWidth + "px";
 		if (obj.headsObj != undefined) obj.headsObj.style.width = obj.offsetWidth + "px";
 		if (obj.p != undefined && obj.p.dsObj != undefined) this.refreshObj(id);
	},

	autoHeight : function(id)
	{
		var obj = $(id);
		SYSTEMBROWSER.autoHeight(id);
		if (obj.clientHeight == 0) return;
		if (obj.bodyObj != undefined)
		{
			var h = (obj.labelObj == undefined) ? obj.clientHeight - obj.headsObj.offsetHeight  : obj.clientHeight - obj.headsObj.offsetHeight - obj.labelObj.offsetHeight ;
			obj.bodyObj.style.height = (h < 0) ? "0px" : h + "px";
		}
	},

	autoWidth : function(id)
	{
		var obj = $(id);
		var hObj = 100;
		SYSTEMBROWSER.autoWidth(id);
		
		if (obj.clientWidth == 0) return;
 		if (obj.bodyObj != undefined)
		{
			 obj.bodyObj.style.width = obj.clientWidth + "px";
			 if (obj.clientHeight>0) 
			 {
				  if (obj.labelObj == undefined) {
						hObj = (obj.clientHeight - obj.headsObj.offsetHeight)>0 ? (obj.clientHeight - obj.headsObj.offsetHeight) : 100;
				  } else {
						hObj = (obj.clientHeight - obj.headsObj.offsetHeight - obj.labelObj.offsetHeight)>0 ? (obj.clientHeight - obj.headsObj.offsetHeight - obj.labelObj.offsetHeight) : 100;						
				  }						
				  obj.bodyObj.style.height = hObj + "px";
			 }
		}
 		if (obj.headsObj != undefined) obj.headsObj.style.width = obj.clientWidth + "px";
 		GRIDDS.scrollBody(id);
	},

	setFocus : function(id)
	{
		if (document.activeElement && document.activeElement.p != undefined && document.activeElement.p.typeObj == "page") SYSTEMEVENT.setFocus($(id + "_body")); 
	},

	setFocusInput : function(row)
	{
		var regexp = new RegExp("_" + document.activeElement.row + "$");
		var newid = document.activeElement.id.replace(regexp, "_" + row);
 		var newid = $(newid);
		SYSTEMEVENT.setFocus(newid); 
		if (newid.select) newid.select();
	},

	scrollBody : function(id)
	{
		var obj = $(id);
		if (obj == undefined) return;
		if (obj.headObj != undefined) obj.headObj.style.left = -obj.bodyObj.scrollLeft + "px";
		if (obj.colObj != undefined) obj.colObj.style.left = -obj.bodyObj.scrollLeft + "px";
	},

	keyUp : function (id, event)
	{
		if (!event) event = window.event;
		var keynum = (window.event) ? event.keyCode : event.which;
		var gridobj = $(id);
		var dsObj = gridobj.DS;
		if (dsObj.DSpos < 1 || dsObj.lock == true) return;
		switch (keynum) 
		{
			case 9:
				objTarget = (event.srcElement == undefined) ? event.target : event.srcElement;
				if (objTarget == undefined) return;
				var pos = (objTarget.row == undefined) ? dsObj.DSpos : objTarget.row;

				if (event.shiftKey == true && pos != dsObj.DSpos)
				{
					eval(dsObj.id+"MovePrev();");
					this.setFocusInput(dsObj.DSpos);
				}

				if (event.shiftKey == false && pos!= dsObj.DSpos)
				{
					eval(dsObj.id+"MoveNext();");
					this.setFocusInput(dsObj.DSpos);
				}
			break;
		}
	},

	keyDown : function (id, event)
	{
		if (!event) event = window.event;
		var keynum = (window.event) ? event.keyCode : event.which;
		var dsObj = $(id).DS;
		if (dsObj.DSpos < 1 || dsObj.lock == true) return;
		switch (keynum) 
		{
			case 36: //First
				eval(dsObj.id+"MoveFirst();");
				if (document.activeElement.id != id + "_body") this.setFocusInput(dsObj.DSpos) ;
 				SYSTEMEVENT.preventDefault(event);
			break;

			case 35: //Last
				eval(dsObj.id+"MoveLast();");
				if (document.activeElement.id != id + "_body") this.setFocusInput(dsObj.DSpos);
 				SYSTEMEVENT.preventDefault(event);
			break;

			case 33: //Page Up
				DS.movePrevPage(dsObj.id);
				if (document.activeElement.id != id + "_body") this.setFocusInput(dsObj.DSpos);
 				SYSTEMEVENT.preventDefault(event);
			break;

			case 34: //Page Down
				DS.moveNextPage(dsObj.id);
				if (document.activeElement.id != id + "_body") this.setFocusInput(dsObj.DSpos);
 				SYSTEMEVENT.preventDefault(event);
			break;

			case 38: //Up
				if (event.shiftKey == true)
				{
					var obj = $(id);
					obj.startROW = dsObj.DSpos;
					if (obj.UpROW == undefined) 
					{
						if (obj.DownROW != undefined) this.unselected(obj);
						obj.UpROW = dsObj.DSpos;
					}
					if (obj.UpROW > 1) obj.UpROW--;
					this.selectedROW(obj, obj.UpROW , event.shiftKey, event.ctrlKey);
				}
				else
				{
					eval(dsObj.id+"MovePrev();");
					if (document.activeElement.id != id + "_body") this.setFocusInput(dsObj.DSpos);
				}
 				SYSTEMEVENT.preventDefault(event);
			break;

			case 40: //Down
				if (event.shiftKey == true)
				{
					var obj = $(id);
					obj.startROW = dsObj.DSpos;
					if (obj.DownROW == undefined) 
					{
						  if (obj.UpROW != undefined) this.unselected(obj);
						  obj.DownROW = dsObj.DSpos;
					}
					if (obj.DownROW < dsObj.DSresult.length -1) obj.DownROW++;
					this.selectedROW(obj, obj.DownROW , event.shiftKey, event.ctrlKey);
				}
				else
				{
					eval(dsObj.id+"MoveNext();");
					if (document.activeElement.id != id + "_body") this.setFocusInput(dsObj.DSpos);
				}
 				SYSTEMEVENT.preventDefault(event);
			break;
		}
	},

	colMouseMove : function(colObj, event)
	{
		if (!event) event = window.event;
		var offX = event.offsetX | event.layerX;
 		offX = Math.abs(offX);
		if (offX < 10 && offX > 2) colObj.style.cursor = "w-resize";
  	 	else if (offX > (colObj.clientWidth - 20)) colObj.style.cursor = "e-resize";
		else colObj.style.cursor = colObj.defaultCursor;
	},

	colMouseDown : function(colObj, event)
	{
		if (!event) event = window.event;
		this.start = event.clientX;
		if (colObj.style.cursor == "w-resize") 
		{
			this.colObjNext = colObj;
			this.colObjPrev = colObj.parentNode.cols[colObj.col-1];
			this.startResize(event);
		}
		if (colObj.style.cursor == "e-resize")
		{
			this.colObjNext = colObj.parentNode.cols[colObj.col+1];
			this.colObjPrev = colObj;
			this.startResize(event);
		}
		if (colObj.style.cursor == "pointer" && colObj.order != undefined)
		{
			var dsObj = colObj.parentNode.parentNode.parentNode.DS;
			dsObj.p.DSorder = colObj.order;
			AJAX.dsmore(dsObj, "data=load&dsobjname=" + dsObj.id, false, true);
		}
	},

	startResize : function(event)
	{
		if (GRIDDS.colObjPrev == undefined) return;
		GRIDDS.delta = 0;
		GRIDDS.widthPrev = parseInt(GRIDDS.colObjPrev.style.width);
		if (GRIDDS.colObjNext != undefined) 
		{
			GRIDDS.widthNext = parseInt(GRIDDS.colObjNext.style.width);
			SYSTEMEVENT.addEventListener(document, "mousemove", GRIDDS.colResize);
			SYSTEMEVENT.addEventListener(document, "mouseup", GRIDDS.colMouseUp);
		}
		else //Last Col
		{
			GRIDDS.colObjNext = GRIDDS.colObjPrev.parentNode.parentNode.parentNode;
			GRIDDS.widthNext = parseInt(GRIDDS.colObjNext.colObj.width);
			SYSTEMEVENT.addEventListener(document, "mousemove", GRIDDS.lastColResize);
			SYSTEMEVENT.addEventListener(document, "mouseup", GRIDDS.lastColMouseUp);
		}
		SYSTEMEVENT.stopPropagation(event);
		SYSTEMEVENT.preventDefault(event);
	},

	colResize : function(event)
	{
		if (!event) event = window.event;
		var delta = GRIDDS.start - event.clientX;
		if ((GRIDDS.widthPrev - delta) > 0 && (GRIDDS.widthNext + delta) > 0)
		{
			GRIDDS.delta = GRIDDS.start - event.clientX;
			GRIDDS.colObjPrev.style.width = (GRIDDS.widthPrev - GRIDDS.delta) + "px";
			GRIDDS.colObjNext.style.width = (GRIDDS.widthNext + GRIDDS.delta) + "px";
		}
		SYSTEMEVENT.stopPropagation(event);
	},

	lastColResize : function(event)
	{
		if (!event) event = window.event;
		var delta = GRIDDS.start - event.clientX;
		if ((GRIDDS.widthPrev - delta) > 0 )
		{
			GRIDDS.delta = GRIDDS.start - event.clientX;
			GRIDDS.colObjPrev.style.width = (GRIDDS.widthPrev - GRIDDS.delta) +"px";
			GRIDDS.colObjNext.colObj.width = (GRIDDS.widthNext + (GRIDDS.delta * -1));
			GRIDDS.colObjNext.colObj.style.width = GRIDDS.colObjNext.colObj.width  + "px";
		}
		SYSTEMEVENT.stopPropagation(event);
	},

	colMouseUp : function(event)
	{
		SYSTEMEVENT.removeEventListener(document, "mousemove", GRIDDS.colResize);
		SYSTEMEVENT.removeEventListener(document, "mouseup", GRIDDS.colMouseUp);
		SYSTEMEVENT.stopPropagation(event);
		if (GRIDDS.delta != 0) GRIDDS.doResize(GRIDDS.colObjPrev.parentNode.parentNode.parentNode);
	},

	lastColMouseUp : function(event)
	{
		SYSTEMEVENT.removeEventListener(document, "mousemove", GRIDDS.lastColResize);
		SYSTEMEVENT.removeEventListener(document, "mouseup", GRIDDS.lastColMouseUp);
		SYSTEMEVENT.stopPropagation(event);
		if (GRIDDS.delta != 0)
		{
			GRIDDS.colObjNext = undefined;
			GRIDDS.doResize(GRIDDS.colObjPrev.parentNode.parentNode.parentNode);
		}
	},

	doResize : function(obj)
	{
		this.setWidthHead(obj);
		this.doResizeROWZero(obj)
		if (obj.bodyObj.rows == undefined) return;
		if (GRIDDS.colObjNext != undefined)
		{
			for (i in obj.bodyObj.rows)
			{
				if (obj.bodyObj.rows.hasOwnProperty(i))
				{
					var objPREV = obj.bodyObj.rows[i].cols[GRIDDS.colObjPrev.col];
					objPREV.style.width = GRIDDS.colObjPrev.clientWidth + "px";
					objPREV.colOBJ.style.width = parseInt(objPREV.colOBJ.style.width) - GRIDDS.delta + "px";
	
					var objNEXT = obj.bodyObj.rows[i].cols[GRIDDS.colObjNext.col];
					objNEXT.style.width = GRIDDS.colObjNext.clientWidth + "px";
					objNEXT.colOBJ.style.width = parseInt(objNEXT.colOBJ.style.width) + GRIDDS.delta + "px";
				}
			}
		}
		else //last Col
		{
			if (obj.headObj != undefined) obj.headObj.style.width = obj.colObj.width + "px";
			for (i in obj.bodyObj.rows)
			{
				if (obj.bodyObj.rows.hasOwnProperty(i))
				{
					obj.bodyObj.rows[i].style.width = obj.colObj.width + "px";
					var objPREV = obj.bodyObj.rows[i].cols[GRIDDS.colObjPrev.col];
					objPREV.style.width = GRIDDS.colObjPrev.clientWidth + "px";
					objPREV.colOBJ.style.width = parseInt(objPREV.colOBJ.style.width) - GRIDDS.delta + "px";
				}
			}
		}
	},

	doResizeROWZero : function(obj)
	{
		obj.rowZero.cols[GRIDDS.colObjPrev.col].style.width = GRIDDS.colObjPrev.clientWidth + "px";
		if (obj.rowZero.cols[GRIDDS.colObjPrev.col].colOBJ != undefined) obj.rowZero.cols[GRIDDS.colObjPrev.col].colOBJ.style.width = parseInt(obj.rowZero.cols[GRIDDS.colObjPrev.col].colOBJ.style.width) - GRIDDS.delta + "px";

		if (GRIDDS.colObjNext != undefined)
		{
			obj.rowZero.cols[GRIDDS.colObjNext.col].style.width = GRIDDS.colObjNext.clientWidth + "px";
			if (obj.rowZero.cols[GRIDDS.colObjNext.col].colOBJ != undefined) obj.rowZero.cols[GRIDDS.colObjNext.col].colOBJ.style.width = parseInt(obj.rowZero.cols[GRIDDS.colObjNext.col].colOBJ.style.width) + GRIDDS.delta + "px";
		}
	},

	saveRow : function(id)
	{
		var obj = $(id);
		if (obj.DS == undefined) return;
		var pos = obj.DS.DSmodpos;
		if (pos < 0)
		{
			if (obj.bodyObj.rows[pos] != undefined) obj.bodyObj.rows[pos].parentNode.removeChild(obj.bodyObj.rows[pos]);
			pos = obj.DS.DSpos;
			this.addROW(obj, pos);
		}
		eval(obj.bodyObj.rows[pos].refreshROW);
	}
}

var GRIDDS = new clsGridds();