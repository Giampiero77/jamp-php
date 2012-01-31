/**
* Class DSNAV
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsDsnav()
{
}

clsDsnav.prototype = 
{
	dsdelete : function(dsObjName, evt)
	{
		if (evt.ctrlKey) DS.dsdeleteall(dsObjName);
		else DS.dsdelete(dsObjName);
	},

	setButton : function(dsObjName, button, lock)
	{		
		var bt = $(dsObjName + "_" + button);
		if (bt != undefined) bt.className = (lock) ? $(dsObjName).className + '_' + button : $(dsObjName).className + '_' + button + "_gray";
	},

	setPage : function(dsObj, dsObjName)
	{
		var pageObj = $(dsObjName + "_page");
		if (pageObj == undefined) return;
		pageObj.disabled = (dsObj.DSpos < 0) ? true : false;
		if (pageObj.limit != dsObj.DSlimit || pageObj.tot != dsObj.DSrow)
		{
			pageObj.limit = dsObj.DSlimit;
			pageObj.tot = dsObj.DSrow;
			pageObj.options.length = 0;
			var page = (pageObj.limit > 0) ? Math.ceil(pageObj.tot / pageObj.limit) : 0;
//			pageObj.options.length = page; //non serve si può togliere si blocca questo sotto
			for (var i = 0; i < page; i++) pageObj.options[i] = new Option(i + 1, i * pageObj.limit);
		}
		if (dsObj.DSlimit == 0) pageObj.options[i] = new Option(1, 0);
		else pageObj.selectedIndex = parseInt(dsObj.DSstart / dsObj.DSlimit);
	},
	
	page : function(dsObjName, pageObj) 
	{
		var dsObj = $(dsObjName);
		dsObj.DSpre = dsObj.DSpos;
		dsObj.DSpos = 1;
		dsObj.DSstart = pageObj.value;
		AJAX.dsmore(dsObj, "data=load&dsobjname=" + dsObj.id + "&start=" + pageObj.value);
	},

	ChangeItem : function(dsObjName)
	{
		var obj = $(dsObjName);
		var dsObj = $(obj.p.dsObj);
		this.setButton(dsObjName, "save", dsObj.DSchange);
		if (dsObj.p.DSsavetype == "live")
		{
			this.setButton(dsObjName, "new", 	false)
			this.setButton(dsObjName, "cancel", false);
		}
	},

	dsfind : function(dsObjName)
	{
		var obj = $(dsObjName);
		var search_value = $(dsObjName + "_search").value;
		var dsObj = $(obj.p.dsObj);
		dsObj.DSsearch = (search_value==undefined || search_value=='') ? '' : obj.p.DSsearch.replaceAll('$$VALUE$$', search_value.replace(/'/g,"\\'"));
		dsObj.DSstart = 0;
      AJAX.dsmore(dsObj, 'data=load&dsobjname=' + dsObj.id + '&start=' + dsObj.DSstart );
	},

	dskeyfind : function(id, e)
	{
		var keynum = (window.event) ? e.keyCode : e.which;
		if (keynum == 13) this.dsfind(id);
	},

	refreshObj : function(dsObjName)
	{
		var total = $(dsObjName + "_total");
		var obj = $(dsObjName);
		var dsObj = $(obj.p.dsObj);
		this.setPage(dsObj, dsObjName);
		this.setButton(dsObjName, "reload", true);
		if (dsObj.DSpos < 0)
		{
	 		if (total != undefined) total.innerHTML = LANG.translate("DSNAV001");
			this.setButton(dsObjName,"new",dsObj.p.DSsavetype == "table");
			this.setButton(dsObjName,"save",true);
			this.setButton(dsObjName,"delete",false);
			this.setButton(dsObjName,"cancel",true);
			this.setButton(dsObjName,"first",false);
			this.setButton(dsObjName,"last",false);
			this.setButton(dsObjName,"prev",false);
			this.setButton(dsObjName,"next",false);
		}
		else 
		{
			 this.setButton(dsObjName, "new", 	true);
			 if (dsObj.p.DSreferences != undefined)
			 {
				var dsref = dsObj.p.DSreferences.split(",");
				var dsreflength = dsref.length;
				for (var i = 0; i < dsreflength; i++)
				{
					 var objref = $(dsref[i]);
					 if (objref.DSpos < 0 || objref.DSrow == 0) this.setButton(dsObjName, "new", 	false);
				}
			 }
			 if (total != undefined) total.innerHTML = (parseInt(dsObj.DSstart) + parseInt(dsObj.DSpos)) + " / " + dsObj.DSrow;
			 this.setButton(dsObjName, "cancel", false);
			 if (dsObj.DSresult.length == 0)
			 {
				  this.setButton(dsObjName, "save", 	false);
				  this.setButton(dsObjName, "delete", false);
				  this.setButton(dsObjName, "first", 	false);
				  this.setButton(dsObjName, "last", 	false);
				  this.setButton(dsObjName, "prev", 	false);
				  this.setButton(dsObjName, "next", 	false);
			 }
			 else
			 {
				  this.setButton(dsObjName, "save", 	dsObj.DSchange);
				  this.setButton(dsObjName, "delete", true);
				  this.setButton(dsObjName, "first", 	true);
				  this.setButton(dsObjName, "last", 	true);
				  this.setButton(dsObjName, "prev", 	true);
				  this.setButton(dsObjName, "next", 	true);
			 }
		}
	}
}

var DSNAV = new clsDsnav();