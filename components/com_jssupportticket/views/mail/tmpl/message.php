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
$editor = JFactory::getConfig()->get('editor');
$editor = JEditor::getInstance($editor);
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/mail-message.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){
        if(!$this->user->getIsGuest()){
            if($this->user->getIsStaff()){
                if(!$this->user->getIsStaffDisable()){  ?>
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
	    				            <?php echo JText::_('Message'); ?>
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
                                <a class="js-add-link button" href="index.php?option=com_jssupportticket&c=mail&layout=formmessage&Itemid=<?php echo $this->Itemid; ?>">
                                    <img id="compose-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/compose-black.png" />
                                    <img id="compose-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/compose-black.png" />
                                    <?php echo JText::_('Compose'); ?>
                                </a>
                            </div>
                        </div>

                        <!-- Message Portion -->
                        <?php if($this->message != null){ 
                            $message = $this->message ?>
                            <div class="js-ticket-post-reply-wrapper"><!-- Ticket Post Replay -->
                                <div class="js-ticket-thread-heading"><?php echo JText::_('Subject'); ?>
                                </div>
                                <div class="js-ticket-detail-box js-ticket-post-reply-box"><!-- Ticket Detail Box -->
                                    <div class="js-ticket-detail-right js-ticket-detail-right-null-border js-ticket-background"><!-- Right Side Ticket Data -->
                                        <div class="js-ticket-rows-wrp js-ticket-rows-wrp-null-shadow" >
                                            <?php echo $message->subject; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="js-ticket-thread-heading"><?php echo JText::_('Messages'); ?>
                                </div> <!-- Heading -->
                                <div class="js-ticket-detail-box js-ticket-post-reply-box"><!-- Ticket Detail Box -->
                                    <div class="js-ticket-detail-left js-ticket-white-background"><!-- Left Side Image -->
                                        <div class="js-ticket-user-img-wrp">
                                            <?php if ($this->message->staffphoto) { ?>
                                                <img  class="js-ticket-staff-img" src="<?php echo JURI::root(). $this->config['data_directory'] . "/staffdata/staff_" . $this->message->staffid . "/" . $this->message->staffphoto; ?>" />
                                            <?php } else { ?>
                                                <img class="js-ticket-staff-img" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/ticketman.png" />
                                            <?php } ?>
                                        </div>
                                        <div class="js-ticket-user-name-wrp">
                                           <?php echo $message->staffname; ?>
                                        </div>
                                        <div class="js-ticket-user-email-wrp">
                                            <?php echo JHtml::_('date',$message->created,"l F d, Y, h:i:s"); ?>
                                        </div>
                                    </div>
                                    <div class="js-ticket-detail-right js-ticket-background"><!-- Right Side Ticket Data -->
                                        <div class="js-ticket-rows-wrp js-ticket-min-height js-ticket-rows-wrp-null-shadow" >
                                            <div class="js-ticket-row">
                                                <div class="js-ticket-field-value">
                                                   <?php echo $message->message; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            
                            <!-- Reply Portion -->
                            <?php if($this->replies){ ?>
                                <div class="js-ticket-post-reply-wrapper"><!-- Ticket Post Replay -->
                                    <div class="js-ticket-thread-heading"><?php echo JText::_('Replies'); ?></div> <!-- Heading -->
                                    <?php  foreach ($this->replies AS $reply) { ?>
                                        <div class="js-ticket-detail-box js-ticket-post-reply-box"><!-- Ticket Detail Box -->
                                            <div class="js-ticket-detail-left js-ticket-white-background"><!-- Left Side Image -->
                                                <div class="js-ticket-user-img-wrp">
                                                    <?php if ($reply->staffphoto) { ?>
                                                        <img class="js-ticket-staff-img" src="<?php echo JURI::root(). $this->config['data_directory'] . "/staffdata/staff_" . $reply->staffid . "/" . $reply->staffphoto; ?>" />
                                                    <?php } else { ?>
                                                        <img class="js-ticket-staff-img" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/ticketman.png" />
                                                    <?php } ?>
                                                </div>
                                                <div class="js-ticket-user-name-wrp">
                                                    <?php echo $reply->staffname; ?>
                                                </div>
                                                <div class="js-ticket-user-email-wrp">
                                                    <?php echo JHtml::_('date',$reply->created,"l F d, Y, h:i:s");?>
                                                </div>
                                            </div>
                                            <div class="js-ticket-detail-right js-ticket-background"><!-- Right Side Ticket Data -->
                                                <div class="js-ticket-rows-wrp js-ticket-min-height" >
                                                    <div class="js-ticket-row">
                                                        <div class="js-ticket-field-value">
                                                           <?php echo $reply->message; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            
                            <!-- Form Message -->
                            <div class="js-ticket-post-reply-wrapper"><!-- Ticket Post Replay -->
                                <div class="js-ticket-thread-heading"><?php echo JText::_('Type a Message'); ?></div> 
                                <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
                                    <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                        <div class="js-ticket-from-field">
                                            <?php echo $editor->display('message', '', '100%', '300', '60', '20', false); ?>
                                        </div>
                                    </div>
                                    <div class="js-ticket-form-btn-wrp js-ticket-margin-top">
                                        <input type="submit" class="js-ticket-save-button" name="submit_app" value="<?php echo JText::_('Send'); ?>" onclick="return validateEditor('message');" />
                                    </div>
                                    <input type="hidden" name="messageid" value="<?php echo $this->message->id; ?>" />
                                    <input type="hidden" name="replytoid" value="<?php echo $this->replytoid; ?>"/>
                                    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                                    <input type="hidden" name="from" value="<?php echo $this->user->getId(); ?>" />
                                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                                    <input type="hidden" name="c" value="mail" />
                                    <input type="hidden" name="task" value="savemessagereply"/>
                                    <input type="hidden" name="view" value="mail" />
                                    <input type="hidden" name="layout" value="message" />
                                    <input type="hidden" name="boxchecked" value="0" />
                                    <input type="hidden" name="status" value="1" />
                                    <input type="hidden" name="created" value="<?php echo date('Y-m-d H:i:s'); ?>" />
                                    <?php echo JHtml::_('form.token'); ?>
                                </form>
                            </div>

                    <?php }else{
                        messageslayout::getRecordNotFound(); // empty record
                    }
                    ?>
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
<script language="Javascript" type="text/javascript">
    function validateEditor(editorid) {
        return true;
    }
</script>
</div>
