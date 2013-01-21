/**
* Class JMAP
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

function clsMap()
{
	this.before_drawmap = function(){};
	this.after_drawmap = function(){};
	this.before_drawmarker = function(){};
	this.after_drawmarker = function(){};
	this.before_drawdirection = function(){};
	this.after_drawdirection = function(){};
	this.before_marker_drag = function(){};
	this.after_marker_drag = function(){};
}

clsMap.prototype = 
{
  toggleTraffic : function () 
  {
	 if (JMAP.toggleState) JMAP.Map.removeOverlay(JMAP.trafficInfo);
	 else JMAP.Map.addOverlay(JMAP.trafficInfo);
	 JMAP.toggleState = !JMAP.toggleState;
  },

  createMap: function(point, zoom) 
  {
		if (JMAP.Marker) JMAP.clearOverlays();
		JMAP.Marker = new Array();
		var p = JMAP.jmapObj.p;
 		var dsObj = p.dsObj;
		var html = p.html;
		var icon = p.icon;
		var draggable = p.draggable;
 		if (p.dsObj && $(p.dsObj).DSresult[$(p.dsObj).DSpos]) 
		{
			 if ($(dsObj).DSresult[$(dsObj).DSpos]['html']) html = $(dsObj).DSresult[$(dsObj).DSpos]['html'];
			 if ($(dsObj).DSresult[$(dsObj).DSpos]['icon']) icon = $(dsObj).DSresult[$(dsObj).DSpos]['icon'];
			 if ($(dsObj).DSresult[$(dsObj).DSpos]['draggable']) draggable = $(dsObj).DSresult[$(dsObj).DSpos]['draggable'];
		}
		JMAP.Map.setCenter(point);
		JMAP.Map.setZoom(parseInt(zoom));
		if (p.marker == "true") JMAP.setMarker(point, icon, draggable, html);
		if (p.traffic=="true") 
		{
		  JMAP.trafficInfo = new google.maps.TrafficLayer();
		  JMAP.trafficInfo.setMap(JMAP.Map);
		  JMAP.toggleState = false;
		}
		JMAP.after_drawmap();
		if (p.dsmarker=="true" && JMAP.DataMarker!=undefined) JMAP.createMarkers();
  },

  createGraph: function() 
  {
		JMAP.before_drawmap();
		JMAP.jmapObj.p.zoom = (JMAP.jmapObj.p.zoom==undefined) ? 13 : parseInt(JMAP.jmapObj.p.zoom);
		var p = JMAP.jmapObj.p;
 		var dsObj = p.dsObj;
		var lat = p.lat;
		var lng = p.lng;
		var zoom = p.zoom;
		var address = p.address;

 		if (p.dsObj && $(p.dsObj).DSresult[$(p.dsObj).DSpos]) 
		{
			 if ($(dsObj).DSresult[$(dsObj).DSpos]['lat']) lat = $(dsObj).DSresult[$(dsObj).DSpos]['lat'];
			 if ($(dsObj).DSresult[$(dsObj).DSpos]['lng']) lng = $(dsObj).DSresult[$(dsObj).DSpos]['lng'];
			 if ($(dsObj).DSresult[$(dsObj).DSpos]['zoom']) zoom = $(dsObj).DSresult[$(dsObj).DSpos]['zoom'];
			 if ($(dsObj).DSresult[$(dsObj).DSpos]['address']) address = $(dsObj).DSresult[$(dsObj).DSpos]['address'];
			 if ($(dsObj).DSresult[$(dsObj).DSpos]['html']) html = $(dsObj).DSresult[$(dsObj).DSpos]['html'];
		}
		if (lat && lng) 
		{
			var point = new google.maps.LatLng(parseFloat(lat), parseFloat(lng));
			JMAP.createMap(point, zoom); 
	    }
		else if (address) 
		{
		  JMAP.geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					 JMAP.createMap(results[0].geometry.location, zoom); 
				  } else {
					 alert("Geocode was not successful for the following reason: " + status);
				  }
				});
		}
    },

	 setMarker: function(point, icon, draggable, html) 
	 {
		var i = JMAP.Marker.length;
		JMAP.Marker[i] = new Array();
		if (!point) return;
		if (JMAP.jmapObj.p.route!=undefined) return;
		JMAP.before_drawmarker(JMAP.Marker[i]);
		JMAP.Marker[i] = new google.maps.Marker({
			 position: point, 
			 map: JMAP.Map,
			 icon: icon
		});
		if(draggable == "true") 
		{
			 JMAP.Marker[i].setDraggable(true);		
			 google.maps.event.addListener(JMAP.Marker[i], 'drag', JMAP.before_marker_drag);
			 google.maps.event.addListener(JMAP.Marker[i], 'dragend', JMAP.after_marker_drag);
		}
		if (html != undefined)
		{              
			 JMAP.Marker[i].infoWindow = new google.maps.InfoWindow();
			 google.maps.event.addListener(JMAP.Marker[i], 'click', function() 
			 {
				  JMAP.Marker[i].infoWindow.setContent(html);
				  JMAP.Marker[i].infoWindow.open(JMAP.Map, JMAP.Marker[i]);
			 });
		}
		JMAP.after_drawmarker(JMAP.Marker[i]);
    },

	 drawMarker: function(point) 
	 {
		  var i = JMAP.Marker.length;
		  var data = JMAP.DataMarker[i+1];
		  JMAP.setMarker(point, data['icon'], data['draggable'], data['html']);
    },

	 createMarkers: function() 
	 {	
		var i = JMAP.Marker.length+1;
		if (JMAP.DataMarker[i]==undefined) 
		{
 			JMAP.createDirection();
			return;
	    }
		if (JMAP.DataMarker[i]['lat']!=undefined && JMAP.DataMarker[i]['lng']!=undefined) 
		{
			var lat = parseFloat(JMAP.DataMarker[i]['lat']);
			var lng = parseFloat(JMAP.DataMarker[i]['lng']);
			var point = new google.maps.LatLng(lat, lng);
			JMAP.drawMarker(point);
			JMAP.createMarkers();
	    }
		else if (JMAP.DataMarker[i]['address']!=undefined) 
		{
		  JMAP.geocoder.geocode( { 'address': JMAP.DataMarker[i]['address']}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					 JMAP.drawMarker(results[0].geometry.location);
					 JMAP.createMarkers();
				  } 
				});
	    }
    },

   createDirection: function() 
	{	
		if (JMAP.DataMarker.length==0) return;            
		JMAP.before_drawdirection();
		var userPoints = [];
		var len = JMAP.DataMarker.length;
		for (var i=1; i<len; i++) 
		{
			if (JMAP.DataMarker[i]['point']=="true")
			{
				if (JMAP.DataMarker[i]['lat']!=undefined && JMAP.DataMarker[i]['lng']!=undefined) 
				{
				  var lat = parseFloat(JMAP.DataMarker[i]['lat']);
				  var lng = parseFloat(JMAP.DataMarker[i]['lng']);
				  var point = new google.maps.LatLng(lat, lng);
				  userPoints.push({
						  location: point,
						  stopover: true
					 });
				}
				else if (JMAP.DataMarker[i]['address']!=undefined) 
					 {
						userPoints.push({
								location: JMAP.DataMarker[i]['address'],
								stopover: true
						  });
					 }
				}
        }
		  if (userPoints.length > 0)
		  {
			 var directionsPanel = null;
			 if (JMAP.jmapObj.p.route!=undefined) directionsPanel = $(JMAP.jmapObj.p.route);
				var mode = (JMAP.jmapObj.p.travelmode==undefined) ? google.maps.DirectionsTravelMode.DRIVING : JMAP.jmapObj.p.travelmode.toUpperCase();;
				var directionsService = new google.maps.DirectionsService();
				JMAP.directionsDisplay = new google.maps.DirectionsRenderer();
				JMAP.directionsDisplay.setMap(JMAP.Map);
				var start = userPoints[0]['location'];
				var stop = userPoints[userPoints.length-1]['location'];
				var len = userPoints.length-1;
				var points = [];
				for (var i=1; i<len; i++) points[i-1] = userPoints[i];
				var request = {
					 origin: start, 
					 destination: stop,
					 travelMode: mode,
					 waypoints: points,
					 optimizeWaypoints: true
				};
				directionsService.route(request, function(response, status) 
				{
				  if (JMAP.after_drawdirection(JMAP.directionsDisplay, response, status) == false) return;
				  if (status == google.maps.DirectionsStatus.OK)
				  {
					 JMAP.directionsDisplay.setPanel(directionsPanel);
					 JMAP.directionsDisplay.setDirections(response);
				  }
				});
		  } 
	 },

	 clearOverlays : function() 
	 {
		if (JMAP.jmapObj.p.route!=undefined)
		{
			if(JMAP.directionsDisplay != undefined) JMAP.directionsDisplay.setMap(null); 
		} 
		else for (var i=0; i<JMAP.Marker.length; i++) JMAP.Marker[i].setMap(null);
	 },

	getDsValue : function(id)
	{
		JMAP.jmapObj = $(id);
 		if (JMAP.jmapObj.p.dsObj!=undefined) JMAP.DataMarker = $(JMAP.jmapObj.p.dsObj).DSresult;
		JMAP.geocoder = new google.maps.Geocoder();
		var myOptions = {
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		if (JMAP.Map==undefined) JMAP.Map = new google.maps.Map($(id), myOptions);
		JMAP.createGraph();
	},

	displayObj : function(id)
	{
		google.maps.event.trigger(JMAP.Map, 'resize'); 
	},

	refreshObj : function(id)
	{
		JMAP.getDsValue(id);
	}
};

var JMAP = new clsMap();