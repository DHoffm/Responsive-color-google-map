jQuery( document ).ready(function( $ ) {
  var fieldCounter = $('.geoDataHolder').length - 1;
  $('#geoDataAddMore').click(function() {
    fieldCounter += 1;
    $('#geoDataHolder_0').clone(true, true).addClass('geoDataHolderCloned').insertAfter('div.geoDataHolder:last');
    $('.geoDataHolderCloned').attr('id', 'geoDataHolder_' + fieldCounter).removeClass('geoDataHolderCloned');
    $('div.geoDataHolder:last').append(mod_profil_responsive_map_config.general.removeButton);
    $('div.geoDataHolder:last input, div.geoDataHolder:last textarea').val('');
    $('.geoDataRemove').click(function() {
      $(this).parent().remove();
      return false;
    });

    // goofy hack to get the "chosen" js to work for a cloned select list in joomla 3
    var icon_options = $("#geoDataHolder_0 .geoDataHolderIcon > option").clone();
    $("div.geoDataHolder:last .geoDataHolderIcon").replaceWith('<select class="geoDataHolderIcon" name="' + $("#geoDataHolder_0 .geoDataHolderIcon").attr('name') + '"></select>');
    $("div.geoDataHolder:last .chzn-container").remove();
    $("div.geoDataHolder:last .geoDataHolderIcon").append(icon_options);
    if (jQuery.isFunction($("div.geoDataHolder:last .geoDataHolderIcon").chosen)) {
      $("div.geoDataHolder:last .geoDataHolderIcon").chosen();
    }
    return false;
  });
  $('a.geoDataRemove').click(function() {
    $(this).parent().remove();
    return false;
  });
  $('#geocodemap_submit').click(function() {
    $('#geoDataHolder_' + $('#geoDataHolderId').val() + ' .geoDataHolderLat').val($('#geoDataHolderLatitutde').val());
    $('#geoDataHolder_' + $('#geoDataHolderId').val() + ' .geoDataHolderLng').val($('#geoDataHolderLongitude').val());
    $.fancybox.close();
    return false;
  });
  $('.geoDataGeocodeLink').click(function() {
    var id = $(this).parent().parent().attr('id');
    var id_parts = id.split("_");
    $('#geoDataHolderId').val(id_parts[1]);
  });

  // checkbox hack
  $('.geoDataHolderPopupCheckbox').change(function() {
      if($(this).is(":checked")) {
        $(this).next('.geoDataHolderPopupInput').val('1');
      } else {
        $(this).next('.geoDataHolderPopupInput').val('0');
      }
  });

  $('a.fancymodal').fancybox({
    'width': 500,
    'height': 'auto',
    'autoSize': false,
    'afterShow': function() {
      $('#geoDataHolderLocation').geocomplete({
        map: '.map_canvas',
        location: 'Erfurt',
        details: 'form ',
        markerOptions: {
          draggable: true
        }
      });

      $('#geoDataHolderLocation').bind("geocode:dragged", function(event, latLng) {
        $('#geoDataHolderLatitutde').val(latLng.lat());
        $('#geoDataHolderLongitude').val(latLng.lng());
      });
      
      $('#geoDataHolderLocation').geocomplete().bind("geocode:result", function(event, result){
        $('#geoDataHolderLatitutde').val(result.geometry.location.lat());
        $('#geoDataHolderLongitude').val(result.geometry.location.lng());
      });

      
      $('#geocodemap_search').click(function() {
          $('#geoDataHolderLocation').trigger("geocode");
      }).click();
    }
  });
});