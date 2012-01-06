/**
* Class PROPERTY
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/


function clsProperty()
{
}

clsProperty.prototype =
{
	setDsValue : function(id, property)
	{ 
		var propertyObj = $(id);
		var dsObj = $(propertyObj.p.dsObj);
		var item = propertyObj.p.dsItem.split(","); //0=property, 1=value, 2=maskvalue, 3=html
		if (dsObj.DSresult[property.DSrow][item[1]] == property.value) return;
		dsObj.DSresult[property.DSrow][item[1]] = property.value;	
		if (dsObj.DSresult[property.DSrow][item[3]] == "true" && propertyObj.objSelected != undefined)
		{
			propertyObj.objSelected.setAttribute(dsObj.DSresult[property.DSrow][item[0]], dsObj.DSresult[property.DSrow][item[1]]);
		}
		DS.dschange(dsObj);
	},

	click : function(property, name)
	{
	},

	getDsValue : function(id)
	{
	},

	refreshObj : function(id)
	{
		var propertyObj = $(id);
		var dsObj = $(propertyObj.p.dsObj);
		var item = propertyObj.p.dsItem.split(","); //0=property, 1=value, 2=maskvalue, 3=html
		var tableObj = document.createElement("table");

		propertyObj.innerHTML = "";
		propertyObj.appendChild(tableObj);

		tableObj.setAttribute("bgcolor", "white");
		tableObj.setAttribute("width", "100%");
// 		tableObj.style.height = propertyObj.clientHeight + "px";
		tableObj.appendChild(document.createElement("tbody"));
		var length = dsObj.DSrow;
		for (var i = 1; i < length+1; i++)
		{
			var textProperty = document.createElement("SPAN");
			textProperty.innerHTML = dsObj.DSresult[i][item[0]];
			if (dsObj.DSresult[i][item[2]] != "")
			{
				var valueProperty = document.createElement("SELECT");
				var valueMask = dsObj.DSresult[i][item[2]].split(",");
				var ValueSel = dsObj.DSresult[i][item[1]];
				var length1 = valueMask.length;
				for (var ii = 0; ii < length1; ii++) 
				{
					valueProperty.options[ii] = new Option(valueMask[ii], valueMask[ii]);
					if (valueMask[ii]==ValueSel) valueProperty.options[ii].selected = true;
				}
			}
			else
			{
				var valueProperty = document.createElement("INPUT");
				valueProperty.setAttribute("type", "text");
				valueProperty.setAttribute("value", dsObj.DSresult[i][item[1]]);
			}
			valueProperty.DSrow = i;
			valueProperty.setAttribute("id", propertyObj.id + "_" + dsObj.DSresult[i][item[0]]);
			valueProperty.setAttribute("onchange","PROPERTY.setDsValue(\""+propertyObj.id + "\", this);");

			valueProperty.setAttribute("onclick","DS.moveRow(\""+ propertyObj.p.dsObj + "\", " + i + ");PROPERTY.click(this, '" + dsObj.DSresult[i][item[0]] + "');");
	
			valueProperty.setAttribute("title", dsObj.DSresult[i][item[3]]);
			tableObj.tBodies[0].insertRow(i-1);
			tableObj.tBodies[0].rows[i-1].appendChild(document.createElement("td"));
			tableObj.tBodies[0].rows[i-1].appendChild(document.createElement("td"));
			tableObj.tBodies[0].rows[i-1].cells[0].appendChild(textProperty);
			tableObj.tBodies[0].rows[i-1].cells[1].appendChild(valueProperty);
			tableObj.tBodies[0].rows[i-1].cells[0].setAttribute("width","80px");
		}
		PROPERTY.resize(id);
	},

	resize : function (id)
	{
		var obj = $(id);
		if (obj.childNodes.length == 0) return; //IE
		if (obj.firstChild.tagName == "TABLE")
		{
			var tableObj = obj.firstChild.tBodies[0];
			var length = tableObj.rows.length;
			for (var i = 0; i < length; i++)
			{
				tableObj.rows[i].cells[1].firstChild.style.width = (obj.clientWidth - 10 - tableObj.rows[i].cells[1].offsetLeft ) + "px";
			}
		}
	}
	
}

var PROPERTY = new clsProperty();