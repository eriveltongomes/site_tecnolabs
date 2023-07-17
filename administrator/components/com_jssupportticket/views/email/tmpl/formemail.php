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

$editor = JFactory::getConfig()->get('editor');
$editor = JEditor::getInstance($editor);

jimport('joomla.html.pane');
JHTML::_('behavior.formvalidator');
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_jssupportticket/include/css/custom.boots.css');
$document->addStyleSheet(JUri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');
$securesmtp = array(
    '0' => array('value' => '1',
        'text' => JText::_('TLS')),
    '1' => array('value' => '0',
        'text' => JText::_('SSL')),);

$yesno = array(
    '0' => array('value' => '1',
        'text' => JText::_('Yes')),
    '1' => array('value' => '0',
        'text' => JText::_('No')),);
$emailtype = array(
    '0' => array('value' => '0',
        'text' => JText::_('Default')),
    '1' => array('value' => '1',
        'text' => JText::_('SMTP')),);
$truefalse = array(
    '0' => array('value' => '1',
        'text' => JText::_('True')),
    '1' => array('value' => '0',
        'text' => JText::_('False')),);
$smtphost = array(
    '0' => array('value' => '1',
        'text' => JText::_('Gmail')),
    '1' => array('value' => '2',
        'text' => JText::_('Yahoo')),
    '2' => array('value' => '3',
        'text' => JText::_('Hotmail')),
    '3' => array('value' => '4',
        'text' => JText::_('Aol')),
    '4' => array('value' => '5',
        'text' => JText::_('Other')),);

?>

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'saveemail' || task == 'saveemailandnew' || task == 'saveemailsave') {
                returnvalue = validate_form(document.adminForm);
            } else
                returnvalue = true;
            if (returnvalue) {
                Joomla.submitform(task);
                return true;
            } else
                return false;
        }
    }

    function validate_form(f)
    {
        if (document.formvalidator.isValid(f)) {
            f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken(); else echo JSession::getFormToken(); ?>';//send token
        } else {
            alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
        return true;
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
                        <li><?php echo JText::_('Add Email'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo JText::_('Add Email'); ?></h1></div> 
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="email"><?php echo JText::_('Email'); ?><font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required validate-email" type="text" id="email" name="email" size="40" maxlength="255" value="<?php if (isset($this->email)) echo $this->email->email; ?>" /></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="email"><?php echo JText::_('Send Email by'); ?>&nbsp;<font color="red">*</font></label></div>
                <div class="js-value">
                <?php echo JHTML::_('select.genericList', $emailtype, 'smtpemailauth', 'class="inputbox" ' . '', 'value', 'text', isset($this->email) ? $this->email->smtpemailauth : ''); ?>
                <?php echo JText::_('Send email by').' '.JText::_('SMTP'); ?>
                </div>
            </div>

            <div id="smtpauthselect" style="display: none;">
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="hosttype"><?php echo JText::_('SMTP host type'); ?>&nbsp;<font color="red">*</font></label></div>
                    <div class="js-value">
                    <?php echo JHTML::_('select.genericList', $smtphost, 'smtphosttype', 'class="inputbox" ' . '', 'value', 'text', isset($this->email) ? $this->email->smtphosttype : ''); ?>
                    </div>
                </div>
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="host"><?php echo JText::_('SMTP host'); ?>&nbsp;<font color="red">*</font></label></div>
                    <div class="js-value"><input class="inputbox" type="text" id="host" name="smtphost" size="40" maxlength="255" value="<?php if (isset($this->email)) echo $this->email->smtphost; ?>" /></div>
                </div>
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="auth"><?php echo JText::_('SMTP Authencation'); ?>&nbsp;<font color="red">*</font></label></div>
                    <div class="js-value">
                    <?php echo JHTML::_('select.genericList', $truefalse, 'smtpauthencation', 'class="inputbox" ' . '', 'value', 'text', isset($this->email) ? $this->email->smtpauthencation : ''); ?>
                    </div>
                </div>
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="uname"><?php echo JText::_('Username'); ?>&nbsp;<font color="red">*</font></label></div>
                    <div class="js-value"><input class="inputbox" type="text" id="username" name="name" size="40" maxlength="255" value="<?php if (isset($this->email)) echo $this->email->name; ?>" /></div>
                </div>
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="password"><?php echo JText::_('Password'); ?>&nbsp;<font color="red">*</font></label></div>
                    <div class="js-value"><input class="inputbox" type="password" id="password" name="password" size="40" maxlength="255" value="<?php if (isset($this->email)) echo base64_decode($this->email->password); ?>" /></div>
                </div>
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="smtpsecure"><?php echo JText::_('SMTP Secure'); ?>&nbsp;<font color="red">*</font></label></div>
                    <div class="js-value">
                    <?php echo JHTML::_('select.genericList', $securesmtp, 'smtpsecure', 'class="inputbox" ' . '', 'value', 'text', isset($this->email) ? $this->email->smtpsecure : ''); ?>
                    </div>
                </div>
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="port"><?php echo JText::_('SMTP Port'); ?>&nbsp;<font color="red">*</font></label></div>
                    <div class="js-value"><input class="inputbox" type="text" id="port" name="mailport" size="40" maxlength="255" value="<?php if (isset($this->email)) echo $this->email->mailport; ?>" /></div>
                </div>

                <div class="js-col-md-12 js-col-xs-12 js-col-md-offset-2 js-admin-ticketviaemail-wrapper-checksetting">
                    <a href="#" id="js-admin-ticketviaemail"><img src="components/com_jssupportticket/include/images/tick_ticketviaemail.png" /><?php echo JText::_('Check Settings'); ?></a>
                    <div id="js-admin-ticketviaemail-bar"></div>
                    <div class="js-col-md-12" id="js-admin-ticketviaemail-text"><?php echo JText::_('If System Not Respond In 30 Seconds').', '.JText::_('It Means System Unable To Connect Email Server'); ?></div>
                    <div class="js-col-md-12">
                       <div id="js-admin-ticketviaemail-msg"></div>
                   </div>
                </div>
            </div>
            <?php /*
            <div class="js-col-xs-12 js-col-md-2 js-title"><?php echo JText::_('Auto Response'); ?></div>
            <div class="js-col-xs-12 js-col-md-10 js-value"><input type="radio" value="1" name="autoresponce"<?php if (isset($this->email)) {if ($this->email->autoresponce == 1) echo "checked=''"; } else echo "checked=''"; ?> /><?php echo JText::_('Yes'); ?> <input type="radio" value="0" name="autoresponce"<?php if (isset($this->email)) {if ($this->email->autoresponce == 0) echo "checked=''"; } ?> /><?php echo JText::_('No'); ?></div>
            <div class="js-col-xs-12 js-col-md-2 js-title"><?php echo JText::_('Priority'); ?>:&nbsp;</div>
            <div class="js-col-xs-12 js-col-md-10 js-value"><?php echo $this->lists['priority']; ?></div>
                */ ?>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Status'); ?>:&nbsp;</div>
                <div class="js-value-radio-btn">
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="1" name="status"<?php if (isset($this->email)) {if ($this->email->status == 1) echo "checked=''"; } else echo "checked=''"; ?> /><?php echo JText::_('Active'); ?>
                    </div>
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="0" name="status"<?php if (isset($this->email)) {if ($this->email->status == 0) echo "checked=''"; } ?> /><?php echo JText::_('Disabled'); ?></div>
                    </div>
            </div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" id="submit_app" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Email'); ?>" /></div></div>
            <input type="hidden" name="id" value="<?php if (isset($this->email)) echo $this->email->id; ?>" />
            <input type="hidden" name="c" value="email" />
            <input type="hidden" name="task" value="saveemail" />
            <input type="hidden" name="layout" value="formemail" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="created" value="<?php if (!isset($this->email)) echo $curdate = date('Y-m-d H:i:s'); else echo $this->email->created; ?>"/>
            <input type="hidden" name="update" value="<?php if (isset($this->email)) echo $update = date('Y-m-d H:i:s'); ?>"/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($){
        smtpAuthSelect();
        if(jQuery("#host").val() == "")
            smtphosttype(1);
        $("select#smtpemailauth").change(function(){
        	smtpAuthSelect();
        });
        $("#smtphosttype").change(function(){
            smtphosttype(1);
        });

        function smtpAuthSelect(){
        	if(jQuery("select#smtpemailauth").val() == 1){
                jQuery("div#smtpauthselect").show();
            }else{
                jQuery("div#smtpauthselect").hide();
            }
        }
            
        function smtphosttype(n){
            if(n==1 || jQuery("#host").val() == ""){
                if(jQuery("#smtphosttype").val() == 1){
                    jQuery("#host").val("smtp.gmail.com");
                }else if(jQuery("#smtphosttype").val() == 2){
                    jQuery("#host").val("smtp.mail.yahoo.com");
                }else if(jQuery("#smtphosttype").val() == 3){
                    jQuery("#host").val("smtp.live.com");
                }else if(jQuery("#smtphosttype").val() == 4){
                    jQuery("#host").val("smtp.aol.com");
                }else{
                    jQuery("#host").val("");
                }
            }
        }

        $("form").submit(function(e){
            if(jQuery("select#smtpemailauth").val() == 1){
                if($("#host").val() == "" || $("#username").val() == "" || $("#password").val() == "" || $("#smtpsecure").val() == "" || $("#port").val() == "" || $("#smtpauthencation").val() == ""){
                    e.preventDefault();
                    alert("Some values are not acceptable please retry");
                }
            }
            if(jQuery("select#smtpemailauth").val() == 0){
                $("#host").val("");
                $("#username").val("");
                $("#password").val("");
                $("#smtpsecure").val("");
                $("#port").val("");
                $("#smtpauthencation").val("");
            }
        });

        jQuery("a#js-admin-ticketviaemail").click(function(e){
            e.preventDefault();

            var tve_hosttype = jQuery('select#smtphosttype').val();
            var hostname = jQuery('input#host').val();
            if(tve_hosttype == 4){
                var tve_hostname = jQuery('input#host').val();
                if(tve_hostname != ''){
                    var hostname = jQuery('input#host').val();
                }else{
                    alert("<?php echo JText::_('Please enter the hostname first'); ?>");
                    return;
                }
            }
            var hosttype = jQuery('select#smtphosttype').val();
            var emailaddress = jQuery('input#email').val();
            var username = jQuery('input#username').val();
            var password = jQuery('input#password').val();
            var enabled_ssl = jQuery('select#smtpsecure').val();
            var hostportnumber = jQuery('input#port').val();
            var smtpauthencation_val = jQuery('select#smtpauthencation').val();
            jQuery("div#js-admin-ticketviaemail-bar").show();
            jQuery("div#js-admin-ticketviaemail-text").show();
            jQuery.post("index.php?option=com_jssupportticket&c=email&task=sendtestemail&<?php echo JSession::getFormToken(); ?>=1",{hosttype: hosttype,hostname:hostname, emailaddress: emailaddress, username: username,password:password,ssl:enabled_ssl,hostportnumber:hostportnumber,smtpauthencation:smtpauthencation_val}, function (data) {
                if (data) {
                    jQuery("div#js-admin-ticketviaemail-bar").hide();
                    jQuery("div#js-admin-ticketviaemail-text").hide();
                    try {
                        var obj = jQuery.parseJSON(data);
                        if(obj.type == 0){
                            jQuery("div#js-admin-ticketviaemail-msg").html(obj.msg).addClass('no-error');
                        }else{
                            jQuery("div#js-admin-ticketviaemail-msg").html(obj.msg).addClass('imap-error');
                        }
                    } catch (e) {
                        jQuery("div#js-admin-ticketviaemail-msg").html(data).addClass('server-error');
                    }
                    jQuery("div#js-admin-ticketviaemail-msg").show();
                }
            });//jquery closed
        });
    });
</script>
