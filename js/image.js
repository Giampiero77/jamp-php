/**
* Class IMAGE WITH UPLOAD
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsImage()
{
	try 
	{
		this.iframe = document.createElement("<iframe name='iframe_image'>");
	}
	catch(e)
	{
		this.iframe = document.createElement('iframe');
		this.iframe.id = "iframe_image";
		this.iframe.name = "iframe_image";	
	}
	this.iframe.style.display = "none";

	this.directory = document.createElement('input');
	this.directory.name = "directory";
	this.directory.type = "hidden";

	this.extension = document.createElement('input');
	this.extension.name = "extension";
	this.extension.type = "hidden";

	this.classname = document.createElement('input');
	this.classname.name = "classname";
	this.classname.type = "hidden";
	this.classname.value = "IMAGE";

	this.form = document.createElement('form');
 	this.form.encoding = "multipart/form-data";
 	this.form.method = "post";
	this.form.target = "iframe_image";

	this.maxsize 	  = document.createElement('input');
	this.maxsize.type = 'hidden';
	this.maxsize.name = "MAX_FILE_SIZE";

	this.dimension = document.createElement('select');
	this.dimension.name = "dimension[]";
	this.dimension.multiple = true;
	this.dimension.style.display = "none";

	this.forcename = document.createElement('input');
	this.forcename.name = "forcename";
	this.forcename.type = "hidden";

	this.createdir = document.createElement('input');
	this.createdir.name = "createdir";
	this.createdir.type = "hidden";

	this.backgroundcolor = document.createElement('input');
	this.backgroundcolor.name = "backgroundcolor";
	this.backgroundcolor.type = "hidden";
	
	this.form.appendChild(this.directory);
	this.form.appendChild(this.extension);
	this.form.appendChild(this.classname);
	this.form.appendChild(this.maxsize);
	this.form.appendChild(this.dimension);
	this.form.appendChild(this.forcename);
	this.form.appendChild(this.createdir);
	this.form.appendChild(this.backgroundcolor);	
	document.body.appendChild(this.form);		
	document.body.appendChild(this.iframe);
}

clsImage.prototype =
{
	moveRow : function(obj)
	{
 		DS.moveRow(obj.p.dsObj, obj.row);
	},

	AfterPost : function(ris, mes, newfilename)
	{
		if (ris)
		{
			var posSlash = IMAGE.file.value.lastIndexOf('/')+1; // client Windows  
			if (posSlash==0) posSlash = IMAGE.file.value.lastIndexOf('\\')+1; // client Unix/Linux  
			var filename = (newfilename != undefined && newfilename!='') ? newfilename.substr(posSlash) : IMAGE.file.value.substr(posSlash);
			IMAGE.objImage.src = IMAGE.directory.value+"/"+filename;	
			var dsObj = $(IMAGE.objImage.p.dsObj);
			if (dsObj.DSpos!=0) 
			{ 	
				dsObj.DSresult[dsObj.DSpos][IMAGE.objImage.p.dsItem];
				var pos = (IMAGE.objImage.row == undefined) ? dsObj.DSpos : IMAGE.objImage.row;
				if (pos == -1) dsObj.DSresult[pos][IMAGE.objImage.p.dsItem] = Array();
				dsObj.DSresult[pos][IMAGE.objImage.p.dsItem] = filename;
				DS.dschange(dsObj);
			}
		}
		else SYSTEMEVENT.showMessage(mes, LANG.translate("IMAGE001"), self.location.href, 0, 80);
		IMAGE.hideAdd(IMAGE.objImage.id);
		AJAX.loader(false);
	},

 	setSelect : function(id, selectObj, valueStr)
	{
		selectObj.options.lenght = 0;
		if (valueStr == undefined) return;
		var arrayStr = valueStr.split(',');
		for (var i = 0; i < arrayStr.length; i++)
		{
		      selectObj.options[i] = new Option(arrayStr[i], arrayStr[i]);
		      selectObj.options[i].selected = true;
		}
	},

	setDsValue : function(id, inputFile)
	{
		if (inputFile.value == '') return;
		AJAX.loader(true);
		IMAGE.objImage = $(id);
		IMAGE.file = inputFile;
		IMAGE.directory.value 	= IMAGE.objImage.p.directory;
		IMAGE.extension.value 	= IMAGE.objImage.p.extension;	
		IMAGE.maxsize.value   	= IMAGE.objImage.p.maxsize;
		IMAGE.setSelect(id, IMAGE.dimension, $(id).p.dimension);
		IMAGE.setSelect(id, IMAGE.dimension, $(id).p.dimension);
		if (IMAGE.objImage.p.forcename) IMAGE.forcename.value 	= (IMAGE.objImage.p.forcename == '$$TIMESTAMP$$') ? new Date().getTime() : IMAGE.objImage.p.forcename;
		IMAGE.createdir.value  	= (IMAGE.objImage.p.createdir == 'true') ? 'true' : 'false';
		IMAGE.backgroundcolor.value   	= IMAGE.objImage.p.backgroundcolor;		
		IMAGE.form.action 	= IMAGE.objImage.p.action;	
		IMAGE.form.appendChild(IMAGE.file);
		IMAGE.form.submit();
		IMAGE.objImage.parentNode.insertBefore(IMAGE.file, IMAGE.objImage.nextSibling);
	},

	displayObj : function(id)
	{
 		var objImage = $(id);
		objImage.parentNode.style.width = (objImage.clientWidth + parseInt(objImage.border) * 2) + "px";
		objImage.parentNode.style.height = (objImage.clientHeight + parseInt(objImage.border) * 2) + "px";
	},

	getDsValue : function(id)
	{
 		var objImage = $(id);
		var dsObj = $(objImage.p.dsObj);
		objImage.readOnly = (dsObj.DSpos != 0) ? false : true;
		if (dsObj.DSresult.length == 0) 
		{
		    objImage.src = objImage.p.empty; 
			 setTimeout("IMAGE.displayObj('"+id+"');", 100);
		    return;
		}
		var row = (objImage.row == undefined) ? dsObj.DSpos : objImage.row;
		objImage.filename = (dsObj.DSresult[row][objImage.p.dsItem] == undefined) ? "" : dsObj.DSresult[row][objImage.p.dsItem];
		var img = dsObj.DSresult[row][objImage.p.dsItem]; 
		if (img != undefined && img!="") objImage.src = (objImage.p.directory == undefined) ? img : objImage.p.directory+img;
		else objImage.src = objImage.p.empty;
		setTimeout("IMAGE.displayObj('"+id+"');", 100);
	},

	refreshObj : function(id)
	{
		this.getDsValue(id);
		var obj = $(id+"_file");
		if (obj!=undefined)
		{
			obj.onchange=function(){IMAGE.setDsValue(id, this);}
			obj.onmouseout=function(){IMAGE.hideAdd(id);}
			obj.onmouseover=function(){IMAGE.showAdd(id);}
			obj.oncontextmenu=function(){IMAGE.cancel(id, event);}
		}
	},

	showAdd : function(id)
	{
		var obj = $(id + "_add");
		var img = $(id);
		obj.style.display = "block";
	},

	hideAdd : function(id)
	{
		var obj = $(id + "_add");
		obj.style.display = "none";
	},

	cancel : function(id, event)
	{
		if (!event) event = window.event;
		SYSTEMEVENT.preventDefault(event);
		SYSTEMEVENT.stopPropagation(event);

 		var objImage = $(id);
		var dsObj = $(objImage.p.dsObj);
		if (objImage.readOnly) return;
		if (dsObj.DSpos!=0) 
		{ 	
			dsObj.DSresult[dsObj.DSpos][objImage.p.dsItem]; 
			var pos = (objImage.row == undefined) ? dsObj.DSpos : objImage.row;
			if (pos == -1) dsObj.DSresult[pos][objImage.p.dsItem] = Array();
			dsObj.DSresult[pos][objImage.p.dsItem] = "";
			DS.dschange(dsObj);
		}
		objImage.src = objImage.p.empty;
	},

	browser : function(obj, event)
	{
		if (event.button == 0) obj.click();
	} 
}

var IMAGE = new clsImage();