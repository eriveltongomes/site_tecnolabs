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
JHTML::_('behavior.formvalidator');
JHTML::_('bootstrap.renderModal');
?>

<script type="text/javascript">
// for joomla 1.6

    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'savemessage' || task == 'savemessageandnew' || task == 'savemessagesave') {
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
                        <li><?php echo JText::_('Compose'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo JText::_('Compose'); ?></h1></div>
        <div id="jsstadmin-data-wrp" class="js-padding-all-null">
        <div  id="mail-admin-links" class="js-margin-all-null">
            <a href="index.php?option=com_jssupportticket&c=mail&layout=inbox"><img alt="Inbox" src="components/com_jssupportticket/include/images/inboxadmin.png"> <?php if ($this->unreadmessages >= 1) {$inbox = $this->unreadmessages; } else {$inbox = $this->totalinboxmessages; } echo JText::_('inbox') . "&nbsp;(" . $inbox . ")"; ?></a>
            <a href="index.php?option=com_jssupportticket&c=mail&layout=outbox"><img alt="Inbox" src="components/com_jssupportticket/include/images/outboxadmin.png"> <?php echo JText::_('Outbox') . "&nbsp;(" . $this->outboxmessages . ")"; ?></a>
            <a href="index.php?option=com_jssupportticket&c=mail&layout=formmessage"><img alt="Inbox" src="components/com_jssupportticket/include/images/add_icon.png"> <?php echo JText::_('Compose'); ?></a>
        </div></div>
        <div id="jsst-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="POST" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('To'); ?>:&nbsp;</div>
                <div class="js-value"><?php echo $this->lists['staff']; ?></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="subject"><?php echo JText::_('Subject'); ?>:&nbsp;<font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required" type="text" id="subject" name="subject" size="60" value=""/></div>
            </div>
            <div class="js-form-wrapper fullwidth">
                <div class="js-title"><?php echo JText::_('Message'); ?>:&nbsp;</div>
                <div class="js-value"><?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('message', '', '', '300', '60', '20', false); ?></div>
            </div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Send'); ?>" /></div></div>

            <input type="hidden" name="created" value="<?php $curdate = date('Y-m-d H:i:s'); echo $curdate; ?>" />
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
            <input type="hidden" name="from" value="<?php echo $this->uid; ?>" />
            <input type="hidden" name="isread" value="2" />
            <input type="hidden" name="status" value="1" />
            <input type="hidden" name="c" value="mail" />
            <input type="hidden" name="layout" value="inbox" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="task" value="savemessage" />
            <input type="hidden" name="view" value="mail" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <?php echo JHtml::_( 'form.token' ); ?>
        </form>
    </div>
        
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
