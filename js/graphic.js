/**
* Class Graphic
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsGraphic()
{
}

clsGraphic.prototype =
{
 	chkstate : function()
 	{
		AJAX.loader(true);
		if (this.loader) setTimeout("GRAPHIC.chkstate()",1000);		
 		else AJAX.loader(false);
	},

	getDsValue : function(id)
	{
		var img = $(id);
 		img.src = img.p.path + '&' + new Date().getTime();
		this.loader = true;
		this.chkstate();
	},

	refreshObj : function(id)
	{
		this.getDsValue(id);
	}
}

var GRAPHIC = new clsGraphic();