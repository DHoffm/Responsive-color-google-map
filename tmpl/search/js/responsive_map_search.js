jQuery( document ).ready(function( $ ) {
  $('.search_responsive_map_wrapper:not(.search-processed)').addClass('search-processed').each(function () {
    var identifier = $('.search_responsive_map', this).attr('id');
    var map_data = window[identifier];
    if (typeof map_data !== 'undefined') {
      var config = map_data.general;
      var gmap = $('#' + identifier).data('gmap').gmap;
      var markers = $('#' + identifier).data('gmap').markers;
      var infoWindow = new google.maps.InfoWindow();
      var infoWindowOpened = false;
      var lastSearch = '';
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

      function map_search(locationAddress, useViewportBoundaries){
        // get map boundaries for location
        useViewportBoundaries = useViewportBoundaries || false;
        $('#' + identifier).gMap('geocode', locationAddress, function(location, boundaries) {
          
          // set new map boundaries with result from geocode
          var new_bounds = new google.maps.LatLngBounds();

          // remove all markers
          $('#' + identifier).gMap('removeAllMarkers');
          $('.coordinates_description_list_item_' + identifier).css('display', 'none');
          var marker_counter = 0;

          if (useViewportBoundaries == true) {
            new_bounds.extend(new google.maps.LatLng(boundaries.getSouthWest().lat(), boundaries.getSouthWest().lng()));
            new_bounds.extend(new google.maps.LatLng(boundaries.getNorthEast().lat(), boundaries.getNorthEast().lng()));
          }

          $.each(map_data.coordinates, function(key, value) {
            // check each marker and compare to boundaries
            
            if (value.latitude >= boundaries.getSouthWest().lat() && value.latitude <= boundaries.getNorthEast().lat() && value.longitude >= boundaries.getSouthWest().lng() && value.longitude <= boundaries.getNorthEast().lng() ) {
              var current_marker = {
                latitude: value.latitude,
                longitude: value.longitude,
                html: value.label,
                popup: value.popup
              };
              if (typeof value.icon !== 'undefined') {
                var custom_icon = {
                  image: value.icon,
                  iconsize: [value.icon_width, value.icon_height], 
                  iconanchor: [parseInt(value.icon_width/2),parseInt(value.icon_height/2)], 
                  infowindowanchor: [parseInt(value.icon_width/2), 0]
                }
                current_marker.icon = custom_icon;
              }
              if (useViewportBoundaries == false) {
                new_bounds.extend(new google.maps.LatLng(value.latitude, value.longitude));
              }
              // readd the marker
              $('#' + identifier).gMap('addMarker', current_marker);
              $('#coordinates_description_list_item_' + identifier + '_' + key).css('display', 'block');
              marker_counter++;
            }
          });

          if (marker_counter == 0) {
            openInfoWindow('<div class="responsive-map-alert-window"><h4>' + config.searchNoResultHeadline + '</h4>' + config.searchNoResult + '</div>');
          } else {
            closeInfoWindow();
            gmap.fitBounds(new_bounds);
          }
        });
      }
      $( ".responsive_map_search_submit", this).click(function() {
        if ($(this).prev('.responsive_map_perimeter_search').val() != lastSearch) {
          map_search($(this).prev('.responsive_map_perimeter_search').val(), true);
          lastSearch = $(this).prev('.responsive_map_perimeter_search').val();
        }
        return false;
      });
    }
  });
});