<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
  + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 03, 2012
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
?>
<div class="js-row js-null-margin">
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/download-formdownload.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){
        if(!$this->user->getIsGuest()){
            if($this->user->getIsStaff()){
                if(!$this->user->getIsStaffDisable()){ ?>
                    <?php
                        jimport('joomla.html.pane');
                        JHTML::_('behavior.formvalidator');
                        $document = JFactory::getDocument();
                        $document->addScript('administrator/components/com_jssupportticket/include/js/file/file_validate.js');
                        JText::script('Error file size too large');
                        JText::script('Error file extension mismatch');
                        ?>
                        <script language="javascript">
                            function validate_form(f) {
                                if (document.formvalidator.isValid(f)) {
                                    f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken();
                            else echo JSession::getFormToken(); ?>';//send token
                                }
                                else {
                                    alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
                                    return false;
                                }
                                return true;
                            }

                        </script>
                        <div id="jsst-wrapper-top">
                            <?php if($this->config['cur_location'] == 1){ ?>
				    <div id="jsst-wrapper-top-left">
	    				<div id="jsst-breadcrunbs">
	    				    <ul>
	    				        <li>
	    				            <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo $this->Itemid; ?>" title="Dashboard">
	    				                <?php echo JText::_('Dashboard'); ?>
	    				            </a>
	    				        </li>
	    				        <li><a href="index.php?option=com_jssupportticket&c=downloads&layout=downloads" title="Dashboard"><?php echo JText::_('Downloads'); ?></a>
	    				        </li>
	    				        <li>
	    				            <?php echo JText::_('Add Download'); ?>
	    				        </li>
	    				    </ul>
	    				</div>
				    </div>
                            <?php } ?>
            			</div>
                        <div class="js-ticket-add-form-wrapper">
                            <form class="js-ticket-form" action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm" >
                                <div class="js-ticket-from-field-wrp">
                                    <div class="js-ticket-from-field-title">
                                        <label for="title">
                                            <?php echo JText::_('Title'); ?>&nbsp;<font color="red">*</font>
                                        </label>
                                    </div>
                                    <div class="js-ticket-from-field">
                                        <input class="inputbox js-ticket-form-field-input required" type="text" id="title" name="title" value="<?php if (isset($this->form_data)) echo $this->form_data->title; ?>"/>
                                    </div>
                                </div>
                                <div class="js-ticket-from-field-wrp">
                                    <div class="js-ticket-from-field-title">
                                        <label for="categoryid">
                                            <?php echo JText::_('Category'); ?>
                                        </label>
                                    </div>
                                    <div class="js-ticket-from-field js-ticket-form-field-select">
                                        <?php echo $this->lists['categories']; ?>
                                    </div>
                                </div>
                                <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width ">
                                    <div class="js-ticket-from-field-title">
                                        <label>
                                            <?php echo JText::_('Description'); ?>
                                        </label>
                                    </div>
                                    <div class="js-ticket-from-field">
                                        <?php
                                            $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);
                                            if (isset($this->form_data->description))
                                                echo $editor->display('description', $this->form_data->description, '550', '300', '60', '20', false);
                                            else
                                                echo $editor->display('description', '', '550', '300', '60', '20', false);
                                        ?>
                                    </div>
                                </div>
                                <!-- Attachments -->
                                <?php   $attachmentFlag = true;
                                        $nAllowedAttachments = $this->config['noofattachment'];
                                        if(isset($this->downloadattachments) && count($this->downloadattachments)>$nAllowedAttachments){
                                            $attachmentFlag = false;
                                        } ?>
                                <?php if($attachmentFlag == true){ ?>
                                        <div class="js-ticket-reply-attachments"><!-- Attachments -->
                                            <div class="js-attachment-field-title">
                                                <label for="attachemtid">
                                                    <?php echo JText::_('Attachments'); ?>
                                                </label>
                                            </div>
                                            <div class="js-attachment-field">
                                                <div class="tk_attachment_value_wrapperform tk_attachment_user_reply_wrapper">
                                                    <span class="tk_attachment_value_text">
                                                        <input id="attachemtid" type="file" class="inputbox js-attachment-inputbox" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlength='30'/>
                                                        <span class='tk_attachment_remove'></span>
                                                    </span>
                                                </div>  
                                                <span class="tk_attachments_configform">
                                                    <?php echo JText::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo JText::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?>
                                                </span>
                                                <span id="js-attachment-add" data-ident="tk_attachment_user_reply_wrapper" class="tk_attachments_addform"><?php echo JText::_('Add More'); ?></span>
                                            </div>
                                            <?php if(isset($this->downloadattachments)){
                                                foreach($this->downloadattachments AS $attach){
                                                    echo '
                                                        <div class="js-ticket-attached-files-wrp">
                                                            <div class="js_ticketattachment">
                                                                    ' . $attach->filename . '
                                                            </div>
                                                            <a class="js-ticket-delete-attachment" href="index.php?option=com_jssupportticket&c=downloads&task=deleteattachmentbyid&id='.$attach->id.'&downloadid='.$this->form_data->id.'&'.JSession::getFormToken().'=1">' .JText::_('Remove').'</a>
                                                        </div>';
                                                }
                                            } ?>
                                        </div>
                                <?php } ?>
                                <div class="js-ticket-from-field-wrp">
                                    <div class="js-ticket-from-field-title">
                                       <label for="status">
                                        <?php echo JText::_('Status'); ?>
                                    </label>
                                    </div>
                                    <div class="js-ticket-from-field js-ticket-form-field-select">
                                        <?php echo $this->lists['status']; ?>
                                    </div>
                                </div>
                                <div class="js-ticket-form-btn-wrp">
                                    <input type="submit" class="js-ticket-save-button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Download'); ?>" />

                                    <a href="index.php?option=com_jssupportticket&c=downloads&layout=downloads" class="js-ticket-cancel-button"><?php echo JText::_('Cancel'); ?></a>
                                </div>
                                
                                <input type="hidden" name="created" value="<?php if (isset($this->form_data)) {echo $this->form_data->created; } else {$curdate = date('Y-m-d H:i:s'); echo $curdate; } ?>" />
                                <input type="hidden" name="id" value="<?php if (isset($this->id)) echo $this->id; ?>" />
                                <input type="hidden" name="c" value="downloads" />
                                <input type="hidden" name="view" value="downloads" />
                                <input type="hidden" name="layout" value="formdownload" />
                                <input type="hidden" name="check" value="" />
                                <input type="hidden" name="task" value="savedownload" />
                                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                                <?php echo JHtml::_('form.token'); ?>
                            </form>    
                        </div>
            <?php    
                }else{
                    messageslayout::getStaffDisable(); //staff disabled
                }
            }else{
                messageslayout::getNotStaffMember(); //user not staff
            }
        }else{
            messageslayout::getUserGuest($this->layoutname,$this->Itemid); //user guest
        }
    }else{
        messageslayout::getPermissionNotAllow(); //permission not granted
    }
}else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End ?>

    <script>
        jQuery("#js-attachment-add").click(function () {
            var obj = this;
            var current_files = jQuery('input[type="file"]').length;
            var total_allow =<?php echo $this->config['noofattachment']; ?>;
            var append_text = "<span class='tk_attachment_value_text'><input name='filename[]' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlenght='30' /><span  class='tk_attachment_remove'></span></span>";

            <?php if(isset($this->downloadattachments) && count($this->downloadattachments)>0){ ?>
            current_files += <?php echo count($this->downloadattachments); ?>;
            <?php } ?>

            if (current_files < total_allow) {
                jQuery(".tk_attachment_value_wrapperform").append(append_text);
            } else if ((current_files === total_allow) || (current_files > total_allow)) {
                alert("<?php echo JText::_('File upload limit exceed'); ?>");
                jQuery(obj).hide();
            }
        });

        jQuery(document).delegate(".tk_attachment_remove", "click", function (e) {
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
    
</div>
