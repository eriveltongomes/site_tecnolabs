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

$overduetype_array = array(
    '0' => array('value' => '1',
        'text' => JText::_('Days')),
    '1' => array('value' => '2',
        'text' => JText::_('Hours')),);
$med_field_width = 25;
?>

<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="jsst-main-wrapper" class="post-installation">
            <div class="js-admin-title-installtion">
                <span class="jsst_heading"><?php echo JText::_('JS Support Ticket Configurations'); ?></span>
                <div class="close-button-bottom">
                    <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" class="close-button">
                        <img src="components/com_jssupportticket/include/images/postinstallation/close-icon.png" />
                    </a>
                </div>
            </div>
            <div class="post-installtion-content-wrapper">
                <div class="post-installtion-content-header">
                    <ul class="update-header-img step-1">
                        <li class="header-parts first-part">
                            <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=stepone" title="link" class="tab_icon">
                                <img class="start" src="components/com_jssupportticket/include/images/postinstallation/general-settings.png" />
                                <span class="text"><?php echo JText::_('General Setting'); ?></span>
                            </a>
                        </li>
                        <li class="header-parts second-part">
                           <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=steptwo" title="link" class="tab_icon">
                               <img class="start" src="components/com_jssupportticket/include/images/postinstallation/ticket.png" />
                                <span class="text"><?php echo JText::_('Ticket Setting'); ?></span>
                            </a>
                        </li>
                        <li class="header-parts third-part active">
                           <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=stepthree" title="link" class="tab_icon">
                               <img class="start" src="components/com_jssupportticket/include/images/postinstallation/feedback.png" />
                                <span class="text"><?php echo JText::_('Feedback Setting'); ?></span>
                            </a>
                        </li>
                        <li class="header-parts forth-part">
                            <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=settingcomplete" title="link" class="tab_icon">
                               <img class="start" src="components/com_jssupportticket/include/images/postinstallation/complete.png" />
                                <span class="text"><?php echo JText::_('Complete'); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="post-installtion-content_wrapper_right">
                    <div class="jsst-config-topheading">    
                        <span class="heading-post-ins jsst-configurations-heading"><?php echo JText::_('Feedback Configurations');?></span>
                        <span class="heading-post-ins jsst-config-steps"><?php echo JText::_('Step 3 of 4');?></span>
                    </div>
                    <div class="post-installtion-content">
                        <form id="jssupportticket-form-ins" method="post" action="index.php">
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo JText::_('Feedback Email Delay Type'); ?>:  
                                </div>
                                <div class="field"> 
                                    <?php echo JHTML::_('select.genericList', $overduetype_array, 'feedback_email_delay_type', 'class="inputbox jsst-postsetting" ' . '', 'value', 'text', $this->result['feedback_email_delay_type']); ?>
                                </div>
                                <div class="desc">
                                    <?php echo JText::_('Select delay type for feedback email'); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo JText::_('Feedback Email Delay'); ?>:  
                                </div>
                                <div class="field"> 
                                    <input type="text" name="feedback_email_delay" value="<?php echo $this->result['feedback_email_delay']; ?>" class="inputbox jsst-postsetting" size="<?php echo $med_field_width; ?>" />
                                </div>
                                <div class="desc">
                                    <?php echo JText::_('Set no. of days or hours to send feedback email after ticket is closed'); ?>
                                </div>
                            </div>
                            
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo JText::_('Feedback successfully stored message'); ?>:  
                                </div>
                                <div class="field"> 
                                    <?php 
                                        $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('feedback_thanks_message', $this->result['feedback_thanks_message'], '550', '200', '40', '10', false);
                                    ?>
                                </div>
                            </div>
                            

                            <div class="pic-button-part">
                                <a class="next-step" href="#"  onclick="document.getElementById('jssupportticket-form-ins').submit();" >
                                    <?php echo JText::_('Next'); ?>
                                    <img src="components/com_jssupportticket/include/images/postinstallation/next-arrow-2.png">
                                </a>
                                <a class="back" href="index.php?option=com_jssupportticket&c=postinstallation&layout=steptwo"> 
                                   <img src="components/com_jssupportticket/include/images/postinstallation/back-arrow.png">
                                    <?php echo JText::_('Back'); ?>
                                </a>
                            </div>
                            
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden" name="c" value="postinstallation" />
                            <input type="hidden" name="layout" value="stepthree" />
                            <input type="hidden" name="step" value="3">
                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                            <?php echo JHtml::_( 'form.token' ); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
