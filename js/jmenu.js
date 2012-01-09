/**
* Class JMENU
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and source to the official website of JAMP
* http://jamp.alyx.it/
*/

function clsMenu()
{
	if (SYSTEMBROWSER.isIE() && SYSTEMBROWSER.getVersion() <= 6)
  	{
		this.iframe = document.createElement('iframe');
		this.iframe.id = "frame-hidden";
  	}
}

clsMenu.prototype =
{
	nocache:function(obj)
	{
		if (obj.url == undefined) obj.url = obj.href;
		if (obj.url.match(/\?/) == null) obj.href = obj.url + "?" + Math.random();
		else obj.href = obj.url + "&" + Math.random();
	},

	addClass:function()
	{	
		var lu = this.childNodes[1];
		if (JMENU.iframe != undefined && lu != undefined)
		{	
			JMENU.iframe.style.width = parseInt(lu.style.width)+2;
			JMENU.iframe.style.height = lu.childNodes.length*25;
			this.appendChild(JMENU.iframe); 
		}
		this.className = "hover";
		return this;
	},
	
	removeClass:function(className)
	{
		this.className = (this.lastChild.tagName == "UL") ? "menu" : "";
		var iframe = $('frame-hidden');
		if (iframe!=null) iframe.parentNode.removeChild(iframe);
		return this;
	},

	getDsValue : function(id)
	{
  		var menu = $(id);
		var dsObj = $(menu.p.dsObj);
		var data = dsObj.DSresult;
		var value = new Array();
		var parent = new Array();
		menu.innerHTML = "";
		var length = data.length;

		for(var i=1; i<length; i++)
		{		
			var li = document.createElement('li');
			li.className = "menu";
			var link = "<a";
			if (data[i]['url']!=undefined && data[i]['url']!="")
			{
				link += " href=\""+data[i]['url']+"\"";
				if (data[i]['nocache']== "1") link += "onmouseover=\"JMENU.nocache(this);\"";
			}
		  if (data[i]['image'])
		  {
				link += " style=\"";
				if (data[i]['image']!="" && data[i]['level']==0) link += "padding-left:22px;";
				if (data[i]['image']!="") link += "background-image:url('"+data[i]['image'].replace(/ /g,'%20')+"')";	
				link += "\"";
		  }
		  if (data[i]['title']) link += " title=\""+data[i]['title']+"\"";
			link += ">"+data[i]['text']+"</a>";
			li.id = id + '_' + data[i]['id'];
			li.innerHTML = link;
			li.container = document.createElement('ul');
			li.appendChild(li.container);
			if (dsObj.p.DSparentkey == null)
			{
			  parent[data[i]['level']] = li.container;
			  var par = (parent[data[i]['level']-1]!=null) ? parent[data[i]['level']-1] : menu;
			}
			else
			{
			  var par = ($(id+'_'+data[i][dsObj.p.DSparentkey]) != null) ? $(id+'_'+data[i][dsObj.p.DSparentkey]).container : menu;
			}
			par.appendChild(li);	
		}	
		var nested = null
		var elements = menu.getElementsByTagName('li');
		var length = elements.length;
		for (var i=0; i<length; i++)
		{
			var element = elements[i];
			if (element.className=="menu") 
			{
				element.onmouseover = this.addClass;
				element.onmouseout = this.removeClass;
				var uls = element.getElementsByTagName('ul');
				if(uls==null) continue;
				nested = uls[0];
				if (!nested.hasChildNodes()) 
				{
					nested.parentNode.className = "";
					nested.parentNode.removeChild(nested);
				}
				var offsetWidth  = 0;
				var nlength = nested.childNodes.length;
				for (k=0; k < nlength; k++) 
				{
					var node  = nested.childNodes[k];
					if (node.nodeName == "LI")
						offsetWidth = (offsetWidth >= node.offsetWidth) ? offsetWidth : node.offsetWidth;
				}
				for (l=0; l < nlength; l++) 
				{
					var node = nested.childNodes[l];
					if (node.nodeName == "LI" && offsetWidth>0) node.style.width = offsetWidth+'px';
				}
				if (offsetWidth>0) nested.style.width = offsetWidth+'px';
			}
	 	}
	},

	refreshObj : function(id)
	{
		this.getDsValue(id);
	}
}

var JMENU = new clsMenu();

try {
  document.execCommand('BackgroundImageCache', false, true);
} catch(e) {}
