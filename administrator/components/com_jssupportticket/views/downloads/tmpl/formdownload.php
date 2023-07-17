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
$document->addScript('components/com_jssupportticket/include/js/file/file_validate.js');
JText::script('Error file size too large');
JText::script('Error file extension mismatch');
?>

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if ((task == 'savedownloadsavenew') || (task == 'savedownload') || (task == 'savedownloadsave')) {
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
                        <li><a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard"><?php echo JText::_('Dashboard'); ?></a>
                        </li>
                        <li><?php echo JText::_('Add Download'); ?></li>
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
        
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo JText::_('Add Download'); ?></h1></div> 
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="categoryid"><?php echo JText::_('Category'); ?>:&nbsp;</label></div>
                <div class="js-value"><?php echo $this->lists['categories'] ?></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="title"><?php echo JText::_('Title'); ?>:&nbsp;<font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required" type="text" id="title" name="title" value="<?php if (isset($this->form_data)) echo $this->form_data->title; ?>"/></div>
            </div>
            <div class="js-form-wrapper fullwidth">
                <div class="js-title"><?php echo JText::_('Description'); ?>:&nbsp;</div>
                <div class="js-value"><?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); if (isset($this->form_data->description)) echo $editor->display('description', $this->form_data->description, '', '300', '60', '20', false); else echo $editor->display('description', '', '', '300', '60', '20', false); ?> </div>
            </div>
            <div class="js-form-wrapper fullwidth">
                <div class="js-title"><?php echo JText::_('Attachments'); ?>:&nbsp;</div>
                <div class="js-value">
                    <div id="js-attachment-files" class="js-attachment-files">
                    <span class="js-value-attachment-text">
                        <input type="file" class="inputbox" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
                        <span class='js-attachment-remove'></span>
                    </span>
                </div>
                <div id="js-attachment-option">
                    <span class="js-attachment-ins">
                        <small><?php echo JText::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo JText::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?></small>
                    </span>
                    <span id="js-attachment-add"><?php echo JText::_('Add Files'); ?></span>
                </div>
                <?php
					if(isset($this->downloadattachments)){
						foreach($this->downloadattachments AS $attach){
							echo '<div class="download_attachments">'.$attach->filename.'&nbsp;('.$attach->filesize.')&nbsp;<a href="index.php?option=com_jssupportticket&c=downloads&task=deleteattachmentbyid&id='.$attach->id.'&downloadid='.$this->form_data->id.'&'.JSession::getFormToken() .'=1">'.JText::_('Delete').'</a></div>';
						}
					}
                ?>
                </div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Status'); ?>:&nbsp;</div>
                <div class="js-value"><?php echo $this->lists['status']; ?></div>
            </div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Download'); ?>" /></div></div>

            <input type="hidden" name="created" value="<?php if (isset($this->form_data)) {echo $this->form_data->created; } else {$curdate = date('Y-m-d H:i:s'); echo $curdate; } ?>" />
            <input type="hidden" name="id" value="<?php if (isset($this->id)) echo $this->id; ?>" />
            <input type="hidden" name="c" value="downloads" />
            <input type="hidden" name="layout" value="formdownload" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="task" value="savedownload" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
<script>
    var isAdmin = true;
    jQuery("#js-attachment-add").click(function () {
        var obj = this;
        var current_files = jQuery('input[type="file"]').length;
        var total_allow =<?php echo $this->config['noofattachment']; ?>;
        var append_text = "<span class='js-value-attachment-text'><input name='filename[]' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlenght='30' /><span  class='js-attachment-remove'></span></span>";
        if (isAdmin==true || current_files < total_allow) {
            jQuery(".js-attachment-files").append(append_text);
        } else if ((current_files === total_allow) || (current_files > total_allow)) {
            alert("<?php echo JText::_('File upload limit exceed'); ?>");
            jQuery(obj).hide();
        }
    });

    jQuery(document).delegate(".js-attachment-remove", "click", function (e) {
        var current_files = jQuery('input[type="file"]').length;
        if(current_files!=1)
            jQuery(this).parent().remove();
        var current_files = jQuery('input[type="file"]').length;
        var total_allow =<?php echo $this->config['noofattachment']; ?>;
        if (isAdmin==true || current_files < total_allow) {
            jQuery("#js-attachment-add").show();
        }
    });
</script>
