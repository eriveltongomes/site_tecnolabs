<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

$this->i = JFactory::getApplication()->input->getInt('i');
$this->field = $this->get('component');

echo JHtml::_('jgrid.state', array(
	0 => array('setrequired', 'JYES', '', '', false, 'unpublish', 'unpublish'),
	1 => array('unsetrequired', 'JNO', '', '', false, 'publish', 'publish')
), $this->field->required, $this->i, 'components.', true, true, 'legacycb');