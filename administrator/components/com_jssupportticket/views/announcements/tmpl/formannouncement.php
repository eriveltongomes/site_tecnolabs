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
jimport('joomla.html.pane');
JHTML::_('behavior.formvalidator');
JHTML::_('bootstrap.renderModal');
?>

<script type="text/javascript">
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if ((task == 'saveannouncementsavenew') || (task == 'saveannouncement') || (task == 'saveannouncementsave')) {
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
    function validate_form(f){
        if (document.formvalidator.isValid(f)) {
            f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken();else echo JSession::getFormToken(); ?>';//send token
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
                        <li><?php echo JText::_('Add Announcement'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo JText::_('Add Announcement'); ?></h1></div> 
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
            <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="categoryid"><?php echo JText::_('Category'); ?>:&nbsp</label></div>
                <div class="js-value"><?php echo $this->lists['categories'] ?></div>
            </div>
            <?php /*<div class="js-col-xs-12 js-col-md-2 js-title"><label for="type"><?php echo JText::_('Type'); ?>:&nbsp<font color="red">*</font></label></div>
            <div class="js-col-xs-12 js-col-md-10 js-value"><?php echo $this->lists['type'] ?></div>*/ ?>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="title"><?php echo JText::_('Title'); ?>:&nbsp;<font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required" type="text" id="title" name="title" value="<?php if (isset($this->form_data)) echo $this->form_data->title; ?>"/></div>
            </div>
            <div class="js-form-wrapper fullwidth">
                <div class="js-title"><?php echo JText::_('Description'); ?>:&nbsp;</div>
                <div class="js-value"> <?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); if (isset($this->form_data->description)) echo $editor->display('description', $this->form_data->description, '', '300', '60', '20', false); else echo $editor->display('description', '', '', '300', '60', '20', false); ?></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Status'); ?>:&nbsp;</div>
                <div class="js-value"><?php echo $this->lists['status']; ?></div>
            </div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" onClick="return validate_form(document.adminForm);"name="submit_app" value="<?php echo JText::_('Save Announcement'); ?>" /></div></div>

            <input type="hidden" name="created" id="created" value="<?php if (isset($this->form_data)) {echo $this->form_data->created; } else {$curdate = date('Y-m-d H:i:s'); echo $curdate; } ?>" />
            <input type="hidden" name="id" id="id" value="<?php if (isset($this->id)) echo $this->id; ?>" />
            <input type="hidden" name="c" id="c" value="announcements" />
            <input type="hidden" name="check" id="check" value="" />
            <input type="hidden" name="layout" id="layout" value="formannouncement" />
            <input type="hidden" name="check" id="check" value="" />
            <input type="hidden" name="task" id="task" value="saveannouncement" />
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
