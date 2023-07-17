<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketMessage{

    public static $recordid;
    function __construct(){
    }

    public static function getMessage($result,$entity){
        $msg = JText::_('Unknown');
        $entity =  strtoupper ($entity);
        switch ($result) {
            case POSTED:
                switch ($entity) {
                    case 'REPLY':$msg = JText::_('Reply has been posted');break;
                    case 'MESSAGE':$msg = JText::_('Mail has been Send');break;
                    case 'INTERNAL_NOTE':$msg = JText::_('Internal Note Has Been Posted');break;
                }
            break;
            case POST_ERROR:
                switch ($entity) {
                    case 'REPLY':$msg = JText::_('Reply has not been stored');break;
                    case 'MESSAGE':$msg = JText::_('Mail has not been send');break;
                    case 'INTERNAL_NOTE':$msg = JText::_('Internal Note has not been posted');break;
                }
            break;
            case TICKET_TRANSFERED:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = JText::_('Department has been transfered');break;
                    case 'STAFF':$msg = JText::_('Assigned to Staff');break;
                }
            break;
            case TICKET_TRANSFER_ERROR:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = JText::_('Department has not been transfered');break;
                    case 'STAFF':$msg = JText::_('Not assigned to staff');break;
                }
            break;
            case PRIORITY_CHANGED:$msg = JText::_('Priority has been changed');break;
            case PRIORITY_CHANGE_ERROR:$msg = JText::_('Priority has not been changed');break;
            case OTHER_USER_TASK:$msg = JText::_('You Are Not Allowed');break;
            case TICKET_ACTION_OK:
                switch ($entity) {
                    case 'CLOSE':$msg = JText::_('Ticket has been closed');break;
                    case 'REOPEN':$msg = JText::_('Ticket has been reopened');break;
                    case 'MARK_IN_PROGRESS':$msg = JText::_('Ticket has been marked as in progress');break;
                    case 'DELETE':$msg = JText::_('Ticket delete');break;
                    case 'MARK_OVERDUE':$msg = JText::_('Ticket has been marked as overdue');break;
                    case 'UN_MARK_OVERDUE':$msg = JText::_('Ticket has been unmarked as overdue');break;
                    case 'LOCK':$msg = JText::_('Ticket has been locked');break;
                    case 'UNLOCK':$msg = JText::_('Ticket has been unlocked');break;
                }
            break;
            case TICKET_ACTION_ERROR:
                switch ($entity) {
                    case 'ClOSE':$msg = JText::_('Ticket has not been closed');break;
                    case 'REOPEN':$msg = JText::_('Ticket has not been reopened');break;
                    case 'MARK_IN_PROGRESS':$msg = JText::_('Ticket has not been marked as in progress');break;
                    case 'DELETE':$msg = JText::_('Ticket has not been deleted');break;
                    case 'Mark Overdue':$msg = JText::_('Ticket has not been marked as overdue');break;
                    case 'LOCK':$msg = JText::_('Ticket has not been locked');break;
                    case 'UNLOCK':$msg = JText::_('Ticket has not been unlocked');break;
                }
            break;
            case FILE_SIZE_ERROR:$msg = JText::_('Error File Size Too Large');break;
            case FILE_EXTENTION_ERROR:$msg = JText::_('Error file ext mismatch');break;
            case TIME_LIMIT_END:$msg = JText::_('Ticket reopen time limit end');break;
            case EMAIL_NOT_EXIST:$msg = JText::_('Ban email does not exist');break;
            case INVALID_CAPTCHA:$msg = JText::_('Incorrect Captcha code');break;
            case LIMIT_EXCEED:$msg = JText::_('Limit exceeds maximum tickets');break;
            case LIMIT_EXCEED_OPEN:$msg = JText::_('Limit exceeds maximum open tickets');break;
            case FILE_RW_ERROR:
                $msg = JText::_('File read write issue files cannot upload');
            break;
            case MAIL_MARKED:
                switch ($entity) {
                    case 'READ':$msg = JText::_('Mail mark as read');break;
                    case 'UNREAD':$msg = JText::_('Mail mark as unread');break;
                }
            break;
            case MAIL_MARKED_ERROR:
                switch ($entity) {
                    case 'READ':$msg = JText::_('Mail cannot mark as read');break;
                    case 'UNREAD':$msg = JText::_('Mail cannot mark as unread');break;
                }
            break;
            case SENT_ERROR:
                switch ($entity) {
                    case 'MAIL':$msg = JText::_('Mail has not been send');break;
                    case 'MESSAGE':$msg = JText::_('Mail has not been send');break;
                    case 'REPLY':$msg = JText::_('Reply has not been posted');break;
                }
            break;
            case SENT:
                switch ($entity) {
                    case 'MAIL':$msg = JText::_('Mail has been send');break;break;
                    case 'MESSAGE':$msg = JText::_('Mail has been send');break;break;
                    case 'REPLY':$msg = JText::_('Reply has been posted');break;break;
                }
            case SET_DEFAULT:
                switch ($entity) {
                    case 'PRIORITY':$msg = JText::_('Priority has been make default');break;
                }
            break;
            case SET_DEFAULT_ERROR:
                switch ($entity) {
                    case 'PRIORITY':$msg = JText::_('Priority has not been make default');break;
                }
            break;
            case SAVED:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = JText::_('Department has been stored');break;
                    case 'EMAIL':$msg = JText::_('Email has been stored');break;
                    case 'EMAIL_TEMPLATE':$msg = JText::_('Email template has been stored');break;
                    case 'PRIORITY':$msg = JText::_('Priority has been stored');break;
                    case 'TICKET':$msg = JText::_('Ticket has been created');break;
                    case 'USER_FIELD':$msg = JText::_('Field has been stored');break;
                    case 'MESSAGE':$msg = JText::_('Reply has been posted');break;
                    case 'ANNOUNCEMENT':$msg = JText::_('The announcement has been stored');break;
                    case 'DEPARTMENT':$msg = JText::_('Department has been stored');break;
                    case 'DOWNLOAD':$msg = JText::_('Download has been stored');break;
                    case 'EMAIL':$msg = JText::_('Email has been stored');break;
                    case 'BAN_EMAIL':$msg = JText::_('Ban Email has been stored');break;
                    case 'EMAIL_TEMPLATE':$msg = JText::_('Email template has been stored');break;
                    case 'FAQ':$msg = JText::_('FAQ has been stored');break;
                    case 'HELP_TOPIC':$msg = JText::_('Help topic has been stored');break;
                    case 'CATEGORY':$msg = JText::_('Category has been stored');break;
                    case 'ARTICLE':$msg = JText::_('Knowledge base article has been stored');break;
                    case 'DEPARTMENT_PREMADE':$msg = JText::_('Premade department message has been stored');break;
                    case 'ROLE':$msg = JText::_('The role has been stored');break;
                    case 'STAFF':$msg = JText::_('Staff member has been stored');break;
                    case 'TICKETVIAEMAIL':$msg = JText::_('Ticket via email has been stored');break;
                    case 'TICKETMERGE':$msg = JText::_('Ticket has been successfully merged');break;
                    case 'GDPR':$msg = JText::_('Erase data request has been submitted');break;
                }
            break;
            case SAVE_ERROR:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = JText::_('Department has not been stored');break;
                    case 'EMAIL':$msg = JText::_('Email has not been stored');break;
                    case 'EMAIL_TEMPLATE':$msg = JText::_('Email template has not been stored');break;
                    case 'PRIORITY':$msg = JText::_('Priority has not been stored');break;
                    case 'TICKET':$msg = JText::_('Ticket has not been stored');break;
                    case 'USER_FIELD':$msg = JText::_('User field has not been stored');break;
                    case 'MESSAGE':$msg = JText::_('Mail has not been send');break;
                    case 'ANNOUNCEMENT':$msg = JText::_('The announcement has not been stored');break;
                    case 'DEPARTMENT':$msg = JText::_('Department has not been stored');break;
                    case 'DOWNLOAD':$msg = JText::_('Download has not been stored');break;
                    case 'EMAIL':$msg = JText::_('Email has not been stored');break;
                    case 'BAN_EMAIL':$msg = JText::_('Ban Email has not been stored');break;
                    case 'EMAIL_TEMPLATE':$msg = JText::_('Email template has not been stored');break;
                    case 'FAQ':$msg = JText::_('FAQ has not been stored');break;
                    case 'HELP_TOPIC':$msg = JText::_('Help topic has not been stored');break;
                    case 'CATEGORY':$msg = JText::_('Category has not been stored');break;
                    case 'ARTICLE':$msg = JText::_('Knowledge base article has not been stored');break;
                    case 'DEPARTMENT_PREMADE':$msg = JText::_('Premade department message has not been stored');break;
                    case 'ROLE':$msg = JText::_('Role has not been stored');break;
                    case 'STAFF':$msg = JText::_('Staff member has not been stored');break;
                    case 'TICKETVIAEMAIL':$msg = JText::_('Ticket via email has not been stored');break;
                    case 'TICKETMERGE':$msg = JText::_('Error occur while merging ticket');break;
                    case 'GDPR':$msg = JText::_('Erase data request has not been submitted');break;
                }
            break;
            case ALREADY_EXIST:
                switch ($entity) {
                    case 'BAN_EMAIL':$msg = JText::_('Email already banned');break;
                    case 'STAFF':$msg = JText::_('User already staff member');break;
                    case 'EMAIL':$msg = JText::_('Email already exist');break;
                    case 'TICKETVIAEMAIL':$msg = JText::_('Email already exist');break;
                }
            break;
            case DELETED:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = JText::_('Department has been deleted');break;
                    case 'EMAIL':$msg = JText::_('Email has been deleted');break;
                    case 'PRIORITY':$msg = JText::_('Priority has been deleted');break;
                    case 'TICKET':$msg = JText::_('Ticket has been deleted');break;
                    case 'USER_FIELD':$msg = JText::_('User field has been deleted');break;
                    case 'MESSAGE':$msg = JText::_('Mail Has Been Deleted');break;
                    case 'ANNOUNCEMENT':$msg = JText::_('The announcement has been deleted');break;
                    case 'DEPARTMENT':$msg = JText::_('Department has been deleted');break;
                    case 'DOWNLOAD':$msg = JText::_('Download has been deleted');break;
                    case 'EMAIL':$msg = JText::_('Email has been deleted');break;
                    case 'BAN_EMAIL':$msg = JText::_('Ban email has been deleted');break;
                    case 'FAQ':$msg = JText::_('FAQ has been deleted');break;
                    case 'HELP_TOPIC':$msg = JText::_('Help topic has been deleted');break;
                    case 'CATEGORY':$msg = JText::_('Category has been deleted');break;
                    case 'ARTICLE':$msg = JText::_('Article has been deleted');break;
                    case 'MAIL':$msg = JText::_('Mail has been deleted');break;
                    case 'DEPARTMENT_PREMADE':$msg = JText::_('Premade department message has been deleted');break;
                    case 'ROLE':$msg = JText::_('The role has been deleted');break;
                    case 'STAFF':$msg = JText::_('Staff member has been deleted');break;
                    case 'ATTACHMENT':$msg = JText::_('Attachment has been deleted');break;
                    case 'TICKETVIAEMAIL':$msg = JText::_('Email has been deleted');break;
                    case 'GDPR':$msg = JText::_('Erase data request has been deleted');break;
                    case 'ERASED':$msg = JText::_('User identifying data has been erased');break;
                }
            break;
            case DELETE_ERROR:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = JText::_('Department has not been deleted');break;
                    case 'EMAIL':$msg = JText::_('Email has not been deleted');break;
                    case 'PRIORITY':$msg = JText::_('Priority has not been deleted');break;
                    case 'TICKET':$msg = JText::_('Ticket has not been deleted');break;
                    case 'USER_FIELD':$msg = JText::_('User field has not been deleted');break;
                    case 'MESSAGE':$msg = JText::_('Mail has not been deleted');break;
                    case 'ANNOUNCEMENT':$msg = JText::_('The announcement has not been deleted');break;
                    case 'DEPARTMENT':$msg = JText::_('Department has not been deleted');break;
                    case 'DOWNLOAD':$msg = JText::_('Download has not been deleted');break;
                    case 'EMAIL':$msg = JText::_('Email has not been deleted');break;
                    case 'BAN_EMAIL':$msg = JText::_('Ban Email has not been deleted');break;
                    case 'FAQ':$msg = JText::_('FAQ has not been deleted');break;
                    case 'HELP_TOPIC':$msg = JText::_('Help topic has not been deleted');break;
                    case 'CATEGORY':$msg = JText::_('Category has not been deleted');break;
                    case 'ARTICLE':$msg = JText::_('Article has not been deleted');break;
                    case 'MAIL':$msg = JText::_('Mail has not been deleted');break;
                    case 'DEPARTMENT_PREMADE':$msg = JText::_('Premade department message has not been deleted');break;
                    case 'ROLE':$msg = JText::_('Role has not been deleted');break;
                    case 'STAFF':$msg = JText::_('Staff member has not been deleted');break;
                    case 'ATTACHMENT':$msg = JText::_('Attachment has not been deleted');break;
                    case 'TICKETVIAEMAIL':$msg = JText::_('Email has not been deleted');break;
                    case 'GDPR':$msg = JText::_('Erase data request has not been deleted');break;
                }
            break;
            case IN_USE:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = JText::_('Department in use cannot be deleted');break;
                    case 'CATEGORY':$msg = JText::_('Category in use cannot deleted');break;
                    case 'STAFF':$msg = JText::_('Staff member in use cannot deleted');break;
                    case 'TICKET':$msg = JText::_('Ticket in use cannot be deleted');break;
                    case 'HELP_TOPIC':$msg = JText::_('Help topic in use cannot deleted');break;
                    case 'ROLE':$msg = JText::_('Role in use cannot deleted');break;
                    case 'USER_FIELD':$msg = JText::_('User field in use cannot deleted');break;
                }
            break;
            case CANCEL:$msg = JText::_('Operation Cancel');break;
            case PERMISSION_ERROR:$msg = JText::_('You Are Not Allowed');break;
            case BANNED_EMAIL:$msg = JText::_('Banned email cannot create ticket');break;
            case DUE_DATE_ERROR:$msg = JText::_('Due Date Error Is Not Valid');break;
            case TICKET_EMAIL_BAN_ERROR:$msg = JText::_('Email has not been banned');break;
            case TICKET_EMAIL_BAN:$msg = JText::_('Email has been banned');break;
            case TICKET_EMAIL_UNBAN_ERROR:$msg = JText::_('Email has not been unbanned');break;
            case TICKET_EMAIL_UNBAN:$msg = JText::_('Email has been unbanned');break;
            case MESSAGE_EMPTY: $msg = JText::_('Message field cannot be empty'); break;
            case TIME_EDITED: $msg = JText::_('Time has been edited'); break;
            case REPLY_EDITED: $msg = JText::_('Time has been edited'); break;
            case TICKET_DUPLICATE: $msg = JText::_('Duplicate Ticket not allowed.'); break;
        }
        return $msg;
    }
}
?>
