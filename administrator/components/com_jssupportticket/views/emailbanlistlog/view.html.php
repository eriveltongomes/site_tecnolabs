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

class JSSupportticketViewEmailBanlistLog extends JSSupportTicketView
{
	function display($tpl = null)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
		JToolBarHelper::title(JText::_('Banlist log'));
        if($layoutName == 'emailbanlistlog'){
        	
			$emailaddress = JFactory::getApplication()->input->getString('filter_email_address',null);
			$startdate = JFactory::getApplication()->input->getDate('filter_start_date');
			$enddate = JFactory::getApplication()->input->getDate('filter_end_date');

			if(empty($startdate) && empty($enddate)){
				$startdate = date('Y-m-d',strtotime('now -1 month'));
				$enddate = date('Y-m-d');
			}else{
				//to convert dates in mysql format
				$config = $this->getJSModel('config')->getConfigs();
		        $dateformat = $config['date_format'];
		        if ($dateformat == 'm-d-Y') {
		          $arr = explode('-', $startdate);
		          $startdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
		          $arr = explode('-', $enddate);
		          $enddate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
		        } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
		          $arr = explode('-', $startdate);
		          $startdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
		          $arr = explode('-', $enddate);
		          $enddate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
		        }
			}
			
			$statusid = $mainframe->getUserStateFromRequest($option.'filter_ht_statusid', 'filter_ht_statusid', '', 'int');
			$result = $this->getJSModel('emailbanlistlog')->getBanlistLog($startdate,$enddate,$emailaddress,$limitstart, $limit);
			$total = $result[1];
			if ( $total <= $limitstart ) $limitstart = 0;
			$pagination = new JPagination( $total, $limitstart, $limit );
			$this->banlistlog = $result[0];
			$this->lists = $result[2];
		}
		$this->pagination = $pagination;
		
		parent::display($tpl);
	}
}
?>
