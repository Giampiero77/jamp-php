/**
* Class LIGHTBOX
* @author	Alyx Association <info@alyx.it>
* @version	1.0.2 stable
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
* The class is derived from the LIGHTBOX class LyteBox v3.22
* Author: Markus F. Hay
* Website: http://www.dolem.com/lytebox
*/

function clsLightBox() 
{
}

clsLightBox.prototype = 
{
	isIE : function()
	{
		return /MSIE/.test(navigator.userAgent);
	},

	getVersion : function()
	{
		if( this.isIE() )
		{
			return Number(navigator.userAgent.match(/MSIE ([0-9.]+)/)[1]);
		}
		else if( this.isSafari() || this.isChrome())
		{
			return Number(navigator.userAgent.match(/[0-9.]+$/));
		}
		else if( this.isFirefox() )
		{
			return parseInt(navigator.userAgent.match(/Firefox\/([0-9.]+)/)[1]);
		}
		else if(this.isKHTML() )
		{
			return parseInt(navigator.userAgent.match(/KHTML\/([0-9.]+)/)[1]);
		}
		else if( this.isGecko() )
		{
			var n = navigator.userAgent.match(/rv:([0-9.]+)/)[1];
			var ar = n.split(".");
			var s = ar[0] + ".";
			var length = ar.length;
			for(var i = 1; i < length; ++i)
			{
			s += ("0" + ar[i]).match(/.{2}$/)[0];
			}	
			return Number(s);
		}
		else if( this.isOpera() )
		{
			return Number(navigator.userAgent.match(/Opera.([0-9.]+)/)[1]);
		}
		else
		{
			return null;
		}
	},

	initialize : function() 
	{		
		if(this.resizeSpeed > 10) this.resizeSpeed = 10;
		if(this.resizeSpeed < 1) resizeSpeed = 1;
		this.resizeDuration = (11 - this.resizeSpeed) * 0.15;
		this.resizeWTimerArray		= new Array();
		this.resizeWTimerCount		= 0;
		this.resizeHTimerArray		= new Array();
		this.resizeHTimerCount		= 0;
		this.showContentTimerArray	= new Array();
		this.showContentTimerCount	= 0;
		this.overlayTimerArray		= new Array();
		this.overlayTimerCount		= 0;
		this.imageTimerArray		= new Array();
		this.imageTimerCount		= 0;
		this.timerIDArray			= new Array();
		this.timerIDCount			= 0;
		this.slideshowIDArray		= new Array();
		this.slideshowIDCount		= 0;
		this.imageArray	 = new Array();
		this.activeImage = null;
		this.slideArray	 = new Array();
		this.activeSlide = null;
		this.frameArray	 = new Array();
		this.activeFrame = null;
		this.checkFrame();
		this.isSlideshow = false;
		this.isLightframe = false;
		this.ie = (this.isIE());
		this.ie7 = (this.isIE() && this.getVersion()>6);
		var objBody = this.doc.getElementsByTagName("body").item(0);	
		if (this.doc.getElementById('lbOverlay')) {
			objBody.removeChild(this.doc.getElementById("lbOverlay"));
			objBody.removeChild(this.doc.getElementById("lbMain"));
		}
		var objOverlay = this.doc.createElement("div");
		objOverlay.setAttribute('id','lbOverlay');
		objOverlay.className = 'lbOverlay_'+this.theme;
		if ((this.ie && !this.ie7) || (this.ie7 && this.doc.compatMode == 'BackCompat')) objOverlay.style.position = 'absolute';
		objOverlay.style.display = 'none';
		objBody.appendChild(objOverlay);
		var objLIGHTBOX = this.doc.createElement("div");
		objLIGHTBOX.setAttribute('id','lbMain');
		objLIGHTBOX.style.display = 'none';
		objBody.appendChild(objLIGHTBOX);
		var objOuterContainer = this.doc.createElement("div");
		objOuterContainer.setAttribute('id','lbOuterContainer');
		objOuterContainer.className = 'lbOuterContainer_'+this.theme;
		objLIGHTBOX.appendChild(objOuterContainer);
		var objIframeContainer = this.doc.createElement("div");
		objIframeContainer.setAttribute('id','lbIframeContainer');
		objIframeContainer.style.display = 'none';
		objOuterContainer.appendChild(objIframeContainer);
		var objIframe = this.doc.createElement("iframe");
		objIframe.setAttribute('id','lbIframe');
		objIframe.setAttribute('name','lbIframe');
		objIframe.setAttribute('frameborder',0);
		objIframe.style.display = 'none';
		objIframeContainer.appendChild(objIframe);
		var objImageContainer = this.doc.createElement("div");
		objImageContainer.setAttribute('id','lbImageContainer');
		objOuterContainer.appendChild(objImageContainer);
		var objLIGHTBOXImage = this.doc.createElement("img");
		objLIGHTBOXImage.setAttribute('id','lbImage');
		objImageContainer.appendChild(objLIGHTBOXImage);
		var objLoading = this.doc.createElement("div");
		objLoading.setAttribute('id','lbLoading');
		objOuterContainer.appendChild(objLoading);
		var objDetailsContainer = this.doc.createElement("div");
		objDetailsContainer.setAttribute('id','lbDetailsContainer');
		objDetailsContainer.className = 'lbDetailsContainer_'+this.theme;
		objLIGHTBOX.appendChild(objDetailsContainer);
		var objDetailsData =this.doc.createElement("div");
		objDetailsData.setAttribute('id','lbDetailsData');
		objDetailsData.className = 'lbDetailsData_'+this.theme;
		objDetailsContainer.appendChild(objDetailsData);
		var objDetails = this.doc.createElement("div");
		objDetails.setAttribute('id','lbDetails');
		objDetailsData.appendChild(objDetails);
		var objCaption = this.doc.createElement("span");
		objCaption.setAttribute('id','lbCaption');
		objDetails.appendChild(objCaption);
		var objHoverNav = this.doc.createElement("div");
		objHoverNav.setAttribute('id','lbHoverNav');
		objImageContainer.appendChild(objHoverNav);
		var objBottomNav = this.doc.createElement("div");
		objBottomNav.setAttribute('id','lbBottomNav');
		objDetailsData.appendChild(objBottomNav);
		var objPrev = this.doc.createElement("a");
		objPrev.setAttribute('id','lbPrev');
		objPrev.className = 'lbPrev_'+this.theme;
		objPrev.setAttribute('href','#');
		objHoverNav.appendChild(objPrev);
		var objNext = this.doc.createElement("a");
		objNext.setAttribute('id','lbNext');
		objNext.className = 'lbNext_'+this.theme;
		objNext.setAttribute('href','#');
		objHoverNav.appendChild(objNext);
		var objNumberDisplay = this.doc.createElement("span");
		objNumberDisplay.setAttribute('id','lbNumberDisplay');
		objDetails.appendChild(objNumberDisplay);
		var objNavDisplay = this.doc.createElement("span");
		objNavDisplay.setAttribute('id','lbNavDisplay');
		objNavDisplay.style.display = 'none';
		objDetails.appendChild(objNavDisplay);
		var objClose = this.doc.createElement("a");
		objClose.setAttribute('id','lbClose');
		objClose.className = 'lbClose_'+this.theme;
		objClose.setAttribute('href','#');
		objBottomNav.appendChild(objClose);
		var objPause = this.doc.createElement("a");
		objPause.setAttribute('id','lbPause');
		objPause.className = 'lbPause_'+this.theme;
		objPause.setAttribute('href','#');
		objPause.style.display = 'none';
		objBottomNav.appendChild(objPause);
		var objPlay = this.doc.createElement("a");
		objPlay.setAttribute('id','lbPlay');
		objPlay.className = 'lbPlay_'+this.theme;
		objPlay.setAttribute('href','#');
		objPlay.style.display = 'none';
		objBottomNav.appendChild(objPlay);
	},

	updateLightboxItems : function(id) 
	{
		var objLIGHTBOX = $(id);
 		var anchors = objLIGHTBOX.getElementsByTagName('a');
		var length = anchors.length;
 		for (var i = 0; i < length; i++) 
 		{
 			var anchor = anchors[i];
 			var relAttribute = String(anchor.getAttribute('rel'));
 			if (anchor.getAttribute('href')) 
			{
 				if (relAttribute.toLowerCase().match('lightbox')) {
 					anchor.onclick = function () { LIGHTBOX.start(objLIGHTBOX, this, false, false); return false; }
 				} else if (relAttribute.toLowerCase().match('lightshow')) {
 					anchor.onclick = function () { LIGHTBOX.start(objLIGHTBOX, this, true, false); return false; }
 				} else if (relAttribute.toLowerCase().match('lightframe')) {
 					anchor.onclick = function () { LIGHTBOX.start(objLIGHTBOX, this, false, true); return false; }
 				}
 			}
 		}
	},

	start : function(objLIGHTBOX, imageLink, doSlide, doFrame) 
	{
		this.modal			= (objLIGHTBOX.p.modal!=true) ? false : true;
		// themes: grey, red, green, blue, gold
		this.theme				= (objLIGHTBOX.p.theme==undefined) ? 'grey' : objLIGHTBOX.p.theme; 
		// controls whether or not Flash objects should be hidden
		this.hideFlash			= (objLIGHTBOX.p.hideflash=="false") ? false : true;
		// controls whether to show the outer grey (or theme) border
		this.outerBorder		= (objLIGHTBOX.p.outerborder=="false") ? false : true; 
		// controls the speed of the image resizing (1=slowest and 10=fastest)
		this.resizeSpeed		= (objLIGHTBOX.p.resizespeed==undefined) ? 8 : objLIGHTBOX.resizespeed;
		// higher opacity = darker overlay, lower opacity = lighter overlay
		this.maxOpacity			= (objLIGHTBOX.p.maxopacity==undefined) ? 80 : objLIGHTBOX.maxopacity;
		// 1 = "Prev/Next" buttons on top left and left (default), 2 = "<< prev | next >>" links next to image number
		this.navType			= (objLIGHTBOX.p.navtype==undefined) ? 1 : objLIGHTBOX.navtype;
		// controls whether or not images should be resized if larger than the browser window dimensions
		this.autoResize			= (objLIGHTBOX.p.autoresize=="false") ? false : true; 
		// controls whether or not "animate" LIGHTBOX, i.e. resize transition between images, fade in/out effects, etc.
		this.doAnimations		= (objLIGHTBOX.p.doanimations=="false") ? false : true;
		// if you adjust the padding in the CSS, you will need to update this variable - otherwise, leave this alone. 
		this.borderSize			= (objLIGHTBOX.p.bordersize==undefined) ? 12 : objLIGHTBOX.bordersize;	 
		// Change value (milliseconds) to increase/decrease the time between "slides" (10000 = 10 seconds)
		this.slideInterval		= (objLIGHTBOX.p.slideinterval==undefined) ? 4000 : objLIGHTBOX.slideinterval;
		// true to display Next/Prev buttons/text during slideshow, false to hide
		this.showNavigation		= (objLIGHTBOX.p.shownavigation=="false") ? false : true;
		// true to display the Close button, false to hide
		this.showClose			= (objLIGHTBOX.p.showclose=="false") ? false: true; 
		// true to display image details (caption, count), false to hide
		this.showDetails		= (objLIGHTBOX.p.showdetails=="false") ? false : true;	
		// true to display pause/play buttons next to close button, false to hide
		this.showPlayPause		= (objLIGHTBOX.p.showplaypause=="false") ? false : true;
		// true to automatically close LIGHTBOX after the last image is reached, false to keep open
		this.autoEnd			= (objLIGHTBOX.p.autoend=="false") ? false : true;	
		// true to pause the slideshow when the "Next" button is clicked
		this.pauseOnNextClick	= (objLIGHTBOX.p.pauseonnextclick=="true") ? true : false;	
		// true to pause the slideshow when the "Prev" button is clicked
		this.pauseOnPrevClick 	= (objLIGHTBOX.p.pauseonprevclick=="false") ? false : true;	
		this.initialize();
		if (this.ie && !this.ie7) {	this.toggleSelects('hide'); }
		if (this.hideFlash) { this.toggleFlash('hide'); }
		this.isLightframe = (doFrame ? true : false);
		var pageSize	= this.getPageSize();
		var objOverlay	= this.doc.getElementById('lbOverlay');
		var objBody		= this.doc.getElementsByTagName("body").item(0);
		objOverlay.style.height = pageSize[1] + "px";
		objOverlay.style.display = '';
		this.appear('lbOverlay', (this.doAnimations ? 0 : this.maxOpacity));
		var anchors = (this.isFrame) ? window.parent.frames[window.name].document.getElementsByTagName('a') : document.getElementsByTagName('a');
		if (this.isLightframe) 
		{
			this.frameArray = [];
			this.frameNum = 0;
			if ((imageLink.getAttribute('rel') == 'lightframe')) 
			{
				var rev = imageLink.getAttribute('rev');
				this.frameArray.push(new Array(imageLink.getAttribute('href'), imageLink.getAttribute('title'), (rev == null || rev == '' ? 'width: 400px; height: 400px; scrolling: auto;' : rev)));
			} 
			else 
			{
				if (imageLink.getAttribute('rel').indexOf('lightframe') != -1) 
				{
					var length = anchors.length;
					for (var i = 0; i < length; i++) 
					{
						var anchor = anchors[i];
						if (anchor.getAttribute('href') && (anchor.getAttribute('rel') == imageLink.getAttribute('rel'))) 
						{
							var rev = anchor.getAttribute('rev');
							this.frameArray.push(new Array(anchor.getAttribute('href'), anchor.getAttribute('title'), (rev == null || rev == '' ? 'width: 400px; height: 400px; scrolling: auto;' : rev)));
						}
					}
					this.frameArray.removeDuplicates();
					while(this.frameArray[this.frameNum][0] != imageLink.getAttribute('href')) { this.frameNum++; }
				}
			}
		} 
		else 
		{
			this.imageArray = [];
			this.imageNum = 0;
			this.slideArray = [];
			this.slideNum = 0;
			if ((imageLink.getAttribute('rel') == 'lightbox'))
			{
				this.imageArray.push(new Array(imageLink.getAttribute('href'), imageLink.getAttribute('title')));
			} 
			else 
			{
				if (imageLink.getAttribute('rel').indexOf('lightbox') != -1) 
				{
					var length = anchors.length;
					for (var i = 0; i < length; i++) 
					{
						var anchor = anchors[i];
						if (anchor.getAttribute('href') && (anchor.getAttribute('rel') == imageLink.getAttribute('rel'))) 
						{
							this.imageArray.push(new Array(anchor.getAttribute('href'), anchor.getAttribute('title')));
						}
					}
					this.imageArray.removeDuplicates();
					while(this.imageArray[this.imageNum][0] != imageLink.getAttribute('href')) this.imageNum++;
				}
				if (imageLink.getAttribute('rel').indexOf('lightshow') != -1) 
				{
					var length = anchors.length;
					for (var i = 0; i < length; i++) 
					{
						var anchor = anchors[i];
						if (anchor.getAttribute('href') && (anchor.getAttribute('rel') == imageLink.getAttribute('rel'))) 
						{
							this.slideArray.push(new Array(anchor.getAttribute('href'), anchor.getAttribute('title')));
						}
					}
					this.slideArray.removeDuplicates();
					while(this.slideArray[this.slideNum][0] != imageLink.getAttribute('href')) this.slideNum++;
				}
			}
		}
		var object = this.doc.getElementById('lbMain');
		object.style.top = (this.getPageScroll() + (pageSize[3] / 15)) + "px";
		object.style.display = '';
		if (!this.outerBorder) 
		{
			this.doc.getElementById('lbOuterContainer').style.border = 'none';
			this.doc.getElementById('lbDetailsContainer').style.border = 'none';
		} 
		else 
		{
			this.doc.getElementById('lbOuterContainer').style.borderBottom = '';
			this.doc.getElementById('lbOuterContainer').className = 'lbOuterContainer_'+this.theme;
		}
		this.doc.getElementById('lbOverlay').onclick = function() { if(LIGHTBOX.modal==false)LIGHTBOX.end(); return false; }
		this.doc.getElementById('lbMain').onclick = function(e) 
		{
			var e = e;
			if (!e) 
			{
				if (window.parent.frames[window.name] && (parent.document.getElementsByTagName('frameset').length <= 0)) 
				{
					e = window.parent.window.event;
				} 
				else e = window.event;
			}
			var id = (e.target ? e.target.id : e.srcElement.id);
			if (id == 'lbMain') { if(LIGHTBOX.modal==false) LIGHTBOX.end(); return false; }
		}
		this.doc.getElementById('lbClose').onclick = function() { LIGHTBOX.end(); return false; }
		this.doc.getElementById('lbPause').onclick = function() { LIGHTBOX.togglePlayPause("lbPause", "lbPlay"); return false; }
		this.doc.getElementById('lbPlay').onclick = function() { LIGHTBOX.togglePlayPause("lbPlay", "lbPause"); return false; }	
		this.isSlideshow = doSlide;
		this.isPaused = (this.slideNum != 0 ? true : false);
		if (this.isSlideshow && this.showPlayPause && this.isPaused) 
		{
			this.doc.getElementById('lbPlay').style.display = '';
			this.doc.getElementById('lbPause').style.display = 'none';
		}
		if (this.isLightframe) this.changeContent(this.frameNum);
		else 
		{
			if (this.isSlideshow) this.changeContent(this.slideNum);
			else this.changeContent(this.imageNum);
		}
	},

	changeContent : function(imageNum) 
	{
		if (this.isSlideshow) 
		{
			for (var i = 0; i < this.slideshowIDCount; i++) window.clearTimeout(this.slideshowIDArray[i]);
		}
		this.activeImage = this.activeSlide = this.activeFrame = imageNum;
		if (!this.outerBorder) 
		{
			this.doc.getElementById('lbOuterContainer').style.border = 'none';
			this.doc.getElementById('lbDetailsContainer').style.border = 'none';
		} 
		else 
		{
			this.doc.getElementById('lbOuterContainer').style.borderBottom = '';
			this.doc.getElementById('lbOuterContainer').className = 'lbOuterContainer_'+this.theme;
		}
		this.doc.getElementById('lbLoading').style.display = '';
		this.doc.getElementById('lbImage').style.display = 'none';
		this.doc.getElementById('lbIframe').style.display = 'none';
		this.doc.getElementById('lbPrev').style.display = 'none';
		this.doc.getElementById('lbNext').style.display = 'none';
		this.doc.getElementById('lbIframeContainer').style.display = 'none';
		this.doc.getElementById('lbDetailsContainer').style.display = 'none';
		this.doc.getElementById('lbNumberDisplay').style.display = 'none';
		if (this.navType == 2 || this.isLightframe) 
		{
			object = this.doc.getElementById('lbNavDisplay');
			object.innerHTML = '&nbsp;&nbsp;&nbsp;<span id="lbPrev2_Off" style="display: none;" class="lbPrev2_Off_' + this.theme + '">&laquo; prev</span><a href="#" id="lbPrev2" class="lbPrev2_' + this.theme + '" style="display: none;">&laquo; prev</a> <b id="lbSpacer" class="lbSpacer_' + this.theme + '">||</b> <span id="lbNext2_Off" style="display: none;" class="lbNext2_Off_' + this.theme + '">next &raquo;</span><a href="#" id="lbNext2" class="lbNext2_' + this.theme + '" style="display: none;">next &raquo;</a>';
			object.style.display = 'none';
		}
		if (this.isLightframe) 
		{
			var iframe = LIGHTBOX.doc.getElementById('lbIframe');
			var styles = this.frameArray[this.activeFrame][2];
			var aStyles = styles.split(';');
			var length = aStyles.length; 
			for (var i = 0; i < length; i++) 
			{
				if (aStyles[i].indexOf('width:') >= 0) {
					var w = aStyles[i].replace('width:', '');
					iframe.width = w.trim();
				} else if (aStyles[i].indexOf('height:') >= 0) {
					var h = aStyles[i].replace('height:', '');
					iframe.height = h.trim();
				} else if (aStyles[i].indexOf('scrolling:') >= 0) {
					var s = aStyles[i].replace('scrolling:', '');
					iframe.scrolling = s.trim();
				}
			}
			this.resizeContainer(parseInt(iframe.width), parseInt(iframe.height));
		} 
		else 
		{
			imgPreloader = new Image();
			imgPreloader.onload = function() {
				var imageWidth = imgPreloader.width;
				var imageHeight = imgPreloader.height;
				if (LIGHTBOX.autoResize) {
					var pagesize = LIGHTBOX.getPageSize();
					var x = pagesize[2] - 150;
					var y = pagesize[3] - 150;
					if (imageWidth > x) {
						imageHeight = Math.round(imageHeight * (x / imageWidth));
						imageWidth = x; 
						if (imageHeight > y) { 
							imageWidth = Math.round(imageWidth * (y / imageHeight));
							imageHeight = y; 
						}
					} else if (imageHeight > y) { 
						imageWidth = Math.round(imageWidth * (y / imageHeight));
						imageHeight = y; 
						if (imageWidth > x) {
							imageHeight = Math.round(imageHeight * (x / imageWidth));
							imageWidth = x;
						}
					}
				}
				var lbImage = LIGHTBOX.doc.getElementById('lbImage')
				lbImage.src = (LIGHTBOX.isSlideshow ? LIGHTBOX.slideArray[LIGHTBOX.activeSlide][0] : LIGHTBOX.imageArray[LIGHTBOX.activeImage][0]);
				lbImage.width = imageWidth;
				lbImage.height = imageHeight;
				LIGHTBOX.resizeContainer(imageWidth, imageHeight);
				imgPreloader.onload = function() {};
			}
			imgPreloader.src = (this.isSlideshow ? this.slideArray[this.activeSlide][0] : this.imageArray[this.activeImage][0]);
		}
	},

	resizeContainer : function(imgWidth, imgHeight) 
	{
		this.wCur = this.doc.getElementById('lbOuterContainer').offsetWidth;
		this.hCur = this.doc.getElementById('lbOuterContainer').offsetHeight;
		this.xScale = ((imgWidth  + (this.borderSize * 2)) / this.wCur) * 100;
		this.yScale = ((imgHeight  + (this.borderSize * 2)) / this.hCur) * 100;
		var wDiff = (this.wCur - this.borderSize * 2) - imgWidth;
		var hDiff = (this.hCur - this.borderSize * 2) - imgHeight;
		if (!(hDiff == 0)) 
		{
			this.hDone = false;
			this.resizeH('lbOuterContainer', this.hCur, imgHeight + this.borderSize*2, this.getPixelRate(this.hCur, imgHeight));
		} 
		else this.hDone = true;
		if (!(wDiff == 0)) 
		{
			this.wDone = false;
			this.resizeW('lbOuterContainer', this.wCur, imgWidth + this.borderSize*2, this.getPixelRate(this.wCur, imgWidth));
		} 
		else this.wDone = true;
		if ((hDiff == 0) && (wDiff == 0)) 
		{
			if (this.ie) this.pause(250); 
			else this.pause(100); 
		}
		this.doc.getElementById('lbPrev').style.height = imgHeight + "px";
		this.doc.getElementById('lbNext').style.height = imgHeight + "px";
		this.doc.getElementById('lbDetailsContainer').style.width = (imgWidth + (this.borderSize * 2) + (this.ie && this.doc.compatMode == "BackCompat" && this.outerBorder ? 2 : 0)) + "px";
		this.showContent();
	},

	showContent : function() 
	{
		if (this.wDone && this.hDone) {
			for (var i = 0; i < this.showContentTimerCount; i++) { window.clearTimeout(this.showContentTimerArray[i]); }
			if (this.outerBorder) {
				this.doc.getElementById('lbOuterContainer').style.borderBottom = 'none';
			}
			this.doc.getElementById('lbLoading').style.display = 'none';
			if (this.isLightframe) {
				this.doc.getElementById('lbIframe').style.display = '';
				this.appear('lbIframe', (this.doAnimations ? 0 : 100));
			} else {
				this.doc.getElementById('lbImage').style.display = '';
				this.appear('lbImage', (this.doAnimations ? 0 : 100));
				this.preloadNeighborImages();
			}
			if (this.isSlideshow) {
				if(this.activeSlide == (this.slideArray.length - 1)) {
					if (this.autoEnd) {
						this.slideshowIDArray[this.slideshowIDCount++] = setTimeout("LIGHTBOX.end('slideshow')", this.slideInterval);
					}
				} else {
					if (!this.isPaused) {
						this.slideshowIDArray[this.slideshowIDCount++] = setTimeout("LIGHTBOX.changeContent("+(this.activeSlide+1)+")", this.slideInterval);
					}
				}
				this.doc.getElementById('lbHoverNav').style.display = (this.showNavigation && this.navType == 1 ? '' : 'none');
				this.doc.getElementById('lbClose').style.display = (this.showClose ? '' : 'none');
				this.doc.getElementById('lbDetails').style.display = (this.showDetails ? '' : 'none');
				this.doc.getElementById('lbPause').style.display = (this.showPlayPause && !this.isPaused ? '' : 'none');
				this.doc.getElementById('lbPlay').style.display = (this.showPlayPause && !this.isPaused ? 'none' : '');
				this.doc.getElementById('lbNavDisplay').style.display = (this.showNavigation && this.navType == 2 ? '' : 'none');
			} else {
				this.doc.getElementById('lbHoverNav').style.display = (this.navType == 1 && !this.isLightframe ? '' : 'none');
				if ((this.navType == 2 && !this.isLightframe && this.imageArray.length > 1) || (this.frameArray.length > 1 && this.isLightframe)) {
					this.doc.getElementById('lbNavDisplay').style.display = '';
				} else {
					this.doc.getElementById('lbNavDisplay').style.display = 'none';
				}
				this.doc.getElementById('lbClose').style.display = (this.showClose ? '' : 'none');
				this.doc.getElementById('lbDetails').style.display = (this.showDetails ? '' : 'none');
				this.doc.getElementById('lbPause').style.display = 'none';
				this.doc.getElementById('lbPlay').style.display = 'none';
			}
			this.doc.getElementById('lbImageContainer').style.display = (this.isLightframe ? 'none' : '');
			this.doc.getElementById('lbIframeContainer').style.display = (this.isLightframe ? '' : 'none');
			try {
				this.doc.getElementById('lbIframe').src = this.frameArray[this.activeFrame][0];
			} catch(e) { }
		} else {
			this.showContentTimerArray[this.showContentTimerCount++] = setTimeout("LIGHTBOX.showContent()", 200);
		}
	},

	updateDetails : function() 
	{
		var object = this.doc.getElementById('lbCaption');
		try {
		  var sTitle = (this.isSlideshow ? this.slideArray[this.activeSlide][1] : (this.isLightframe ? this.frameArray[this.activeFrame][1] : this.imageArray[this.activeImage][1]));
		}
		catch (e) {}
		object.style.display = '';
		object.innerHTML = (sTitle == null ? '' : sTitle);
		this.updateNav();
		this.doc.getElementById('lbDetailsContainer').style.display = '';
		object = this.doc.getElementById('lbNumberDisplay');
		if (this.isSlideshow && this.slideArray.length > 1) {
			object.style.display = '';
			object.innerHTML = "Image " + eval(this.activeSlide + 1) + " of " + this.slideArray.length;
			this.doc.getElementById('lbNavDisplay').style.display = (this.navType == 2 && this.showNavigation ? '' : 'none');
		} else if (this.imageArray.length > 1 && !this.isLightframe) {
			object.style.display = '';
			object.innerHTML = "Image " + eval(this.activeImage + 1) + " of " + this.imageArray.length;
			this.doc.getElementById('lbNavDisplay').style.display = (this.navType == 2 ? '' : 'none');
		} else if (this.frameArray.length > 1 && this.isLightframe) {
			object.style.display = '';
			object.innerHTML = "Page " + eval(this.activeFrame + 1) + " of " + this.frameArray.length;
			this.doc.getElementById('lbNavDisplay').style.display = '';
		} else {
			this.doc.getElementById('lbNavDisplay').style.display = 'none';
		}
		this.appear('lbDetailsContainer', (this.doAnimations ? 0 : 100));
	},

	updateNav : function() 
	{
		if (this.isSlideshow) {
			if (this.activeSlide != 0) {
				var object = (this.navType == 2 ? this.doc.getElementById('lbPrev2') : this.doc.getElementById('lbPrev'));
					object.style.display = '';
					object.onclick = function() {
						if (LIGHTBOX.pauseOnPrevClick) { LIGHTBOX.togglePlayPause("lbPause", "lbPlay"); }
						LIGHTBOX.changeContent(LIGHTBOX.activeSlide - 1); return false;
					}
			} else {
				if (this.navType == 2) { this.doc.getElementById('lbPrev2_Off').style.display = ''; }
			}
			if (this.activeSlide != (this.slideArray.length - 1)) {
				var object = (this.navType == 2 ? this.doc.getElementById('lbNext2') : this.doc.getElementById('lbNext'));
					object.style.display = '';
					object.onclick = function() {
						if (LIGHTBOX.pauseOnNextClick) { LIGHTBOX.togglePlayPause("lbPause", "lbPlay"); }
						LIGHTBOX.changeContent(LIGHTBOX.activeSlide + 1); return false;
					}
			} else {
				if (this.navType == 2) { this.doc.getElementById('lbNext2_Off').style.display = ''; }
			}
		} else if (this.isLightframe) {
			if(this.activeFrame != 0) {
				var object = this.doc.getElementById('lbPrev2');
					object.style.display = '';
					object.onclick = function() {
						LIGHTBOX.changeContent(LIGHTBOX.activeFrame - 1); return false;
					}
			} else {
				this.doc.getElementById('lbPrev2_Off').style.display = '';
			}
			if(this.activeFrame != (this.frameArray.length - 1)) {
				var object = this.doc.getElementById('lbNext2');
					object.style.display = '';
					object.onclick = function() {
						LIGHTBOX.changeContent(LIGHTBOX.activeFrame + 1); return false;
					}
			} else {
				this.doc.getElementById('lbNext2_Off').style.display = '';
			}		
		} else {
			if(this.activeImage != 0) {
				var object = (this.navType == 2 ? this.doc.getElementById('lbPrev2') : this.doc.getElementById('lbPrev'));
					object.style.display = '';
					object.onclick = function() {LIGHTBOX.changeContent(LIGHTBOX.activeImage - 1); return false;}
			} else {
				if (this.navType == 2) { this.doc.getElementById('lbPrev2_Off').style.display = ''; }
			}
			if(this.activeImage != (this.imageArray.length - 1)) {
				var object = (this.navType == 2 ? this.doc.getElementById('lbNext2') : this.doc.getElementById('lbNext'));
					object.style.display = '';
					object.onclick = function() {LIGHTBOX.changeContent(LIGHTBOX.activeImage + 1); return false;}
			} else {
				if (this.navType == 2) { this.doc.getElementById('lbNext2_Off').style.display = ''; }
			}
		}
		this.enableKeyboardNav();
	},

	enableKeyboardNav : function() { document.onkeydown = this.keyboardAction; },
	disableKeyboardNav : function() { document.onkeydown = ''; },
	keyboardAction : function(e) 
	{
		var keycode = key = escape = null;
		keycode	= (e == null) ? event.keyCode : e.which;
		key		= String.fromCharCode(keycode).toLowerCase();
		escape  = (e == null) ? 27 : e.DOM_VK_ESCAPE;
		if ((key == 'x') || (key == 'c') || (keycode == escape)) {
			LIGHTBOX.end();
		} else if ((key == 'p') || (keycode == 37)) {
			if (LIGHTBOX.isSlideshow) {
				if(LIGHTBOX.activeSlide != 0) {
					LIGHTBOX.disableKeyboardNav();
					LIGHTBOX.changeContent(LIGHTBOX.activeSlide - 1);
				}
			} else if (LIGHTBOX.isLightframe) {
				if(LIGHTBOX.activeFrame != 0) {
					LIGHTBOX.disableKeyboardNav();
					LIGHTBOX.changeContent(LIGHTBOX.activeFrame - 1);
				}
			} else {
				if(LIGHTBOX.activeImage != 0) {
					LIGHTBOX.disableKeyboardNav();
					LIGHTBOX.changeContent(LIGHTBOX.activeImage - 1);
				}
			}
		} else if ((key == 'n') || (keycode == 39)) {
			if (LIGHTBOX.isSlideshow) {
				if(LIGHTBOX.activeSlide != (LIGHTBOX.slideArray.length - 1)) {
					LIGHTBOX.disableKeyboardNav();
					LIGHTBOX.changeContent(LIGHTBOX.activeSlide + 1);
				}
			} else if (LIGHTBOX.isLightframe) {
				if(LIGHTBOX.activeFrame != (LIGHTBOX.frameArray.length - 1)) {
					LIGHTBOX.disableKeyboardNav();
					LIGHTBOX.changeContent(LIGHTBOX.activeFrame + 1);
				}
			} else {
				if(LIGHTBOX.activeImage != (LIGHTBOX.imageArray.length - 1)) {
					LIGHTBOX.disableKeyboardNav();
					LIGHTBOX.changeContent(LIGHTBOX.activeImage + 1);
				}
			}
		}
	},

	preloadNeighborImages : function() 
	{
		if (this.isSlideshow) 
		{
			if ((this.slideArray.length - 1) > this.activeSlide) {
				preloadNextImage = new Image();
				preloadNextImage.src = this.slideArray[this.activeSlide + 1][0];
			}
			if(this.activeSlide > 0) {
				preloadPrevImage = new Image();
				preloadPrevImage.src = this.slideArray[this.activeSlide - 1][0];
			}
		} else {
			if ((this.imageArray.length - 1) > this.activeImage) 
			{
				preloadNextImage = new Image();
				preloadNextImage.src = this.imageArray[this.activeImage + 1][0];
			}
			if(this.activeImage > 0) 
			{
				preloadPrevImage = new Image();
				preloadPrevImage.src = this.imageArray[this.activeImage - 1][0];
			}
		}
	},

	togglePlayPause : function(hideID, showID) 
	{
		if (this.isSlideshow && hideID == "lbPause") 
		{
			for (var i = 0; i < this.slideshowIDCount; i++) { window.clearTimeout(this.slideshowIDArray[i]); }
		}
		this.doc.getElementById(hideID).style.display = 'none';
		this.doc.getElementById(showID).style.display = '';
		if (hideID == "lbPlay") 
		{
			this.isPaused = false;
			if (this.activeSlide == (this.slideArray.length - 1)) this.end();
			else this.changeContent(this.activeSlide + 1);
		}
		else this.isPaused = true;
	},

	end : function(caller) 
	{
		var closeClick = (caller == 'slideshow' ? false : true);
		if (this.isSlideshow && this.isPaused && !closeClick) { return; }
		this.disableKeyboardNav();
		this.doc.getElementById('lbMain').style.display = 'none';
		this.fade('lbOverlay', (this.doAnimations ? this.maxOpacity : 0));
		if (this.ie && !this.ie7) this.toggleSelects('visible');
		if (this.hideFlash) { this.toggleFlash('visible'); }
		if (this.isSlideshow) {
			for (var i = 0; i < this.slideshowIDCount; i++) { window.clearTimeout(this.slideshowIDArray[i]); }
		}
		if (this.isLightframe) this.initialize();
	},

	checkFrame : function() 
	{
		if (window.parent.frames[window.name] && (parent.document.getElementsByTagName('frameset').length <= 0)) 
		{
			this.isFrame = true;
			this.LIGHTBOX = "window.parent." + window.name + ".LIGHTBOX";
			this.doc = parent.document;
		} 
		else 
		{
			this.isFrame = false;
			this.LIGHTBOX = "LIGHTBOX";
			this.doc = document;
		}
	},

	getPixelRate : function(cur, img) 
	{
		var diff = (img > cur) ? img - cur : cur - img;
		if (diff >= 0 && diff <= 100) return 10;
		if (diff > 100 && diff <= 200) return 15;
		if (diff > 200 && diff <= 300) return 20;
		if (diff > 300 && diff <= 400) return 25;
		if (diff > 400 && diff <= 500) return 30;
		if (diff > 500 && diff <= 600) return 35;
		if (diff > 600 && diff <= 700) return 40;
		if (diff > 700) return 45;
	},

	appear : function(id, opacity) 
	{
		var object = this.doc.getElementById(id).style;
		object.opacity = (opacity / 100);
		object.MozOpacity = (opacity / 100);
		object.KhtmlOpacity = (opacity / 100);
		object.filter = "alpha(opacity=" + (opacity + 10) + ")";
		object.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity='+opacity+')';
		if (opacity == 100 && (id == 'lbImage' || id == 'lbIframe')) {
			try { object.removeAttribute("filter"); } catch(e) {}	/* Fix added for IE Alpha Opacity Filter bug. */
			this.updateDetails();
		} else if (opacity >= this.maxOpacity && id == 'lbOverlay') {
			for (var i = 0; i < this.overlayTimerCount; i++) { window.clearTimeout(this.overlayTimerArray[i]); }
			return;
		} else if (opacity >= 100 && id == 'lbDetailsContainer') {
			try { object.removeAttribute("filter"); } catch(e) {}	/* Fix added for IE Alpha Opacity Filter bug. */
			for (var i = 0; i < this.imageTimerCount; i++) { window.clearTimeout(this.imageTimerArray[i]); }
			this.doc.getElementById('lbOverlay').style.height = this.getPageSize()[1] + "px";
		} else {
			if (id == 'lbOverlay') {
				this.overlayTimerArray[this.overlayTimerCount++] = setTimeout("LIGHTBOX.appear('" + id + "', " + (opacity+20) + ")", 1);
			} else {
				this.imageTimerArray[this.imageTimerCount++] = setTimeout("LIGHTBOX.appear('" + id + "', " + (opacity+10) + ")", 1);
			}
		}
	},

	fade : function(id, opacity) 
	{
		var object = this.doc.getElementById(id).style;
		object.opacity = (opacity / 100);
		object.MozOpacity = (opacity / 100);
		object.KhtmlOpacity = (opacity / 100);
		object.filter = "alpha(opacity=" + opacity + ")";
		object.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity='+opacity+')';
		if (opacity <= 0) 
		{
			try 
			{
				object.display = 'none';
			} catch(err) { }
		} 
		else if (id == 'lbOverlay') this.overlayTimerArray[this.overlayTimerCount++] = setTimeout("LIGHTBOX.fade('" + id + "', " + (opacity-20) + ")", 1);
		else this.timerIDArray[this.timerIDCount++] = setTimeout("LIGHTBOX.fade('" + id + "', " + (opacity-10) + ")", 1);
	},

	resizeW : function(id, curW, maxW, pixelrate, speed) 
	{
		if (!this.hDone) 
		{
			this.resizeWTimerArray[this.resizeWTimerCount++] = setTimeout("LIGHTBOX.resizeW('" + id + "', " + curW + ", " + maxW + ", " + pixelrate + ")", 100);
			return;
		}
		var object = this.doc.getElementById(id);
		var timer = speed ? speed : (this.resizeDuration/2);
		var newW = (this.doAnimations ? curW : maxW);
		object.style.width = (newW) + "px";
		if (newW < maxW) newW += (newW + pixelrate >= maxW) ? (maxW - newW) : pixelrate;
		else if (newW > maxW) newW -= (newW - pixelrate <= maxW) ? (newW - maxW) : pixelrate;
		this.resizeWTimerArray[this.resizeWTimerCount++] = setTimeout("LIGHTBOX.resizeW('" + id + "', " + newW + ", " + maxW + ", " + pixelrate + ", " + (timer+0.02) + ")", timer+0.02);
		if (parseInt(object.style.width) == maxW) 
		{
			this.wDone = true;
			for (var i = 0; i < this.resizeWTimerCount; i++) { window.clearTimeout(this.resizeWTimerArray[i]); }
		}
	},

	resizeH : function(id, curH, maxH, pixelrate, speed) 
	{
		var timer = speed ? speed : (this.resizeDuration/2);
		var object = this.doc.getElementById(id);
		var newH = (this.doAnimations ? curH : maxH);
		object.style.height = (newH) + "px";
		if (newH < maxH) newH += (newH + pixelrate >= maxH) ? (maxH - newH) : pixelrate;
		else if (newH > maxH) newH -= (newH - pixelrate <= maxH) ? (newH - maxH) : pixelrate;
		this.resizeHTimerArray[this.resizeHTimerCount++] = setTimeout("LIGHTBOX.resizeH('" + id + "', " + newH + ", " + maxH + ", " + pixelrate + ", " + (timer+.02) + ")", timer+.02);
		if (parseInt(object.style.height) == maxH) 
		{
			this.hDone = true;
			for (var i = 0; i < this.resizeHTimerCount; i++) { window.clearTimeout(this.resizeHTimerArray[i]); }
		}
	},

	getPageScroll : function() 
	{
		if (self.pageYOffset) return this.isFrame ? parent.pageYOffset : self.pageYOffset;
		else if (this.doc.documentElement && this.doc.documentElement.scrollTop) return this.doc.documentElement.scrollTop;
		else if (document.body) return this.doc.body.scrollTop;
	},

	getPageSize : function() 
	{	
		var xScroll, yScroll, windowWidth, windowHeight;
		if (window.innerHeight && window.scrollMaxY) 
		{
			xScroll = this.doc.scrollWidth;
			yScroll = (this.isFrame ? parent.innerHeight : self.innerHeight) + (this.isFrame ? parent.scrollMaxY : self.scrollMaxY);
		} 
		else if (this.doc.body.scrollHeight > this.doc.body.offsetHeight)
		{
			xScroll = this.doc.body.scrollWidth;
			yScroll = this.doc.body.scrollHeight;
		} 
		else 
		{
			xScroll = this.doc.getElementsByTagName("html").item(0).offsetWidth;
			yScroll = this.doc.getElementsByTagName("html").item(0).offsetHeight;
			xScroll = (xScroll < this.doc.body.offsetWidth) ? this.doc.body.offsetWidth : xScroll;
			yScroll = (yScroll < this.doc.body.offsetHeight) ? this.doc.body.offsetHeight : yScroll;
		}
		if (self.innerHeight) 
		{
			windowWidth = (this.isFrame) ? parent.innerWidth : self.innerWidth;
			windowHeight = (this.isFrame) ? parent.innerHeight : self.innerHeight;
		} else if (document.documentElement && document.documentElement.clientHeight) {
			windowWidth = this.doc.documentElement.clientWidth;
			windowHeight = this.doc.documentElement.clientHeight;
		} else if (document.body) {
			windowWidth = this.doc.getElementsByTagName("html").item(0).clientWidth;
			windowHeight = this.doc.getElementsByTagName("html").item(0).clientHeight;
			windowWidth = (windowWidth == 0) ? this.doc.body.clientWidth : windowWidth;
			windowHeight = (windowHeight == 0) ? this.doc.body.clientHeight : windowHeight;
		}
		var pageHeight = (yScroll < windowHeight) ? windowHeight : yScroll;
		var pageWidth = (xScroll < windowWidth) ? windowWidth : xScroll;
		return new Array(pageWidth, pageHeight, windowWidth, windowHeight);
	},

	toggleFlash : function(state) 
	{
		var objects = this.doc.getElementsByTagName("object");
		var length = objects.length;
		for (var i = 0; i < length; i++) objects[i].style.visibility = (state == "hide") ? 'hidden' : 'visible';
		var embeds = this.doc.getElementsByTagName("embed");
		var length = embeds.length;
		for (var i = 0; i < length; i++) embeds[i].style.visibility = (state == "hide") ? 'hidden' : 'visible';
		if (this.isFrame) 
		{
			var length = parent.frames.length
			for (var i = 0; i < length; i++) 
			{
				try 
				{
					objects = parent.frames[i].window.document.getElementsByTagName("object");
					var objectslength = objects.length;
					for (var j = 0; j < objectslength; j++) objects[j].style.visibility = (state == "hide") ? 'hidden' : 'visible';
				} catch(e) {}
				try {
					embeds = parent.frames[i].window.document.getElementsByTagName("embed");
					var embedslength = embeds.length;
					for (var j = 0; j < embedslength; j++) embeds[j].style.visibility = (state == "hide") ? 'hidden' : 'visible';
				} catch(e) {}
			}
		}
	},

	toggleSelects : function(state) 
	{
		var selects = this.doc.getElementsByTagName("select");
		var length = selects.length;
		for (var i = 0; i < length; i++ ) selects[i].style.visibility = (state == "hide") ? 'hidden' : 'visible';
		if (this.isFrame) 
		{
			var length = parent.frames.length;
			for (var i = 0; i < length; i++) 
			{
				try {
					selects = parent.frames[i].window.document.getElementsByTagName("select");
					var selectslen = selects.length;
					for (var j = 0; j < selectslen; j++) selects[j].style.visibility = (state == "hide") ? 'hidden' : 'visible';
				} catch(e) {}
			}
		}
	},

	pause : function(numberMillis) 
	{
		var now = new Date();
		var exitTime = now.getTime() + numberMillis;
		while (true) 
		{
			now = new Date();
			if (now.getTime() > exitTime) { return; }
		}
	},

	 Dialog : function(id, page, width, heigth)
	 {
		  var AOBJ = document.createElement('a');
		  AOBJ.setAttribute('href', page);
		  AOBJ.setAttribute('rel', 'lightframe');
		  AOBJ.setAttribute('rev', 'width: '+parseInt(width)+'px;height: '+parseInt(heigth)+'px');
		  LIGHTBOX.start($(id), AOBJ, false, true);
	 }
}

var LIGHTBOX = new clsLightBox();