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

class JSSupportticketControllerProinstaller extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function getversionlist() {
        $data =  JFactory::getApplication()->input->post->getArray();
        $response = $this->getJSModel('proinstaller')->getmyversionlist($data);
        $response = base64_encode($response);
        $_SESSION['response'] = $response;
        $response = base64_decode($response);
        $response = json_decode($response);
        if($response[0] == true){
            $url = "index.php?option=com_jssupportticket&c=proinstaller&layout=step2";    
        }else{
            $url = "index.php?option=com_jssupportticket&c=proinstaller&layout=step1";
        }
        $this->setRedirect($url);
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $jinput = JFactory::getApplication()->input;
        $viewName = $jinput->get('view', 'proinstaller');
        $layoutName = $jinput->get('layout', 'step1');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }
}
?>
