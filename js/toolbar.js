/**
* Class TOOLBAR
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsToolbar()
{
}

clsToolbar.prototype =
{
	getDsValue : function(id)
	{
  		var bar = $(id);
		var dsObj = $(bar.p.dsObj);
		var data = dsObj.DSresult;
		var length = data.length;
		var tBody = document.createElement('tbody');
		var tr = document.createElement('tr');
		var classtoolbar = bar.className;
		bar.appendChild(tBody);
		for(i=1; i<length; i++)
		{		
			var td = document.createElement('td');
			var img = '<img src="'+data[i]['img']+'"';
			if (data[i]['onclick']!=undefined && data[i]['onclick']!="") 
			{
				var onmouseout = (data[i]['onmouseout']!=undefined && data[i]['onmouseout']!="") ? data[i]['onmouseout'] : data[i]['img'];
				var onmouseover = (data[i]['onmouseover']!=undefined && data[i]['onmouseover']!="") ? data[i]['onmouseover'] : data[i]['img'];
				img += ' onmouseout="this.className=\''+classtoolbar+'_buttonEditor\'; this.src=\''+onmouseout+'\'"';
				img += ' onmouseover="this.className=\''+classtoolbar+'_buttonEditorOver\'; this.src=\''+onmouseover+'\'"';
				img += ' onclick="'+data[i]['onclick']+'"';
				img += ' class="'+classtoolbar+'_buttonEditor"';
				if (data[i]['title']!=undefined && data[i]['title']!="") img += ' title="'+data[i]['title']+'"';
			}
			img += '>';
			td.innerHTML = img;
			tr.appendChild(td);
		}
		var td = document.createElement('td');
		td.innerHTML = "&nbsp";
		tr.appendChild(td);
		tBody.appendChild(tr);
	},

	refreshObj : function(id)
	{
		this.getDsValue(id);
	}
}

var TOOLBAR = new clsToolbar();