/**
* Class DIVLABEL
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsDivLabel()
{
}

clsDivLabel.prototype = 
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

	 limitAlt : function(divlabelObj)
	 {
		  var limit = parseInt(divlabelObj.p.limit);
		  if (isNaN(limit) == false)
		  {
				divlabelObj.title = divlabelObj.innerHTML;
				if (divlabelObj.innerHTML.length > limit) divlabelObj.innerHTML = divlabelObj.innerHTML.substr(0, parseInt(limit)) + "...";
		  }
	 },

   getVal : function(divlabelObj, valueDs)
   {
		  if (divlabelObj.p.dsObjList && valueDs)
		  {
				var dsObjList = $(divlabelObj.p.dsObjList);
				var dsObj = $(divlabelObj.p.dsObj);
				var post = "data=load&dsobjname=" + dsObjList.id + "&dsforeignkey=" + encodeURIComponent(divlabelObj.p.dsItemKeyList);
				post += "&dsforeignkeyvalue=" + encodeURIComponent(valueDs);
				AJAX.request("POST", dsObj.p.DSaction, post, true, true);
				if (dsObjList.DSresult.length == 0) valueDs = "";
				else 
				{
					 valueDs = new Array();
					 var item = divlabelObj.p.dsItemList.split(",");
					 var itemlength = item.length;
					 for (var i = 0; i < itemlength; i++) valueDs[i] = dsObjList.DSresult[1][item[i]];
					 valueDs = valueDs.join(" - ");
					 return valueDs;
				}
		  }
		  return valueDs;
	 },
    
	 getDsValue : function(id)
	 {
		  var divlabelObj = $(id);
		  var dsObj = $(divlabelObj.p.dsObj);
		  divlabelObj.innerHTML = "&nbsp;";
		  var valueDs = divlabelObj.p.defaultvalue;
		  if (dsObj.DSresult.length > 0 && dsObj.DSpos > -1)
		  {
				var row = (divlabelObj.row == undefined) ? dsObj.DSpos : divlabelObj.row;
				if (dsObj.DSresult[row] == undefined) return;
				valueDs = (dsObj.DSresult[row][divlabelObj.p.dsItem] == undefined) ? "" : DIVLABEL.getVal(divlabelObj, dsObj.DSresult[row][divlabelObj.p.dsItem]);
				if (divlabelObj.p.format == null) divlabelObj.innerHTML = valueDs;
				else FORMAT.format(divlabelObj, valueDs);
				DIVLABEL.limitAlt(divlabelObj);
		  }
		  else if (valueDs != null)
		  {
				valueDs = DIVLABEL.getVal(divlabelObj, valueDs);
				DIVLABEL.setDsValue(divlabelObj, valueDs);
				if (divlabelObj.p.format != null) divlabelObj.innerHTML = FORMAT.Format(valueDs, divlabelObj.p.format);
				DIVLABEL.limitAlt(divlabelObj);
		  }
	 },

	 refreshObj : function(id)
	 {
		this.getDsValue(id);
	 }
};

var DIVLABEL = new clsDivLabel();