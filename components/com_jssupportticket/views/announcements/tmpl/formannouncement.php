<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
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

    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/announcement-formannouncement.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){
        if(!$this->user->getIsGuest()){
            if($this->user->getIsStaff()){
                if(!$this->user->getIsStaffDisable()){
                    jimport('joomla.html.pane');
                    JHTML::_('behavior.formvalidator');
                    /*
                    JHtml::_('stylesheet', 'system/calendar-jos.css', array('version' => 'auto', 'relative' => true), $attribs);
                    JHtml::_('script', $tag . '/calendar.js', array('version' => 'auto', 'relative' => true));
                    JHtml::_('script', $tag . '/calendar-setup.js', array('version' => 'auto', 'relative' => true)); ?>
                    */
                    ?>
                    <script language="javascript">
                        function validate_form(f) {
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
                				        <li><a href="index.php?option=com_jssupportticket&c=announcements&layout=announcements&Itemid=<?php echo $this->Itemid; ?>" title="Dashboard"><?php echo JText::_('Announcements'); ?></a>
                				        </li>
                				        <li>
                				            <?php echo JText::_('Add Announcement'); ?>
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
                                        echo $editor->display('description', $this->form_data->description, '', '300', '60', '20', false);
                                    else
                                        echo $editor->display('description', '', '', '300', '60', '20', false);
                                    ?>
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
                                <input type="submit" class="js-ticket-save-button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Announcement'); ?>" />
                                <a href="index.php?option=com_jssupportticket&c=announcements&layout=announcements&Itemid=<?php echo $this->Itemid; ?>" class="js-ticket-cancel-button"><?php echo JText::_('Cancel'); ?></a>
                            </div>
                            <input type="hidden" name="created" value="<?php if (isset($this->form_data)) {echo $this->form_data->created; } else {$curdate = date('Y-m-d H:i:s'); echo $curdate; } ?>" />
                            <input type="hidden" name="id" value="<?php if ($this->id) echo $this->id; ?>" />
                            <input type="hidden" name="c" value="announcements" />
                            <input type="hidden" name="view" value="announcements" />
                            <input type="hidden" name="layout" value="formannouncement" />
                            <input type="hidden" name="check" value="" />
                            <input type="hidden" name="task" value="saveannouncement" />
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
