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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
?>

<script type="text/javascript">
    function updateuserlist(pagenum){
        var name = jQuery("input#name").val();
        var username = jQuery("input#username").val();
        var emailaddress = jQuery("input#emailaddress").val();
        jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1", {name:name,username:username,emailaddress:emailaddress,userlimit:pagenum}, function (data) {
            if(data){
                jQuery("div#records").html("");
                jQuery("div#records").html(data);
                setUserLink();
            }
        });
    }
    function setUserLink() {
        jQuery("a.js-userpopup-link").each(function () {
            var anchor = jQuery(this);
            jQuery(anchor).click(function (e) {
                var id = jQuery(this).attr('data-id');
                var name = jQuery(this).html();
                var email = jQuery(this).attr('data-email');
                var displayname = jQuery(this).attr('data-name');
                jQuery("input#username-text").val(name);
                if(jQuery('input#name').val() == ''){
                    jQuery('input#name').val(displayname);
                }
                if(jQuery('input#email').val() == ''){
                    jQuery('input#email').val(email);
                }
                jQuery("input#uid").val(id);
                jQuery("div#userpopup").slideUp('slow', function () {
                    jQuery("div#userpopupblack").hide();
                });
            });
        });
    }
        jQuery(document).ready(function () {
            jQuery("a#userpopup").click(function (e) {
                e.preventDefault();
                jQuery("div#userpopupblack").show();
                jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1",{},function(data){
                  if(data){
                    jQuery('div#records').html("");
                    jQuery('div#records').html(data);
                    setUserLink();
                  }
                });
                jQuery("div#userpopup").slideDown('slow');
            });
            jQuery("form#userpopupsearch").submit(function (e) {
                e.preventDefault();
                var name = jQuery("input#name").val();
                var username = jQuery("input#username").val();
                var emailaddress = jQuery("input#emailaddress").val();
                jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1",{name: name, emailaddress: emailaddress,username:username}, function (data) {
                    if (data) {
                        jQuery("div#records").html(data);
                        setUserLink();
                    }
                });//jquery closed
            });
            jQuery("span.close, div#userpopupblack").click(function (e) {
                jQuery("div#userpopup").slideUp('slow', function () {
                    jQuery("div#userpopupblack").hide();
                });

            });
        });

</script>
<div id="userpopupblack" style="display:none;"></div>
<div id="userpopup" style="display:none;">
    <form id="userpopupsearch">
            <div class="search-center">
                <div class="search-center-heading"><?php echo JText::_('Select user'); ?><span class="close"></span></div>
                <div class="js-col-md-12">
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="username" id="username" placeholder="<?php echo JText::_('Username'); ?>" />
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="name" id="name" placeholder="<?php echo JText::_('Name'); ?>" />
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="emailaddress" id="emailaddress" placeholder="<?php echo JText::_('Email Address'); ?>"/>
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value-button">
                        <div class="js-button">
                            <input class="js-button-search" type="submit" value="<?php echo JText::_('Search'); ?>" />
                        </div>
                        <div class="js-button">
                            <input class="js-button-reset" type="submit" onclick="document.getElementById('name').value = '';document.getElementById('username').value = ''; document.getElementById('emailaddress').value = '';" value="<?php echo JText::_('Reset'); ?>" />
                        </div>
                    </div>
                </div>
            </div>
    </form>
    <div id="records">
        <div id="records-inner">
            <div class="js-staff-searc-desc">
                <?php echo JText::_('Use Search Feature To Select The User'); ?>
            </div>
        </div>
    </div>
</div>
<?php 


$enableddisabled = array('0' => array('value' => '1','text' => JText::_('Enabled')),
                        '1' => array('value' => '0','text' => JText::_('Disabled')),);
$status_combo = array(
    array('value' => '0', 'text' => JText::_('New')),
    array('value' => '1', 'text' => JText::_('Pending')),
    array('value' => '2', 'text' => JText::_('In Progress')),
    array('value' => '3', 'text' => JText::_('Answerd')),
    array('value' => '4', 'text' => JText::_('Closed'))
);
$yesno = array(
    array('value' => '1', 'text' => JText::_('Yes')),
    array('value' => '2', 'text' => JText::_('No'))
);
$dash = '-';
$dateformat = $this->config['date_format'];
$firstdash = strpos($dateformat, $dash, 0);
$firstvalue = substr($dateformat, 0, $firstdash);
$firstdash = $firstdash + 1;
$seconddash = strpos($dateformat, $dash, $firstdash);
$secondvalue = substr($dateformat, $firstdash, $seconddash - $firstdash);
$seconddash = $seconddash + 1;
$thirdvalue = substr($dateformat, $seconddash, strlen($dateformat) - $seconddash);
$js_dateformat = '%' . $firstvalue . $dash . '%' . $secondvalue . $dash . '%' . $thirdvalue;

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
                        <li><?php echo JText::_('Export'); ?></li>
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
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Export'); ?></h1s>
        </div>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
            <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
                
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="departmentname"><?php echo JText::_('Start Date'); ?>:&nbsp;</label></div>
                    <div class="js-value"><?php echo JHTML::_('calendar', '', 'startdate', 'startdate', $js_dateformat, array('class' => 'inputbox ', 'size' => '10', 'maxlength' => '19'));?></div>
                </div>

                <div class="js-form-wrapper">
                    <div class="js-title"><label for="departmentname"><?php echo JText::_('End Date'); ?>:&nbsp;</label></div>
                    <div class="js-value"><?php echo JHTML::_('calendar', '', 'enddate', 'enddate', $js_dateformat, array('class' => 'inputbox ', 'size' => '10', 'maxlength' => '19'));?></div>
                </div>

                <div class="js-form-wrapper">
                    <div class="js-title"><label for="departmentid"><?php echo JText::_('Department'); ?>:&nbsp;</label></div>
                    <div class="js-value js-export-row-alue"><?php echo $this->lists['departments'] ?></div>
                </div>
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="staffid"><?php echo JText::_('Staff Member'); ?>:&nbsp;</label></div>
                    <div class="js-value js-export-row-alue"><?php echo $this->lists['staffmembers'] ?></div>
                </div>
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="email"><?php echo JText::_('Select User'); ?></label></div>
                    <div class="js-value">
                        <div id="username-div"></div><input type="text" value="" id="username-text" name="username-text" readonly="readonly" /><a href="#" id="userpopup"><?php echo JText::_('Select User'); ?></a>
                    </div>
                </div>
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="priorityid"><?php echo JText::_('Priority'); ?>:&nbsp;</label></div>
                    <div class="js-value js-export-row-alue"><?php echo $this->lists['priorities'] ?></div>
                </div>
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="ticketstatus"><?php echo JText::_('Ticket Status'); ?>:&nbsp;</label></div>
                    <div class="js-export-row-alue js-value"><?php echo $this->lists['ticketstatus'] ?></div>
                </div>
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="isoverdue"><?php echo JText::_('Ticket Overdue'); ?>:&nbsp;</label></div>
                    <div class="js-value js-export-row-alue"><?php echo $this->lists['isoverdue'] ?></div>
                </div>
                <div class="js-col-md-12">
                    <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app"  value="<?php echo JText::_('Export Tickets'); ?>" /></div></div>
                </div>
                <input type="hidden" name="c" value="export" />
                <input type="hidden" name="task" value="getticketsexport" />
                <input type="hidden" name="check" value="" />
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="uid" value="" />
            </form>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
