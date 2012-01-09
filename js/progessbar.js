/**
* Class PROGESSBAR
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsProgessBar()
{
}

clsProgessBar.prototype = 
{
    getDsValue : function(id)
    {
	var pbarObj = $(id);
	var dsObj = $(pbarObj.p.dsObj);
	var percent = pbarObj.p.percent;
	if (dsObj.DSresult.length > 0 && dsObj.DSpos > -1)
	{
	      var row = (pbarObj.row == undefined) ? dsObj.DSpos : pbarObj.row;
	      if (dsObj.DSresult[row] == undefined) return;	    
	      percent = (dsObj.DSresult[row][pbarObj.p.dsItem] == undefined) ? "" : dsObj.DSresult[row][pbarObj.p.dsItem];
	}
	var width = pbarObj.parentNode.style.width;
	pbarObj.style.width =  ((parseInt(percent) * parseInt(width)) / 100) + 'px'; 
	$(id+'_percent').innetHTML = percent + '%';
    },

    refreshObj : function(id)
    {
	this.getDsValue(id);
    }
}

var PROGESSBAR = new clsProgessBar();