/**
* Class VIDEO
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsVideo()
{
}

clsVideo.prototype =
{
	getDsValue : function(id)
	{
		var videoObj = $(id);
		var src = videoObj.p.src;
		if (videoObj.p.dsObj != undefined)
		{
			var dsObj = $(videoObj.p.dsObj);
			if (dsObj.DSresult.length == 0) return
			var row = (videoObj.row == undefined) ? dsObj.DSpos : videoObj.row;
			src = (dsObj.DSresult[row][videoObj.p.dsItem] == undefined) ? "" : dsObj.DSresult[row][videoObj.p.dsItem];
		}
		videoObj.p.src = src;
		var extension = (src.indexOf('youtube') != -1) ? 'youtube' : src.slice(src.length-4, src.length);
		if (extension == '.wmv' || extension == '.asx' || extension == '.avi') this.initMediaPlayer(videoObj);
		else if (extension == 'youtube') this.initYouTube(videoObj);
		else if (extension == '.mpg' || extension == 'mpeg') this.initMpeg(videoObj);
		else if (extension == '.rmv' || extension == 'rmvb') this.initRealPlayer(videoObj);
		else if (extension == '.mov') this.initQuickTime(videoObj);
		else if (extension == '.swf') this.initFlash(videoObj);
		else if (extension == '.flv') this.initFlashPlayer(videoObj);
	},

	refreshObj : function(id)
	{
		this.getDsValue(id);
	},

	initFlashPlayer : function(videoObj)
	{
		var code = '<embed ';
		code += 'width="' + videoObj.p.width + '" ';
		code += 'height="' + videoObj.p.height + '" ';
		code += 'autostart="' + videoObj.p.autostart + '" ';
		code += 'showdigits="' + videoObj.p.showdigits + '" ';
		code += 'allowfullscreen="' + videoObj.p.allowfullscreen + '" ';
		code += 'allowsciptaccess="' + videoObj.p.allowsciptaccess + '" ';
		code += 'quality="' + videoObj.p.quality + '" ';
		code += 'name="' + videoObj.p.name + '" ';
		code += 'style="' + videoObj.p.style + '" ';
		code += 'src="' + videoObj.p.player + '" ';
 		code += 'flashvars="movieName=' + videoObj.p.src + '" ';
		code += 'type="application/x-shockwave-flash" />';
		videoObj.innerHTML = code;
		videoObj.init = "videoflash";
	},

	initFlash : function(videoObj)
	{
		var code = '<embed ';
		code += 'src="' + videoObj.p.src + '" ';
		code += 'wmode="' + videoObj.p.wmode + '" ';
		code += 'pluginspage="http://www.macromedia.com/go/getflashplayer" ';
	 	code += 'width="' + videoObj.p.width + '" ';
		code += 'height="' + videoObj.p.height + '" />';
		videoObj.innerHTML = code;
		videoObj.init = "flash";
	},

	initQuickTime : function(videoObj)
	{
		var code = '<OBJECT  CLASSID="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" ';
		code += 'WIDTH="' + videoObj.p.width + '" ';
		code += 'HEIGHT="' + videoObj.p.height + '" ';
		code += 'CODEBASE="http://www.apple.com/qtactivex/qtplugin.cab">';
		code += '<PARAM name="SRC" VALUE="' + videoObj.p.src + '">';
		code += '<PARAM name="AUTOPLAY" VALUE="' + videoObj.p.autostart + '">';
		code += '<PARAM name="CONTROLLER" VALUE="' + videoObj.p.controls + '">';
		code += '<PARAM name="KIOSKMODE" VALUE="true">';
		code += '<EMBED SRC="' + videoObj.p.src + '" ';
		code += 'WIDTH="' + videoObj.p.width + '" ';
		code += 'HEIGHT="' + videoObj.p.height + '" ';
		code += 'AUTOPLAY="' + videoObj.p.autostart + '" ';
		code += 'CONTROLLER="' + videoObj.p.controls + '" ';
		code += 'kioskmode="true"';
		code += 'PLUGINSPAGE="http://www.apple.com/quicktime/download/">';
		code += '</EMBED>';
		code += '</OBJECT>';
		videoObj.innerHTML = code;
		videoObj.init = "quicktime";
	},

	initRealPlayer : function(videoObj)
	{
		var controls = (videoObj.p.control == "true") ? "all" : "";
		var code = '<object ';
		code += 'width="' + videoObj.p.width + '" ';
		code += 'height="' + videoObj.p.height + '">';
		code += '<param name="src" value="' + videoObj.p.src + '">';
		code += '<param name="console" value="' + videoObj.p.console + '">';
		code += '<param name="controls" value="' + controls + '">';
		code += '<param name="autostart" value="' + videoObj.p.autostart + '">';
		code += '<param name="loop" value="' + videoObj.p.loop + '">';
		code += '<embed name="myMovie" src="' + videoObj.p.src + '" '; 
		code += 'width="' + videoObj.p.width + '" ';
		code += 'height="' + videoObj.p.height + '" ';
		code += 'autostart="' + videoObj.p.autostart + '" ';
		code += 'loop="' + videoObj.p.loop + '" ';
		code += 'nojava="' + videoObj.p.nojava+ '" ';
		code += 'console="' + videoObj.p.console + '" ';
		code += 'controls="' + controls + '">';
		code += '</embed>';
		code += '<noembed><a href="' + videoObj.p.src + '">Play first clip</a></noembed>';
		code += '</object>';
		videoObj.innerHTML = code;
		videoObj.init = "realplayer";
	},

	initMpeg : function(videoObj)
	{
		var code = '<embed ';
		code += 'src="' + videoObj.p.src + '" ';
		code += 'autostart="' + videoObj.p.autostart+ '" ';
		code += 'loop="' + videoObj.p.loop + '" ';
		code += 'controller="' + videoObj.p.controls + '" ';
	 	code += 'width="' + videoObj.p.width + '" ';
		code += 'height="' + videoObj.p.height + '" />';
		videoObj.innerHTML = code;
		videoObj.init = "mpeg";
	},

	initMediaPlayer : function(videoObj)
	{
		var code = '<object CLASSID="CLSID:6BF52A52-394A-11D3-B153-00C04F79FAA6" STANDBY="Loading Windows Media Player components..." TYPE="application/x-oleobject"';
		code += 'width="' + videoObj.p.width + '" ';
		code += 'height="' + videoObj.p.height + '">';
		code += '<param id="' + videoObj.id + '_src" name="src" value="' + videoObj.p.src + '"/>';
		code += '<param name="autostart" value="' + videoObj.p.autostart + '"/>';
		code += '<param name="ShowControls" value="' + videoObj.p.controls + '"/>';
		code += '<EMBED TYPE="application/x-mplayer2" SRC="' + videoObj.p.src + '" NAME="MediaPlayer" ';
		code += 'width="' + videoObj.p.width + '" ';
		code += 'height="' + videoObj.p.height + '" ';
		code += 'autostart="' + videoObj.p.autostart + '" ';
		code += 'ShowControls="' + videoObj.p.controls + '" ';
		code += '</EMBED>';
		code += '</object>';
		videoObj.innerHTML = code;
		videoObj.init = "mediaplayer";
	},

	initYouTube : function(videoObj)
	{
		var code = "";
		code += '<embed ';
		code += 'width="' + videoObj.p.width + '" ';
		code += 'height="' + videoObj.p.height + '" ';
		code += 'autostart="' + videoObj.p.autostart + '" ';
		code += 'showdigits="' + videoObj.p.showdigits + '" ';
		code += 'allowfullscreen="' + videoObj.p.allowfullscreen + '" ';
		code += 'allowsciptaccess="' + videoObj.p.allowsciptaccess + '" ';
		code += 'quality="' + videoObj.p.quality + '" ';
		code += 'name="' + videoObj.p.name + '" ';
		code += 'style="' + videoObj.p.style + '" ';
		code += 'src="' + videoObj.p.src + '" ';
		code += 'type="application/x-shockwave-flash" ';
		code += " />";
		videoObj.innerHTML = code;
		videoObj.init = "youtube";
	}
};

var VIDEO = new clsVideo();