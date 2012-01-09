/**
* Class TEXTRESIZE
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsTxtResize()
{
	this.tags = new Array('input', 'select', 'textarea', 'label', 'div', 'li', 'a');
	this.size = 10;
}

clsTxtResize.prototype =
{
    resize : function(inc) 
    {
    	this.size += inc;
		var tagslength = this.tags.length;
    	for (var i=0; i<tagslength; i++) 
		{
    		var tg = document.getElementsByTagName(this.tags[i]);
			var length = tg.length;
    		for (var j=0; j<length; j++) tg[j].style.fontSize = (this.size+1)+'px';
    	}
    }
}
var TXTRESIZE = new clsTxtResize();
