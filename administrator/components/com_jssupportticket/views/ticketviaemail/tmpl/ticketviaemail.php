<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 03, 2012
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
$enableddisabled = array(
    array('value' => '1', 'text' => JText::_('Enabled')),
    array('value' => '2', 'text' => JText::_('Disabled'))
);
$mailreadtype = array(
    array('value' => '1', 'text' => JText::_('Only New Tickets')),
    array('value' => '2', 'text' => JText::_('Only Replies')),
    array('value' => '3', 'text' => JText::_('Both'))
);
$hosttype = array(
    array('value' => '1', 'text' => JText::_('Gmail')),
    array('value' => '2', 'text' => JText::_('Yahoo')),
    array('value' => '3', 'text' => JText::_('Aol')),
    array('value' => '4', 'text' => JText::_('Other'))
);
$yesno = array(
    array('value' => '1', 'text' => JText::_('Yes')),
    array('value' => '2', 'text' => JText::_('No'))
);
$document = JFactory::getDocument();

if (JVERSION < 3) {
    JHtml::_('behavior.mootools');
    $document->addScript('components/com_jssupportticket/include/js/jquery.js');
} else {
    JHtml::_('bootstrap.framework');
    JHtml::_('jquery.framework');
}
$document->addScript('components/com_jssupportticket/include/js/jquery_idTabs.js');
?>

<script>
    jQuery(document).ready(function () {
        jQuery("a#js-admin-ticketviaemail").click(function(e){
            e.preventDefault();
            var enable = jQuery('select#tve_enabled').val();
            if(enable == 1){
                var tve_hosttype = jQuery('select#tve_hosttype').val();
                var hostname = jQuery('input#tve_hostname').val();
                if(tve_hosttype == 4){
                    var tve_hostname = jQuery('input#tve_hostname').val();
                    if(tve_hostname != ''){
                        var hostname = jQuery('input#tve_hostname').val();
                    }else{
                        alert("<?php echo JText::_('Please enter the hostname first'); ?>");
                        return;
                    }
                }
                var hosttype = jQuery('select#tve_hosttype').val();
                var emailaddress = jQuery('input#tve_emailaddress').val();
                var password = jQuery('input#tve_emailpassword').val();
                var ssl = jQuery('select#tve_ssl').val();
                var hostportnumber = jQuery('input#tve_hostportnumber').val();
                jQuery("div#js-admin-ticketviaemail-bar").show();
                jQuery("div#js-admin-ticketviaemail-text").show();
                jQuery.post("index.php?option=com_jssupportticket&c=ticketviaemail&task=readEmailsAjax",{hosttype: hosttype,hostname:hostname, emailaddress: emailaddress,password:password,ssl:ssl,hostportnumber:hostportnumber}, function (data) {
                    if (data) {
                        jQuery("div#js-admin-ticketviaemail-bar").hide();
                        jQuery("div#js-admin-ticketviaemail-text").hide();
                        try {
                            var obj = jQuery.parseJSON(data);
                            if(obj.type == 0){
                                jQuery("div#js-admin-ticketviaemail-msg").html(obj.msg).addClass('no-error');
                            }else if(obj.type == 1){
                                jQuery("div#js-admin-ticketviaemail-msg").html(obj.msg).addClass('imap-error');
                            }else if(obj.type == 2){
                                jQuery("div#js-admin-ticketviaemail-msg").html(obj.msg).addClass('email-error');
                            }
                        } catch (e) {
                            jQuery("div#js-admin-ticketviaemail-msg").html(data).addClass('server-error');
                        }
                        jQuery("div#js-admin-ticketviaemail-msg").show();
                    }
                });//jquery closed
            }else{
                alert("<?php echo JText::_('Please enable ticket via email setting first'); ?>");
            }           
        });
    });
    function showhidehostname(value){
        if(value == 4){
            jQuery("div#tve_hostname").show();
        }else{
            jQuery("div#tve_hostname").hide();
        }
    }
    function confirmdelete(deletefor) {
        msg = '';
        if(deletefor == 0){
            msg = "<?php echo JText::_('Are you sure to delete'); ?>";
        }else if(deletefor == 1){
            msg = "<?php echo JText::_('Are you sure to enforce delete'); ?>";
        }

        if (confirm(msg) == true) {
            return true;
        } else
            return false;
    }
</script>
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
                        <li><?php echo JText::_('Ticket via email'); ?></li>
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
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Ticket via email'); ?></h1>
            <?php $link = 'index.php?option='.$this->option.'&c=ticketviaemail&layout=ticketviaemailform'; ?>
            <a class="tk-heading-addbutton" href="<?php echo $link; ?>">
                <img class="js-heading-addimage" src="components/com_jssupportticket/include/images/plus.png">
                <?php echo JText::_('Add').' '.JText::_('Ticket via email'); ?>
            </a>
        </div>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm">
            <div id="js-tk-filter">
                <div class="tk-search-value"><input type="text" name="filter_email" placeholder="<?php echo JText::_('Email'); ?>" id="filter_email" value="<?php if (isset($this->lists['searchemail'])) echo $this->lists['searchemail']; ?>" class="text_area"/></div>
                <div class="tk-search-button">
                    <button class="js-form-search" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
                    <button class="js-form-reset" onclick="document.getElementById('filter_email').value = ''; /*this.form.getElementById('filter_type').value = '';*/ this.form.submit();"><?php echo JText::_('Reset'); ?></button>
                </div>
            </div>
            <?php
            if (!(empty($this->result)) && is_array($this->result)) {  ?>
                    <table id="js-table" class="js-ticket-box-shadow">
                        <thead>
                        <tr>
                            <th class="js-form-min-width"><?php echo JText::_("Email Address"); ?></th>
                            <th class="center"><?php echo JText::_("Attachment"); ?></th>
                            <th class="center"><?php echo JText::_("Host Type"); ?></th>
                            <th class="center"><?php echo JText::_("Ticket type"); ?></th>
                            <th class="center"><?php echo JText::_("Action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($this->result AS $row) {
                                if($row->attachment == 1) $attachment_status = 'Yes'; else $attachment_status = 'No';
                                if($row->mailreadtype == 1) $tickettype = 'Only New Tickets'; elseif($row->mailreadtype == 2) $tickettype = 'Only Replies'; else $tickettype = 'Both';
                                if($row->hosttype == 1) $hosttype = 'Gmail'; elseif($row->hosttype == 2) $hosttype = 'Yahoo'; elseif($row->hosttype == 3) $hosttype = 'Aol'; else $hosttype = $row->hostname;
                                $editlink = 'index.php?option=' . $this->option .'&c=ticketviaemail&layout=ticketviaemailform&cid=' . $row->id;
                                $deletelink = 'index.php?option='.$this->option.'&c=ticketviaemail&task=delete&cid='.$row->id.'&'. JSession::getFormToken() .'=1';?>
                                <tr>
                                    <td><a href="<?php echo $editlink;?>"><?php echo JText::_($row->emailaddress); ?></a></td>
                                    <td><?php echo JText::_($attachment_status); ?></td>
                                    <td class="center"><?php echo JText::_($hosttype); ?></td>
                                    <td class="center"><?php echo JText::_($tickettype); ?></td>
                                    <td class="center">
                                        <a class="js-tk-button" href="<?php echo $editlink; ?>">
                                            <img src="components/com_jssupportticket/include/images/edit.png">
                                        </a>&nbsp;
                                        <a class="js-tk-button" onclick="return confirmdelete(0)" href="<?php echo $deletelink; ?>">
                                            <img src="components/com_jssupportticket/include/images/delete.png">
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            } ?>
                        </tbody>
                    </table>
                <div class="js-row js-tk-pagination js-ticket-pagination-shadow">
                    <?php echo $this->pagination->getListFooter(); ?>
                </div>
            <?php
            }else{
                messagesLayout::getRecordNotFound();
            } ?>

            <input type="hidden" name="option" value="<?php echo $this->option; ?>"/>
            <input type="hidden" name="c" value="ticketviaemail"/>
            <input type="hidden" name="layout" value="ticketviaemail"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
