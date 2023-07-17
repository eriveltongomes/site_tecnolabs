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
            if ((task == 'saveknowledgebasearticlesavenew') || (task == 'saveknowledgebasearticle') || (task == 'saveknowledgebasearticlesave')) {
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
                        <li><?php echo JText::_('Add knowledge Base'); ?></li>
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
                <?php echo JText::_('Add Knowledge base'); ?>
            </h1>
        </div> 
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="categoryid"><?php echo JText::_('Category'); ?>:</label></div>
                <div class="js-value"><?php echo $this->lists['categories'] ?></div>
            </div>
            <?php /*<div class="js-col-xs-12 js-col-md-2 js-title"><label for="pullish"><?php echo JText::_('Type'); ?>:&nbsp;<font color="red">*</font></label></div>
            <div class="js-col-xs-12 js-col-md-10 js-value">
                <input type="radio" name="type" id="publish" value="0" <?php if (isset($this->article_form_data)) {if ($this->article_form_data->type == 0) echo "checked="; } else echo "checked="; ?>/><label for="publish"><?php echo JText::_('Published'); ?></label>
                <input type="radio" name="type" id="private" value="1" <?php if (isset($this->article_form_data)) if ($this->article_form_data->type == 1) echo "checked="; ?>/><label for="private"><?php echo JText::_('Private');?></label>
                <input type="radio" name="type" id="draft" value="2" <?php if (isset($this->article_form_data)) if ($this->article_form_data->type == 2) echo "checked="; ?>/><label for="draft"><?php echo JText::_('Draft'); ?></label>
            </div>*/ ?>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="subject"><?php echo JText::_('Subject'); ?>:&nbsp;<font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required" type="text" id="subject" name="subject" value="<?php if (isset($this->article_form_data)) echo $this->article_form_data->subject; ?>"/></div>
            </div>
            <div class="js-form-wrapper fullwidth">
                <div class="js-title"><?php echo JText::_('Content'); ?>:&nbsp;</div>
                <div class="js-value"><?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); if (isset($this->article_form_data->content)) echo $editor->display('content_article', $this->article_form_data->content, '', '300', '60', '20', false); else echo $editor->display('content_article', '', '', '300', '60', '20', false); ?></div>
            </div>
            <div class="js-form-wrapper fullwidth">
                <div class="js-title"><?php echo JText::_('Attachments'); ?>:&nbsp;</div>
                <div class="js-value">
                    <div id="js-attachment-files" class="js-attachment-files">
                        <span class="js-value-attachment-text">
                            <input type="file" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
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
					   if(isset($this->articleattachments)){
						  foreach($this->articleattachments AS $attach){
							echo '<div class="download_attachments">'.$attach->filename.'&nbsp;('.$attach->filesize.')&nbsp;<a href="index.php?option=com_jssupportticket&c=knowledgebase&task=deleteattachmentbyid&id='.$attach->id.'&kbid='.$this->article_form_data->id.'&'. JSession::getFormToken() .'=1">'.JText::_('Delete').'</a></div>';
						}
					}
                    ?>
                </div>
            </div>
            <div class="js-subheading"><?php echo JText::_('Meta Data Options'); ?></div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Meta description'); ?>:&nbsp;</div>
                <div class="js-value"><textarea id="metadesc" rows ="3" cols="40" name="metadesc"><?php if (isset($this->article_form_data)) echo $this->article_form_data->metadesc; ?></textarea></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Meta keywords'); ?>:&nbsp;</div>
                <div class="js-value"><textarea id="metakey" rows ="3" cols="40" name="metakey"><?php if (isset($this->article_form_data)) echo $this->article_form_data->metakey; ?></textarea></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Status'); ?>:&nbsp;</div>
                <div class="js-value"><?php echo $this->lists['status']; ?></div>
            </div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Knowledge base'); ?>" /></div></div>

            <input type="hidden" name="created" value="<?php if (isset($this->article_form_data)) {echo $this->article_form_data->created; } else {$curdate = date('Y-m-d H:i:s'); echo $curdate; } ?>" />
            <input type="hidden" name="id" value="<?php if (isset($this->id)) echo $this->id; ?>" />
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
            <input type="hidden" name="c" value="knowledgebase" />
            <input type="hidden" name="layout" value="formarticle" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="task" value="saveknowledgebasearticle" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
<script>
    jQuery("#js-attachment-add").click(function () {
        var obj = this;
        var current_files = jQuery('input[type="file"]').length;
        var total_allow =<?php echo $this->config['noofattachment']; ?>;
        var append_text = "<span class='js-value-attachment-text'><input name='filename[]' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlenght='30' /><span  class='js-attachment-remove'></span></span>";
        if (current_files < total_allow) {
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
        if (current_files < total_allow) {
            jQuery("#js-attachment-add").show();
        }
    });
</script>       
