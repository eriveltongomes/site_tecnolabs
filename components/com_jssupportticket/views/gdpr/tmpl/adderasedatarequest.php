<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
/*
JHtml::_('stylesheet', 'system/calendar-jos.css', array('version' => 'auto', 'relative' => true), $attribs);
JHtml::_('script', $tag . '/calendar.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', $tag . '/calendar-setup.js', array('version' => 'auto', 'relative' => true));
*/
JHTML::_('behavior.formvalidator');
$document = JFactory::getDocument();
$document->addScript('administrator/components/com_jssupportticket/include/js/file/file_validate.js');
JText::script('Error file size too large');
JText::script('Error file extension mismatch');
?>
<div class="js-row js-null-margin">
    <?php if($this->config['offline'] != '1'){ ?>
        <?php require_once JPATH_COMPONENT_SITE . '/views/header.php';
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/ticket-formticket.css', 'text/css');
        $language = JFactory::getLanguage();
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
        if($language->isRTL()){
            $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
        }?>

        <script language="javascript">
            function myValidate(f){
                if (document.formvalidator.isValid(f)){
                    f.check.value = '<?php if (JVERSION < 3) echo JUtility::getToken(); else echo JSession::getFormToken(); ?>';//send token
                    return confirm("Are you sure to submit erase data request?");
                }else{
                    alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
                    return false;
                }
                jQuery('#submit_app_button').attr('disabled',true);
                f.submit();
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
                            <li>
                                <?php echo JText::_('Add').' '.JText::_('Erase Data Requests'); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php
        if($this->user->getIsGuest()){
            messageslayout::getUserGuest($this->layoutname,$this->Itemid); //user guest
        }else{?>
            <div id="js-tk-formwrapper">
                <div class="js-ticket-top-search-wrp">
                    <div class="js-ticket-search-heading-main-wrp">
                        <div class="js-ticket-heading-left">
                            <?php echo JText::_("Export your data"); ?>
                        </div>
                        <div class="js-ticket-heading-right">
                            <a class="js-ticket-add-download-btn" href="index.php?option=com_jssupportticket&c=gdpr&task=exportusereraserequest&<?php echo JSession::getFormToken(); ?>=1"><span class="js-ticket-add-img-wrp"></span><?php echo JText::_("Export"); ?></a>
                        </div>
                    </div>
                </div>
                <?php if(isset($this->erasedaatrequest) && is_numeric($this->erasedaatrequest->id)){ ?>
                    <div class="js-ticket-top-search-wrp second-style">
                        <div class="js-ticket-search-heading-main-wrp second-style">
                            <div class="js-ticket-heading-left">
                                <?php echo JText::_('You have filed a request to remove your data.') ?>
                            </div>
                            <div class="js-ticket-heading-right">
                                <a class="js-ticket-add-download-btn" href="index.php?option=com_jssupportticket&c=gdpr&task=removeusereraserequest&id=<?php echo $this->erasedaatrequest->id; ?>&<?php echo JSession::getFormToken(); ?>=1"><span class="js-ticket-add-img-wrp"></span><?php echo JText::_('To withdraw erases data request') ?></a>
                            </div>
                        </div>
                    </div>
                <?php }else{ ?>
                    <div class="js-ticket-top-search-wrp second-style">
                        <div class="js-ticket-search-heading-main-wrp second-style">
                            <div class="js-ticket-heading-left">
                                <?php echo JText::_("Request data removal from the system."); ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <form action="index.php" method="post" name="adminForm" id="adminForm" class="jsticket_form" enctype="multipart/form-data">
                    <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                        <div class="js-ticket-from-field-title">
                            <label for="subject"><?php echo JText::_("Subject"); ?>&nbsp;<font color="red">*</font></label>
                        </div>
                        <div class="js-ticket-from-field">
                            <input class="inputbox js-ticket-form-field-input" type="text" name="subject" id="subject" size="40" maxlength="255" value="<?php echo isset($this->erasedaatrequest) ? $this->erasedaatrequest->subject : ''; ?>" required/></div>
                    </div>
                    <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                        <div class="js-ticket-from-field-title">
                            <label for="message"><?php echo JText::_("Message"); ?>&nbsp;<font color="red">*</font></label></div>
                        <div class="js-ticket-from-field">
                            <?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('message', isset($this->erasedaatrequest) ? $this->erasedaatrequest->message : '', '550', '300', '60', '20', array('class'=>'required')); ?>
                                
                        </div>
                    </div>
                    <?php /*
                    <div class="js-col-md-12 js-col-xs-12 js-margin-bottom js-padding-null">
                        <div class="js-form-title"><label for="subject"><?php echo JText::_("Subject"); ?>&nbsp;<font color="red">*</font></label></div>
                        <div class="js-form-value"><input class="js-form-input-field required" type="text" name="subject" id="subject" size="40" maxlength="255" value="<?php echo isset($this->erasedaatrequest) ? $this->erasedaatrequest->subject : ''; ?>" required/></div>
                    </div>
                    <div class="js-col-md-12 js-col-xs-12 js-margin-bottom js-padding-null">
                        <div class="js-form-title"><label for="message"><?php echo JText::_("Message"); ?>&nbsp;<font color="red">*</font></label></div>
                        <div class="js-form-value"><?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('message', isset($this->erasedaatrequest) ? $this->erasedaatrequest->message : '', '550', '300', '60', '20', array('class'=>'required')); ?></div>
                    </div> */?>
                    <input type="hidden" name="view" value="gdpr" />
                    <input type="hidden" name="c" value="gdpr" />
                    <input type="hidden" name="layout" value="adderasedatarequest" />
                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                    <input type="hidden" name="task" value="saveusereraserequest" />
                    <input type="hidden" name="check" value="" />
                    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                    <input type="hidden" name="id" value="<?php if (isset($this->erasedaatrequest)) echo $this->erasedaatrequest->id; ?>" />
                    <?php echo JHtml::_('form.token'); ?>

                    <div class="js-form-submit-btn-wrp">
                        <input class="js-save-button" type="submit" onclick="return myValidate(document.adminForm);"  name="submit_app" id="submit_app_button" value="<?php echo JText::_('Save'); ?>" />
                        <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo $this->Itemid; ?>" class="js-ticket-cancel-button"><?php echo JText::_('Cancel'); ?></a>
                    </div>
                </form>
            </div>
        <?php
        } ?>

    <?php }else{
        messagesLayout::getSystemOffline($this->config['title'],$this->config['offline_text']);
    } ?>
</div>
