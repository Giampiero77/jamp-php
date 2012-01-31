/**
* Class HIDDEN
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsHidden()
{
}

clsHidden.prototype =
{
	getDsValue : function(id)
	{
		var hiddenObj = $(id);
		var dsObj = $(hiddenObj.p.dsObj);
		if (dsObj != null)
		{
			hiddenObj.value = "";
			if (dsObj.DSresult.length == 0) return
			var row = (hiddenObj.row == undefined) ? dsObj.DSpos :  hiddenObj.row;
			var valueDs = (dsObj.DSresult[row][hiddenObj.p.dsItem] == undefined) ? "" : dsObj.DSresult[row][hiddenObj.p.dsItem];
			if (hiddenObj.p.format == null) hiddenObj.value = valueDs;
			else FORMAT.format(hiddenObj, valueDs);
		}
	},

	refreshObj : function(id)
	{
		this.getDsValue(id);
	}
}

var HIDDEN = new clsHidden();