<?php
/**
 * @version		1.0
 * @package		Profil responsive map (module)
 * @author    David Hoffmann - http://www.profilpr.de
 * @copyright	Copyright (c) 2014 Profilpr. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

// a class for every map template is added via jquery (example: the template "headline_higlight" gets the class .headline_higlight_responsive_map )

// if you need to access some of the variables you can find a list of everything by outputting var_dump($output);

?>
<div id="<?php echo $output['identifier']; ?>" class="responsive_map"></div>
<?php echo $output['general']['description']; ?>
<div class="clr"></div>
