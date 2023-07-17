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
$editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
JHTML::_('behavior.formvalidator');
$document = JFactory::getDocument();
// $document->addStyleSheet(JUri::root() . 'administrator/components/com_jssupportticket/include/css/custom.boots.css');
$document->addStyleSheet(JUri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');

if (JVERSION >= 3) {
    JHtml::_('bootstrap.framework');
    JHtml::_('jquery.framework');
}
$document->addScript('components/com_jssupportticket/include/js/colorpicker.js');
$document->addStyleSheet('components/com_jssupportticket/include/css/colorpicker.css');
$overduetype_array = array(
    '0' => array('value' => '1',
        'text' => JText::_('Days')),
    '1' => array('value' => '2',
        'text' => JText::_('Hours')));
// $overduetype = JHTML::_('select.genericList', $overduetype_array, 'ticket_overdue_type', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_type']);
?>

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'savepriority' || task == 'savepriorityandnew' || task == 'saveprioritysave') {
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
    jQuery(document).ready(function(){
        jQuery('input#color1').ColorPicker({
            onChange: function (hsb, hex, rgb) {
                jQuery('input#color1').css('backgroundColor', '#' + hex).val('#' + hex);                
            }
        });
    });
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
                        <li><?php echo JText::_('Add Priority'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo JText::_('Add Priority'); ?></h1></div> 
        <div id="jsstadmin-data-wrp" class="js-ticket-pagination-shadow">
        <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="title"><?php echo JText::_('Title'); ?><font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required" id="title" type="text" name="priority" size="40" maxlength="255" value="<?php if (isset($this->priority)) echo $this->priority->priority; ?>" /></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="color1"><?php echo JText::_('Color'); ?><font color="red">*</font></label></div>
                <div class="js-value" id="color1_div">
                <input id="color1" class="inputbox required" name="prioritycolour" type="text" value="<?php if (isset($this->priority)) echo $this->priority->prioritycolour; ?>"/>
                </div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="title"><?php echo JText::_('Overdue').' '.JText::_('type'); ?>:</label></div>
                <div class="js-value"><?php echo JHTML::_('select.genericList', $overduetype_array, 'overduetypeid', 'class="inputbox" ' . '', 'value', 'text', isset($this->priority) ? $this->priority->overduetypeid : 0); ?></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="title"><?php echo JText::_('Overdue').' '.JText::_('interval'); ?>:</label></div>
                <div class="js-value"><input class="inputbox required" id="title" type="text" name="overdueinterval" size="10" maxlength="10" value="<?php if (isset($this->priority)) echo $this->priority->overdueinterval; else echo '5' ?>" /></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Type'); ?>:&nbsp;</div>
                <div class="js-value-radio-btn">
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="1" id="public" name="ispublic"<?php if (isset($this->priority)) {if ($this->priority->ispublic == 1) echo "checked=''"; } else echo "checked=''"; ?> /> <label for="public"><?php echo JText::_('Public'); ?></label>
                    </div>
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="0" id="private" name="ispublic"<?php if (isset($this->priority)) {if ($this->priority->ispublic == 0) echo "checked=''"; } ?> /><label for="private"><?php echo JText::_('Private'); ?></label>
                    </div>
                </div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Default'); ?></div>
                <div class="js-value-radio-btn">
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="1" id="yes" name="isdefault"<?php if (isset($this->priority)) {if ($this->priority->isdefault == 1) echo "checked=''"; } else echo "checked=''"; ?> /> <label for="yes"><?php echo JText::_('Yes'); ?></label>
                    </div>
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="0" id="no" name="isdefault"<?php if (isset($this->priority)) {if ($this->priority->isdefault == 0) echo "checked=''"; } ?> /><label for="no"><?php echo JText::_('No'); ?></label>
                    </div>
                </div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Status'); ?>:&nbsp;</div>
                <div class="js-value-radio-btn">
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="1" id="active" name="status"<?php if (isset($this->priority)) {if ($this->priority->status == 1) echo "checked=''"; } else echo "checked=''"; ?> /><label for="active"> <?php echo JText::_('Active'); ?></label>
                    </div>
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="0" id="disable" name="status"<?php if (isset($this->priority)) {if ($this->priority->status == 0) echo "checked=''"; } ?> /><label for="disable"> <?php echo JText::_('Disabled'); ?></label>
                    </div>
                </div>
            </div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Priority'); ?>" /></div></div>

            <input type="hidden" name="id" value="<?php if (isset($this->priority)) echo $this->priority->id; ?>" />
            <input type="hidden" name="c" value="priority" />
            <input type="hidden" name="task" value="savepriority" />
            <input type="hidden" name="layout" value="formpriority" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="created" value="<?php if (!isset($this->department)) echo $curdate = date('Y-m-d H:i:s'); else echo $this->editgroup[0]->created; ?>"/>
            <input type="hidden" name="update" value="<?php if (isset($this->department)) echo $update = date('Y-m-d H:i:s'); ?>"/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
