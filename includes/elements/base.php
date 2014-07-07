<?php
/**
 * @version   1.1.1
 * @package   Responsive color google map (module)
 * @author    David Hoffmann - http://www.profilpr.de
 * @copyright Copyright (c) 2014 Profilpr. All rights reserved.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die ;

if (version_compare(JVERSION, '1.6.0', 'ge')) {
  jimport('joomla.form.formfield');
  class ProfilResponsiveMapElement extends JFormField
  {
    function getInput() {
      return $this->fetchElement($this->name, $this->value, $this->element, $this->options['control']);
    }
    function getLabel() {
      if (method_exists($this, 'fetchTooltip')) {
        return $this->fetchTooltip($this->element['label'], $this->description, $this->element, $this->options['control'], $this->element['name'] = '');
      } else {
        return parent::getLabel();
      }
    }
    function render() {
      return $this->getInput();
    }
  }
} else {
  jimport('joomla.html.parameter.element');
  class ProfilResponsiveMapElement extends JElement { }
}
