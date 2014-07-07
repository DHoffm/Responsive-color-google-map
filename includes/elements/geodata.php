<?php
/**
 * @version   1.1.1
 * @package   Responsive color google map (module)
 * @author    David Hoffmann - http://www.profilpr.de
 * @copyright Copyright (c) 2014 Profilpr. All rights reserved.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldGeoData extends JFormField {

  protected $type = 'GeoData';

  public function getInput() {

    $document = JFactory::getDocument();

    // find the frontpage template form the backend, this is the best solution i found so far
    $db = JFactory::getDBO();
    $query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
    $db->setQuery($query);
    $frontpageTemplate = $db->loadResult();

    // load jquery if necesarry
    JLoader::import( 'joomla.version' );
    $version = new JVersion();
    if (version_compare( $version->RELEASE, '2.5', '<=')) {
      if(JFactory::getApplication()->get('jquery') !== true) {
        // load jQuery here
        $document->addScript('https://code.jquery.com/jquery-1.11.0.min.js');
        JFactory::getApplication()->set('jquery', true);
        $wrapper_class = "mod_profil_responsive_map_holder_j25";
      }
    } else {
        JHtml::_('jquery.framework');
        $wrapper_class = "mod_profil_responsive_map_holder_j3";
    }

    // normal joomla modal box is not sufficient because its prototype and can't be reattached to elements, instead use fancybox which works perfectly
    $document->addScript(JURI::root().'modules/mod_profil_responsive_map/js/fancybox/source/jquery.fancybox.pack.js');
    $document->addStyleSheet(JURI::root().'modules/mod_profil_responsive_map/js/fancybox/source/jquery.fancybox.css');

    // some css to theme the admin module config
    $document->addStyleSheet(JURI::root().'modules/mod_profil_responsive_map/css/mod_profil_responsive_map.css');

    // google api
    $document->addScript('http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places');

    // geocoder javascript for address to coordinates conversion
    $document->addScript(JURI::root().'modules/mod_profil_responsive_map/js/jquery.geocomplete.min.js');

    // backend javascript processing
    $document->addScript(JURI::root().'modules/mod_profil_responsive_map/js/responsive_map_admin.js');

    // remove button, will be added to javascript array for translation purposes
    $remove_button = '<a href="#" class="btn icon-cancel geoDataRemove"><span>' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_REMOVE') . '</span></a>';

    // the javascript config array
    $map_config['general'] = array(
      'removeButton' => $remove_button
    );

    // add the javascript config array to the page
    $js = 'var mod_profil_responsive_map_config =' . json_encode( $map_config ) . ';';
    $document->addScriptDeclaration($js);

    // get all field values
    $data = $this->__get('value');

    $output = '';

    // get all marker images from folders here, each address could have its own marker
    $marker_paths = array();

    // module path for markers
    $marker_paths[] = array(
      'server_path' => JPATH_SITE . '/modules/mod_profil_responsive_map/tmpl/default/images',
      'website_path' => 'modules/mod_profil_responsive_map/tmpl/default/images/',
      'administrator_path' => JURI::root() . '/modules/mod_profil_responsive_map/tmpl/default/images/'
    );

    // template override path
    $override_server_path = JPATH_SITE . '/templates/' . $frontpageTemplate . '/html/mod_profil_responsive_map';
    $override_website_path = 'templates/' . $frontpageTemplate. '/html/mod_profil_responsive_map';
    $override_administrator_path = JURI::root() . '/' . $override_website_path;
    // check if override paths are present, if so add the marker image if they are present too
    if (JFolder::exists($override_server_path)) {
      $map_templates = JFolder::folders($override_server_path);
      if (!empty($map_templates)) {
        foreach($map_templates as $map_template_key => $map_template_value) {
          if (JFolder::exists($override_server_path . '/' . $map_template_value . '/images')) {
            $marker_paths[] = array(
              'server_path' => $override_server_path . '/' . $map_template_value . '/images',
              'website_path' => $override_website_path . '/' . $map_template_value . '/images/',
              'administrator_path' => $override_administrator_path . '/' . $map_template_value . '/images/',
            );
          }
        }
      }
    }

    // output all coordinate blocks
    if (!empty($data)) {
      for($i = 0, $j = 0; $i < sizeof($data); $i+=5, $j++) {
        $output .= '<div id="geoDataHolder_' . $j . '" class="geoDataHolder well well-small">';
        $output .= '<div class="geoDataHolderWrapper"><div>' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_LATITUDE') . '</div>';
        $output .= '<input required aria-required="true" type="text" name="' . $this->name . '[]" class="'. $this->id . '_lat geoDataHolderLat input-medium" value="' . $data[$i] . '" />';
        $output .= '</div>';
        $output .= '<div class="geoDataHolderWrapper"><div>' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_LONGITUDE') . '</div>';
        $output .= '<input required aria-required="true" type="text" name="' . $this->name . '[]" class="'. $this->id . '_lng geoDataHolderLng input-medium" value="' . $data[$i + 1] . '" />';
        $output .= '</div>';
        $output .= '<div class="geoDataHolderWrapper"><a href="#geocodemap" class="btn fancymodal icon-apply geoDataGeocodeLink">' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_GEOCODE') . '</a></div>';

        $output .= '<div class="geoDataHolderWrapperSingle"><div>' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_LABEL') . '</div>';
        $output .= '<textarea name="' . $this->name . '[]" class="'. $this->id . '_label inputbox">' . $data[$i + 2] . '</textarea>';

        $output .= '<span class="geoDataHolderPopup">' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_POPUP') . '</span>';
        $output .= '<input type="checkbox" class="geoDataHolderPopupCheckbox" value="' . $data[$i + 3] . '" ' . (($data[$i + 3] == 1) ? 'checked="checked"' : '') . '/>';
        $output .= '<input type="hidden" class="geoDataHolderPopupInput" name="' . $this->name . '[]" value="' . $data[$i + 3] . '" />';

        $output .= '</div>';
        $marker_list = '<select name="' . $this->name . '[]" class="'.$this->id.'_icon geoDataHolderIcon" >';
        foreach ($marker_paths as $marker_paths_key => $marker_path) {
          $marker_icons = JFolder::files($marker_path['server_path'], '.png', TRUE);
          if(!empty($marker_icons)) {
            foreach ($marker_icons as $marker_icon_key => $marker_icon_name) {
              if (($marker_path['website_path'] . $marker_icon_name) == $data[$i + 4]) {
                $marker_list .= '<option selected="selected" value="' . $marker_path['website_path'] . $marker_icon_name . '" style="background-image:url(' . $marker_path['administrator_path'] .  $marker_icon_name . '); background-repeat: no-repeat; padding-left: 20px; background-position: left center;background-size:14px auto;">' . $marker_icon_name . '</option>';
              } else {
                $marker_list .= '<option value="' . $marker_path['website_path'] . $marker_icon_name . '" style="background-image:url(' . $marker_path['administrator_path'] .  $marker_icon_name . '); background-repeat: no-repeat; padding-left: 20px; background-position: left center;background-size:14px auto;">' . $marker_icon_name . '</option>';
              }
            }
          }
        }
        $marker_list .= '</select>';

        $output .= '<div class="geoDataHolderWrapper"><div>' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_ICON') . '</div>' . $marker_list . '</div>';
        if ($i > 0) {
          $output .= $remove_button;
        }

        $output .= '</div>';
      }
    // if no coordinates are present supply the default block without remove button
    } else {
      $output = '<div id="geoDataHolder_0" class="geoDataHolder well well-small">';
      $output .= '<div class="geoDataHolderWrapper"><div>' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_LATITUDE') . '</div>';
      $output .= '<input required aria-required="true" type="text" name="'.$this->name.'[]" class="'.$this->id.'_lat geoDataHolderLat input-medium" value="" />';
      $output .= '</div>';
      $output .= '<div class="geoDataHolderWrapper"><div>' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_LONGITUDE') . '</div>';
      $output .= '<input required aria-required="true" type="text" name="'.$this->name.'[]" class="'.$this->id.'_lng geoDataHolderLng input-medium" value="" />';
      $output .= '</div>';
      $output .= '<div class="geoDataHolderWrapper"><a href="#geocodemap" class="btn fancymodal icon-apply geoDataGeocodeLink">' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_GEOCODE') . '</a></div>';
      $output .= '<div class="geoDataHolderWrapperSingle"><div>' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_LABEL') . '</div>';
      $output .= '<textarea name="'.$this->name.'[]" class="'.$this->id.'_label inputbox"></textarea>';
      $output .= '<span>' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_POPUP') . '</span>';
      $output .= '<input type="checkbox" class="geoDataHolderPopupCheckbox" value="0" />';
      $output .= '<input type="hidden" class="geoDataHolderPopupInput" name="' . $this->name . '[]" value="0" />';
      $output .= '</div>';

      $marker_list = '<select name="' . $this->name . '[]" class="'.$this->id.'_icon geoDataHolderIcon" >';
      foreach ($marker_paths as $marker_paths_key => $marker_path) {
        $marker_icons = JFolder::files($marker_path['server_path'], '.png', TRUE);
        if(!empty($marker_icons)) {
          foreach ($marker_icons as $marker_icon_key => $marker_icon_name) {
            $marker_list .= '<option value="' . $marker_path['website_path'] . $marker_icon_name . '" style="background-image:url(' . $marker_path['administrator_path'] .  $marker_icon_name . '); background-repeat: no-repeat; padding-left: 20px; background-position: left center;background-size:14px auto;">' . $marker_icon_name . '</option>';

          }
        }
      }
      $marker_list .= '</select>';

      $output .= '<div class="geoDataHolderWrapper"><div>' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_ICON') . '</div>' . $marker_list . '</div>';
      $output .= '</div>';
    }

    $add_more_button = '<a href="#" id="geoDataAddMore" class="btn icon-save-new">' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_ADD_MORE') . '</a>';

    // content for the fancybox, displays an input field and a map to target the location easily
    $modal_geocode_map  = '<div class="hidden"><div id="geocodemap" class="' . $wrapper_class . '">';
    $modal_geocode_map .= '<input id="geoDataHolderLocation" name="geoDataHolderLocation" class="input-xlarge" type="text" size="30" maxlength="30" />';
    $modal_geocode_map .= '<input id="geoDataHolderLatitutde" name="geoDataHolderLatitutde" type="hidden" size="30" maxlength="30" value="lat-test" />';
    $modal_geocode_map .= '<input id="geoDataHolderLongitude" name="geoDataHolderLongitude" type="hidden" size="30" maxlength="30" value="lng-test" />';
    $modal_geocode_map .= '<input type="hidden" id="geoDataHolderId" name="geoDataHolderId" value="0" />';
    $modal_geocode_map .= '<input id="geocodemap_search" class="btn" type="button" value="' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_SEARCH') . '" />';
    $modal_geocode_map .= '<a href="#" id="geocodemap_submit" class="btn btn-success">' . JText::_('MOD_PROFIL_RESPONSIVE_MAP_APPLY') . '</a>';
    $modal_geocode_map .= '<div class="map_canvas" style="width: 100%; height: 400px;"></div>';
    $modal_geocode_map .= '</div></div>';

    return  '<div class="' . $wrapper_class . '">' . $output . $add_more_button . $modal_geocode_map . '</div>';
  }
}