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

$data = JSSupportTicketModel::getJSModelForAdminMP('jssupportticket')->getLatestTicketsAdminModule();

?>

<div class="row-striped">
	<div class="row-fluid">
        <div class="js-tickets-admin-cp-module-tickets">
            <div class="js-row js-tickets-admin-cp-module-head js-ticket-admin-hide-head">
                <div class="js-col-xs-12 js-col-md-2"><?php echo JText::_('Ticket Id'); ?></div>
                <div class="js-col-xs-12 js-col-md-3"><?php echo JText::_('Subject'); ?></div>
                <div class="js-col-xs-12 js-col-md-1"><?php echo JText::_('Status'); ?></div>
                <div class="js-col-xs-12 js-col-md-2"><?php echo JText::_('From'); ?></div>
                <div class="js-col-xs-12 js-col-md-2"><?php echo JText::_('Priority'); ?></div>
                <div class="js-col-xs-12 js-col-md-2"><?php echo JText::_('Created'); ?></div>
            </div>
            <?php foreach ($data['tickets'] AS $ticket): ?>
                <div class="js-tickets-admin-cp-module-data">
                    <div class="js-col-xs-12 js-col-md-2"><span class="js-tickets-admin-cp-module-showhide"><?php echo JText::_('Ticket Id');
            echo " : "; ?></span> <a href="index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=<?php echo $ticket->id; ?>"><?php echo $ticket->ticketid; ?></a></div>
                    <div class="js-col-xs-12 js-col-md-3 js-admin-cp-text-elipses"><span class="js-tickets-admin-cp-module-showhide" ><?php echo JText::_('Subject');
            echo " : "; ?></span> <?php echo $ticket->subject; ?></div>
                    <div class="js-col-xs-12 js-col-md-1">
                        <span class="js-tickets-admin-cp-module-showhide" ><?php echo JText::_('Status');
            echo " : "; ?></span>
                        <?php
                        if ($ticket->status == 0) {
                            $style = "red;";
                            $status = JText::_('New');
                        } elseif ($ticket->status == 1) {
                            $style = "orange;";
                            $status = JText::_('Waiting Staff Reply');
                        } elseif ($ticket->status == 2) {
                            $style = "#FF7F50;";
                            $status = JText::_('In Progress');
                        } elseif ($ticket->status == 3) {
                            $style = "green;";
                            $status = JText::_('Waiting Your Reply');
                        } elseif ($ticket->status == 4) {
                            $style = "blue;";
                            $status = JText::_('Closed');
                        }
                        echo '<span style="color:' . $style . '">' . $status . '</span>';
                        ?>
                    </div>
                    <div class="js-col-xs-12 js-col-md-2"> <span class="js-tickets-admin-cp-module-showhide" ><?php echo JText::_('From');
                        echo " : "; ?></span> <?php echo $ticket->name; ?></div>
                    <div class="js-col-xs-12 js-col-md-2" style="color:<?php echo $ticket->prioritycolour; ?>;"> <span class="js-tickets-admin-cp-module-showhide" ><?php echo JText::_('Priority');
            echo " : "; ?></span> <?php echo JText::_($ticket->priority); ?></div>
                    <div class="js-col-xs-12 js-col-md-2"><span class="js-tickets-admin-cp-module-showhide" ><?php echo JText::_('Created');
            echo " : "; ?></span> <?php echo JHtml::_('date',$ticket->created,$data['date_format']); ?></div>
                </div>
        <?php endforeach; ?>
        </div>

	</div>
</div>