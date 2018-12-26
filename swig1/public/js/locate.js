jQuery(function($) {
    var marker;
		// Asynchronously Load the map API 
		var script = document.createElement('script');
		script.src = "http://maps.googleapis.com/maps/api/js?key=AIzaSyCHSdKVY2gQ2E5cy3f-YfyP7W7NoT60vqA&sensor=false&callback=initialize";
    lt=0;

    ln=0;
   
   // lon=-81.8695939;

		document.body.appendChild(script);
	});
  
   
   function initialize() {
     if($('#latitude').val()!='' && $('#longitude').val() !=''){
       lt=$('#latitude').val();
       ln=$('#longitude').val();
     }

    var latlng = new google.maps.LatLng(lt,ln);
    var myOptions = {
      zoom: 7,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
     
    var map = new google.maps.Map(document.getElementById("location_div"),
        myOptions);
 	
    // Creating a marker and positioning it on the map  
      marker = new google.maps.Marker({  
      position: new google.maps.LatLng(lt,ln),  
      map: map  
    });
    google.maps.event.addListener(map, "click", function (e) {

		    //lat and lng is available in e object
		    var latLng = e.latLng;

			var r = confirm( 'Do you want to pin this location ?' );
			if( r == true ) {
				$('#latitude').val( latLng.lat() );
				$('#longitude').val( latLng.lng() );

			}
		});

 
 /*google.maps.event.addListener(map, "click", function (e) {

    //lat and lng is available in e object
    var latLng = e.latLng;
var marker = new google.maps.Marker({  
      position: latLng,  
      map: map  
    });

});*/



function placeMarker(location) {
  if ( marker ) {
    marker.setPosition(location);
  } else {
    marker = new google.maps.Marker({
      position: location,
      map: map
    });
  }
}

google.maps.event.addListener(map, 'click', function(event) {
  placeMarker(event.latLng);
});
  }
