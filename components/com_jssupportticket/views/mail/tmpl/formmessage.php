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
?>
<div class="js-row js-null-margin">
<?php
JHTML::_('behavior.formvalidator');
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/mail-formmessage.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){
        if(!$this->user->getIsGuest()){
            if($this->user->getIsStaff()){
                if(!$this->user->getIsStaffDisable()){
                        JHTML::_('behavior.formvalidator');
                        //require_once JPATH_COMPONENT_SITE . '/views/ticket_header_bottom.php';
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
                            <!-- Button Wrapper -->
                        <?php if ($this->unreadmessages >= 1) {$inbox = $this->unreadmessages; } else {$inbox = $this->totalinboxmessages; } ?>
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
                                                <?php echo JText::_('Add').' '.JText::_('Message'); ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="js-ticket-mails-btn-wrp">
                            <div class="js-ticket-mail-btn">
                                <a class="js-add-link button" href="index.php?option=com_jssupportticket&c=mail&layout=inbox&Itemid=<?php echo $this->Itemid; ?>">
                                    <img id="inbox-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/inbox-black.png" />
                                    <img id="inbox-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/inbox-black.png" />
                                    <?php echo JText::_('inbox') . "&nbsp;(" . $inbox . ")"; ?>
                                </a>
                            </div>
                            <div class="js-ticket-mail-btn">
                                <a class="js-add-link button" href="index.php?option=com_jssupportticket&c=mail&layout=outbox&Itemid=<?php echo $this->Itemid; ?>">
                                    <img id="outbox-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/outbox-black.png" />
                                    <img id="outbox-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/outbox-black.png" />
                                    <?php echo JText::_('Outbox') . "&nbsp;(" . $this->outboxmessages . ")"; ?>
                                </a>
                            </div>
                            <div class="js-ticket-mail-btn">
                                <a class="js-add-link button active" href="index.php?option=com_jssupportticket&c=mail&layout=formmessage&Itemid=<?php echo $this->Itemid; ?>">
                                    <img id="compose-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/compose-black.png" />
                                    <img id="compose-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/compose-black.png" />
                                    <?php echo JText::_('Compose'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="js-ticket-add-form-wrapper">
                        <form class="js-ticket-form" action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm" >
                            <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                <div class="js-ticket-from-field-title">
                                    <label for="to">
                                        <?php echo JText::_('To'); ?>&nbsp;<font color="red">*</font>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field js-ticket-form-field-select">
                                    <?php echo $this->lists['staff']; ?>
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                <div class="js-ticket-from-field-title">
                                    <label for="subject">
                                        <?php echo JText::_('Subject'); ?>&nbsp;<font color="red">*</font>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                    <input class="inputbox required js-ticket-form-field-input" type="text" name="subject" id="subject" size="40" maxlength="255" value="" />
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                <div class="js-ticket-from-field-title">
                                    <?php echo JText::_('Message'); ?>
                                </div>
                                <div class="js-ticket-from-field">
                                    <?php
                                        $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);
                                        echo $editor->display('message', '', '550', '300', '60', '20', false);
                                    ?>
                                </div>
                            </div>
                            <div class="js-ticket-form-btn-wrp">
                                <input type="submit" class="js-ticket-save-button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Send'); ?>" />
                            </div>
                            <input type="hidden" name="created" value="<?php $curdate = date('Y-m-d H:i:s'); echo $curdate; ?>" />
                            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                            <input type="hidden" name="from" value="<?php echo $this->user->getId(); ?>" />
                            <input type="hidden" name="isread" value="2" />
                            <input type="hidden" name="status" value="1" />
                            <input type="hidden" name="c" value="mail" />
                            <input type="hidden" name="view" value="mail" />
                            <input type="hidden" name="layout" value="inbox" />
                            <input type="hidden" name="check" value="" />
                            <input type="hidden" name="task" value="savemessage" />
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
