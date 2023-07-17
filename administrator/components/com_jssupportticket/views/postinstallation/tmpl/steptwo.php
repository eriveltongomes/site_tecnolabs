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

$yesno = array(
    '0' => array('value' => '1',
        'text' => JText::_('Yes')),
    '1' => array('value' => '0',
        'text' => JText::_('No')),);
$ticketidsequence = array(
    '0' => array('value' => '1',
        'text' => JText::_('Random')),
    '1' => array('value' => '2',
        'text' => JText::_('Sequential')),);
$owncaptchaoparend = array(
    array('value' => '2', 'text' => JText::_('2')),
    array('value' => '3', 'text' => JText::_('3'))
);
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
                        <li class="header-parts second-part active">
                           <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=steptwo" title="link" class="tab_icon">
                               <img class="start" src="components/com_jssupportticket/include/images/postinstallation/ticket.png" />
                                <span class="text"><?php echo JText::_('Ticket Setting'); ?></span>
                            </a>
                        </li>
                        <li class="header-parts third-part">
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
                        <span class="heading-post-ins jsst-configurations-heading"><?php echo JText::_('Ticket Configurations');?></span>
                        <span class="heading-post-ins jsst-config-steps"><?php echo JText::_('Step 2 of 4');?></span>
                    </div>
                    <div class="post-installtion-content">
                        <form id="jssupportticket-form-ins" method="post" action="index.php">
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo JText::_('Visitor can create ticket'); ?>:  
                                </div>
                                <div class="field"> 
                                    <?php echo JHTML::_('select.genericList', $yesno, 'visitor_can_create_ticket', 'class="inputbox jsst-postsetting" ' . '', 'value', 'text', $this->result['visitor_can_create_ticket']); ?>
                                </div>
                                <div class="desc">
                                    <?php echo JText::_('Can visitor create ticket or not'); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo JText::_('Ticketid sequence'); ?>:  
                                </div>
                                <div class="field"> 
                                    <?php echo JHTML::_('select.genericList', $ticketidsequence, 'ticketid_sequence', 'class="inputbox jsst-postsetting" ' . '', 'value', 'text', $this->result['ticketid_sequence']); ?>
                                </div>
                                <div class="desc">
                                    <?php echo JText::_('Set ticketid sequential or random'); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo JText::_('Maximum tickets'); ?>:  
                                </div>
                                <div class="field"> 
                                    <input type="text" name="maximum_ticket" value="<?php echo $this->result['maximum_ticket']; ?>" class="inputbox jsst-postsetting" />
                                </div>
                                <div class="desc">
                                    <?php echo JText::_('Maximum ticket per user'); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo JText::_('Maximum open tickets'); ?>:  
                                </div>
                                <div class="field"> 
                                    <input type="text" name="ticket_per_email" value="<?php echo $this->result['ticket_per_email']; ?>" class="inputbox jsst-postsetting" />
                                </div>
                                <div class="desc">
                                    <?php echo JText::_('Maximum opened tickets per user'); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo JText::_('Reopen ticket within days'); ?>:  
                                </div>
                                <div class="field"> 
                                    <input type="text" name="ticket_reopen_within_days" value="<?php echo $this->result['ticket_reopen_within_days']; ?>" class="inputbox jsst-postsetting" />
                                </div>
                                <div class="desc">
                                    <?php echo JText::_('Ticket can be reopen within given number of days'); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo JText::_('Show Captcha To Visitor On Form Ticket'); ?>:  
                                </div>
                                <div class="field"> 
                                    <?php echo JHTML::_('select.genericList', $yesno, 'show_captcha_visitor_form_ticket', 'class="inputbox jsst-postsetting" ' . '', 'value', 'text', $this->result['show_captcha_visitor_form_ticket']); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo JText::_('Own captcha operands'); ?>:  
                                </div>
                                <div class="field"> 
                                    <?php echo JHTML::_('select.genericList', $owncaptchaoparend, 'owncaptcha_totaloperand', 'class="inputbox jsst-postsetting" ' . '', 'value', 'text', $this->result['owncaptcha_totaloperand']); ?>
                                </div>
                                <div class="desc">
                                    <?php echo JText::_('Select the total operands to be given'); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo JText::_('Own captcha subtraction answer positive'); ?>:  
                                </div>
                                <div class="field"> 
                                    <?php echo JHTML::_('select.genericList', $yesno, 'owncaptcha_subtractionans', 'class="inputbox jsst-postsetting" ' . '', 'value', 'text', $this->result['owncaptcha_subtractionans']); ?>
                                </div>
                                <div class="desc">
                                    <?php echo JText::_('Is subtraction answer should be positive'); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo JText::_('Enable print ticket'); ?>:  
                                </div>
                                <div class="field"> 
                                    <?php echo JHTML::_('select.genericList', $yesno, 'print_ticket_user', 'class="inputbox jsst-postsetting" ' . '', 'value', 'text', $this->result['print_ticket_user']); ?>
                                </div>
                                <div class="desc">
                                    <?php echo JText::_('show print ticket icon on ticketdetail page to user'); ?>
                                </div>
                            </div>


                            <div class="pic-button-part">
                                <a class="next-step" href="index.php?option=com_jssupportticket&c=postinstallation&layout=stepthree"  onclick="document.getElementById('jssupportticket-form-ins').submit();" >
                                    <?php echo JText::_('Next'); ?>
                                    <img src="components/com_jssupportticket/include/images/postinstallation/next-arrow-2.png">
                                </a>
                                <a class="back" href="index.php?option=com_jssupportticket&c=postinstallation&layout=stepone"> 
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
