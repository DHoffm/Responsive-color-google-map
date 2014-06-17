jQuery( document ).ready(function( $ ) {
  $('.route_responsive_map_wrapper:not(.route-processed)').addClass('route-processed').each(function () {
    var identifier = $('.route_responsive_map', this).attr('id');
    var map_data = window[identifier];
    if (typeof map_data !== 'undefined') {
      var config = map_data.general;
      var gmap = $('#' + identifier).data('gmap').gmap;
      var infoWindow = new google.maps.InfoWindow();
      var infoWindowOpened = false;
      var renderOptions = {
        map: gmap,
        suppressMarkers : true
      };
      var directionsDisplay = new google.maps.DirectionsRenderer(renderOptions);
      var directionsService = new google.maps.DirectionsService();

      function openInfoWindow(content) {
        if (!infoWindowOpened) {
          // Set the info window's content and position.
          infoWindow.setContent(content);
          infoWindow.setPosition(gmap.getCenter());
          infoWindowOpened = true;
          infoWindow.open(gmap);
        }
      }
      function closeInfoWindow() {
        infoWindowOpened = false;
        infoWindow.close(gmap);
      }
      function map_route(index, locationAddress, destinationAddress) {
        $('#' + identifier).gMap('removeAllMarkers');
        $('#' + identifier).gMap('geocode', locationAddress, function(location, boundaries) {
          console.log(location);
          // remove all markers

          var request = {
            origin: location,
            destination: destinationAddress,
            travelMode: google.maps.TravelMode.DRIVING
          };

          directionsService.route(request, function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
              closeInfoWindow();
              directionsDisplay.setMap(gmap);
              directionsDisplay.setPanel(document.getElementById('route_list_item_' + identifier));
              directionsDisplay.setDirections(response);
              changeMarker(index, response);
            } else if (status == google.maps.DirectionsStatus.ZERO_RESULTS) {
              directionsDisplay.setMap(null);
              directionsDisplay.setPanel(null);
              openInfoWindow('<div class="responsive-map-alert-window"><h4>' + config.routeNoResultHeadline + '</h4>' + config.routeNoResult + '</div>');
            }
          });

          function changeMarker(index, directionResult) {
            var route = directionResult.routes[0].legs[0];
            var destination = map_data.coordinates[index];
            if (typeof destination.icon !== 'undefined') {
              var custom_icon = {
                image: destination.icon,
                iconsize: [destination.icon_width, destination.icon_height], 
                iconanchor: [parseInt(destination.icon_width/2),parseInt(destination.icon_height/2)], 
                infowindowanchor: [parseInt(destination.icon_width/2), 0]
              };
            }

            // start
            var current_marker = {
              latitude: route.steps[0].start_point.lat(),
              longitude: route.steps[0].start_point.lng(),
              html: route.start_address,
              popup: false,
            };
            if (typeof destination.icon !== 'undefined') {
              current_marker.icon = custom_icon;
            }
            $('#' + identifier).gMap('addMarker', current_marker);
            // destination
            var current_marker = {
              latitude: route.steps[route.steps.length - 1].end_point.lat(),
              longitude: route.steps[route.steps.length - 1].end_point.lng(),
              html: ((destination.label != '') ? destination.label : route.end_address),
              popup: destination.popup
            };
            if (typeof destination.icon !== 'undefined') {
              current_marker.icon = custom_icon;
            }
            $('#' + identifier).gMap('addMarker', current_marker);
          }
        }, function(results, status) {
          directionsDisplay.setMap(null);
          directionsDisplay.setPanel(null);
          openInfoWindow('<div class="responsive-map-alert-window"><h4>' + config.routeNoResultHeadline + '</h4>' + config.routeNoResult + '</div>');
        });
      }
      $( ".responsive_map_route_submit", this).click(function() {
        map_route(
          $('.responsive_map_route_destination_' + identifier)[0].selectedIndex, 
          $('.responsive_map_route_search_' + identifier).val(), 
          $('.responsive_map_route_destination_' + identifier).val()
        );
        return false;
      });
    }
  });
});