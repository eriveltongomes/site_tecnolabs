<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
 ^
 + Project: 	JS Tickets
 ^ 
*/
defined ('_JEXEC') or die('Not Allowed');
jimport('joomla.application.component.controller');

class JSSupportTicketControllerjssupportticket extends JSSupportTicketController{
	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}
	function logout() {
        $url = JFactory::getApplication()->input->get('return', '');
        $url = base64_decode($url);
        JFactory::getApplication()->logout(JFactory::getUser()->id);
        if(empty($url)) $url = "index.php";
        JFactory::getApplication()->redirect($url);
        }

	function display($cachable = false, $urlparams = false){
		$document =  JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->post->get('view','jssupportticket');
		$layoutName = JFactory::getApplication()->input->post->get('layout','controlpanel');
		$viewType = $document->getType();		
		$view = $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
}
?>

