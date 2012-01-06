/**
* Class WINDOWS
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsWindow()
{
}

clsWindow.prototype =
{
	slide : function(id, duration, alg)
	{
		var objWindow = $(id);
		if (objWindow.lock == true) return;
		objWindow.lock = true;
		objWindowIcon = $(id + "_title_icon");
		objWindow.style.overflow = "hidden";
		if (objWindow.type == 'vertical')
		{     
    		if (objWindow.isExpanded == true)
    		{
				objWindow.contentheight = parseInt(objWindow.style.height);
    		}
    		if (parseInt(objWindow.style.height)==0)
    		{
				objWindow.style.display = "";
    			objWindowIcon.className = objWindowIcon.className.replace("_close", "_open");
				ANIMATE.animate(id, "height:"+objWindow.contentheight+"px", duration, function(){objWindow.isExpanded = true;objWindow.lock=false;WIN.display(objWindow);}, alg);
    		} 
    		else if (parseInt(objWindow.style.height)==objWindow.contentheight) 
    		{
    			objWindowIcon.className = objWindowIcon.className.replace("_open", "_close");
				ANIMATE.animate(id, "height:0px;display:none", duration, function(){objWindow.isExpanded = false;objWindow.lock=false;}, alg);
    		}
		}
		else
		{
    		if (objWindow.isExpanded == true)
    		{
    			objWindow.contentwidth = parseInt(objWindow.style.width);
    		}
    		if (parseInt(objWindow.style.width)==0)
    		{
				objWindow.style.display = "";
    			objWindowIcon.className = objWindowIcon.className.replace("_close", "_open");
				ANIMATE.animate(id, "width:"+objWindow.contentwidth+"px", duration, function(){objWindow.isExpanded = true;objWindow.lock=false;WIN.display(objWindow);}, alg);
    		} 
    		else if (parseInt(objWindow.style.width)==objWindow.contentwidth) 
    		{
    			objWindowIcon.className = objWindowIcon.className.replace("_open", "_close");
				ANIMATE.animate(id, "width:0px;display:none", duration, function(){objWindow.isExpanded = false;objWindow.lock=false;}, alg);
    		}
		}
	},
	
	display : function(objWindow)
	{
		if (AJAX.function_exists(objWindow.id+"Display"))
		{
			Resize();
			try { eval(objWindow.id+"Display();") }
			catch (e) {}
		}
	}
}

var WIN = new clsWindow();