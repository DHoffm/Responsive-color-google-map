<?php
/**
 * @version   1.0.2
 * @package   Profil responsive map (module)
 * @author    David Hoffmann - http://www.profilpr.de
 * @copyright Copyright (c) 2014 Profilpr. All rights reserved.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class ProfilResponsiveMapHelper {
  static $css_style = 'default';
  /*
   * function to add the jquery scripts and css necessary to run the map
   *
   */
  function show($coordinatesArray, $parameters, $template, $mod_name){
    // API
    $document = JFactory::getDocument();
    
    // get the coordinate data in a more readable way
    $coordinates = self::get_coordinates($coordinatesArray);
    $result = array();

    if (!empty($coordinates)) {

      // load jquery if necesarry
      JLoader::import( 'joomla.version' );
      $version = new JVersion();
      if (version_compare( $version->RELEASE, '2.5', '<=')) {
        if(JFactory::getApplication()->get('jquery') !== true) {
          // load jQuery here
          $document->addScript('https://code.jquery.com/jquery-1.11.0.min.js');
          JFactory::getApplication()->set('jquery', true);
        }
      } else {
          JHtml::_('jquery.framework');
      }

      // google api
      $document->addScript('http://maps.googleapis.com/maps/api/js?sensor=false&v=3.exp&libraries=weather');
      
      // jquery gmap script
      $document->addScript(JURI::base().'modules/' . $mod_name . '/js/jquery.gmap.js');
      
      // custom jquery to run the jquery gmap script
      $document->addScript(JURI::base().'modules/' . $mod_name . '/js/responsive_map.js');

      // load responsive_map.css from module or override path
      $filePath = dirname(JModuleHelper::getLayoutPath($mod_name, $template . DS . 'default'));
      $filePath = str_replace(JPATH_SITE, '', $filePath);
      $filePath .= '/css/responsive_map.css';
      if (file_exists(JPATH_SITE . $filePath)) {
        $document->addStyleSheet(JUri::root(true) . $filePath);
      }

      // unique id for every javascript map array
      $result['identifier'] = 'responsive_map_config_' . self::get_unique_identifier();

      // add coordinate data to javascript map array
      foreach ($coordinates as $key => $value) {
        $result['coordinates'][$key] = array(
          'latitude' => $value['latitude'], 
          'longitude' => $value['longitude'], 
          'label' => $value['label'],
          'popup' => ($value['popup'] ? true : false),
          'icon' => JURI::root() . '/' . $value['icon'],
          'icon_width' => $value['icon_width'],
          'icon_height' => $value['icon_height'],
        );
      }

      // add general settings to javascript map array
      $result['general'] = array( 
        'hue' => $parameters['color'], 
        'gamma' => $parameters['contrast'],
        'saturation' => $parameters['saturation'],
        'lightness' => $parameters['lightness'],
        'zoom' => (int)$parameters['zoom'], 
        'panControl' => ($parameters['panControl'] ? true : false),
        'zoomControl' => ($parameters['zoomControl'] ? true : false),
        'mapTypeControl' => ($parameters['mapTypeControl'] ? true : false),
        'scaleControl' => ($parameters['scaleControl'] ? true : false),
        'streetViewControl' => ($parameters['streetViewControl'] ? true : false),
        'scrollwheel' => ($parameters['scrollwheel'] ? true : false),
        'fullscreen' => ($parameters['fullscreen'] ? true : false),
        'fullscreenTitle' => JText::_('MOD_PROFIL_RESPONSIVE_FULLSCREEN_TITLE'),
        'fullscreenCloseTitle' => JText::_('MOD_PROFIL_RESPONSIVE_FULLSCREEN_CLOSE_TITLE'),
        'searchNoResultHeadline' => JText::_('MOD_PROFIL_RESPONSIVE_MAP_SEARCH_NO_RESULT_HEADLINE'),
        'searchNoResult' => JText::_('MOD_PROFIL_RESPONSIVE_MAP_SEARCH_NO_RESULT'),
        'searchLabel' => JText::_('MOD_PROFIL_RESPONSIVE_MAP_SEARCH_LABEL'),
        'searchLocationListHeadline' => JText::_('MOD_PROFIL_RESPONSIVE_MAP_SEARCH_LOCATION_LIST_HEADLINE'),
        'searchRouteLabel' => JText::_('MOD_PROFIL_RESPONSIVE_MAP_ROUTE_SEARCH_LABEL'),
        'routeNoResultHeadline' => JText::_('MOD_PROFIL_RESPONSIVE_MAP_ROUTE_NO_RESULT_HEADLINE'),
        'routeNoResult' => JText::_('MOD_PROFIL_RESPONSIVE_MAP_ROUTE_NO_RESULT'),
        'searchSubmit' => JText::_('MOD_PROFIL_RESPONSIVE_MAP_SEARCH_SUBMIT'),
        'bikemap' => ($parameters['bikemap'] ? true : false),
        'weathermap' => ($parameters['weathermap'] ? true : false),
        'weathermap_temperatureunit' => ($parameters['weathermap_temperatureunit'] ? 'fahrenheit' : 'celsius'),
        'cloudmap' => ($parameters['cloudmap'] ? true : false),
        'trafficmap' => ($parameters['trafficmap'] ? true : false),
        'visibility' => ($parameters['visibility'] ? 'simplified' : 'on'),
        'template' => $parameters['template'],
        'description' => (!empty($parameters['description']) ? '<div class="responsive_map_description">' . $parameters['description'] . '</div>' : '')
      );
      
      // add the javascript map array to the page
      $js = 'var ' . $result['identifier'] . '=' . json_encode( $result ) . ';';
      $document->addScriptDeclaration($js);
    }
    return $result;
  }

  /*
   * Function to retrieve all coordinate data (latitude, longitude, popup opened, icon, label)
   *
   */
  function get_coordinates($data) {
    $result = array();
    if (!empty($data)) {
      for($i = 0, $j = 0; $i < sizeof($data); $i+=5, $j++) {
        // do not add marker if coordinates are missing
        if (!empty($data[$i]) && !empty($data[$i + 1])) {
          $width = '';
          $height = '';
          if (!empty($data[$i + 4])) {
            list($width, $height) = getimagesize($data[$i + 4]);
          }
          $result[] = array(
            'latitude' => $data[$i],
            'longitude' => $data[$i + 1],
            'label' => $data[$i + 2],
            'popup' => $data[$i + 3],
            'icon' => $data[$i + 4],
            'icon_width' => $width,
            'icon_height' => $height);
        }
      }
    }
    return $result;
  }

  /*
   * function to generate a unique id
   *
   */
  function get_unique_identifier($length = '32') {
    $uniquekey ='';
    $code = array_merge(range('0', '9'), range('a', 'z'), range('A', 'Z'));
    mt_srand((double)microtime() * 1000000);
    if($length > 1024): $length = '1024'; endif;
    for ($i = 1; $i <= $length; $i++) {
      $swap = mt_rand(0, count($code) - 1);
      $uniquekey .= $code[$swap];
    }
    return $uniquekey;
  }
} // END CLASS
