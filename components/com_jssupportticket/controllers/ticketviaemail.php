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

    function readEmails() {
        // $today = date('Y-m-d');
        // $f = fopen(jssupportticket::$_path .  'mylogone.txt', 'a') or exit("Can't open $lfile!");
        // $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        //define current time
        // $time = date('H:i:s');
        // $message = ' start ';
        // fwrite($f, "$time ($script_name) $message\n");
        $config = $this->getJSModel('config')->getConfigs();
        // $message = ' tve_enabled = 1 ';
        // fwrite($f, "$time ($script_name) $message\n");
        $time = null;
        $runscript = false;
        $time = $this->getJSModel('config')->getEmailReadTime();
        // $message = ' time in configuration =  '.$time;
        // fwrite($f, "$time ($script_name) $message\n");
        if($time == null){
            $runscript = true;
        }else{
            $currenttime = time();
            $lastruntime = strtotime($time);
            $nextruntime = $currenttime + $config['tve_emailreadingdelay'];
            if($lastruntime >= $nextruntime){
                $runscript = false;
            }else{
                $runscript = true;
            }
        }
        if($runscript == true){
            $newtime = time();
			$this->getJSModel('config')->setEmailReadTime($newtime);
			$this->getJSModel('ticketviaemail')->getAllEmailsforticket();
        }
        JFactory::getApplication()->close();
    }

}?>
