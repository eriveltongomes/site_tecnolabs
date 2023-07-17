<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldStyler extends JFormField {

    protected $type = 'styler';

    public function getLabel(){
		
	}

    public function getInput() {
		// Add styles
		$document = JFactory::getDocument();
		$document->addStyleSheet( JURI::root() . 'modules/mod_aikon_awesome_compare/assets/css/backend.css');

    }
}