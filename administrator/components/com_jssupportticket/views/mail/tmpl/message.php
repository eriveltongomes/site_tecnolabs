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
$editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);
?>

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
                        <li><?php echo JText::_('Message'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo JText::_('Message'); ?></h1></div>
        <form class="jsstadmin-data-wrp" action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <?php if ($this->unreadmessages >= 1) {$inbox = $this->unreadmessages; } else {$inbox = $this->totalinboxmessages; } ?>
            <div class="js-col-md-12" id="mail-admin-links">
                <a class="visitedlink" href="index.php?option=com_jssupportticket&c=mail&layout=inbox&Itemid=<?php echo $this->Itemid; ?>"><img alt="Inbox" src="components/com_jssupportticket/include/images/inboxadmin.png"> <?php echo JText::_('inbox') . "&nbsp;(" . $inbox . ")"; ?></a>
                <a href="index.php?option=com_jssupportticket&c=mail&layout=outbox&Itemid=<?php echo $this->Itemid; ?>"><img alt="Inbox" src="components/com_jssupportticket/include/images/outboxadmin.png"><?php echo JText::_('Outbox') . "&nbsp;(" . $this->outboxmessages . ")"; ?></a>
                <a href="index.php?option=com_jssupportticket&c=mail&layout=formmessage&Itemid=<?php echo $this->Itemid; ?>"> <img alt="Inbox" src="components/com_jssupportticket/include/images/add_icon.png"><?php echo JText::_('Compose'); ?></a>
            </div>            
            <div class="js-tk-subheading">
                <?php echo JText::_('Message'); ?>
            </div>
            <?php $message = $this->message; ?>
            <div class="js-col-md-12">
                <div id="js-mail-threads">
                    <div class="js-tk-pic-subject">
                        <?php echo JText::_('Subject'); ?>
                    </div>
                    <div class="js-tk-message">
                        <div class="message-area-subject"><?php echo $message->subject; ?></div>
                    </div>
                </div>
                <div id="js-mail-threads">
                    <div class="js-tk-pic">
                        <img src="components/com_jssupportticket/include/images/user.png"/>
                    </div>
                    <div class="js-tk-message">
                        <div id="pointer"><img src="components/com_jssupportticket/include/images/corner.png"/></div>
                        <div class="js-tk-row"><?php echo JText::_('From').' : '.$message->staffname; ?><span class="timedate"><?php $replyby = JHtml::_('date',$message->created,"l F d, Y, h:i:s"); echo ' ( '. $replyby.' )'; ?></span></div>
                        <div class="message-area"><?php echo $message->message; ?></div>
                    </div>
                </div>
            </div>
            <?php if($this->replies){ ?>
            <div class="js-tk-subheading">
                <?php echo JText::_('Replies'); ?>
            </div>
            <div class="js-col-md-12"> <?php
                foreach ($this->replies AS $reply) { ?>
                    <div id="js-mail-threads">
                        <div class="js-tk-pic">
                            <img src="components/com_jssupportticket/include/images/user.png"/>
                        </div>
                        <div class="js-tk-message">
                            <div id="pointer"><img src="components/com_jssupportticket/include/images/corner.png"/></div>
                            <div class="js-tk-row"><?php echo JText::_('Posted by').' : '.$reply->staffname; ?><span class="timedate"><?php $replyby = JHtml::_('date',$reply->created,"l F d, Y, h:i:s"); echo ' ( '. $replyby.' )'; ?></span></div>
                            <div class="message-area"><?php echo $reply->message; ?></div>
                        </div>
                    </div>  <?php
                }  ?>
            </div>
            <?php } ?>
            <div class="js-tk-subheading">
                <?php echo JText::_('Reply'); ?>
            </div>
            <div class="js-col-xs-12 js-col-md-2 js-title"><?php echo JText::_('Message'); ?>:&nbsp;<font color="red">*</font></div>
            <div class="js-col-xs-12 js-col-md-10 js-value"><?php echo $editor->display('message', '', '100%', '300', '60', '20', false); ?></div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input class="button" type="submit" value="<?php echo JText::_('Send'); ?>"  onclick="return validateEditor('message');" /></div></div>

            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="c" value="mail" />
            <input type="hidden" name="task" value="savemessagereply"/>
            <input type="hidden" name="layout" value="message" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="cid" value="<?php echo $this->message->id; ?>" />
            <input type="hidden" name="replytoid" value="<?php echo $this->replytoid; ?>"/>
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
            <input type="hidden" name="from" value="<?php echo $this->uid; ?>" />
            <input type="hidden" name="status" value="1" />
            <input type="hidden" name="created" value="<?php echo date('Y-m-d H:i:s'); ?>" />
            <?php echo JHtml::_( 'form.token' ); ?>
        </form>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
<script language="Javascript" type="text/javascript">
    function validateEditor(editorid) {
        var contant = tinyMCE.get(editorid).getContent();
        if (contant == '') {
            alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
            tinyMCE.get(editorid).focus();
            return false;
        } else
            return true;
    }
</script>
