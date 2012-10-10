/**
* Class ANIMATE
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsAnimate()
{
	this.fps = 25;
	this.time = 1000 / this.fps;
	this.timeline = Array();
	this.bumpline = Array();
}

clsAnimate.prototype =
{
	setfps : function(value)
	{
		this.fps = parseInt(value);
		this.time = 1000 / this.fps;
	},

	animate : function(id, property, duration, fnz, algorithm)
	{
		var obj = id;
		if (id.id == undefined) obj = $(id);
		else id = id.id;
		this.timeline[id] = {"obj":obj, "prop":property, "alg":algorithm, "duration":parseInt(duration), "fnz":fnz};
		this.timeline[id]["start"] = new Date().getTime();
		this.timeline[id]["step"] =  this.timeline[id]["duration"]/this.time;
		property = this.timeline[id]["prop"].split(";");
		var p = Array();
		var length = property.length;
		for (var i = 0; i < length; i++)
		{
			var prop = property[i].split(":");
			var val =  this.getProp(this.timeline[id]["obj"], prop[0]);
			if (val == "auto") val = 0;
			this.setType(p, prop, val);		
		}
		this.timeline[id]["prop"] = p; 
		setTimeout("ANIMATE.step('"+id+"');", (this.time));
	},

	collision : function(id, property, range, event)
	{
		var obj = id;
		if (id.id == undefined) obj = $(id);
		else id = id.id;
		if (this.bumpline[id] == undefined)
		{
			this.bumpline[id] = {"obj":obj, "prop":property, "range":parseInt(range)};
			property = this.bumpline[id]["prop"].split(";");
			var p = Array();
			var length = property.length;
			for (var i = 0; i < length; i++)
			{
				var prop = property[i].split(":");
				var val =  this.getProp(this.bumpline[id]["obj"], prop[0]);
				this.setType(p, prop, val);	
				if (typeof p[prop[0]][0] == "object")
				{
					p[prop[0]][3] = Array(0,0,0);
					for (var ii = 0; ii < 3; ii++) p[prop[0]][3][ii] = (p[prop[0]][1][ii] - p[prop[0]][0][ii]) / 100;
				}
				else p[prop[0]][3] = (p[prop[0]][1] - p[prop[0]][0]) / 100;
			}
			this.bumpline[id]["prop"] = p; 
			this.bumpline[id]["x"] = SYSTEMBROWSER.getOffSetX(obj) + (obj.offsetWidth / 2);
			this.bumpline[id]["y"] = SYSTEMBROWSER.getOffSetY(obj) + (obj.offsetHeight / 2);
		}
		else
		{
			if (!event) event = window.event;
			var x = (event.clientX > this.bumpline[id]["x"]) ? event.clientX - this.bumpline[id]["x"] : this.bumpline[id]["x"] - event.clientX;
			var y = (event.clientY > this.bumpline[id]["y"]) ? event.clientY - this.bumpline[id]["y"] : this.bumpline[id]["y"] - event.clientY;
			var r = (Math.sqrt(x*x + y*y))/2;
			this.bumpline[id]["x"] = SYSTEMBROWSER.getOffSetX(obj) + (obj.offsetWidth / 2);
			this.bumpline[id]["y"] = SYSTEMBROWSER.getOffSetY(obj) + (obj.offsetHeight / 2);
			this.alg_collision(id, r, this.bumpline[id]["range"]);
		}
	},

	setType : function(p, prop, val)
	{
		var text = "";
		if (prop[1].match(/#[0-9a-fA-F]{1,6}/)) //Hex
		{
			val = val[1].match(/#[0-9a-fA-F]{1,6}/) ? this.html2rgb(val[1]) : Array(0,0,0);
			prop[1] = this.html2rgb(prop[1]);
		}
		else if (prop[1].match(/[0-9]{1}/)) //Number
		{
			text = (val) ? val.replace(/[0-9.]/g,"") : 0;
			val = parseFloat(val);
			prop[1] = parseFloat(prop[1]);
		}
		p[prop[0]] = Array(val, prop[1], text);
	},

	step : function(id)
	{
		var elapsed = (this.timeline[id]["start"] + this.timeline[id]["duration"]) - new Date().getTime();
		if (elapsed-this.time > 0)
		{
			this.timeline[id]["step"] = parseInt(elapsed/this.time);
			eval("ANIMATE.alg_"+this.timeline[id]["alg"]+"('"+id+"');");
			setTimeout("ANIMATE.step('"+id+"');", (this.time));
		}
		else
		{
			this.setEnd(id);
			if (typeof this.timeline[id]["fnz"] == "function" ) this.timeline[id]["fnz"]();
		}
	},

	setEnd :function(id)
	{
		var prop = this.timeline[id]["prop"];
		for (var p in prop) 
		if (prop.hasOwnProperty(p))
		{
			if (typeof prop[p][0] == "object")
			{
				this.timeline[id]["obj"].style[p]  = this.rgb2html(prop[p][1][0], prop[p][1][1], prop[p][1][2]) + prop[p][2];
			}
			else
			{	
				this.timeline[id]["obj"].style[p] = prop[p][1] + prop[p][2];
				if(p == "opacity") this.timeline[id]["obj"].style["filter"] = "alpha(opacity=" + this.timeline[id]["obj"].style[p]*100 + ")";
			}
		}
	},

	getProp : function(obj, cssprop)
	{
		var out = "";
		if (obj.currentStyle) out = obj.currentStyle[cssprop];
		else if (document.defaultView && document.defaultView.getComputedStyle)
		out =  document.defaultView.getComputedStyle(obj, "")[cssprop];
		else out =  obj.style[cssprop];
		if (out == "auto")
		{
			if (cssprop == "height") return obj.height+"px";
			else if (cssprop == "width") return obj.width+"px";
		}
		else return out;
	},

	rgb2html : function(R, G, B)
	{
		R = parseInt(R); 
		G = parseInt(G);
		B = parseInt(B);

		R = this.dechex(R < 0 ? 0 : (R > 255 ? 255 : R));
		G = this.dechex(G < 0 ? 0 : (G > 255 ? 255 : G));
		B = this.dechex(B < 0 ? 0 : (B > 255 ? 255 : B));

		var color = (R.length < 2 ? '0' : '') + R;
		color += (G.length < 2 ? '0' : '') + G;
		color += (B.length < 2 ? '0' : '') + B;
		return '#' + color;
	},

	html2rgb : function(color)
	{
		var rgb = Array();
		color = color.match(/[0-9a-fA-F]{1,6}/)[0];
		rgb[0] = this.hexdec(color.substr(0, 2));
		rgb[1] = this.hexdec(color.substr(2, 2));
		rgb[2] = this.hexdec(color.substr(4, 2));
		return rgb;
	}, 

	hexdec : function(hex_string) 
	{
		 hex_string = (hex_string+'').replace(/[^a-f0-9]/gi, '');
		 return parseInt(hex_string, 16);
	},

	dechex : function (number)
	{
		if (number < 0)
		{
			number = 0xFFFFFFFF + number + 1;	
		}
		return parseInt(number, 10).toString(16);
	},

	ghost : function(id, duration)
	{
		duration = (duration==undefined) ? 1000 : duration;
		$(id).style.display = "block";
		ANIMATE.animate(id, 'opacity:0.8', 500, function(){ANIMATE.animate(id, 'opacity:0.8', duration, function(){ANIMATE.animate(id, 'opacity:0;display:none', 500, '', 'default');}, 'default');}, 'default');
	},

	scroll : function(id)
	{
		var left = ANIMATE.getProp($(id), "left");
		if(/%/.test(left))
		{
			left = parseInt(left);
			ANIMATE.animate(id, "left:"+(left-4)+"%", "100", function(){
				ANIMATE.animate(id, "left:"+(left+6)+"%", "100", function(){
					ANIMATE.animate(id, "left:"+(left-4)+"%", "200", function(){
						ANIMATE.animate(id, "left:"+(left)+"%", "300", "", "default");
					}, "default");
				}, "default");
			}, "default");
		} 
		else
		{
			left = parseInt(left);
			ANIMATE.animate(id, "left:"+(left-100)+"px", "100", function(){
				ANIMATE.animate(id, "left:"+(left+150)+"px", "100", function(){
					ANIMATE.animate(id, "left:"+(left-100)+"px", "200", function(){
						ANIMATE.animate(id, "left:"+(left)+"px", "300", "", "default");
					}, "default");
				}, "default");
			}, "default");
		}
	},

	/******************  algorithm ******************/
	alg_collision : function(id, r, range)
	{
		var prop = this.bumpline[id]["prop"];
		for (var p in prop) 
		if (prop.hasOwnProperty(p))
		{
			var per = 100 - ((r * 100) / range);
			if (per > 80) per = 100;
			else if (per < 0) per = 0;
			if (typeof prop[p][0] == "object")
			{
				var v = Array(0,0,0);
				for (var i = 0; i < 3; i++) v[i] = prop[p][0][i] +  prop[p][3][i] * per;
				this.bumpline[id]["obj"].style[p] = this.rgb2html(v[0], v[1], v[2]) + prop[p][2];
			}
			else
			{	
				var v = prop[p][0] +  prop[p][3] * per;
				this.bumpline[id]["obj"].style[p] = v + prop[p][2];
				if(p == "opacity") this.bumpline[id]["obj"].style["filter"] = "alpha(opacity=" + this.bumpline[id]["obj"].style[p]*100 + ")";
			}
		}
	},

	alg_default : function(id)
	{
		var prop = this.timeline[id]["prop"];
		for (var p in prop) 
		if (prop.hasOwnProperty(p))
		{
			if (typeof prop[p][0] == "object")
			{
				for (var i = 0; i < 3; i++) prop[p][0][i] = prop[p][0][i] - ((prop[p][0][i]-prop[p][1][i])/this.timeline[id]["step"]);
				this.timeline[id]["obj"].style[p]  = this.rgb2html(prop[p][0][0], prop[p][0][1], prop[p][0][2]) + prop[p][2];
			}
			else
			{	
				var pre = prop[p][0];
				prop[p][0] = prop[p][0] - ((prop[p][0]-prop[p][1])/this.timeline[id]["step"]);
				if (isNaN(prop[p][0])) prop[p][0] = pre;
				this.timeline[id]["obj"].style[p] = prop[p][0] + prop[p][2];
				if(p == "opacity") this.timeline[id]["obj"].style["filter"] = "alpha(opacity=" + this.timeline[id]["obj"].style[p]*100 + ")";
			}
		}
	},

	alg_sin : function(id)
	{
		var prop = this.timeline[id]["prop"];
		for (var p in prop) 
		if (prop.hasOwnProperty(p))
		{
			if (typeof prop[p][0] == "object")
			{
				for (var i = 0; i < 3; i++) 
				{
					var delta = - ((prop[p][0][i]-prop[p][1][i])/this.timeline[id]["step"])
					prop[p][0][i] = prop[p][0][i] + delta*(Math.sin(delta));
				}
				this.timeline[id]["obj"].style[p]  = this.rgb2html(prop[p][0][0], prop[p][0][1], prop[p][0][2]) + prop[p][2];
			}
			else
			{	
				var pre = prop[p][0];
				var delta = - ((prop[p][0]-prop[p][1])/this.timeline[id]["step"]);
				prop[p][0] = prop[p][0] + delta*(Math.sin(delta));
				if (isNaN(prop[p][0])) prop[p][0] = pre;
				this.timeline[id]["obj"].style[p] = prop[p][0] + prop[p][2];
				if(p == "opacity") this.timeline[id]["obj"].style["filter"] = "alpha(opacity=" + this.timeline[id]["obj"].style[p]*100 + ")";
			}
		}
	},

	alg_cos : function(id)
	{
		var prop = this.timeline[id]["prop"];
		for (var p in prop) 
		if (prop.hasOwnProperty(p))
		{
			if (typeof prop[p][0] == "object")
			{
				for (var i = 0; i < 3; i++) 
				{
					var delta = - ((prop[p][0][i]-prop[p][1][i])/this.timeline[id]["step"])
					prop[p][0][i] = prop[p][0][i] + delta*0.5+(Math.cos(delta));
				}
				this.timeline[id]["obj"].style[p]  = this.rgb2html(prop[p][0][0], prop[p][0][1], prop[p][0][2]) + prop[p][2];
			}
			else
			{	
				var pre = prop[p][0];
				var delta = - ((prop[p][0]-prop[p][1])/this.timeline[id]["step"]);
				prop[p][0] = prop[p][0] + delta*0.5+(Math.cos(delta));
				if (isNaN(prop[p][0])) prop[p][0] = pre;
				this.timeline[id]["obj"].style[p] = prop[p][0] + prop[p][2];
				if(p == "opacity") this.timeline[id]["obj"].style["filter"] = "alpha(opacity=" + this.timeline[id]["obj"].style[p]*100 + ")";
			}
		}
	}

}

var ANIMATE = new clsAnimate();
