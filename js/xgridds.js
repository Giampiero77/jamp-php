/**
* Class XGRIDDS
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsxGridds()
{
}

clsxGridds.prototype = 
{
	setClass : function(id, row, classCSS)
	{
		var divRow = $(id + "_row" + row);
		if (divRow)
		{
			 var className = $(id).className;
			 if (divRow.className == className+"_row_error") divRow.error = true;
			 if (divRow.error == true && classCSS == className) classCSS = className+"_row_error";
			 divRow.className = classCSS;
		}
	},

	moveRow : function(id)
	{
		var griddsObj = $(id);
		var dsObj = $(griddsObj.p.dsObj);
		var className = (dsObj.DSpre % 2 == 0) ? griddsObj.className + "_row" : griddsObj.className + "_row_event";
		if (dsObj.DSpos < 0) 
		{
			 this.newRow(griddsObj, dsObj.DSpos);
			 this.setClass(id, dsObj.DSpos, griddsObj.className+"_row_pos");
		}
		if (dsObj.DSpre > 0) this.setClass(id, dsObj.DSpre, className);
		if (dsObj.DSpos > 0) this.setClass(id, dsObj.DSpos, griddsObj.className+"_row_pos");
	},

	addRow : function(i, id, bodies)
	{
		if (bodies.divRow == undefined) bodies.divRow = $(id+'_row0');
		if (i==-1 && $(id+'_row1')) var row = bodies.insertBefore(bodies.divRow.cloneNode(true), $(id+'_row1'));
		else var row = bodies.appendChild(bodies.divRow.cloneNode(true));
		var col = 1;
		var refreshCode = "";
		row.id = id + "_row" + i;
		row.pos = i;
		row.style.display = "";
		row.className = (i % 2 == 0) ?  $(id).className + "_row" : $(id).className + "_row_event";
		if ($(id).p.cellheight) row.style.height = parseInt($(id).p.cellheight)+'px';

		var objects = row.getElementsByTagName('*');
		var length = objects.length;
		for (var ii = 0; ii < length; ii++)
		{
			if (objects[ii].id)
			{
				var obj = objects[ii];
				if (obj.id == id+'_'+col+'_0')
				{
					obj.id = id+'_'+col+'_'+i;
					var objRif = $(id+'_'+col+'_0');
					obj.p = SYSTEMBROWSER.clone(objRif.p);
					if (objRif.p.dsItem)
					{
						obj.row = i;
						refreshCode += obj.p.typeObj.toUpperCase() + ".refreshObj(\"" + obj.id + "\");\n";
					}
					col++;
				}
				else if (obj.id.indexOf(id+'_'+col+'_0')>-1) obj.id = obj.id.replace('_0','_'+i);
			}
 		}
 		eval(refreshCode);
	},

	delRow : function(i, bodies)
	{
	},

	newRow : function(obj, pos)
	{
		this.addRow(pos, obj.id, $(obj.id+'_body'));
	},

	clear : function(id, tot)
	{
		var i = 1;
		var row = $(id + "_row" + i);
		while (row != undefined)
		{
			row.parentNode.removeChild(row);
			row = $(id + "_row" + ++i);
		}
	},

	saveRow : function(id)
	{
		if ($(id+'_row-1')) this.refreshObj(id);
	},

	refreshObj : function(id)
	{
		var griddsObj = $(id);
		var dsObj = $(griddsObj.p.dsObj);
		var bodies = $(id+'_body');
		if ($(id+'_row-1')) bodies.removeChild($(id+'_row-1'));
		bodies.divRow = $(id+'_row0');
		bodies.divRow.style.display = "none";
		this.clear(id, dsObj.DSresult.length);
		bodies.dsObjId = dsObj.id;
		var length = dsObj.DSresult.length; 
		for (var i = 1; i < length; i++) this.addRow(i, id, bodies);
		this.moveRow(id, bodies);
	}
}

var XGRIDDS = new clsxGridds();