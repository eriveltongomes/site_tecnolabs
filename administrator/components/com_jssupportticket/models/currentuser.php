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

class JSSupportticketCurrentUser{

    private $id;
    private $name;
    private $username;
    private $email;
    private $isguest;
    private $isstaff;
    private $isstaffdisable;
    private $staffid;
    private $isadmin;

    private function __construct() {

        $user = JFactory::getUser();
        $app = JFactory::getApplication();
        $this->id = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->isguest = $user->guest;
        $this->isadmin = $app->isClient('administrator');
        if($user->id != 0){
	        $staff = JSSupportTicketModel::getJSModel('staff')->isStaffMember($user->id);
	        $isstaffdisable = JSSupportTicketModel::getJSModel('staff')->isCurrentStaffDisabled();
	        if($staff){
	        	$this->isstaff = true;
	        	$this->staffid = $staff;
	        }
	    }
    }
    //getters
    function getId(){
        return $this->id;
    }
    function getName(){
        return $this->name;
    }
    function getUserName(){
        return $this->username;
    }
    function getEmail(){
        return $this->email;
    }
    function getIsGuest(){
        return $this->isguest;
    }
    function getIsStaff(){
        return $this->isstaff;
    }
    function getIsStaffDisable(){
        return $this->isstaffdisable;
    }
    function getIsAdmin(){
        return $this->isadmin;
    }
    function getStaffId(){
        return $this->staffid;
    }

    public static function getInstance()  // Singleton class concept
    {   

        static $inst = null;
        if ($inst === null) {
            $inst = new JSSupportticketCurrentUser();
        }
        return $inst;
    }

    public function checkUserPermission($task){
    	if($task){
	    	if($this->getIsStaff()){
                $per = JSSupportTicketModel::getJSModel('acl')->checkUserPermissionForTask($task,$this->getId());
                return $per;
	    	}
	    }
	    return false;
    }
    public function checkUserPermissionsByView($viewname){
        if($viewname){
            if($this->getIsStaff()){
                $per = JSSupportTicketModel::getJSModel('acl')->getUserPermissionsByView($this->getId(),$viewname);
                return $per;
            }
        }
        return false;
    }
}
?>
