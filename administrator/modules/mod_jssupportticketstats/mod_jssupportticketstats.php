 <?php
/**
 + Created by:	Ahmad Bilal
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
				www.joomsky.com, ahmad@joomsky.com
 * Created on:	Dec 2, 2009
 ^
 + Project: 		JS Jobs 
 * File Name:	module/hotjsjobs.php
 ^ 
 * Description: Module for JS Jobs
 ^ 
 * History:		1.0.2 - Nov 27, 2010
 ^ 
 */

defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion < 3){
	$document->addScript('components/com_jssupportticket/js/jquery.js');
	JHtml::_('behavior.mootools');
}else{
	JHtml::_('bootstrap.framework');
	JHtml::_('jquery.framework');
}
$document->addStyleSheet(JUri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');
$document->addStyleSheet(JUri::root() . 'administrator/components/com_jssupportticket/include/css/bootstrap.min.css');
$lang = JFactory::getLanguage();
$lang->load('com_jssupportticket', JPATH_ADMINISTRATOR, null, true);
$componentPath =  JPATH_ADMINISTRATOR.'/components/com_jssupportticket/';
require_once $componentPath.'JSApplication.php';

$data = JSSupportTicketModel::getJSModelForAdminMP('jssupportticket')->getTicketsSummaryForAdminModule();

?>

<div class="row-striped">
	<div class="row-fluid">
	    <div id="js-stat-box" class="js-admin-controlpanel">
	    	<a href="index.php?option=com_jssupportticket&c=ticket&view=ticket&layout=tickets">
	        <div class="js-col-md-3 js-box js-col-md-offset-2 box1">
	            <div class="js-col-md-4 js-box-image">
	                <img src="components/com_jssupportticket/include/images/report/ticket_icon.png" />
	            </div>
	            <div class="js-col-md-8 js-box-content">
	                <div class="js-col-md-12 js-box-content-number"><?php echo $data['new'];?></div>
	                <div class="js-col-md-12 js-box-content-label"><?php echo JText::_("New");?></div>
	            </div>
	            <div class="js-col-md-12 js-box-label"></div>
	        </div></a>
	        <a href="index.php?option=com_jssupportticket&c=ticket&view=ticket&layout=tickets">
	        <div class="js-col-md-3 js-box box2">
	            <div class="js-col-md-4 js-box-image">
	                <img src="components/com_jssupportticket/include/images/report/ticket_answered.png" />
	            </div>
	            <div class="js-col-md-8 js-box-content">
	                <div class="js-col-md-12 js-box-content-number"><?php echo $data['answered'];?></div>
	                <div class="js-col-md-12 js-box-content-label"><?php echo JText::_("Answered");?></div>
	            </div>
	            <div class="js-col-md-12 js-box-label"></div>
	        </div></a>
	        <a href="index.php?option=com_jssupportticket&c=ticket&view=ticket&layout=tickets">
	        <div class="js-col-md-3 js-box box3">
	            <div class="js-col-md-4 js-box-image">
	                <img src="components/com_jssupportticket/include/images/report/ticket_pending.png" />
	            </div>
	            <div class="js-col-md-8 js-box-content">
	                <div class="js-col-md-12 js-box-content-number"><?php echo $data['pending'];?></div>
	                <div class="js-col-md-12 js-box-content-label"><?php echo JText::_("Pending");?></div>
	            </div>
	            <div class="js-col-md-12 js-box-label"></div>
	        </div></a>
	        <a href="index.php?option=com_jssupportticket&c=ticket&view=ticket&layout=tickets">
	        <div class="js-col-md-3 js-box box4">
	            <div class="js-col-md-4 js-box-image">
	                <img src="components/com_jssupportticket/include/images/report/ticket_overdue.png" />
	            </div>
	            <div class="js-col-md-8 js-box-content">
	                <div class="js-col-md-12 js-box-content-number"><?php echo $data['overdue'];?></div>
	                <div class="js-col-md-12 js-box-content-label"><?php echo JText::_("Overdue");?></div>
	            </div>
	            <div class="js-col-md-12 js-box-label"></div>
	        </div></a>
	    </div>
	</div>
</div>
