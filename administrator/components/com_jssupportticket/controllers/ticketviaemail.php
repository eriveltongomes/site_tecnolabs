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

defined('_JEXEC') or die('Restricted access');

class JSSupportticketControllerTicketviaemail extends JSSupportTicketController
{
	function __construct() {
		parent::__construct();
	}
    function readEmailsAjax(){
        $result = $this->getJSModel('ticketviaemail')->readEmailsAjax();
        JFactory::getApplication()->close();
    }
    function readEmails() {
        // $today = date('Y-m-d');
        // $f = fopen(jssupportticket::$_path .  'mylogone.txt', 'a') or exit("Can't open $lfile!");
        // $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        //define current time
        // $time = date('H:i:s');
        // $message = ' start ';
        // fwrite($f, "$time ($script_name) $message\n");
        // $message = ' tve_enabled = 1 ';
        // fwrite($f, "$time ($script_name) $message\n");
        $config = $this->getJSModel('config')->getConfigs();
        $time = null;
        $runscript = false;
        $time = $this->getJSModel('config')->getEmailReadTime();
        // $message = ' time in configuration =  '.$time;
        // fwrite($f, "$time ($script_name) $message\n");
        if($time == null){
            $runscript = true;
        }else{
            $currenttime = time();
            $lastruntime = $time;
            $nextruntime = $lastruntime + $config['tve_emailreadingdelay'];
            if($currenttime >= $nextruntime){
                $runscript = true;
            }else{
                $runscript = false;
            }
        }
        if($runscript == true){
            $newtime = time();
			$this->getJSModel('config')->setEmailReadTime($newtime);
			$this->getJSModel('ticketviaemail')->getAllEmailsforticket();
        }
        JFactory::getApplication()->close();
    }

    function saveticketviaemail(){
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('ticketviaemail')->storeTicketviaEmail($data);
        $msg = JSSupportticketMessage::getMessage($result,'TICKETVIAEMAIL');
        if($result == 2){
            $msg = JText::_('Email address already in use. Please use any other email.');
        }
        $link = 'index.php?option=com_jssupportticket&c=ticketviaemail&layout=ticketviaemail';
        $this->setRedirect($link, $msg);
    }

    function delete() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('ticketviaemail')->delete_TicketviaEmail();
        $msg = JSSupportticketMessage::getMessage($result,'TICKETVIAEMAIL');
        $link = "index.php?option=com_jssupportticket&c=ticketviaemail&layout=ticketviaemail";
        $this->setRedirect($link, $msg);
    }

    function addnewticketviaemail() {
        $layoutName = JFactory::getApplication()->input->set('layout', 'ticketviaemailform');
        $this->display();
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'ticketviaemail';
        $layoutName = JFactory::getApplication()->input->get('layout', 'ticketviaemail');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}?>
