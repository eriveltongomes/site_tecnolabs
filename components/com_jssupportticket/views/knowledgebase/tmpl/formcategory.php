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
?>
<div class="js-row js-null-margin">
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE.'/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/knowledgebase-formcategory.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){
        if(!$this->user->getIsGuest()){
            if($this->user->getIsStaff()){
                if(!$this->user->getIsStaffDisable()){ ?>
                    <script language="javascript">
                        function validate_form(f) {
                            var cat_for = jQuery('input[type="checkbox"]:checked').length;
                            if (cat_for == 0) {
                                alert("<?php echo JText::_('Please select atleast one category for'); ?>");
                                return false;
                            }
                            if (document.formvalidator.isValid(f)) {
                                f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken(); else echo JSession::getFormToken(); ?>';//send token
                            }
                            else {
                                alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
                                return false;
                            }
                            return true;
                        }
                    </script>
                    <?php
                    JHTML::_('behavior.formvalidator');
                    JText::script('JS_ERROR_FILE_SIZE_TO_LARGE');
                    JText::script('JS_ERROR_FILE_EXT_MISMATCH');
                    ?> 
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
                                        <li>
                                            <a href="index.php?option=com_jssupportticket&c=knowledgebase&layout=categories&Itemid=<?php echo $this->Itemid; ?>" title="Dashboard">
                                                <?php echo JText::_('categories'); ?>
                                            </a>
                                        </li>

                                        <li>
                                            <?php echo JText::_('Add Category'); ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="js-ticket-add-form-wrapper">
                        <form class = "js-ticket-form" action="index.php" method="POST" name="adminForm" id="adminForm" enctype="multipart/form-data" >
                            <div class="js-ticket-from-field-wrp">
                                <div class="js-ticket-from-field-title">
                                    <label for="name">
                                        <?php echo JText::_('Category Name'); ?>&nbsp;<font color="red">*</font>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                    <input class="inputbox js-ticket-form-field-input required" type="text" id="name" name="name" value="<?php if (isset($this->category)) echo $this->category->name; ?>"/>
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp">
                                <div class="js-ticket-from-field-title">
                                    <label for="parentid">
                                        <?php echo JText::_('Parent Category'); ?>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field js-ticket-form-field-select">
                                    <?php echo $this->lists['categories']; ?>
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width ">
                                <div id="msgshowcategory"></div>
                                <div class="js-ticket-from-field-title">
                                    <label>
                                        <?php echo JText::_('Category For'); ?>&nbsp;<font color="red">*</font>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                    <span class="js-ticket-sub-fields"><input class="js-ticket-checkbox" type="checkbox" name="kb" id ="kb" value="1" <?php if (isset($this->category)) if ($this->category->kb == 1) echo "checked=''"; ?>/><label for="kb" id="forkb"> <?php echo JText::_('Knowledge base'); ?></label></span>
                                    <span class="js-ticket-sub-fields"><input class="js-ticket-checkbox" type="checkbox" name="downloads" id ="downloads" value="1" <?php if (isset($this->category)) if ($this->category->downloads == 1) echo "checked=''"; ?>/><label for="downloads" id="fordownloads"> <?php echo JText::_('Downloads'); ?></label></span>
                                    <span class="js-ticket-sub-fields"><input class="js-ticket-checkbox" type="checkbox" name="faqs" id ="faqs" value="1" <?php if (isset($this->category)) if ($this->category->faqs == 1) echo "checked=''"; ?>/><label for="faqs" id="forfaqs"> <?php echo JText::_('FAQs'); ?></label></span>
                                    <span class="js-ticket-sub-fields"><input class="js-ticket-checkbox" type="checkbox" name="announcement" id ="announcement" value="1" <?php if (isset($this->category)) if ($this->category->announcement == 1) echo "checked=''"; ?>/><label for="announcement" id="forannouncement"> <?php echo JText::_('Announcement'); ?></label></span>
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width ">
                                <div class="js-ticket-from-field-title">
                                    <label for="categorylogoid">
                                        <?php echo JText::_('Logo'); ?>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                   <input type="file" id="categorylogoid" class="inputbox js-ticket-form-field-input" name="filename" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
                                </div>
                                 <span id="js-form-choose-logo-inst"><?php echo JText::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo JText::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?></span>              
                            </div>
                            <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width ">
                                <div class="js-ticket-from-field-title">
                                    <label for="metadesc">
                                        <?php echo JText::_('Meta Description'); ?>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                   <textarea id="metadesc" name="metadesc" ><?php if (isset($this->category)) echo $this->category->metadesc; ?></textarea>
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width ">
                                <div class="js-ticket-from-field-title">
                                    <label for="metakey">
                                        <?php echo JText::_('Meta Keywords'); ?>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                   <textarea id="metadesc" name="metakey" ><?php if (isset($this->category)) echo $this->category->metakey; ?></textarea>
                                </div>
                            </div>
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
                                <input type="submit" class="js-ticket-save-button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save category'); ?>" />
                                <a href="index.php?option=com_jssupportticket&c=knowledgebase&layout=categories&Itemid=<?php echo $this->Itemid; ?>" class="js-ticket-cancel-button"><?php echo JText::_('Cancel'); ?></a>
                            </div>
                            
                            <input type="hidden" name="created" value="<?php if (isset($this->category)) {echo $this->category->created; } else {$curdate = date('Y-m-d H:i:s'); echo $curdate; } ?>" />
                            <input type="hidden" name="id" value="<?php if (isset($this->category)) echo $this->categoryid; ?>" />
                            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                            <input type="hidden" name="c" value="knowledgebase" />
                            <input type="hidden" name="view" value="knowledgebase" />
                            <input type="hidden" name="layout" value="formcategory" />
                            <input type="hidden" name="check" value="" />
                            <input type="hidden" name="task" value="saveknowledgebasecategory" />
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
</div>
