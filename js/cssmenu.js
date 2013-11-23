/**
* Class CSSMENU
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and source to the official website of JAMP
* http://jamp.alyx.it/
*/

function clsCSSMenu() {}

clsCSSMenu.prototype =
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
		if (CSSMENU.iframe != undefined && lu != undefined)
		{	
			CSSMENU.iframe.style.width = parseInt(lu.style.width)+2;
			CSSMENU.iframe.style.height = lu.childNodes.length*25;
			this.appendChild(CSSMENU.iframe); 
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

	getDsValue : function(id) {
		var menu = $(id);
		var dsObj = $(menu.p.dsObj);
		var data = dsObj.DSresult;
		var parent = new Array();
		var par;
		menu.innerHTML = "";
		var length = data.length;
		var ul = document.createElement('ul');

		for(var i=1; i<length; i++) {
			var li = document.createElement('li');

			var url=data[i]['url'];
			var img=data[i]['image'];
			var text=data[i]['text'];

			if (url!=undefined && url!="") {
				var link = "<a href=\""+url+"\">";
			} else {
				var link = "";
			}

			if (img!="") {
				link += "<img src=\""+img+"\" />";
			}
			link += text;
			if (url!=undefined && url!="") link += "</a>";

			li.id = id + '_' + data[i]['id'];
			if (data[i]['level'] == 0) {
				li.innerHTML = '<span class="'+id+'_top">'+link+'</span>';
			} else {
				li.innerHTML = '<span class="'+id+'_sub">'+link+'</span>';
			}
			li.container = document.createElement('ul');
			li.appendChild(li.container);
			if (dsObj.p.DSparentkey == null) {
				parent[data[i]['level']] = li.container;
				par = (parent[data[i]['level']-1]!=null) ? parent[data[i]['level']-1] : menu;
			} else {
				par = ($(id+'_'+data[i][dsObj.p.DSparentkey]) != null) ? $(id+'_'+data[i][dsObj.p.DSparentkey]).container : menu;
			}
			par.appendChild(li);
		}
		menu.innerHTML = "<ul>"+menu.innerHTML+"</ul>";
	},

	refreshObj : function(id)
	{
		this.getDsValue(id);
	}
};

var CSSMENU = new clsCSSMenu();
