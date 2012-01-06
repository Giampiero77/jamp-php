/**
* Class URL
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsUrl()
{
}

clsUrl.prototype = 
{
	getDsValue : function(id)
	{
		var ulrObj = $(id);
		ulrObj.innerHTML = "&nbsp;";
		var dsObj = $(ulrObj.p.dsObj);
		if (dsObj != undefined)
		{
		  if (dsObj.DSresult.length == 0) return;
		  var row = (ulrObj.row == undefined) ? dsObj.DSpos :  ulrObj.row;
		  var valueDs =  (dsObj.DSresult[row][ulrObj.p.dsItem] == undefined) ? "" : dsObj.DSresult[row][ulrObj.p.dsItem];
		}
		else var valueDs = ulrObj.p.value;
		if (valueDs == "" && ulrObj.p.defaultvalue != null) valueDs = ulrObj.p.defaultvalue;

		var strTarget = ulrObj.p.target;
		var strAction = ulrObj.p.action;
		var strParam = ulrObj.p.actionparam;
		if (valueDs != "")
		{
			 var valueDsArray = Array();
			 if (valueDs.indexOf(",")<0) valueDsArray[0] = valueDs;
			 else valueDsArray = valueDs.split(',');
			 valueDs = '';
			 var length = valueDsArray.length;
			 for(var i=0; i<length; i++)
			 {
				  if (i>0) valueDs += ", ";
				  if (ulrObj.p.format != null) valueDsArray[i] = FORMAT.Format(valueDsArray[i], ulrObj.p.format);
				  if (ulrObj.p.directory != null) 
				  {
				      if (strTarget != null) valueDs += "<a href=\"" + ulrObj.p.directory + valueDsArray[i] + "\" target=\"" + strTarget +"\">" + valueDsArray[i] + "</a>";
				      else valueDs += "<a href=\"" + ulrObj.p.directory + valueDsArray[i] + "\">" + valueDsArray[i] + "</a>";
				  }
				  else if (strParam =='') 
				  {
					 if (strTarget != null) valueDs += "<a href=\"" + strAction + "\" target=\"" + strTarget +"\">" + valueDsArray[i] + "</a>";
					 else valueDs += "<a href=\"" + strAction + "\">" + valueDsArray[i] + "</a>";
				  }
				  else 
				  {
					 if (strTarget != null) valueDs += "<a href=\"" + strAction + "?" + strParam + "=" + valueDsArray[i] + "\" target=\"" + strTarget +"\">" + valueDsArray[i] + "</a>";
					 else valueDs += "<a href=\"" + strAction + "?" + strParam + "=" + valueDsArray[i] + "\">" + valueDsArray[i] + "</a>";
				  }
			 }
		}
		ulrObj.innerHTML = valueDs;
	},

	refreshObj : function(id)
	{
	    this.getDsValue(id);
	}
}

var URL = new clsUrl();
