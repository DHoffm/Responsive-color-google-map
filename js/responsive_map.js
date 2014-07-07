jQuery( document ).ready(function( $ ) {

  function FullscreenControl(controlDiv, identifier, map, config) {
    // Set CSS styles for the DIV containing the control
    // Setting padding to 5 px will offset the control
    // from the edge of the map
    controlDiv.style.padding = '5px';

    // Set CSS for the control border
    var controlUI = document.createElement('div');
    $(controlUI).addClass('responsive_map_fullscreen_button');
    controlDiv.appendChild(controlUI);

    // Set CSS for the control interior
    var controlText = document.createElement('div');
    controlText.innerHTML = config.fullscreenTitle;
    controlUI.appendChild(controlText);

    // Setup the click event listeners: simply set the map to
    // Chicago
    google.maps.event.addDomListener(controlUI, 'click', function() {
      if ($('#' + identifier).hasClass('responsive_map_fullscreen')) {
        $('#' + identifier).removeClass('responsive_map_fullscreen');
        $('#' + identifier + ' div.responsive_map_fullscreen_button div').html(config.fullscreenTitle);
        $('.responsive_map_fullscreen_dim').hide();

      } else {
        $('#' + identifier).addClass('responsive_map_fullscreen');
        $('#' + identifier + ' div.responsive_map_fullscreen_button div').html(config.fullscreenCloseTitle);
        $('.responsive_map_fullscreen_dim').show();
      }
      google.maps.event.trigger(map, 'resize');
      $('#' + identifier).gMap('fixAfterResize');
    });

  }


  $('.responsive_map:not(.processed)').addClass('processed').each(function () {
    var identifier = $(this).attr('id');
    var map_data = window[identifier];
    var custom_markers = [];
    if (typeof map_data !== 'undefined') {
      var config = map_data.general;

      $(this).addClass(config.template + '_responsive_map');
      $.each( map_data.coordinates, function( c_key, c_value ) {
        var marker = {
          latitude: c_value.latitude,
          longitude: c_value.longitude,
          html: c_value.label,
          popup: c_value.popup
        };
        if (typeof c_value.icon !== 'undefined') {
          var custom_icon = {
            image: c_value.icon,
            iconsize: [c_value.icon_width, c_value.icon_height],
            iconanchor: [parseInt(c_value.icon_width/2),parseInt(c_value.icon_height/2)],
            infowindowanchor: [parseInt(c_value.icon_width/2), 0]
          }
          marker.icon = custom_icon;
        }
        custom_markers.push(marker);
      });
      var map = $('#' + identifier).gMap({
        maptype: google.maps.MapTypeId.ROADMAP,
        zoom: config.zoom,
        markers: custom_markers,
        panControl: config.panControl,
        zoomControl: config.zoomControl,
        mapTypeControl: config.mapTypeControl,
        scaleControl: config.scaleControl,
        streetViewControl: config.streetViewControl,
        scrollwheel: config.scrollwheel,
        styles: [ { "stylers": [ { "hue": config.hue }, { "gamma": config.gamma }, { "saturation": config.saturation }, {"lightness": config.lightness}, {"visibility": config.visibility } ] } ],
        log: false,
        onComplete: function() {
          // Resize and re-center the map on window resize event
          window.onresize = function() {
            $('.responsive_map').each(function () {
               var identifier = $(this).attr('id');
               var gmap = $('#' + identifier).data('gmap').gmap;
               google.maps.event.trigger(gmap, 'resize');
               $('#' + identifier).gMap('fixAfterResize');
            });
          };
        }
      });

      var gmap = $('#' + identifier).data('gmap').gmap;

      var marker_amount = 0; // Object.keys(markers).length is not supported in IE < 9 therefore use a loop
      for (i in markers) {
        if (markers.hasOwnProperty(i)) {
          marker_amount++;
        }
      }
      if (marker_amount > 1) {
        var bound = new google.maps.LatLngBounds();
        var fit_marker;
        $.each(custom_markers, function(key, value) {
          fit_marker = new google.maps.LatLng(value.latitude, value.longitude);
          bound.extend(fit_marker);
        });
        gmap.fitBounds(bound);
      }

      if (config.fullscreen) {
        var fullscreenControlDiv = document.createElement('div');
        var fullscreenControl = new FullscreenControl(fullscreenControlDiv, identifier, gmap, config);
        fullscreenControlDiv.index = 1;
        gmap.controls[google.maps.ControlPosition.TOP_RIGHT].push(fullscreenControlDiv);
        $('body').append('<div class="responsive_map_fullscreen_dim"></div>');
      }

      if (config.bikemap) {
        var bikeLayer = new google.maps.BicyclingLayer();
        bikeLayer.setMap(gmap);
      }

      if (config.weathermap) {
        var temperatureunit = google.maps.weather.TemperatureUnit.CELSIUS;
        if (config.weathermap_temperatureunit == 'fahrenheit') {
          temperatureunit = google.maps.weather.TemperatureUnit.FAHRENHEIT;
        }
        var weatherLayer = new google.maps.weather.WeatherLayer({
          temperatureUnits: temperatureunit
        });
        weatherLayer.setMap(gmap);
      }
      if (config.cloudmap) {
        var cloudLayer = new google.maps.weather.CloudLayer();
        cloudLayer.setMap(gmap);
      }

      if (config.trafficmap) {
        var trafficLayer = new google.maps.TrafficLayer();
        trafficLayer.setMap(gmap);
      }
    }

  });
});