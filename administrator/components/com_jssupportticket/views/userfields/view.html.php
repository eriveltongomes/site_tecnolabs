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
 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class JSSupportticketViewUserFields extends JSSupportTicketView{
	
	function display($tpl = null){
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
      	if($layoutName == 'userfields'){
            JToolBarHelper::title(JText::_('User Fields'));
            JToolBarHelper::addNew('adduserfield');
            JToolBarHelper::editList('adduserfield');
            JToolBarHelper::deleteList('Are you sure to delete','removeuserfields');
            $fieldfor = JFactory::getApplication()->input->get('ff',1);
            if ($fieldfor) $_SESSION['ffusr'] = $fieldfor; else $fieldfor = $_SESSION['ffusr'];
            $result =  $this->getJSModel('userfields')->getUserFields($fieldfor, $limitstart, $limit);
            $this->items = $result[0];
            $total = $result[1];
            $this->filter_fieldtitle = $result[2];
            if ( $total <= $limitstart ) $limitstart = 0;
            $pagination = new JPagination( $total, $limitstart, $limit );
	        $this->pagination= $pagination;
     	}elseif ($layoutName == 'formuserfield'){
			if (isset($_GET['cid'][0]))	
				$c_id = $_GET['cid'][0];
			else $c_id = '';
			if ($c_id == ''){
				$cids = JFactory::getApplication()->input->get('cid', array (0), 'post', 'array');
				$c_id = $cids[0];
			}
			if (is_numeric($c_id) == true) 
				$result =  $this->getJSModel('userfields')->getUserFieldbyId($c_id , 1);
			if (isset($_GET['ff'])) $fieldfor = $_GET['ff']; else $fieldfor = 1;
			if(!isset($_SESSION['ffusr'])){
				$_SESSION['ffusr'] = 1;
			}
			if ($fieldfor) $_SESSION['ffusr'] = $fieldfor; else $fieldfor = $_SESSION['ffusr'];
			if(isset($result['userfield'])) $this->userfield = $result['userfield'];
			if(isset($result['userfieldparams'])) $this->userfieldparams = $result['userfieldparams'];
			$this->fieldfor = $result['fieldfor'];
			$this->joomlaarticles = $result['joomlaarticles'];
	        $isNew=true;
			if ( is_numeric($c_id) ) $isNew = false;
			$text = ($isNew) ? JText::_('Add') : JText::_('Edit');
            JToolBarHelper::title(JText::_('User Fields') . ': <small><small>[ ' . $text . ' ]</small></small>');
            JToolBarHelper::save('saveuserfield','Save User field');
	    	if ($isNew) JToolBarHelper::cancel('canceluserfield');
	    	else JToolBarHelper::cancel('canceluserfield', 'Close');
    	}elseif($layoutName == 'fieldsordering'){
			JToolBarHelper::title(JText::_('Fields'));
			$fieldfor = JFactory::getApplication()->input->get('ff');
			if(!isset($_SESSION['ffusr'])){
				$_SESSION['ffusr'] = 1;
			}
			if ($fieldfor) $_SESSION['ffusr'] = $fieldfor; else $fieldfor = $_SESSION['ffusr'];
			$result =  $this->getJSModel('userfields')->getFieldOrderingForList($fieldfor);
			if($fieldfor == 1) {
				JToolBarHelper::title(JText::_('Ticket Fields'));
			}elseif($fieldfor == 2){
				JToolBarHelper::title(JText::_('Feedback Fields'));
			}
			$fields = $result[0];
			//$total = $result[1];
			$total = 0;
			if ( $total <= $limitstart ) $limitstart = 0;
			$pagination = new JPagination( $total, $limitstart, $limit );
	        $this->pagination= $pagination;
			$this->fields=$fields;
		}
        parent::display($tpl);
	}
}
?>
