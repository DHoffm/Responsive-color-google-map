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

// a class for every map template is added via jquery (example: the template "headline_higlight" gets the class .headline_higlight_responsive_map )

// if you need to access some of the variables you can find a list of everything by outputting var_dump($output);

// custom jquery to run the jquery gmap script
$document =& JFactory::getDocument();
// if your are overriding this file you might change the path to the jquery file to:
// $document->addScript(T3_TEMPLATE_URL . '/html/mod_profil_responsive_map/search/js/responsive_map_search.js');
$document->addScript(JURI::base() . 'modules/' . $mod_name . '/tmpl/route/js/responsive_map_route.js');
?>
<div class="route_responsive_map_wrapper">
<form class="responsive_map_route_form">
<div class="responsive_map_route_field_wrapper">
<div><?php echo $output['general']['searchRouteLabel']; ?></div>
<input class="form-control responsive_map_route_search_<?php echo $output['identifier']; ?>" type="text" value="" />
<?php if (!empty($output['coordinates'])) : ?>
<select class="responsive_map_route_destination_<?php echo $output['identifier']; ?>">
<?php foreach ($output['coordinates'] as $key => $value): ?>
<option value="<?php echo $value['latitude'] . ',' . $value['longitude']; ?>"><?php echo $value['label']; ?></option>
<?php endforeach ?>;
</select>
<?php endif; ?>
<input class="btn responsive_map_route_submit" type="submit" value="<?php echo $output['general']['searchSubmit']; ?>" />
</div>
</form>
<div id="<?php echo $output['identifier']; ?>" class="responsive_map"></div>

<?php echo $output['general']['description']; ?>

<div class="route_list_item_<?php echo $output['identifier']; ?>" id="route_list_item_<?php echo $output['identifier']; ?>"></div>


<div class="clr"></div>
</div>