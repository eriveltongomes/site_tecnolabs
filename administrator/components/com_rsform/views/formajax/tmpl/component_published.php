<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

$this->i = JFactory::getApplication()->input->getInt('i');
$this->field = $this->get('component');

echo JHtml::_('jgrid.published', $this->field->published, $this->i, 'components.', true, 'legacycb');