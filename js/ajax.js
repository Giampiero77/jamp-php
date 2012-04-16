/**
* Class AJAX
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsAJAX()
{
	this.loadall = true;
	this.debug	= false;
	this.hideloader = false
}

clsAJAX.prototype =
{
	loader : function(visible)
	{
		if (this.hideloader) return;
		if (SYSTEMEVENT.messagebox_back.style.display=='block') return; 
		SYSTEMEVENT.pagelock.style.display = (visible) ? "block" : "none";
		SYSTEMEVENT.loaderbox.style.display = (visible) ? "block" : "none";
	},

	debugEnable : function()
	{
		this.debug = true;
	},

	keepAlive : function(second, url)
	{
		var url = (url==undefined || url=='') ? document.location.href : url;
		AJAX.hideloader = true;
		AJAX.request("POST", url, "data=keepalive", false, false);
		AJAX.hideloader = false;
		setTimeout(function(){AJAX.keepAlive(second,url);}, second);
	},
	
	login : function(dsObjName, itemuser, itempwd)
	{
		var dsObj = $(dsObjName);
		var user = $(itemuser);
		var pwd = $(itempwd);
		var remember = $(itemremember);
		if (dsObj.DSvalidate == true && AJAX.function_exists('Validate' + dsObjName + '()'))
		{
			 if (!eval('Validate' + dsObjName + '()')) { dsObj.DSchange == false; return;}
		}
		post = "data=login&dsobjname="+dsObj.id+"&user="+user.value+"&pwd="+pwd.value+"&itemuser="+user.p.dsItem+"&itempwd="+pwd.p.dsItem;
		if (remember) post += "&remember_me="+remember.value;
		this.request("POST", dsObj.p.DSaction, post, true, true);
	},

	changePassword : function(dsObjName, itemuser, itempwd, itemoldpwd)
	{
		var dsObj = $(dsObjName);
		var user = $(itemuser);
		var pwd = $(itempwd);
		var oldpwd = $(itemoldpwd);
		if (dsObj.DSvalidate == true) if (!eval('Validate' + dsObjName + '()')) { dsObj.DSchange == false; return;}
		post = "data=changepasswd&dsobjname="+dsObj.id+"&user="+user.value+"&pwd="+pwd.value+"&oldpwd="+oldpwd.value;
		post += "&itemuser="+user.p.dsItem+"&itempwd="+pwd.p.dsItem+"&itemoldpwd="+oldpwd.p.dsItem;
		this.request("POST", dsObj.p.DSaction, post, true, true);
	},

	dssaveRow : function(dsObj, returnxml)
	{
		var post;
		var pos = (dsObj.DSposalter == undefined) ? dsObj.DSpos : dsObj.DSposalter;
		dsObj.DSposalter = undefined;

		if (pos == -1) post = "data=new&dsobjname="+dsObj.id+"&start="+dsObj.DSstart;
		else post = "data=update&dsobjname="+dsObj.id+"&start="+dsObj.DSstart;

		if (dsObj.p.DSreferences != undefined)
		{
			var ref = dsObj.p.DSreferences.split(",");
			var refkey = dsObj.p.DSreferenceskey.split(",");
			var keyval = Array();
			for (var k in ref)
			{
				if (ref.hasOwnProperty(k))
				{
					var dsObjLink = $(ref[k]);
					if (dsObjLink.DSpos == dsObjLink.DSresult.length) keyval[k] =  ""
					else keyval[k] = dsObjLink.DSresult[dsObjLink.DSpos][refkey[k]];
				}
			}
			post += "&dsforeignkey=" + encodeURIComponent(dsObj.p.DSforeignkey) + "&dsforeignkeyvalue=" + encodeURIComponent(keyval.join(","));
		}

		for (var i in dsObj.DSresult[pos])
		{
			if (dsObj.DSresult[pos].hasOwnProperty(i)) post += "&"+i+"="+encodeURIComponent(dsObj.DSresult[pos][i]);
		}
		this.request("POST", dsObj.p.DSaction, post, true, returnxml);
	},

	dssaveTable : function(dsObj, returnxml)
	{
		var post = Array();
		var n = 0;
		for (var i in dsObj.DSresult)
		{
			if (dsObj.DSresult.hasOwnProperty(i))
			{
				n++;
				if (i < 0) post[n] = "data=new&dsobjname="+dsObj.id+"&start="+dsObj.DSstart;
				else post[n] = "data=update&dsobjname="+dsObj.id+"&start="+dsObj.DSstart;
			
				if (dsObj.DSsearch != "" && dsObj.DSsearch != undefined) post[n] += "&" + dsObj.id + "where=" + encodeURIComponent(dsObj.DSsearch);
				if (dsObj.p.DSreferences != undefined)
				{
					var ref = dsObj.p.DSreferences.split(",");
					var refkey = dsObj.p.DSreferenceskey.split(",");
					var keyval = Array();
					for (var k in ref)
					{
						if (ref.hasOwnProperty(k))
						{
							var dsObjLink = $(ref[k]);
							if (dsObjLink.DSpos == dsObjLink.DSresult.length) keyval[k] =  ""
							else keyval[k] = dsObjLink.DSresult[dsObjLink.DSpos][refkey[k]];
						}
					}
					post[n] += "&dsforeignkey=" + encodeURIComponent(dsObj.p.DSforeignkey) + "&dsforeignkeyvalue=" + encodeURIComponent(keyval.join(","));
				}
				for (var ii in dsObj.DSresult[i])
				{
					if (dsObj.DSresult[i].hasOwnProperty(ii))
					{
						post[n] += "&" + ii + "=" + encodeURIComponent(dsObj.DSresult[i][ii]);
					}
				}
			}
		}
		this.request("POST", dsObj.p.DSaction, post, true, returnxml);
	},

	dssave : function(dsObjName, returnxml)
	{
		var dsObj = $(dsObjName);
		if (dsObj.p.DSsavetype == "row" || dsObj.p.DSsavetype == "live") this.dssaveRow(dsObj, returnxml);
		if (dsObj.p.DSsavetype == "table") this.dssaveTable(dsObj, returnxml);
	},

	refreshdslink : function (dsObjName)
	{
		if (this.loadall == false) this.dslink(dsObjName);
	},

	dssimplerefresh : function (dsObj)
	{
		var post = "data=load&dsobjname=" + dsObj.id;
		if (dsObj.DSsearch != "" && dsObj.DSsearch != undefined) post += "&" + dsObj.id + "where=" + encodeURIComponent(dsObj.DSsearch);
		if (dsObj.p.DSorder != "" && dsObj.p.DSorder != undefined) post += "&dsorder=" + dsObj.p.DSorder;
		post += "&start=" + dsObj.DSstart;
		this.request("POST", dsObj.p.DSaction, post, true, true);
	},

	dslink : function (dsObjName)
	{
		var dsObj = $(dsObjName);
		var post = "data=load&dsobjname=" + dsObjName;
		if (dsObj.p.DSreferences == undefined)
		{
			this.dssimplerefresh(dsObj);
			return;	
		}
		var dsref = dsObj.p.DSreferences.split(",");
		var dsrefkey = dsObj.p.DSreferenceskey.split(",");
		var dsObjLink = $(dsref[0]);
		if (dsObjLink.DSpos == -1) return;
		if ((dsObjLink.DSresult.length == 0) && (dsObjLink.p.DSrefresh != undefined))
		{
			dsObj.DSresult = Array();
			dsObj.DSrow = 0;
			dsObj.DSpre = 0;
			dsObj.DSpos = 0;
			if (this.function_exists(dsObj.id+"Refresh")) eval(dsObj.id +"Refresh();");
			return;
		}
		var keyval = Array();
		if (dsObjLink.DSresult[dsObjLink.DSpos] != undefined)
		{
			keyval[0] = dsObjLink.DSresult[dsObjLink.DSpos][dsrefkey[0]];
		}
		else
		{
			var allkey = ""
			for (var itm in dsObjLink.DSresult)
			{
				if (dsObjLink.DSresult.hasOwnProperty(itm)) //Bug Fix
				{
					allkey += (allkey == "") ? dsObjLink.DSresult[itm][dsrefkey[0]] : "," + dsObjLink.DSresult[itm][dsrefkey[0]];
				}
			}
			keyval[0] = allkey;
		}
		var length = dsrefkey.length;
		for (var i = 1; i < length; i++)
		{
			var dsObjLink = $(dsref[i]);
			keyval[i] = (dsObjLink.DSresult[dsObjLink.DSpos] == undefined) ? "" : dsObjLink.DSresult[dsObjLink.DSpos][dsrefkey[i]];
		}
		post += "&dsforeignkey=" + encodeURIComponent(dsObj.p.DSforeignkey) + "&dsforeignkeyvalue=" + encodeURIComponent(keyval.join(","));
		if (dsObj.DSsearch != "" && dsObj.DSsearch != undefined) post += "&" + dsObj.id + "where=" + encodeURIComponent(dsObj.DSsearch);
		if (dsObj.p.DSorder != "" && dsObj.p.DSorder != undefined) post += "&dsorder=" + dsObj.p.DSorder;
		post += "&start=" + dsObj.DSstart;
		this.request("POST", dsObj.p.DSaction, post, true, true);
	},

	dsmore : function(dsObj, post, forceSync, func)
	{
		if (dsObj.p.DSreferences == undefined)
		{
			var sync = (dsObj.p.DSrefresh == undefined) ? false : true;
			var sync = (forceSync == undefined) ? sync : forceSync;
			if (dsObj.DSsearch != "") post += "&" + dsObj.id + "where=" + encodeURIComponent(dsObj.DSsearch);
			if (dsObj.p.DSorder != "" && dsObj.p.DSorder != undefined) post += "&dsorder=" + dsObj.p.DSorder;
			this.request("POST", dsObj.p.DSaction, post, sync, true, func);
		} else this.dslink(dsObj.id);
	},

	dsdelete : function(dsObjName, returnxml)
	{
		var post;
		var dsObj = $(dsObjName);
		dsObj.DSpre = dsObj.DSpos;

		if (dsObj.p.DSrefresh != undefined)
		{
			var ref = dsObj.p.DSrefresh.split(",");
			for (var k in ref)
			{
				if (ref.hasOwnProperty(k))
				{
					var dsObjLink = $(ref[k]);
					if (dsObjLink.p.DSdeleteoncascate)
					{
						post = "data=delete&dsobjname=" + ref[k] + "&";
						post += "keyname=" + encodeURIComponent(dsObjLink.p.DSforeignkey) + "&";
						post += "keynamevalue=" + encodeURIComponent(dsObj.DSresult[dsObj.DSpre]["keynamevalue"]);
						this.request("POST", dsObjLink.p.DSaction, post, true, false);
					} 
					DS.dropObj(dsObjLink);		
				}
			}
		}

		post = "data=delete&dsobjname=" + dsObjName + "&";
		post += "keyname=" + encodeURIComponent(dsObj.p.DSkey) + "&";
		post += "keynamevalue=" + encodeURIComponent(dsObj.DSresult[dsObj.DSpos]["keynamevalue"]);
		if (dsObj.DSsearch != "" && dsObj.DSsearch != undefined) post += "&" + dsObj.id + "where=" + encodeURIComponent(dsObj.DSsearch);
		if (dsObj.p.DSorder != "" && dsObj.p.DSorder != undefined) post += "&dsorder=" + dsObj.p.DSorder;
		if (dsObj.p.DSreferences != undefined)
		{
			var dsObjLink = $(dsObj.p.DSreferences);
			post += "&dsforeignkey=" + encodeURIComponent(dsObj.p.DSforeignkey) + "&dsforeignkeyvalue=" + encodeURIComponent(dsObjLink.DSresult[dsObjLink.DSpos][dsObj.p.DSreferenceskey]);
		}
		this.request("POST", dsObj.p.DSaction, post, true, returnxml);
	},

	dsdeleteall : function(dsObjName, returnxml)
	{
		var dsObj;
		var post;

		dsObj = $(dsObjName);
		dsObj.DSpre = dsObj.DSpos;

		if (dsObj.p.DSrefresh != undefined)
		{
			var ref = dsObj.p.DSrefresh.split(",");
			for (var k in ref)
			{
				if (ref.hasOwnProperty(k))
				{
					var dsObjLink = $(ref[k]);
					if (dsObjLink.p.DSdeleteoncascate)
					{
						post = "data=deleteall&dsobjname=" + ref[k] + "&";
						this.request("POST", dsObjLink.p.DSaction, post, true, returnxml);
					} 
					DS.dropObj(dsObjLink);		
				}
			}
		}
		post = "data=deleteall&dsobjname=" + dsObjName + "&";
		this.request("POST", dsObj.p.DSaction, post, true, returnxml);
	},

	rewriteObj : function (idObj, page)
	{
		if (document.body.id == idObj) window.location = page + "?objname=" + idObj
		else this.request("POST", page, "objname=" + idObj, true, true);
	},

	request : function (method, url, data, sync, returnxml, func)
	{
		this.loader(true);
		if (typeof data == "object") //Multi Request
		{
			var newdata = "";
			for (var i = 1; i < data.length; i++)
			{
				newdata += "&" + data[i].replace(/=/g,'[' + (i-1) + ']=');
			}
			data = 'multirequest=' + parseInt(data.length-1) + newdata; //non toccare
		}
		data = (returnxml == false) ? data+"&returnxml=false" : data;

		if (window.XMLHttpRequest)
		{
			var conn = new XMLHttpRequest();  
			if (conn.overrideMimeType) conn.overrideMimeType("text/xml");
		}
		if (window.ActiveXObject)
		{
			var conn = new ActiveXObject("Microsoft.XMLHTTP");
			if (!conn) conn = new ActiveXObject("Msxml2.XMLHTTP");
		}

		if (conn) //Post
		{
			if (returnxml && sync) 
			{
	    		try
          		{
              		conn.onreadystatechange = null;
    			} catch (e) {} 
			}
			else conn.onreadystatechange = function(){ AJAX.handleResponse(conn, returnxml, func) };
			conn.open(method,url,!sync);
			conn.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
			if (this.debug == true)
			{
				var time = new Date();
				var logtime = time.getHours() + ":" + time.getMinutes() + ":" + time.getSeconds() + " " + time.getDate() + "/" + (time.getMonth()+1) + "/" + time.getFullYear();
				window.consoleAJAX.log(logtime + " - <b>[REQUEST]<\/b> METHOD: <font color=\"blue\">" + method + "<\/font> URL: <font color=\"green\">" + url + "<\/font> SYNC: <font color=\"green\">" + sync + "<\/font> EXPECTED RETURNXML: <font color=\"green\">" + returnxml + "<font>");
				window.consoleAJAX.log(logtime + " - <b>[DATA]<\/b>"+data);
			}
			conn.send(data); 
			if (sync) AJAX.handleResponse(conn, returnxml, func); //Bug Firefox
			if (!returnxml) this.loader(false);
		}
		else
		{
			this.loader(false);
			SYSTEMEVENT.showMessage(LANG.translate("JAJAX001"), 'Errore AJAX', self.location.href, 0, 80) 
		}
	},

	handleResponse : function (conn, returnxml, func)
	{ 
		if (conn.readyState == 4)
		{
			if (conn.status == 200) 
			{
				AJAX.setDsXML(conn);
				if (func) eval(func);
			}
			else SYSTEMEVENT.errorXML(conn.responseText.replace(/&lt;\/?[^&gt;]+&gt;/gi, ""));
			AJAX.loader(false);
		}
	},

	Resize : function()
	{
		  this.loadall = false;
		  Resize();
	},

	loadAll : function(action, param)
	{
		this.loader(true);
		var data = (param == undefined || param=='') ? "data=loadall" : "data=loadall&"+param;
		this.request("POST", action, data, false, true, "AJAX.Resize();");
	},

	function_exists : function(function_name) 
	{
		if (typeof function_name == 'string') return (typeof window[function_name] == 'function');
		else return (function_name instanceof Function);
	},

	setDsJSON : function(dsObjname, start, end, tot, limit)
	{
		var dsObj = $(dsObjname);
		if (dsObj.DSresult.length > 0)
		{
			dsObj.DSresult.unshift('');
			delete dsObj.DSresult[0];
			if (dsObj.p.DSkey != undefined) 
			{
				var slen = dsObj.DSresult.length;
				for (var i=1; i < slen; i++) 
				{
					dsObj.DSresult[i]['keyname'] = dsObj.p.DSkey;
					dsObj.DSresult[i]['keynamevalue'] = dsObj.DSresult[i][dsObj.p.DSkey];
				}
			}
		}
		dsObj.DSstart = start;
		dsObj.DSend = end;
		dsObj.DSrow = tot;
		dsObj.DSlimit = limit;
		dsObj.DSpre = (dsObj.DSposMemory == undefined) ? 1 : parseInt(dsObj.DSposMemory);
		if (dsObj.DSrow == 0) dsObj.DSpre = 0;
		dsObj.DSpos = dsObj.DSpre;
		dsObj.DSchange = false;
		dsObj.DSmultipos = new Array();
		if (dsObj.p.DSorder == undefined) dsObj.p.DSorder = '';
		if (dsObj.DSsearch == undefined) dsObj.DSsearch = '';
		if (this.function_exists(dsObj.id+"Refresh"))
		{ 
			try { eval(dsObj.id+"Refresh();");}
			catch (e) { SYSTEMEVENT.errorJAVASCRIPT(e.message, e.fileName, e.lineNumber); }
		}
	},

	setDsXML : function(conn)
	{
		var xmlDoc = conn.responseXML;
 		if (xmlDoc == undefined)
 		{
			if (conn.responseText!="") SYSTEMEVENT.errorXML(conn.responseText.replace(/&lt;\/?[^&gt;]+&gt;/gi, ""));
 			return false;
 		}
		if (this.debug == true)
		{
			var time = new Date();
			var logtime = time.getHours() + ":" + time.getMinutes() + ":" + time.getSeconds() + " " + time.getDate() + "/" + (time.getMonth()+1) + "/" + time.getFullYear();			
			var resp = conn.responseText.replace(/<\/?[^>]+>/gi, "");
			window.consoleAJAX.log(logtime + " - <b>[RETURN XML]<\/b><br>" + resp + "<\/pre>");
		}
		var root = xmlDoc.getElementsByTagName("data");
 		if (root[0] == undefined)
 		{
			if (conn.responseText!="") SYSTEMEVENT.errorXML(conn.responseText.replace(/&lt;\/?[^&gt;]+&gt;/gi, ""));
 			return false;
 		}
		this.loader(true);
		var dsObj;
		var subNodesDS = root[0].childNodes;
		var resize = false;
		var length = subNodesDS.length;
		for (var i=0; i<length; i++) 
		{
			var dsNode = subNodesDS[i];	
			if (dsNode.nodeType != 1) continue;
			if (dsNode.nodeName == "html")
			{
				var textContent = (dsNode.textContent == undefined) ? dsNode.firstChild.data : dsNode.textContent;
				var ObjRewrite = $(dsNode.getAttribute("id") + "_container");
				if (ObjRewrite == undefined) ObjRewrite = $(dsNode.getAttribute("id"));
				var newparentobj = document.createElement(ObjRewrite.parentNode.tagName);
				newparentobj.innerHTML = textContent.trim(); 
				ObjRewrite.parentNode.replaceChild(newparentobj.firstChild,ObjRewrite);
				resize = true;
			}
			else if (dsNode.nodeName == "script")
			{
				var textContent = (dsNode.textContent == undefined) ? dsNode.firstChild.data : dsNode.textContent;
				eval(textContent);
			}
			else
			{
				dsObj = $(dsNode.nodeName);
				dsObj.DSstart = parseInt(dsNode.getAttribute("start"));
				dsObj.DSend = parseInt(dsNode.getAttribute("end"));
				dsObj.DSlimit = parseInt(dsNode.getAttribute("limit"));
				dsObj.DSrow = parseInt(dsNode.getAttribute("tot"));
				dsObj.DSpre = (dsObj.DSposMemory == undefined) ? 1 :  parseInt(dsObj.DSposMemory);
				if (dsObj.DSrow == 0) dsObj.DSpre = 0;
				dsObj.DSpos = dsObj.DSpre;
				dsObj.DSchange = false;
				dsObj.DSmultipos = new Array();
				if (dsObj.p.DSorder == undefined) dsObj.p.DSorder = '';
				if (dsObj.DSsearch == undefined) dsObj.DSsearch = "";
				dsObj.DSresult = new Array();
				var countrow = 1;
				var subNodesRow = dsNode.childNodes;
				var slen = subNodesRow.length;
				for (var y=0; y < slen; y++) 
				{
					var row = subNodesRow[y];	
					if (row.nodeType != 1) continue;
					dsObj.DSresult[countrow] = new Array();
					var subNodesFields = row.childNodes;
					for (var z=0, zl=subNodesFields.length; z<zl; z++)
					{
						var field = subNodesFields[z];	
						if (field.nodeName == "#text") continue;
						if (field.hasChildNodes()) dsObj.DSresult[countrow][field.nodeName] = field.firstChild.data;		//Internet Explorer
						if (field.textContent != undefined) dsObj.DSresult[countrow][field.nodeName] = field.textContent;	//Firefox
					}
					if (dsObj.DSresult[countrow][dsObj.p.DSkey] != undefined) 
					{	
						dsObj.DSresult[countrow]["keyname"] = dsObj.p.DSkey;
						dsObj.DSresult[countrow]["keynamevalue"] = dsObj.DSresult[countrow][dsObj.p.DSkey];
					}
					countrow++;
				}
				if (this.function_exists(dsNode.nodeName+"Refresh"))
				{ 
					try { eval(dsNode.nodeName+"Refresh();");}
					catch (e) { SYSTEMEVENT.errorJAVASCRIPT(e.message, e.fileName, e.lineNumber); }
				}
			}
		}
		if (resize == true) Resize();
	}
}
var AJAX = new clsAJAX();