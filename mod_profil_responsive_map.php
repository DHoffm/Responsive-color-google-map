<?php
/**
 * @version		1.1.1
 * @package		Profil responsive map(module)
 * @author    David Hoffmann - http://www.profilpr.de
 * @copyright	Copyright (c) 2014 Profilpr. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

// Profil reference parameters
$mod_name = "mod_profil_responsive_map";

// Conventions
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// Module parameters
$template = $params->get('profilResponsiveMapTemplates','default');
$coordinates = $params->get('profilResponsiveMapGeoCoordinates');
$parameters['color'] = $params->get('profilResponsiveMapColor');
$parameters['contrast'] = $params->get('profilResponsiveMapContrast');
$parameters['saturation'] = $params->get('profilResponsiveMapSaturation');
$parameters['lightness'] = $params->get('profilResponsiveMapLightness');
$parameters['zoom'] = $params->get('profilResponsiveMapZoom');
$parameters['panControl'] = $params->get('profilResponsivePanControl');
$parameters['zoomControl'] = $params->get('profilResponsiveZoomControl');
$parameters['mapTypeControl'] = $params->get('profilResponsiveMapTypeControl');
$parameters['mapType'] = $params->get('profilResponsiveMapType');
$parameters['scaleControl'] = $params->get('profilResponsiveScaleControl');
$parameters['streetViewControl'] = $params->get('profilResponsiveStreetViewControl');
$parameters['scrollwheel'] = $params->get('profilResponsiveScrollwheel');
$parameters['visibility'] = $params->get('profilResponsiveVisibility');
$parameters['description'] = $params->get('profilResponsiveDescription');
$parameters['fullscreen'] = $params->get('profilResponsiveFullscreen');
$parameters['bikemap'] = $params->get('profilResponsiveBikemap');
$parameters['weathermap'] = $params->get('profilResponsiveWeathermap');
$parameters['weathermap_temperatureunit'] = $params->get('profilResponsiveWeathermapTemperatureunit');
$parameters['cloudmap'] = $params->get('profilResponsiveCloudmap');
$parameters['trafficmap'] = $params->get('profilResponsiveTrafficmap');
$parameters['template'] = $template;
// Includes
require_once(dirname(__FILE__) . DS . 'helper.php');

// Fetch content
$responsive_map = new ProfilResponsiveMapHelper;
$output = $responsive_map->show($coordinates, $parameters, $template, $mod_name);

// Output content with template
require(JModuleHelper::getLayoutPath($mod_name, $template . DS . 'default'));
// END
