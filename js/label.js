/**
* Class LABEL
* @author	Alyx Association <info@alyx.it>
* @version	2.0.1
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsLabel()
{
}

clsLabel.prototype = 
{
	setDsValue : function(obj, value)
	{
		var dsObj = $(obj.p.dsObj);
		if (value != undefined) obj.innerHTML = value;
		var pos = (obj.row == undefined) ? dsObj.DSpos : obj.row;
		if (pos == -1) dsObj.DSresult[pos][obj.p.dsItem] = Array();
		if (obj.p.format == undefined) dsObj.DSresult[pos][obj.p.dsItem] = obj.innerHTML;
		else
		{
			 dsObj.DSresult[pos][obj.p.dsItem] = obj.innerHTML;
			 FORMAT.unformat(obj, dsObj, pos);
			 FORMAT.format(obj, dsObj.DSresult[pos][obj.p.dsItem]);
		}
		dsObj.DSposalter = pos;
		DS.dschange(dsObj);
	},

	 limitAlt : function(labelObj)
	 {
		  var limit = parseInt(labelObj.p.limit);
		  if (isNaN(limit) == false)
		  {
				labelObj.title = labelObj.innerHTML;
				if (labelObj.innerHTML.length > limit) labelObj.innerHTML = labelObj.innerHTML.substr(0, parseInt(limit)) + "...";
		  }
	 },

   getVal : function(labelObj, valueDs)
   {
		  if (labelObj.p.dsObjList && valueDs)
		  {
				var dsObjList = $(labelObj.p.dsObjList);
				var dsObj = $(labelObj.p.dsObj);
				var post = "data=load&dsobjname=" + dsObjList.id + "&dsforeignkey=" + encodeURIComponent(labelObj.p.dsItemKeyList);
				post += "&dsforeignkeyvalue=" + encodeURIComponent(valueDs);
				AJAX.request("POST", dsObj.p.DSaction, post, true, true);
				if (dsObjList.DSresult.length == 0) var valueDs = "";
				else 
				{
					 var valueDs = new Array();
					 var item = labelObj.p.dsItemList.split(",");
					 var itemlength = item.length;
					 for (var i = 0; i < itemlength; i++) valueDs[i] = dsObjList.DSresult[1][item[i]];
					 valueDs = valueDs.join(" - ");
				}
		  }
		  return valueDs;   
   },
    
	 getDsValue : function(id)
	 {
		  var labelObj = $(id);
		  var dsObj = $(labelObj.p.dsObj);
		  labelObj.innerHTML = "&nbsp;";
		  var valueDs = labelObj.p.defaultvalue;
		  if (dsObj.DSresult.length > 0 && dsObj.DSpos > -1)
		  {
				var row = (labelObj.row == undefined) ? dsObj.DSpos : labelObj.row;
				if (dsObj.DSresult[row] == undefined) return;
				valueDs = (dsObj.DSresult[row][labelObj.p.dsItem] == undefined) ? "" : LABEL.getVal(labelObj, dsObj.DSresult[row][labelObj.p.dsItem]);
				if (labelObj.p.format == null) labelObj.innerHTML = valueDs;
				else FORMAT.format(labelObj, valueDs);
				LABEL.limitAlt(labelObj);
		  }
		  else if (valueDs != null)
		  {
				valueDs = LABEL.getVal(labelObj, valueDs);
				LABEL.setDsValue(labelObj, valueDs);
				if (labelObj.p.format != null) labelObj.innerHTML = FORMAT.Format(valueDs, labelObj.p.format);
				LABEL.limitAlt(labelObj);
		  }
	 },

	 refreshObj : function(id)
	 {
		this.getDsValue(id);
	 }
}

var LABEL = new clsLabel();