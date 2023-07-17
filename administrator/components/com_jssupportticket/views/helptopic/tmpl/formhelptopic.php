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
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');
global $mainframe;
?>

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'savehelptopic' || task == 'savehelptopicandnew' || task == 'savehelptopicsave') {
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
    function validate_form(f) {
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
                        <li><?php echo JText::_('Add Help Topic'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo JText::_('Add Help Topic'); ?></h1></div> 
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="topic"><?php echo JText::_('Help Topic'); ?><font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required" type="text" id="topic" name="topic" value="<?php if (isset($this->helptopic)) echo $this->helptopic->topic; ?>"/></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="departmentid"><?php echo JText::_('Department'); ?><font color="red">*</font></label></div>
                <div class="js-value"><?php echo $this->lists['department']; ?></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Status'); ?></div>
                <div class="js-value-radio-btn">
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" name="status" id="active" value="1" <?php if (isset($this->helptopic)) {if ($this->helptopic->status == 1) echo "checked="; } else echo "checked="; ?>/><label for="active"><?php echo JText::_('Active'); ?></label>
                    </div>
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" name="status" id="disable" value="0" <?php if (isset($this->helptopic)) if ($this->helptopic->status == 0) echo "checked="; ?>/><label for="disable"><?php echo JText::_('Disabled'); ?></label>
                    </div>
                </div>
            </div>
            <?php /*
            <div class="js-col-xs-12 js-col-md-2 js-title"><?php echo JText::_('Auto Response'); ?>:&nbsp;</div>
            <div class="js-col-xs-12 js-col-md-10 js-value"><input type='checkbox' name='autoresponce' id='autoresponce' value='1' <?php if (isset($this->helptopic)) {echo ($this->helptopic->autoresponce == 1) ? "checked='checked'" : ""; } ?> /> <label for="autoresponce"><?php echo JText::_('Auto Response For This Topic') . ' (' . JText::_('override department setting') . ')'; ; ?></label></div>
            
*/ ?>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Help Topic'); ?>" /></div></div>
            <input type="hidden" name="created" value="<?php if (isset($this->helptopic)) {echo $this->helptopic->created; } else {$curdate = date('Y-m-d H:i:s'); echo $curdate; } ?>" />
            <input type="hidden" name="update" value="<?php if (isset($this->helptopic)) {$update = date('Y-m-d H:i:s'); echo $update; } ?>" />
            <input type="hidden" name="id" value="<?php echo $this->helptopicid; ?>" />
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
            <input type="hidden" name="c" value="helptopic" />
            <input type="hidden" name="layout" value="formhelptopic" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="task" value="savehelptopic" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>"/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
