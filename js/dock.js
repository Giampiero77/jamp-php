/**
* Class DOCK
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsDock()
{
	this.ie6 = (SYSTEMBROWSER.isIE() && SYSTEMBROWSER.getVersion()<6);
}

clsDock.prototype =
{
	getDsValue : function(id)
	{
		var dockDiv = $(id);	
		if (dockDiv.p.dsObj != undefined)
		{
			dockDiv.innerHTML = "";
			var dsObj = $(dockDiv.p.dsObj);
	  		dockDiv.style.width = parseInt(dsObj.DSrow*50)+140+'px';
			for (var i = 1; i <= dsObj.DSrow; i++)
			{
				var dsProperty = dsObj.DSresult[i];
				var span = document.createElement('span');
				var link = document.createElement('a');
 				link.className = dockDiv.className+"-item";
 				link.href = (dsProperty['link']!=undefined) ? dsProperty['link'] : "#";
				link.rel = (dsProperty[dockDiv.p.dsrel]!=undefined) ? dsProperty[dockDiv.p.dsrel] : "";
				link.rev = (dsProperty[dockDiv.p.dsrev]!=undefined) ? dsProperty[dockDiv.p.dsrev] : "";
				var	src = (dsProperty['img']!=undefined) ? dsProperty['img'] : "";
				if (this.ie6)
				{
					var img = document.createElement('div');
					img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src="+src+" ,sizingMethod=scale')";
					span.appendChild(document.createTextNode(dsProperty['name']));
				}
				else 
				{
					var img = document.createElement('img');
					img.src = src;
					img.alt = (dsProperty['alt']!=undefined) ? dsProperty['alt'] : "";
					span.textContent = dsProperty['name'];
				}
 				img.pos = i-1;
 				img.parentdiv = id;
 				img.type = 'small';
 				img.width = img.height = 50;
 				img.span = span;
//				img.id = id + "_" +i;
 				img.style.left = 70+(50*(i-1))+'px';
  				link.appendChild(img);
				link.appendChild(span);
				span.style.left = 45+(50*(i-1))+'px';
				span.style.top = (dockDiv.p.valign == "bottom") ? '-70px' : '100px'; 
				dockDiv.appendChild(link);
				SYSTEMEVENT.addEventListener(document, 'mousemove', function(event) {ANIMATE.collision(img, 'opacity:1;height:100;top:55px', '80', event);});
			}
		}
		if (dockDiv.p.align == "left")
		{
			dockDiv.style.marginLeft = "0px";
			dockDiv.style.left = "0px";
		}
		else if (dockDiv.p.align == "center") 
		{
			dockDiv.style.marginLeft = -(dockDiv.clientWidth / 2) + "px";
			dockDiv.style.left = "50%";
		}
		else if (dockDiv.p.align == "right")
		{
			dockDiv.style.marginLeft = -dockDiv.clientWidth + "px";
			dockDiv.style.left = "100%";
		}
		if (dockDiv.p.valign == "top") dockDiv.style.top = "0px";
		if (dockDiv.p.valign == "bottom") dockDiv.style.top = document.documentElement.clientHeight - dockDiv.clientHeight - 1 + "px";
	},

	refreshObj : function(id)
	{
		this.getDsValue(id);
	}
};

var DOCK = new clsDock();
