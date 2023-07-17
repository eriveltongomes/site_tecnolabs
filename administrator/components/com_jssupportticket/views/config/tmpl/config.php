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

global $mainframe;

$document = JFactory::getDocument();

if (JVERSION < 3) {
    JHtml::_('behavior.mootools');
    $document->addScript('components/com_jssupportticket/include/js/jquery.js');
} else {
    JHtml::_('bootstrap.framework');
    JHtml::_('jquery.framework');
}
// $document->addScript('components/com_jssupportticket/include/js/jquery_idTabs.js');

$captchaselection = array(
    array('value' => '1', 'text' => JText::_('Google Recaptcha')),
    array('value' => '2', 'text' => JText::_('Own Captcha'))
);
$owncaptchaoparend = array(
    array('value' => '2', 'text' => JText::_('2')),
    array('value' => '3', 'text' => JText::_('3'))
);
$owncaptchatype = array(
    array('value' => '0', 'text' => JText::_('Any')),
    array('value' => '1', 'text' => JText::_('Addition')),
    array('value' => '2', 'text' => JText::_('Subtraction'))
);


$date_format = array(
    '0' => array('value' => 'd-m-Y', 'text' => JText::_('DD-MM-YYYY')),
    '1' => array('value' => 'm-d-Y', 'text' => JText::_('MM-DD-YYYY')),
    '2' => array('value' => 'Y-m-d', 'text' => JText::_('YYYY-MM-DD')),);

$yesno = array(
    '0' => array('value' => '1',
        'text' => JText::_('Yes')),
    '1' => array('value' => '0',
        'text' => JText::_('No')),);


$overduetype_array = array(
    '0' => array('value' => '1',
        'text' => JText::_('Days')),
    '1' => array('value' => '2',
        'text' => JText::_('Hours')),);


$enableddisabled = array(
    '0' => array('value' => '1',
        'text' => JText::_('Enabled')),
    '1' => array('value' => '0',
        'text' => JText::_('Disabled')),);

$showhide = array(
    '0' => array('value' => '1',
        'text' => JText::_('Show')),
    '1' => array('value' => '0',
        'text' => JText::_('Hide')),);
$ticketidsequence = array(
    '0' => array('value' => '1',
        'text' => JText::_('Random')),
    '1' => array('value' => '2',
        'text' => JText::_('Sequential')),);

$ticketsorting = array(
    '0' => array('value' => '1',
        'text' => JText::_('Ascending')),
    '1' => array('value' => '2',
        'text' => JText::_('Descending')),);

$maxticketinterval = array(
    '0' => array('value' => '1',
        'text' => JText::_('Day')),
    '1' => array('value' => '2',
        'text' => JText::_('Month')),
    '2' => array('value' => '3',
        'text' => JText::_('Year')),
    '3' => array('value' => '4',
        'text' => JText::_('Life Time')));

$offline = JHTML::_('select.genericList', $yesno, 'offline', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['offline']);

$curlocation = JHTML::_('select.genericList', $yesno, 'cur_location', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cur_location']);

$date_format = JHTML::_('select.genericList', $date_format, 'date_format', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['date_format']);


$overduetype = JHTML::_('select.genericList', $overduetype_array, 'ticket_overdue_type', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_type']);


$big_field_width = 40;
$med_field_width = 25;
$sml_field_width = 15;
?>

<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="jsstadmin-wrapper-top">
            <div id="jsstadmin-wrapper-top-left">
                <div id="jsstadmin-breadcrunbs">
                    <ul>
                        <li><a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard"><?php echo JText::_('Dashboard'); ?></a></li>
                        <li><?php echo JText::_('Configurations'); ?></li>
                    </ul>
                </div>
            </div>
            <div id="jsstadmin-wrapper-top-right">
                <div id="jsstadmin-config-btn">
                    <a title="Configuration" href="index.php?option=com_jssupportticket&c=config&layout=config">
                        <img alt="Configuration" src="components/com_jssupportticket/include/images/config.png">
                    </a>
                </div>
                <div id="jsstadmin-vers-txt">
                    <?php echo JText::_('Version').JText::_(' : '); ?>
                    <span class="jsstadmin-ver">
                        <?php $version = str_split($this->version);
                        $version = implode('.', $version);
                        echo $version; ?>
                    </span>
                </div>
            </div>
        </div>
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text">
                <?php echo JText::_('Configurations'); ?>
            </h1>
        </div>
        <?php
			$adminEmail = JSSupportTicketModel::getJSModel('email')->getEmailById($this->configuration['admin_email']);
			$ticketviaemailaddress = $this->configuration['tve_emailaddress'];
			if($adminEmail == $ticketviaemailaddress){
        ?>
			<div id="js-emailsame-error">
				<?php echo JText::_('Admin email address and ticket via email (email address) cannot be same, your ticket via email will not be work.'); ?>
			</div>
        <?php } ?>
        <form action="index.php" class="jsstadmin-data-wrp jsstadmin-bg-color js-ticket-box-shadow" method="POST" name="adminForm" id="adminForm">
            <div id="tabs_wrapper" class="tabs_wrapper js-col-lg-12 js-col-md-12">
                <div class="idTabs">
                    <span><a id="generalsettingbtn" class="tab selected" ><?php echo JText::_('General Settings'); ?></a></span>
                    <span><a id="ticketsettingbtn" class="tab"><?php echo JText::_('Ticket Settings'); ?></a></span>
                    <span><a id="emialsettingbtn" class="tab"><?php echo JText::_('Default System Email'); ?></a></span>
                    <span><a id="auotrespondersettingbtn" class="tab"><?php echo JText::_('Mail Settings'); ?></a></span>
                    <span><a id="usermenusettingbtn" class="tab"><?php echo JText::_('Staff Menu Settings'); ?></a></span>
                    <span><a id="vismenusettingbtn" class="tab"><?php echo JText::_('Visitor Menu Settings'); ?></a></span>
                    <span><a id="feedbacksettingsbtn" class="tab"><?php echo JText::_('Feedback Settings'); ?></a></span>
                    <span><a id="ticketviaemailbtn" class="tab"><?php echo JText::_('Ticket Via Email'); ?></a></span>
                    <span><a id="privatecredentialsbtn" class="tab"><?php echo JText::_('Private Credentials'); ?></a></span>
                </div>
                <div id="generalsetting" style="display: none;">
                        <legend><?php echo JText::_('General Setting'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Title'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><input type="text" name="title" value="<?php echo $this->configuration['title']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" maxlength="255" /></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Offline'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><?php echo $offline; ?></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Offline Message'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><textarea name="offline_text" cols="25" rows="3" class="inputbox"><?php echo $this->configuration['offline_text']; ?></textarea> </div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Data Directory'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="data_directory" value="<?php echo $this->configuration['data_directory']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>"/> </div>
                                 <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('You need to rename the existing data directory in the file system before changing the data directory name'); echo '<br/><b>"'.JPATH_SITE.$this->configuration['data_directory'].'"</b>'; ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Date Format'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><?php echo $date_format; ?></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Control Panel Column'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="controlpanel_column_count" value="<?php echo $this->configuration['controlpanel_column_count']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Columns'); ?><br><?php echo JText::_('Values Between 1 T0 12 Default Is 3'); ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row js-mg-bottom">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Ticket auto-close'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="ticket_auto_close_indays" value="<?php echo $this->configuration['ticket_auto_close_indays']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Days'); ?><br><?php echo JText::_('Ticket auto-close if the user does not respond within the given number of days'); ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('No. of attachment'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="noofattachment" value="<?php echo $this->configuration['noofattachment']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><br clear="all"/><small><?php echo JText::_('No. of attachment allowed at a time'); ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('File maximum size'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><input type="text" name="filesize" value="<?php echo $this->configuration['filesize']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /> &nbsp;KB</div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('File extension'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><textarea name="fileextension" cols="25" rows="3" class="inputbox"><?php echo $this->configuration['fileextension']; ?></textarea></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('File extensions allowed to attach') ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Breadcrumbs'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo $curlocation; ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><small><?php echo JText::_('Show hide breadcrumbs'); ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Top Header'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $showhide, 'show_header', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['show_header']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><small><?php echo JText::_('Show hide top header'); ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Show count on my tickets'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'show_count_tickets', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['show_count_tickets']); ?></div>
                            </div>
                        </div>
                </div>
                <div id="ticketsetting" style="display: none;">
                        <legend><?php echo JText::_('Ticket Setting'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Visitors can create ticket'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'visitor_can_create_ticket', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['visitor_can_create_ticket']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Can visitors create tickets or not'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Ticket id sequence'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $ticketidsequence, 'ticketid_sequence', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticketid_sequence']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Set ticket id sequential or random'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Maximum tickets'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="maximum_ticket" value="<?php echo $this->configuration['maximum_ticket']; ?>" class="inputbox" size="<?php echo $sml_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Maximum tickets per user'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Maximum tickets within interval'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $maxticketinterval, 'maximum_ticket_interval_time', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['maximum_ticket_interval_time']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Maximum tickets within time interval per user'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Maximum open tickets'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="ticket_per_email" value="<?php echo $this->configuration['ticket_per_email']; ?>" class="inputbox" size="<?php echo $sml_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Maximum opened tickets per user'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Reopen ticket within days'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="ticket_reopen_within_days" value="<?php echo $this->configuration['ticket_reopen_within_days']; ?>" class="inputbox" size="<?php echo $sml_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('The ticket can be reopened within the given number of the days'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Visitor ticket creation message'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value">
                                    <?php
                                        $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('visitor_message', $this->configuration['visitor_message'], '550', '300', '60', '20', false);
                                    ?>
                                </div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('New ticket message'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value">
                                    <?php
                                        $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('new_ticket_message', $this->configuration['new_ticket_message'], '550', '300', '60', '20', false);
                                    ?>
                                </div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('This message will show on the new ticket'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Allow Users To Reply via Email On Closed Ticket'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'reply_to_closed_ticket', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['reply_to_closed_ticket']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Select whether users can reply to closed tickets via email or not'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Default').' '.JText::_('ticket listing Ordering'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $ticketsorting, 'tickets_sorting', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tickets_sorting']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Select default sorting for ticket listing.'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Show Captcha To Visitor On Form Ticket'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'show_captcha_visitor_form_ticket', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['show_captcha_visitor_form_ticket']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Captcha selection'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $captchaselection, 'captcha_selection', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['captcha_selection']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Which captcha do you want to add'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Own captcha calculation type'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $owncaptchatype, 'owncaptcha_calculationtype', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['owncaptcha_calculationtype']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Select calculation type addition or subtraction'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Own captcha operands'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $owncaptchaoparend, 'owncaptcha_totaloperand', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['owncaptcha_totaloperand']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Select the total operands to be given'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Own captcha subtraction answer positive'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'owncaptcha_subtractionans', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['owncaptcha_subtractionans']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Is subtraction answer should be positive'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Enable print ticket'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'print_ticket_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['print_ticket_user']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Show print ticket icon on the ticket detail page to the user'); ?></small></div>
                            </div>
                        </div>
                </div>
                <div id="emialsetting" style="display: none;">
                        <legend><?php echo JText::_('Default System Emails'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Default Alert Email'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $this->lists['emails'], 'alert_email', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['alert_email']); ?>&nbsp;<a href="index.php?option=com_jssupportticket&c=email&layout=formemail"><?php echo JText::_('Add Email'); ?></a></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('If Ticket Department Email Is Not Selected Then This Email Is Used To Send Emails'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Default admin email'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $this->lists['emails'], 'admin_email', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['admin_email']); ?>&nbsp;<a href="index.php?option=com_jssupportticket&c=email&layout=formemail"><?php echo JText::_('Add Email'); ?></a></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Admin Email Address To Receive Emails'); ?></small></div>
                            </div>
                        </div>
                </div>
                <div id="usermenusetting" style="display: none;">
                        <legend><?php echo JText::_('Staff Members Control Panel Links'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Open Ticket'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_openticket_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_openticket_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('My Tickets'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_myticket_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_myticket_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Add Role'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_addrole_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addrole_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Roles'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_roles_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_roles_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Add Staff'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_addstaff_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addstaff_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Staff'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_staff_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_staff_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Add Department'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_adddepartment_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_adddepartment_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Departments'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_department_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_department_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Add Category'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_addcategory_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addcategory_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Categories'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_category_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_category_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Add Knowledge Base'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_addkb_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addkb_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Knowledge Base'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_kb_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_kb_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Add Download'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_adddownload_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_adddownload_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Downloads'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_download_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_download_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Add Announcement'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_addannouncement_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addannouncement_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Announcements'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_announcement_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_announcement_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Add FAQ'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_addfaq_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addfaq_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('FAQs'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_faq_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_faq_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Mail'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_mail_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_mail_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('My Profile'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_profile_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_profile_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Staff Reports'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_staff_report_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_staff_report_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Department Reports'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_department_report_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_department_report_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Feedbacks'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_feedback_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_feedback_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Erase Data'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_userdata_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_userdata_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Show').' '.JText::_('Ticket Total Count'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $showhide, 'cplink_totalcount_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_totalcount_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Show').' '.JText::_('Ticket Statistics'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $showhide, 'cplink_ticketstats_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_ticketstats_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Show').' '.JText::_('Latest Tickets'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $showhide, 'cplink_latesttickets_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_latesttickets_staff']); ?></div>
                            </div>
                        </div>
                        <legend><?php echo JText::_('Staff Members Top Menu Links'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Home'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'tplink_home_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_home_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Tickets'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'tplink_ticket_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_ticket_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Knowledge Base'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'tplink_kb_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_kb_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Announcements'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'tplink_announcement_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_announcement_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Downloads'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'tplink_download_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_download_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('FAQs'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'tplink_faq_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_faq_staff']); ?></div>
                            </div>
                        </div>
                </div>
                <div id="vismenusetting" style="display: none;">
                        <legend><?php echo JText::_('User Control Panel Links'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Open Ticket'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_openticket_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_openticket_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('My Tickets'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_myticket_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_myticket_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Check Ticket Status'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_checkstatus_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_checkstatus_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Downloads'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_download_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_download_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Announcements'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_announcement_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_announcement_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('FAQs'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_faq_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_faq_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Knowledge Base'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_kb_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_kb_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Erase Data Requests'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'cplink_userdata_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_userdata_user']); ?></div>
                            </div>
                        </div>
                        <legend><?php echo JText::_('User Top Menu Links'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Home'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'tplink_home_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_home_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Tickets'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'tplink_ticket_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_ticket_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Knowledge Base'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'tplink_kb_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_kb_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Announcements'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'tplink_announcement_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_announcement_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('Downloads'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'tplink_download_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_download_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo JText::_('FAQs'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo JHTML::_('select.genericList', $yesno, 'tplink_faq_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_faq_user']); ?></div>
                            </div>
                        </div>
                </div>
                <div id="auotrespondersetting" style="display: none;">
                        <legend><?php echo JText::_('Ban email New Ticket'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Mail to admin'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'banemail_new_ticket_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['banemail_new_ticket_admin']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Email sent to admin when banned email try to create a ticket'); ?></small></div>
                            </div>
                        </div>
                        <legend><?php echo JText::_('Ticket Operations Mail Setting'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-ticket-configuration-row-mail bgandfontcolor">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo JText::_('Admin'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo JText::_('Staff'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo JText::_('User'); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('New Ticket'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'new_ticket_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['new_ticket_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'new_ticket_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['new_ticket_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket reassign'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_reassign_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reassign_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_reassign_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reassign_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_reassign_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reassign_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket close'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_close_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_close_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_close_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_close_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_close_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_close_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket delete'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_delete_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_delete_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_delete_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_delete_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_delete_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_delete_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket mark overdue'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_overdue_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_overdue_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_overdue_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket ban email'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_ban_email_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_email_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_ban_email_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_email_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_ban_email_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_email_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket Department Transfer'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_department_transfer_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_department_transfer_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_department_transfer_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_department_transfer_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_department_transfer_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_department_transfer_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket Reply User'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_reply_user_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reply_user_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_reply_user_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reply_user_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_reply_user_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reply_user_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket Response Staff'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_response_staff_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_response_staff_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_response_staff_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_response_staff_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_response_staff_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_response_staff_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket ban email and close ticket'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_ban_and_close_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_and_close_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_ban_and_close_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_and_close_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_ban_and_close_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_and_close_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket unban email'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_unbanemail_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unbanemail_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_unbanemail_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unbanemail_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_unbanemail_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unbanemail_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket Lock'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_lock_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_lock_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_lock_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_lock_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_lock_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_lock_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket mark in progress'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_progress_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_progress_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_progress_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_progress_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_progress_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_progress_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket Unlock'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_unlock_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unlock_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_unlock_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unlock_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_unlock_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unlock_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Ticket Change Priority'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_priority_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_priority_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_priority_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_priority_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_priority_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_priority_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Feedback Email To User'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value">----</div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value">----</div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'ticket_feedback_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_feedback_user']); ?></div>
                            </div>
                        </div>
                        <legend><?php echo JText::_('Erase Data'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-ticket-configuration-row-mail bgandfontcolor">
                                <div class="js-col-lg-3 js-col-md-3"></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo JText::_('Admin'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo JText::_('Staff'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo JText::_('User'); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Erase request'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'erase_data_request_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['erase_data_request_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'erase_data_request_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['erase_data_request_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo JText::_('Delete').' '.JText::_('user data'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'delete_user_data', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['delete_user_data']); ?></div>
                            </div>
                        </div>
                </div>
                <div id="feedbacksettings" style="display: none;">
                        <legend><?php echo JText::_('Feedback Email Settings'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Feedback Email Delay Type'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $overduetype_array, 'feedback_email_delay_type', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['feedback_email_delay_type']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Select delay type for feedback email'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Feedback Email Delay'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="feedback_email_delay" value="<?php echo $this->configuration['feedback_email_delay']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Set no. of days or hours to send feedback email after the ticket is closed'); ?></small></div>
                            </div>
                        </div>
                        <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Feedback successfully stored message'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value">
                                    <?php
                                        $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('feedback_thanks_message', $this->configuration['feedback_thanks_message'], '550', '300', '60', '20', false);
                                    ?>
                                </div>
                            </div>
                </div>
                <div id="ticketviaemail" style="display: none;">
                        <legend><?php echo JText::_('Ticket Via Email'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('UTF Auto Switch'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value">
                                    <?php echo JHTML::_('select.genericList',$yesno, 'read_utf_ticket_via_email', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['read_utf_ticket_via_email']); ?>
                                </div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Enabled') ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo JHTML::_('select.genericList', $enableddisabled, 'tve_enabled', '', 'value', 'text',$this->configuration['tve_enabled']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4">
                                    <small><?php echo JText::_('Enable ticket via email'); ?></small></div>
                            </div>
                        </div>
                </div>
                <div id="privatecredentials" style="display: none;">
                        <legend><?php echo JText::_('Private Credentials'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Secret Key'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="private_credentials_secretkey" value="<?php echo $this->configuration['private_credentials_secretkey']; ?>" class="inputbox" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo JText::_('Private Credentials Encryption Key changing this value will discard all existing credentials'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo JText::_('Second Level Security') ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value">
                                    <small><?php echo JText::_('For enhanced security change encryption method in'); ?><?php echo JPATH_ADMINISTRATOR.'/com_jssupportticket/include/classes/privatecredentials.php'; ?> on line 10</small></div>
                            </div>
                        </div>
                </div>
            </div>
            <input type="hidden" name="task" value="saveconf" />
            <input type="hidden" name="c" value="config" />
            <input type="hidden" name="layout" value="config" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <?php echo JHtml::_( 'form.token' ); ?>
            <div class="js-form-button">
                <input type="submit" name="save" id="save" value="<?php echo JText::_('Save Configurations') ?>" class="button js-form-save" onclick="Joomla.submitbutton('saveconf');">
            </div>
        </form>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("div#generalsetting").show();
    });
    jQuery("a#generalsettingbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").show();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").hide();
        jQuery("div#ticketviaemail").hide();
        jQuery("div#privatecredentials").hide();
    });
    jQuery("a#ticketsettingbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").show();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").hide();
        jQuery("div#ticketviaemail").hide();
        jQuery("div#privatecredentials").hide();
    });
    jQuery("a#emialsettingbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").show();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").hide();
        jQuery("div#ticketviaemail").hide();
        jQuery("div#privatecredentials").hide();
    });
    jQuery("a#usermenusettingbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").show();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").hide();
        jQuery("div#ticketviaemail").hide();
        jQuery("div#privatecredentials").hide();
    });
    jQuery("a#vismenusettingbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").show();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").hide();
        jQuery("div#ticketviaemail").hide();
        jQuery("div#privatecredentials").hide();
    });
    jQuery("a#auotrespondersettingbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").show();
        jQuery("div#feedbacksettings").hide();
        jQuery("div#ticketviaemail").hide();
        jQuery("div#privatecredentials").hide();
    });
    jQuery("a#feedbacksettingsbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").show();
        jQuery("div#ticketviaemail").hide();
        jQuery("div#privatecredentials").hide();
    });
    jQuery("a#ticketviaemailbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").hide();
        jQuery("div#ticketviaemail").show();
        jQuery("div#privatecredentials").hide();
    });
    jQuery("a#privatecredentialsbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide()
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").hide();
        jQuery("div#ticketviaemail").hide();
        jQuery("div#privatecredentials").show();
    });
</script>
