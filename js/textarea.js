/**
* Class TEXTAREA
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsTextArea()
{
}

clsTextArea.prototype =
{      
	initEditor : function(n, ronly) 
	{
		if ($(n).initWEB == undefined) $(n).initWEB = true;
		else return;
		tinyMCE.init({
		  // General options
		  mode : "exact",
		  theme : "advanced",
		  elements: $(n).id, 
		  plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist",
		  readonly : ronly,

		  // Theme options
		  theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		  theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		  theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		  theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
		  theme_advanced_toolbar_location : "top",
		  theme_advanced_toolbar_align : "left",
		  theme_advanced_statusbar_location : "bottom",
		  theme_advanced_resizing : true,

		  // Drop lists for link/image/media/template dialogs
		  template_external_list_url : "lists/template_list.js",
		  external_link_list_url : "lists/link_list.js",
		  external_image_list_url : "lists/image_list.js",
		  media_external_list_url : "lists/media_list.js",
		  extended_valid_elements : "header[class]",

		  setup : function(ed) 
		  {
		      ed.onChange.add(function(ed, l) 
				{
					 var textObj = $(n);
					 var dsObj = $(textObj.p.dsObj);
					 if (dsObj != undefined) DS.dschange(dsObj);
				});
				tinyMCE.onBeforeUnload.add(function() {
					 var textObj = $(n);
					 var dsObj = $(textObj.p.dsObj);
					 if (dsObj != undefined && dsObj.DSchange) TEXTAREA.updateTextArea(n);
				});
		  }
		}); 
	},

	getTextArea : function(n) 
	{
	    if ($(n + '_ifr') == undefined) return;
	    tinyMCE.get(n).setContent($(n).value);
	},

	updateTextArea: function(n) 
	{	
		var ed = tinyMCE.get(n);
		if (ed != undefined)
		{
		    var textObj = $(n);
			 try 
			 {
				  var content = ed.getContent();
				  if (textObj.p.dsObj != undefined) TEXTAREA.setDsValue(textObj, content);
				  else textObj.value = content;
			 }
			 catch (e) {}
		}  
	},

	w3c : function(input) 
	{		
		var allowed = '<strong><b><br><p>';
		var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
		commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
		input = input.replace(commentsAndPhpTags, '').replace(tags, function($0, $1){
           return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
      });
		input = input.replace(/<br>/g,'<br />'); 
		input = input.replace(/<b>/g,'<strong>'); 
		input = input.replace(/<\/b>/g,'</strong>'); 
		return input;
	},

	setDsValue : function(obj, value)
	{
		var dsObj = $(obj.p.dsObj);
		if (value != undefined) obj.value = value;
		var pos = (obj.row == undefined) ? dsObj.DSpos : obj.row;
		if (pos == -1) dsObj.DSresult[pos][obj.p.dsItem] = Array();
		dsObj.DSresult[pos][obj.p.dsItem] = (obj.p.w3c && obj.p.w3c=='true') ? TEXTAREA.w3c(obj.value) : obj.value;
		dsObj.DSposalter = pos;
		DS.dschange(dsObj);
	},

	getDsValue : function(id)
	{
		var textareaObj = $(id);
		var dsObj = $(textareaObj.p.dsObj);
		var valueDs = textareaObj.value;
		if (textareaObj.p.InitreadOnly == undefined) textareaObj.p.InitreadOnly = textareaObj.readOnly;
		if (dsObj != null)
		{
			if (textareaObj.p.InitreadOnly == false) textareaObj.readOnly = (dsObj.DSpos != 0) ? false : true;
			if (dsObj.DSresult.length == 0) 
			{
				textareaObj.value = '';
				return;
			}
			var row = (textareaObj.row == undefined) ? dsObj.DSpos : textareaObj.row;
			if (row < 0 && textareaObj.p.defaultvalue != null) TEXTAREA.setDsValue(textareaObj, textareaObj.p.defaultvalue);
			var valueDs = (dsObj.DSresult[row][textareaObj.p.dsItem] == undefined) ? "" : dsObj.DSresult[row][textareaObj.p.dsItem];
		}
		if (valueDs == "" && textareaObj.p.defaultvalue != null) valueDs = textareaObj.p.defaultvalue;
		if (textareaObj.p.format == null) textareaObj.value = valueDs;
		else FORMAT.format(textareaObj, valueDs);
		textareaObj.oldValue = valueDs;
	},

	checkDsValue : function(obj)
	{
		if (obj.oldValue != obj.value) DS.dschangeNoLive($(obj.p.dsObj));
	},

	refreshObj : function(id)
	{
		var textareaObj = $(id);
		textareaObj.oldValue = "";
		this.getDsValue(id);
	}
}

var TEXTAREA = new clsTextArea();