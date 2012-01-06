/**
* Class TREE
* @author	Alyx Association <info@alyx.it>
* @version	1.0.1 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsTree()
{
	this.dragContainer = document.createElement('div');
	this.menuContainer = document.createElement('div');
	this.dragContainer.id = 'dragContainer';
	this.menuContainer.id = 'menuContainer';
	document.body.appendChild(this.dragContainer);		
	document.body.appendChild(this.menuContainer);	
}

clsTree.prototype =
{
	drawExpand : function(parent)
	{		
		var expand = document.createElement('span');
 		expand.className = (parent.nochild=="true") ? "tree_blank" : "tree_plus";
 		expand.onclick = (parent.nochild=="true") ? null : TREE.onExpandClick;
		expand.innerHTML = "&nbsp"; 
		parent.appendChild(expand);
		parent.expand = expand;
	},

	drawCheckBox : function(divTree, parent)
	{		
		var checkbox = document.createElement('li');
		checkbox.className = "tree_"+parent.chkstate;
		checkbox.onclick = this.onCheckClick;
		checkbox.innerHTML = "&nbsp"; 
		parent.appendChild(checkbox);
		parent.checkbox = checkbox;
	},

 	drawIcon : function(divTree, parent)
	{		
		var icon = document.createElement('span');
		icon.className = parent.iconname+'_close';
		icon.onmousedown = (parent.dragable=="true") ? TREE.onStartDragNode : null;
		icon.innerHTML = "&nbsp"; 
		parent.appendChild(icon);
		parent.icon = icon;
	},

	drawText : function(parent, name)
	{
		var span = document.createElement('span');
		span.innerHTML = name;
		span.className = (parent.link) ? "nodeLink" : "nodeText";
		span.onclick = this.onTextClick;
		parent.appendChild(span);
		parent.textNode = span;
	},

	addContainerChild : function(parent)
	{
		var container = document.createElement('span');
		container.style.display = "none";
		parent.appendChild(container);
		container.className = 'Nodecontainer';
		parent.container = container;
	},
	
	addNode : function(divTree, data, parent, i)
	{
		var node = document.createElement('li');
		node.className = 'TreeNode';
		node.setAttribute("id", divTree.getAttribute('id')+"_"+data['key']);
		node.key       = data['key'];
		node.nodetype  = data['nodetype'];
		node.parentkey = data[divTree.p.dsparentkey];
		node.name      = data[divTree.p.dsname];
		node.link      = data[divTree.p.dslink];
		node.iconname  = data[divTree.p.dsicon];
		node.order     = data[divTree.p.dsorder];
		node.nochild   = data[divTree.p.dsnochild];
		node.dragable  = data[divTree.p.dsdragable];
		node.chkstate  = data[divTree.p.dschkstate];		
		node.row			= i;
		node.loaded = (divTree.p.refresh!="tree") ? false : true;
		this.drawExpand(node);
		if (divTree.p.checkbox!="false") this.drawCheckBox(divTree, node);
		this.drawIcon(divTree, node);
		this.drawText(node, node.name);
		if (divTree.p.oncontextmenu=="true")
		{
			node.textNode.mousedown = this.onContextMenu;
			node.textNode.oncontextmenu = this.onContextMenu;
		}
		this.addContainerChild(node);
		parent.appendChild(node);
	},

	drawChildNode : function(divTree, data)
	{
		var value = new Array();
		var lastid;
		var id;
		var parent = null;
		var divTreeId = divTree.getAttribute('id');
		var root = 0;
		var length = data.length; 
		for(var i=1; i<length; i++)
		{		
			value['key'] = data[i][divTree.p.dskey];
			value['nodetype'] = data[i]['type'];
			value[divTree.p.dsparentkey] = data[i][divTree.p.dsparentkey];
			value[divTree.p.dsname] = data[i][divTree.p.dsname];
			value[divTree.p.dslink] = data[i][divTree.p.dslink];
			value[divTree.p.dsicon] = (data[i][divTree.p.dsicon]!=undefined && value[divTree.p.dsparentkey]!='') ? "tree_"+data[i][divTree.p.dsicon] : "tree_base"; 
			value[divTree.p.dsorder] = data[i][divTree.p.dsorder];
			value[divTree.p.dsnochild] = "false";
			if (data[i][divTree.p.dsnochild]!=undefined) value[divTree.p.dsnochild] = data[i][divTree.p.dsnochild];
			else if (data[i]['type']=="file" || data[i]['type']=="filelink") value[divTree.p.dsnochild] = "true";
			value[divTree.p.dsdragable] = (data[i][divTree.p.dsdragable]!=undefined) ? data[i][divTree.p.dsdragable] : divTree.p.dragable;
			value[divTree.p.dschkstate] = (data[i][divTree.p.dschkstate]!=undefined) ? data[i][divTree.p.dschkstate] : "unchecked";
			id = divTreeId+"_"+value[divTree.p.dsparentkey];
			if (lastid!=id) parent = $(id) ? $(id).container : divTree;
			TREE.addNode(divTree, value, parent, i);
			lastid=id;
		}	
	},

	level : function(node)
	{
		var i = 0;
		for (;;) 
		{
			node = node.parentNode;
			if (node == null) return -1;
			if (node.tagName == "DIV") return i;
			i++;
		}
	},

	foundTree : function(node)
	{
		for (;;) 
		{
			node = node.parentNode;
			if (node.tagName == "DIV") return node.id;
		}
	},

	onTextClick : function(e)
	{
		var divTree = $(TREE.foundTree(this));
		if (divTree.selectedNode) divTree.selectedNode.textNode.className = "nodeText";
		divTree.selectedNode = this.parentNode;
		divTree.selectedNode.textNode.className = "nodeSel"; 
		if (divTree.p.dsNav == true)
		{
			var dsObj = $(divTree.p.dsObj);
			dsObj.DSpre = dsObj.DSpos;
			dsObj.DSpos = divTree.selectedNode.row;
			eval(divTree.p.dsObj + "Move()");
		}
	},

	addMenuText : function(text, action, node)
	{
		var popup = document.createElement('a');		
		popup.innerHTML = text;
		popup.href = "#";
		popup.setAttribute("onclick", action + "(this.node);");
		popup.node = node;
		TREE.menuContainer.appendChild(popup);
	},

	onContextMenu : function(e)
	{
		if (!e) e = window.event;
// 		if (e.button != 2) return false;

		var divTree = $(TREE.foundTree(this));
		if (divTree.selectedNode) divTree.selectedNode.textNode.className="nodeText";
		divTree.selectedNode = this.parentNode;
		divTree.selectedNode.textNode.className = "nodeSel"; 
		TREE.menuContainer.innerHTML = "";
		var found = false;
		var length = divTree.p.menuname.length;
		for (var i = 0; i < length; i++)
		{
			if (divTree.p.menufilter[i] != undefined)
			{
				if (new RegExp(divTree.p.menufilter[i]).exec(this.parentNode.nodetype) != null)
				{
					found = true;
					TREE.addMenuText(divTree.p.menuname[i], divTree.p.menufunction[i], this.parentNode);
				}
			}
			else
			{
				found = true;
				TREE.addMenuText(divTree.p.menuname[i], divTree.p.menufunction[i], this.parentNode);
			}		
		}
		if (found)
		{
			TREE.menuContainer.style.top = e.clientY + "px";
			TREE.menuContainer.style.left = e.clientX + "px";
			TREE.menuContainer.style.visibility = "visible";
			TREE.menuContainer.style.zIndex = "999";	
			if ((e.clientX + TREE.menuContainer.offsetWidth) > SYSTEMBROWSER.winWidth()) TREE.menuContainer.style.left = SYSTEMBROWSER.winWidth() - TREE.menuContainer.offsetWidth + "px";
			if ((e.clientY + TREE.menuContainer.offsetHeight) > SYSTEMBROWSER.winHeight()) TREE.menuContainer.style.top = SYSTEMBROWSER.winHeight() - TREE.menuContainer.offsetHeight + "px";
		}
		SYSTEMEVENT.stopPropagation(e);
		return false;
	},

	onAddNodeClick : function(e)
	{
		var value = new Array();
		var node = $(this.objId);
		var divTree = $(TREE.foundTree(node));
		var parent = node.container;
		var dsObj = $(divTree.p.dsObj);
		var value = dsObj.DSresult[dsObj.DSpos];
		value[divTree.p.dsicon] = (value[divTree.p.dsicon]!=undefined) ? "tree_"+value[divTree.p.dsicon] : "tree_icon"; 
		TREE.addNode(divTree, value, parent);
	},

	onDelNodeClick : function(e)
	{
		var node = $(this.objId);
		var divTree = $(TREE.foundTree(node));
		var parentNode = node.parentNode;
		var string = 'data=delete&dsobjname='+divTree.p.dsObj;
		string += '&keyname='+encodeURIComponent(divTree.p.dsparentkey);
		string += '&keynamevalue='+encodeURIComponent(node.key);
		TREE.appendPost(divTree, string);
		parentNode.removeChild(node);
		if (divTree.p.checkbox=="tristate") TREE.checkParentState(divTree, parentNode);
		TREE.savePost(divTree);
	},
	
	loadChildNode : function(divTree, node)
	{
		var dsObj 	= $(divTree.p.dsObj);
		var string 	= 'data=load&dsobjname='+divTree.p.dsObj;
		string 	  += "&scope=onelevel&dswhere="+encodeURIComponent("`"+divTree.p.dsparentkey+"`='"+node.key+"'");
		string 	  += "&type="+encodeURIComponent(node.nodetype);
		string 	  += "&icon="+encodeURIComponent(node.iconname);
		string 	  += "&name="+encodeURIComponent(node.key);
		string 	  += "&link="+encodeURIComponent(node.link);
		string 	  += "&order="+encodeURIComponent(node.order);
		string 	  += "&nochild="+encodeURIComponent(node.nochild);
		string 	  += "&dragable="+encodeURIComponent(node.dragable);
		string 	  += "&chkstate="+encodeURIComponent(node.chkstate);
		AJAX.request('POST', dsObj.p.DSaction, string, true, true); 
		return true;
	},

    ExpandClick : function(divTree, node)
	{
		if (node.container.style.display=="none") 
		{
			if (!node.loaded) node.loaded = TREE.loadChildNode(divTree, node);
			node.expand.className = "tree_minus";
			node.icon.className   = node.icon.className.replace("_close","_open");
			node.container.style.display = "block";
		}
		else 
		{
			node.expand.className = "tree_plus";
			node.icon.className   = node.icon.className.replace("_open","_close");
			if (divTree.p.refresh == "always")	
			{
				node.container.innerHTML = "";
				node.loaded = false;
			}	
			node.container.style.display = "none";
		}
    },

   onExpandClick : function(e)
	{
		var node	= this.parentNode;
		var divTree = $(TREE.foundTree(node));
		TREE.ExpandClick(divTree, node);
	},
	
	 expand : function(divTree, bol) 
	 {
		var subNodes = divTree.parentNode.getElementsByTagName('li');
		var length = subNodes.length;
		for(var i=0; i<length; i++) 
		{
		  if (subNodes[i])
		  {
				if ((bol && subNodes[i].container.style.display=='none') ||  (!bol && subNodes[i].container.style.display!='none'))
				  TREE.ExpandClick(divTree, subNodes[i]);
		  }
		}
	 },

	savePost : function(divTree) 
	{	
		var dsObj = $(divTree.p.dsObj); 
		for (var i=0; i<divTree.codePost.length; i++) AJAX.request('POST', dsObj.p.DSaction, divTree.codePost[i], true, false);
		divTree.codePost = new Array();
	},

	appendPost : function(divTree, post) 
	{
		divTree.codePost[divTree.codePost.length] = post;
	},

	changeCheck : function(divTree, node, state) 
	{
		if (node.id=='' && node.parentNode.chkstate!=state) 
		{
			node.className = "tree_"+state;
			node.parentNode.chkstate = state;
			if (divTree.p.dschkstate!=undefined) 
			{
				var string = 'data=update&dsobjname='+divTree.p.dsObj;
				string += '&keyname='+encodeURIComponent(divTree.p.dsparentkey);
				string += '&keynamevalue='+encodeURIComponent(node.parentNode.key);
				string += '&'+divTree.p.dschkstate+'='+encodeURIComponent(state);
				TREE.appendPost(divTree, string);
			}
		}
	},

	checkParentState : function(divTree, node) 
   	{
		var semichecked=0;
		while (node!=divTree) 
		{
			var checkbox = node.parentNode.getElementsByTagName('li')[0];
			if (semichecked==0) 
			{
				var checked = 0;
				var subNodes = node.childNodes;
				if (subNodes.length==0) checked=-1;
				var length = subNodes.length;
				for(var i=0; i<length; i++) 
				{
					var chk = subNodes[i].getElementsByTagName('li');
					var state = chk[0].parentNode.chkstate;
					if (state=="semichecked") 
					{
						semichecked++;
						i=subNodes.length;
					}	
					if (state=="checked") checked++;
				}
				if (semichecked>0 || (checked<subNodes.length && checked>0)) semichecked++;
			}	
			if (semichecked>0) TREE.changeCheck(divTree, checkbox, "semichecked");
			else TREE.changeCheck(divTree, checkbox, (checked==subNodes.length) ? "checked" : "unchecked");
			node = node.parentNode.parentNode;
		}
    },
	
	onCheckClick : function(e)
	{
		var divTree = $(TREE.foundTree(this));	
		var type  = divTree.p.checkbox;
		var state = this.parentNode.chkstate;
		var changestate = (state=="checked") ? "unchecked" : "checked";
		TREE.changeCheck(divTree, this, changestate);
		if (type=="tristate") 
		{
			var subNodes = this.parentNode.lastChild.getElementsByTagName('li');
			var length = subNodes.length;
			for(var i=0; i<length; i++) TREE.changeCheck(divTree, subNodes[i], changestate);
			TREE.checkParentState(divTree, this.parentNode.parentNode);
		}
		 if (divTree.p.dschkstate!=undefined) TREE.savePost(divTree);
	},

   actionMove : function(divTree, nodeS, nodeD) 
   {
       if (nodeS.key != nodeD.key) 
       {
			var string = 'data=update';
			string += '&dsobjname='+divTree.p.dsObj;
			string += '&keyname='+encodeURIComponent(divTree.p.dskey);
			string += '&keynamevalue='+encodeURIComponent(nodeS.key);
			string += '&'+divTree.p.dskey+'='+encodeURIComponent(nodeS.key);
			string += '&'+divTree.p.dsparentkey+'='+encodeURIComponent(nodeD.key);
 			TREE.appendPost(divTree, string);
			if (divTree.p.dsorder!=undefined) 
			{
				var subNodes = nodeD.container.childNodes;
				var length = subNodes.length;
				for(var i=0; i<length; i++) 
				{
					subNodes[i].order = i;
					var string = 'data=update';
					string += '&dsobjname='+divTree.p.dsObj;
					string += '&keyname='+encodeURIComponent(divTree.p.dskey);
					string += '&keynamevalue='+encodeURIComponent(subNodes[i].key);
					string += '&'+divTree.p.dskey+'='+encodeURIComponent(subNodes[i].key);
					string += '&'+divTree.p.dsorder+'='+encodeURIComponent(i);
		 			TREE.appendPost(divTree, string);
				}
	       }
       }
    },

	onStartDragNode : function(e)
	{ 
		TREE.dragContainer.innerHTML=""; 
		TREE.sourceNode = this.parentNode;
		TREE.destinationNode = null;
		var node = TREE.sourceNode.cloneNode(true);
		TREE.dragContainer.appendChild(node);
		var divTree = $(TREE.foundTree(this));	
  		divTree.appendChild(TREE.dragContainer);
		TREE.dragContainer.style.display = 'block';
		TREE.onMoveNode(e);

		SYSTEMEVENT.addEventListener(document, "mousemove", TREE.onMoveNode);
		SYSTEMEVENT.addEventListener(document, "mouseup", TREE.onDropNode);

 		SYSTEMEVENT.stopPropagation(e);
 		SYSTEMEVENT.preventDefault(e);
	},

	onRelaseContainer : function(e)
	{
		SYSTEMEVENT.removeEventListener(document, "mousemove", TREE.onMoveNode);
		SYSTEMEVENT.removeEventListener(document, "mouseup", TREE.onDropNode);
  		TREE.destinationNode = (e.srcElement == undefined) ? e.target : e.srcElement;
		TREE.dragContainer.style.display = "none";
		TREE.dragContainer.innerHTML = ""; 
  		SYSTEMEVENT.stopPropagation(e); 
 	},
 	
	onDropNode : function(e)
	{
		TREE.onRelaseContainer(e);
		if (TREE.destinationNode.parentNode==TREE.sourceNode || TREE.destinationNode.parentNode.nochild=="true") return;
		TREE.destinationNode = TREE.destinationNode.parentNode;
		var divTree = $(TREE.foundTree(TREE.destinationNode));	
 		var subNodes = TREE.sourceNode.getElementsByTagName('li');
		var length = subNodes.length;
 		for(var i=0; i<length; i++) 
		{
 			if (subNodes[i]==TREE.destinationNode) return;
 		}
		if (TREE.destinationNode.nochild != "true" || divTree.p.dsorder!=undefined) 
		{
			if (TREE.destinationNode.nochild == "true") TREE.destinationNode = TREE.destinationNode.parentNode.parentNode;
			else 
			{
			    if (TREE.destinationNode.container == undefined) return;
			    if (TREE.destinationNode.container.style.display=="none") TREE.destinationNode.expand.onclick();
			}
			var parentSource = TREE.sourceNode.parentNode;
			if (TREE.destinationNode.container.firstChild != undefined)
			{
			      if (TREE.sourceNode.key == TREE.destinationNode.container.firstChild.parentkey) return;
			      TREE.destinationNode.container.insertBefore(TREE.sourceNode, TREE.destinationNode.container.firstChild);
			}
			else
			{
			      TREE.destinationNode.container.appendChild(TREE.sourceNode);
			}
			TREE.actionMove(divTree, TREE.sourceNode, TREE.destinationNode);
			if (divTree.p.checkbox=="tristate") 
			{
				TREE.checkParentState(divTree, parentSource);
				TREE.checkParentState(divTree, TREE.destinationNode.container);
			}
			TREE.savePost(divTree);
			TREE.destinationNode.container.style.display = "block";
		}  
	},

	onMoveNode : function(e)
	{
		if (!e) e = window.event;
		var x = e.clientX/1 + document.body.scrollLeft+5;
		var y = e.clientY/1 + document.documentElement.scrollTop+5;	
		TREE.dragContainer.style.left = x + "px";
		TREE.dragContainer.style.top  = y + "px";
		SYSTEMEVENT.stopPropagation(e);
 		SYSTEMEVENT.preventDefault(e);
	},

	moveNode : function(divTree, up)
	{
		var nodeS = divTree.selectedNode;
		if (!nodeS) alert("Selezionare un nodo");
		else if (divTree.p.dsorder!=undefined) 
		{
			 var nodeD = (up) ? nodeS.previousSibling : nodeS.nextSibling;
			 if (nodeD && nodeD.key!=undefined)
			 {
				  var orderD = nodeD.order;
				  var orderS = nodeS.order;
				  var string = 'data=update';
				  string += '&dsobjname='+divTree.p.dsObj;
				  string += '&keyname='+encodeURIComponent(divTree.p.dskey);

				  var str = '&keynamevalue='+encodeURIComponent(nodeS.key);
				  str += '&'+divTree.p.dsorder+'='+encodeURIComponent(orderD);
				  TREE.appendPost(divTree, string + str);

				  str = '&keynamevalue='+encodeURIComponent(nodeD.key);
				  str += '&'+divTree.p.dsorder+'='+encodeURIComponent(orderS);
				  TREE.appendPost(divTree, string + str);
				  
				  nodeD.order = orderS;
				  nodeS.order = orderD;
				  TREE.savePost(divTree);
				  if (up) nodeS.parentNode.insertBefore(nodeS, nodeD);
				  else nodeS.parentNode.insertBefore(nodeD, nodeS);
 			 }
		}
	},

	getDsValue : function(id)
	{
		var divTree = $(id); 
		var dsObj = $(divTree.p.dsObj);
		if (divTree.innerHTML=="") 
		{
	    	document.documentElement.onclick = function() {TREE.menuContainer.style.visibility = "hidden";};	
			if (divTree.p.refresh==undefined) divTree.p.refresh = "tree";
			if (divTree.p.checkbox==undefined) divTree.p.checkbox = "false";
			if (divTree.p.dragable==undefined) divTree.p.dragable = "false";
			divTree.p.dskey = (dsObj.p.DSkey!=undefined) ? dsObj.p.DSkey : "key";
			divTree.p.dsparentkey = (dsObj.p.DSparentkey!=undefined) ? dsObj.p.DSparentkey : "parentkey";
			divTree.p.dsname = (dsObj.p.DSname!=undefined) ? dsObj.p.DSname : "name";
			divTree.p.dsicon = (divTree.p.dsicon!=undefined) ? divTree.p.dsicon : "type";
			if (divTree.p.dsicon==undefined) divTree.p.dsicon = "type";
			if (divTree.p.dsnochild==undefined) divTree.p.dsnochild = "nochild";
			if (divTree.p.dsdragable==undefined) divTree.p.dsdragable = "dragable";
			if (divTree.p.dschkstate==undefined) divTree.p.dschkstate = "chkstate";
			if (divTree.p.oncontextmenu==undefined) divTree.p.oncontextmenu = "false";
			divTree.p.selectedNode = false;
			divTree.codePost = new Array();
		}
		this.drawChildNode(divTree, dsObj.DSresult); 
	},

	refreshObj : function(id)
	{
		var divTree = $(id);
		if (divTree.p.refresh == "tree") divTree.innerHTML = "";
		this.getDsValue(id);
	}
}
var TREE = new clsTree();