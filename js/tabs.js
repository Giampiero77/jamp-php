/**
* Class TABS
* @author	Alyx Association <info@alyx.it>
* @version	stable 1.0.3
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsTabs()
{
}

clsTabs.prototype =
{
	setFocus : function(id, box, box2)
	{
		var tabsObj = $(id);
		var tabObj = $(box + "_tab");
		if (box2 == undefined) var box2 = box;
		var boxObj = $(box2);
		if(typeof tabsObj.SelectedTab == "undefined")
		{
			tabsObj.SelectedTab = tabObj;
			tabsObj.SelectedBox = boxObj;
			tabObj.className = "selected";
 			boxObj.style.display = "block";
		} 
		else
		{
			tabsObj.SelectedTab.className = "unselected";
 			tabsObj.SelectedBox.style.display = "none";
			tabsObj.SelectedTab = tabObj;
			tabsObj.SelectedBox = boxObj;
 			tabObj.className = "selected";
 			boxObj.style.display = "block";
		}
		Resize();
		if (AJAX.function_exists(box2+"Display"))
		{
			try { eval(box2+"Display();") }
			catch (e) {}
		}
	},

	sizeTabs : function(id)
	{
		var tabsObj = $(id);
		var tabsObjTitle = $(id + "_title");
		var tabsObjTABS = $(id + "_tabs");
 		var Height = tabsObj.clientHeight - tabsObjTitle.clientHeight;
		var Width = tabsObj.clientWidth;
		var Top = parseInt(SYSTEMBROWSER.getCSSProp(tabsObjTABS,"border-top-width"));
		var bottom = parseInt(SYSTEMBROWSER.getCSSProp(tabsObjTABS,"border-bottom-width"));
		Height = (isNaN(top)) ? Height : Height - Top;
		Height = (isNaN(bottom)) ? Height : Height - bottom;
		if (Height <= 0) Height = 1;
		if (Width <= 0) Width = 2;
		tabsObjTABS.style.height = (Height - 1)  + "px";
		tabsObjTABS.style.width = (Width - 2) + "px";
		var length = tabsObjTABS.childNodes.length;
		for (var i=0; i < length; i++)
		{
                if (tabsObjTABS.childNodes[i].nodeType == 1)                               
                {                                                                          
                    Hg = Height - SYSTEMBROWSER.borderHeight(tabsObjTABS.childNodes[i], false);
                    tabsObjTABS.childNodes[i].style.height = ((Hg - 5) < 0) ? "0px" : (Hg - 5) + "px";
                }
		}
	},

	addTab : function(id, box, title)
	{
		//Add TAB
        var tab_title = $(id+'_title');
		var newTab = tab_title.childNodes[1].cloneNode(true);
		tab_title.appendChild(newTab);
		newTab.setAttribute("onclick", "javascript:TABS.setFocus('" + id + "', '" + box + "');");
		newTab.childNodes[1].id = box + "_tab";
		newTab.childNodes[1].rows[0].cells[1].firstChild.nodeValue = title;

		//Add Box
		var div = $(id+'_tabs').childNodes[1];
		var newBox = document.createElement('div');
		div.appendChild(newBox);
		newBox.id = box;
		newBox.p = {typeObj:"tab"}; 
        newBox.className = div.className;
		newBox.height = div.height;
		newBox.style.height = div.style.height;
		newBox.style.display = div.style.display;
	},

	refreshObj : function(id)
	{
	}
}

var TABS = new clsTabs();