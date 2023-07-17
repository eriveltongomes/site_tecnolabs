<?php

/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
  + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 03, 2012
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.controller');

class JSSupportticketControllerEmailBanlistLog extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function display($cachable = false, $urlparams = false) {
        $view = $this->getView('emailbanlistlog', JFactory::getDocument()->getType());
        $view->setLayout($layoutName = JFactory::getApplication()->input->get('layout', 'emailbanlistlog'));
        $view->display();
    }
}
?>
