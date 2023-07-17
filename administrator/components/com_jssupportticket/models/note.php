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

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelNote extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function getDownloadAttachmentById($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDbo();
        $query = "SELECT ticket.id AS ticketid,note.filename,ticket.attachmentdir AS foldername  "
                . " FROM `#__js_ticket_notes` AS note "
                . " JOIN `#__js_ticket_tickets` AS ticket ON ticket.id = note.ticketid "
                . " WHERE note.id = $id";
        $db->setQuery($query);
        $object = $db->loadObject();
        $ticketid = $object->ticketid;
        $filename = $object->filename;
        $foldername = $object->foldername;
        $download = false;
        $user = JFactory::getUser();
        if(!$user->guest){
            if(JFactory::getApplication()->isClient('administrator')){
                $download = true;
            }else{
                if($this->getJSModel('staff')->isUserStaff()){
                    $download = true;
                }else{
                    if($this->getJSModel('ticket')->validateTicketDetailForUser($ticketid)){
                        $download = true;
                    }
                }
            }
        }else{ // user is visitor
            $download = $this->getJSModel('ticket')->validateTicketDetailForVisitor($ticketid);
        }
        if($download == true){
            $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
            $base = JPATH_BASE;
            if(JFactory::getApplication()->isClient('administrator')){
                $base = substr($base, 0, strlen($base) - 14); //remove administrator
            }
            $path = $base.'/'.$datadirectory;
            $path = $path . '/attachmentdata';
            $path = $path . '/ticket/' . $foldername;
            $file = $path . '/' . $filename;
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit();
        }else{
            throw new Exception(JText::_('Page not found'),404);
            exit;
        }
    }

    function uploadAttchment($id){
        if (is_numeric($id) == false) return false;
        $isupload = false;
        $config = $this->getJSModel('config')->getConfigs();
        $datadirectory = $config['data_directory'];
        $base = JPATH_BASE;
        if(JFactory::getApplication()->isClient('administrator')){
            $base = substr($base, 0, strlen($base) - 14); //remove administrator
        }
        $path = $base.'/'.$datadirectory;
        if (!file_exists($path)){ // create user directory
            $this->makeDir($path);
        }
        $path = $path . '/attachmentdata';
        if (!file_exists($path)){ // create user directory
            $this->makeDir($path);
        }
        $path = $path . '/ticket';
        if (!file_exists($path)){ // create user directory
            $this->makeDir($path);
        }
        $db = JFactory::getDbo();
        $query = "SELECT attachmentdir FROM `#__js_ticket_tickets` WHERE id = ".$id;
        $db->setQuery($query);
        $foldername = $db->loadResult();
        if($_FILES['noteattachment']['size'] > 0){
            $file_name = str_replace(' ', '_', $_FILES['noteattachment']['name']);
            $file_tmp = $_FILES['noteattachment']['tmp_name']; // actual location
            $userpath = $path . '/'.$foldername;
            if (!file_exists($userpath)) { // create user directory
                $this->makeDir($userpath);
            }
            $isupload = true;
        }
        if ($isupload == true){
            move_uploaded_file($file_tmp, $userpath.'/' . $file_name);
            return true;
        }
        return false;
    }

    function checkExtension($filename){
        $result = $this->getJSModel('attachments')->checkExtension($filename);
        return $result;
    }

    function makeDir($filepath){
        $result = $this->getJSModel('attachments')->makeDir($filepath);
        return $result;
    }

    function getTimeByNoteID() {
        $db = JFactory::getDbo();
        $noteid = JFactory::getApplication()->input->get('val');
        if(!is_numeric($noteid)) return false;
        $query = "SELECT time.usertime, time.conflict, time.description,time.systemtime
                    FROM `#__js_ticket_staff_time` AS time
                    WHERE time.referencefor = 2 AND time.referenceid =  " . $noteid ;
        $db->setQuery($query);
        $stime = $db->loadObject();
        if(!empty($stime)){
            $hours = floor($stime->usertime / 3600);
            $mins = floor($stime->usertime / 60 % 60);
            $secs = floor($stime->usertime % 60);

            $shours = floor($stime->systemtime / 3600);
            $smins = floor($stime->systemtime / 60 % 60);
            $ssecs = floor($stime->systemtime % 60);
            $result['time'] =  sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
            $result['desc'] =  $stime->description == '' ? ' ' : $stime->description ;
            $result['conflict'] =  $stime->conflict;
            $result['systemtime'] =  sprintf('%02d:%02d:%02d', $shours, $smins, $ssecs);
        }else{
            $result['time'] =  sprintf('%02d:%02d:%02d', 0, 0, 0);
            $result['desc'] =  '' ;
            $result['conflict'] =  0;
            $result['systemtime'] =  sprintf('%02d:%02d:%02d', 0, 0, 0);
        }
        return json_encode($result);
    }
    function removeTicketInternalNote($ticketid) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $condition = array($db->quoteName('ticketid') . ' = ' . $ticketid);
        $query->delete('#__js_ticket_notes');
        $query->where($condition);
        $db->setQuery($query);
        $db->execute();
        return;
    }
}

?>
