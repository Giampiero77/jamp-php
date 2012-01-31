/**
* Class DS
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsDs()
{
	this.handleResponse = undefined;
	this.lock = false;
}

clsDs.prototype =
{
	movePrev : function(dsObjName)
	{
		var dsObj = $(dsObjName);
		if (dsObj.DSpos < 0) return;
		if (dsObj.p.DSsavetype == "row") this.dssaveall(dsObj);
		if (dsObj.DSpos == 1 && (dsObj.DSstart > 0)) 
		{
			var preMemory = dsObj.DSposMemory;
			if (dsObj.DSposMemory == undefined) dsObj.DSposMemory = dsObj.DSlimit ;
			var delta = dsObj.DSstart - dsObj.DSlimit;
			dsObj.DSstart = delta;
			AJAX.dsmore(dsObj, "data=load&dsobjname=" + dsObjName + "&start=" + delta, true, undefined, dsObj.handleResponse);
			dsObj.DSposMemory = preMemory;
		}
		else if(dsObj.DSpos > 1)
		{
			dsObj.DSpre = dsObj.DSpos;
			dsObj.DSpos--;
		}
	},

	moveNext : function(dsObjName)
	{
		var dsObj = $(dsObjName);
		if (dsObj.DSpos < 0) return;
		if (dsObj.p.DSsavetype == "row") this.dssaveall(dsObj);
		if (dsObj.DSpos < dsObj.DSresult.length-1)
		{
			dsObj.DSpre = dsObj.DSpos;
			dsObj.DSpos++;
		}
		else
		{
			if (dsObj.DSlimit == 0) return;
			var delta = dsObj.DSrow - dsObj.DSlimit;
			if (dsObj.DSstart < delta)
			{
				dsObj.DSpre = dsObj.DSpos;
				dsObj.DSpos = 1;
				dsObj.DSstart = dsObj.DSend;
				AJAX.dsmore(dsObj, "data=load&dsobjname=" + dsObjName + "&start=" + dsObj.DSend, undefined, dsObj.handleResponse);
			}
		}
	},

	moveFirst : function(dsObjName)
	{
		var dsObj = $(dsObjName);
		if (dsObj.DSpos < 0) return;
		if (dsObj.DSstart == 0)
		{
			dsObj.DSpre = dsObj.DSpos;
			dsObj.DSpos = 1;
		}
		else
		{
		if (dsObj.p.DSsavetype == "row") this.dssaveall(dsObj);
			AJAX.dsmore(dsObj, "data=load&dsobjname=" + dsObjName + "&start=0", undefined, dsObj.handleResponse);
		}
	},

	moveLast : function(dsObjName)
	{
		var dsObj = $(dsObjName);
		if (dsObj.DSpos < 0) return;
		if (dsObj.p.DSsavetype == "row") this.dssaveall(dsObj);
		var delta = dsObj.DSrow - dsObj.DSlimit;
		if (dsObj.DSlimit == 0 || delta < 0)
		{
			dsObj.DSpre = dsObj.DSpos;
			dsObj.DSpos = dsObj.DSrow;
		}
		else if (dsObj.DSstart < delta)
		{
			var page = Math.ceil(dsObj.DSrow / dsObj.DSlimit);
			delta = (page > 0) ? ((page-1)*dsObj.DSlimit) : 0;
			dsObj.DSpre = dsObj.DSpos;
			dsObj.DSpos = 1;
			dsObj.DSstart = delta;
			AJAX.dsmore(dsObj, "data=load&dsobjname=" + dsObjName + "&start=" + delta, undefined, dsObj.handleResponse);
		}
	},

	movePrevPage : function(dsObjName)
	{
		var dsObj = $(dsObjName);
		if (dsObj.DSpos < 0) return;
		if (dsObj.DSlimit == 0) return;
		if (dsObj.p.DSsavetype == "row") this.dssaveall(dsObj);
		if (dsObj.DSstart > 0) 
		{
			dsObj.DSpre = dsObj.DSpos;
			dsObj.DSpos = 1;
			var delta = dsObj.DSstart - dsObj.DSlimit;
			dsObj.DSstart = delta;
			AJAX.dsmore(dsObj, "data=load&dsobjname=" + dsObjName + "&start=" + delta, undefined, dsObj.handleResponse);
		}
	},

	moveNextPage : function(dsObjName)
	{
		var dsObj = $(dsObjName);
		if (dsObj.DSpos < 0) return;
		if (dsObj.DSlimit == 0) return;
		if (dsObj.p.DSsavetype == "row") this.dssaveall(dsObj);
		var delta = dsObj.DSrow - dsObj.DSlimit;
		if (dsObj.DSstart < delta)
		{
			dsObj.DSpre = dsObj.DSpos;
			dsObj.DSpos = 1;
			dsObj.DSstart = dsObj.DSend;
			AJAX.dsmore(dsObj, "data=load&dsobjname=" + dsObjName + "&start=" + dsObj.DSend, undefined, dsObj.handleResponse);
		}
	},

	moveRow : function(dsObjName, row)
	{
		var dsObj = $(dsObjName);
		if (dsObj.DSpos == row || dsObj.DSpos < 0) return;
		if (dsObj.p.DSsavetype == "row") this.dssaveall(dsObj);
		if (row < dsObj.DSresult.length)
		{
			dsObj.DSpre = dsObj.DSpos;
			dsObj.DSpos = row;
			eval(dsObjName + 'Move();');
		}
	},

	reload : function(dsObjName)
	{
 		var dsObj = $(dsObjName);
		if (dsObj.p.DSreferences == undefined) AJAX.dsmore(dsObj, "data=load&dsobjname=" + dsObjName + "&start=" + dsObj.DSstart, undefined, dsObj.handleResponse);
		else AJAX.dslink(dsObjName);
	},

	dropObj : function(dsObj)
	{
		dsObj.DSresult = Array();
		dsObj.DSrow = 0;
		dsObj.DSpos = 0;
		dsObj.DSpre = 0;
		if (AJAX.function_exists(dsObj.id+"Refresh"))
		{ 
			try { eval(dsObj.id+"Refresh();");}
			catch (e) { SYSTEMEVENT.errorJAVASCRIPT(e.message, e.fileName, e.lineNumber); }
		}
	},

	refreshObj : function(dsObjName)
	{
		var obj = $(dsObjName);
		this.dssave(dsObjName);
		obj.DSchange = false;
	},

	setFocusNew : function(dsObj)
	{
		if (dsObj.p.DSfocus != undefined)
		{
			var objfocus = dsObj.p.DSfocus.replace('$', dsObj.DSpos);
			SYSTEMEVENT.setFocus($(objfocus));
		}
	},

	dsnew : function(dsObjName)
	{
		var dsObj = $(dsObjName);
		if (dsObj.p.DSsavetype == "row") this.dssaveall(dsObj);
		var i = (dsObj.DSpos < 0) ? dsObj.DSpos-1 : -1;
		dsObj.DSresult[i] = Array();
		if (dsObj.DSpos > 0) dsObj.DSpre = dsObj.DSpos;
		dsObj.DSpos = i;
		eval(dsObjName+'Move();');
		this.setFocusNew(dsObj);
		if (dsObj.p.DSrefresh != undefined)
		{
			var dsref = dsObj.p.DSrefresh.split(",");
			var dsreflen = dsref.length;
			for (var i = 0; i < dsreflen; i++)
			{
				var objref = $(dsref[i]);
				objref.DSresult = Array();
				objref.DSrow = 0;
				objref.DSpos = 0;
				objref.DSpre = 0;
				objref.DSstart = 0;
				objref.DSend = 0;
				if (AJAX.function_exists(objref.id+"Refresh")) eval(objref.id + 'Refresh();');
			}
		}
		dsObj.DSchange = false;
	},

	dscancel : function(dsObjName)
	{
		var dsObj = $(dsObjName);
		if (dsObj.DSpos < 0)
		{
			for (var i = dsObj.DSpos; i < 0; i++) delete dsObj.DSresult[i];
			dsObj.DSpos = dsObj.DSpre;
			dsObj.DSchange = false;
			if (AJAX.function_exists(dsObjName+"Refresh")) eval(dsObjName + 'Refresh();');
		}

		if (dsObj.p.DSfocus != undefined) SYSTEMEVENT.setFocus(dsObj);
	},

	dsdelete : function(dsObjName)
	{
		var dsObj = $(dsObjName);
		if (dsObj.DSpos == -1) return;
		if (confirm(LANG.translate("JDS000")))
		{
			AJAX.loader(true);
			dsObj.DSchange = false;
			if (dsObj.DSmultipos.length > 0)
			{
				for (var key in dsObj.DSmultipos)
				{
					if(dsObj.DSmultipos.hasOwnProperty(key))
					{
						var returnxml = (key == dsObj.DSmultipos.length-1) ? true : false;
						dsObj.DSpos = key;
						AJAX.dsdelete(dsObjName, returnxml);
					}
				}
			}
			else AJAX.dsdelete(dsObjName, true);
			dsObj.DSmultipos = new Array();
		}
	},

	dsdeleteall : function(dsObjName)
	{
		var dsObj = $(dsObjName);
		if (dsObj.DSpos == -1) return;
		if (dsObj.p.DSdeleteall != true) return;
		if (confirm(LANG.translate("JDS001")))
		{
			if (confirm(LANG.translate("JDS002")))
			{
				AJAX.loader(true);
				dsObj.DSchange = false;
				AJAX.dsdeleteall(dsObjName, true);
			}
		}
	},

	dsValidate : function(dsObjName, dsObj, tagName, error)
	{
		var o = document.getElementsByTagName(tagName);
		for(var k=0;k<o.length;k++)
		{
			 if (o[k].p && o[k].p.dsObj==dsObjName && o[k].p.send) 
			 {
				  if (!REGEXP.checkForm(o[k], o[k].p.send)) 
				  {
						dsObj.DSchange = false;
						if (!error) 
						{
							 SYSTEMEVENT.setFocus(o[k]);
							 error = true;
						}
				  }
			 } 
		}
		return error;
	},
 
	dssave : function(dsObjName, conf)
	{
		var dsObj = $(dsObjName);
		if (dsObj.DSchange == true)
		{
			 eval(dsObjName + "BeforeSave()");
			 var error = false;
			 error = DS.dsValidate(dsObjName, dsObj, "input", error);
			 error = DS.dsValidate(dsObjName, dsObj, "textarea", error);
			 if (error) return false;
			 if (dsObj.DSvalidate == true) 
			 {
				if (AJAX.function_exists('Validate' + dsObjName + '()') && !eval('Validate' + dsObjName + '()')) 
				{ 
					 dsObj.DSchange = false; 
					 return false;
				}
			 }
			 if (dsObj.p.DSsavetype == "live")
			 {
				dsObj.DSchange = false;
				AJAX.dssave(dsObjName, true);
				return true;
			 }
			 else 
			 {
				if (dsObj.DSpos >0) var msg = LANG.translate("JDS003");
				else var msg = LANG.translate("JDS004");
				var save = (dsObj.p.DSconfirm == true || conf) ? confirm(msg) : true;
				if (save == true)
				{
					 AJAX.loader(true);
					 dsObj.DSchange = false;
					 AJAX.dssave(dsObjName, true);
					 return true;
				}
				else this.reload(dsObjName);
			 }
		}
		return false;
	},

	dsUpdate : function(id)
	{
 		var objdsXML = $(id);
 		objdsXML.DSchange = false;
 		objdsXML.DSmodpos = objdsXML.DSpos;
 		eval(id + 'SaveRow();');
 		eval(id + 'Move();');
	},

	dsInsert : function(id, inslast)
	{
		var objdsXML = $(id);
		objdsXML.DSchange = false;
		var newPos = (objdsXML.DSresult.length == 0) ? 1 : objdsXML.DSresult.length;
		objdsXML.DSrow++;
		objdsXML.DSresult[newPos] = objdsXML.DSresult[objdsXML.DSpos];
		delete(objdsXML.DSresult[objdsXML.DSpos]);
		objdsXML.DSresult[newPos][objdsXML.p.DSkey] = inslast;
		objdsXML.DSresult[newPos]['keyname'] = objdsXML.p.DSkey;
		objdsXML.DSresult[newPos]['keynamevalue'] = inslast;
		objdsXML.DSmodpos = objdsXML.DSpos;
		objdsXML.DSpos = newPos;
 		eval(id + 'SaveRow();');
 		eval(id + 'Move();');
	},

	dschange : function(obj)
	{
		if (obj.p.DSreadonly == true) return;
 		obj.DSchange = true;
		eval(obj.id + 'ChangeItem();');
		if (obj.p.DSsavetype == "live") this.dssave(obj.id);
	},

	dschangeNoLive : function(obj)
	{
		if (obj.p.DSreadonly == true) return;
		obj.DSchange = true;
		eval(obj.id + 'ChangeItem();');
	},

	dssaveall : function(dsObj)
	{
		var conf = (dsObj.p.DSconfirm == false) ? false : true;
		this.dssave(dsObj.id, conf);
		if (dsObj.p.DSrefresh)
		{
			var dsref = dsObj.p.DSrefresh.split(",");
			var dsreflen = dsref.length;
			for (var i = 0; i < dsreflen; i++) this.dssave(dsref[i], conf);
		}
	}
}

var DS = new clsDs();