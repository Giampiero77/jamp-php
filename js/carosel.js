/**
* Class CAROSEL
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and source to the official website of JAMP
* http://jamp.alyx.it/
*/

function clsCarosel()
{
	this.timeout = null;
}

clsCarosel.prototype =
{
	move:function(id, button)
	{	
		var carosel = $(id);
		var left = parseInt(carosel.container.style.left);
		if (button=="next") 
		{
			if (left == 0) {CAROSEL.stop(); return;}
			carosel.container.style.left = left + 5 + "px";
		}
		else
		{
			var size = Math.abs(left) + carosel.offsetWidth -3;
			if (carosel.p.width < size) {CAROSEL.stop(); return;}
			carosel.container.style.left = left - 5 + "px";
		}
	},

	start:function(id, button)
	{	
		var carosel = $(id);
		if (carosel.init == undefined) this.init(carosel);
		CAROSEL.interval = setInterval(function(){CAROSEL.move(id, button);},carosel.p.speed);
	},

	stop:function(id)
	{
		clearInterval(CAROSEL.interval);
	},

	init:function(carosel)
	{
		var length = carosel.p.elm.length;
		var size = 0;
		for (i = 1; i < length; i++) size += carosel.p.elm[i].offsetWidth;
		carosel.container.style.width = size + 3 + "px";
		carosel.p.width = size - (length * 2);
		carosel.init = true;
	},

	getDsValue : function(id)
	{
  		var carosel = $(id);
		var dsObj = $(carosel.p.dsObj);
		var data = dsObj.DSresult;
		var value = new Array();
		var parent = new Array();
		var length = data.length;
		carosel.container = document.createElement('ul');
		carosel.container.style.left = "0px";
		carosel.innerHTML = '<input type="button" class="next" onmouseover="javascript:CAROSEL.start(\''+id+'\', \'next\');" onmouseout="javascript:CAROSEL.stop();" />';
		carosel.innerHTML += '<input type="button" class="prev" onmouseover="javascript:CAROSEL.start(\''+id+'\', \'prev\');" onmouseout="javascript:CAROSEL.stop();" />';
		carosel.appendChild(carosel.container);
		var parent = carosel.parentNode;
		carosel.p.elm = Array();
		lightbox = (parent.p != undefined && parent.p.typeObj == 'lightbox') ? true : false;
		for(var i=1; i<length; i++)
		{		
			var li = document.createElement('li');
			var img = "<img src=\""+data[i]['src']+"\" style=\"height:"+carosel.offsetHeight+"px\">";
			if (lightbox) 
			{
				if (data[i]['href']!=undefined) img = "<a href=\""+data[i]['href']+"\" rel=\"lightshow[picture]\">"+img+"</a>";
				else img = "<a href=\""+data[i]['src']+"\" rel=\"lightshow[picture]\">"+img+"</a>";
			}
			li.innerHTML = img;
			carosel.container.appendChild(li);
			carosel.p.elm[i] = li;
		}
		if (lightbox) LIGHTBOX.updateLightboxItems(parent.id);
	},

	refreshObj : function(id)
	{
		this.getDsValue(id);
	}
}

var CAROSEL = new clsCarosel();
