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

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class JSSupportticketViewFeedback extends JSSupportTicketView
{
	function display($tpl = null){
        require_once(JPATH_COMPONENT."/views/common.php");
        if($layoutName == 'feedbacks'){
            $per_granted = false;
            $per_granted = false;
            $per = $user->checkUserPermission('View Feedback');
            if ($per == true){
                $per_granted = true;
                $subject= $mainframe->getUserStateFromRequest( $option.'subject', 'subject',    '', 'string' );
                $ticketid= $mainframe->getUserStateFromRequest( $option.'ticketid', 'ticketid',  '', 'string' );
                $staffid= $mainframe->getUserStateFromRequest( $option.'staffid', 'staffid',  '', 'string' );
                $from= $mainframe->getUserStateFromRequest( $option.'from', 'from',  '', 'string' );
                $departmentid= $mainframe->getUserStateFromRequest( $option.'departmentid', 'departmentid', '', 'string' );
                $result = $this->getJSModel('feedback')->getAllFeedbacks($subject,$ticketid,$staffid,$from,$departmentid,$limitstart,$limit);
                $total = $result[1];
                $this->feedbacks = $result[0];
                $this->lists = $result['2'];
                $pagination = new JPagination($total, $limitstart, $limit);
            }
            $this->per_granted = $per_granted;
        }elseif($layoutName == 'formfeedback'){
            // handling encoded token
            $jsticket = JFactory::getApplication()->input->get('jsticket',null);
            $id = 0;
            if($jsticket != null){
                $jsticket = base64_decode($jsticket);
                $array = explode(',', $jsticket);
                $ticketid = $array[0];
                $email = $array[1];
                $res = $this->getJSModel('ticket')->checkEmailAndTicketID($email,$ticketid);
                if($res == 1){
                    $id = $this->getJSModel('ticket')->getIdFromTrackingId($ticketid);
                }else{
                    $success_flag = 2;// ticket does not exsist
                }
                if($id != 0){
                    $feedback_flag = $this->getJSModel('feedback')->getIdFromFeedbackId($id);
                    if($feedback_flag){
                        $success_flag = 0;// feedback can be stored
                    }else{
                        $success_flag = 3;// feedback already exsists
                    }
                }
            }else{
                $success_flag = JFactory::getApplication()->input->get('successflag');
            }
            $this->successflag = $success_flag;
            $result = $this->getJSModel('feedback')->getFeedBackForFrom();
            $this->fieldordering = $result;
            $this->ticketid = $id;
        }
        $this->pagination = $pagination;

        parent::display($tpl);
	}
}
?>
