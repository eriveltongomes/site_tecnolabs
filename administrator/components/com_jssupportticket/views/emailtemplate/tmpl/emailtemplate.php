<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
?>

<script language="javascript">
    // for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'save') {
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
                        <li><?php echo JText::_('Email Templates'); ?></li>
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
        
        <div id="js-tk-heading"> <h1 class="jsstadmin-head-text"><?php echo JText::_('Email Templates'); ?></h1> </div>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >
            <div class="js-email-menu">
                <?php $link = 'index.php?option='.$this->option.'&c=emailtemplate&layout=emailtemplate&tf='; ?>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'ew-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>ew-tk"><?php echo JText::_('New Ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'sntk-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>sntk-tk"><?php echo JText::_('Staff Ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'ew-md') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>ew-md"><?php echo JText::_('New Department'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'ew-sm') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>ew-sm"><?php echo JText::_('New Staff'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'ew-ht') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>ew-ht"><?php echo JText::_('New Help Topic'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'rs-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>rs-tk"><?php echo JText::_('Reassign Ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'cl-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>cl-tk"><?php echo JText::_('Close Ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'dl-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>dl-tk"><?php echo JText::_('Delete Ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'mo-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>mo-tk"><?php echo JText::_('Mark Overdue'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'be-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>be-tk"><?php echo JText::_('Ban email'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'be-trtk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>be-trtk"><?php echo JText::_('Ban email try to create ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'dt-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>dt-tk"><?php echo JText::_('Department transfer'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'ebct-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>ebct-tk"><?php echo JText::_('Ban Email And Close Ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'ube-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>ube-tk"><?php echo JText::_('Unban Email'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'rsp-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>rsp-tk"><?php echo JText::_('Response Ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'rpy-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>rpy-tk"><?php echo JText::_('Reply Ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'tk-ew-ad') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>tk-ew-ad"><?php echo JText::_('New Ticket Admin Alert'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'lk-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>lk-tk"><?php echo JText::_('Lock Ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'ulk-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>ulk-tk"><?php echo JText::_('Unlock ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'minp-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>minp-tk"><?php echo JText::_('In progress ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'pc-tk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>pc-tk"><?php echo JText::_('Ticket priority is changed by'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'ml-ew') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>ml-ew"><?php echo JText::_('New Mail Receviced'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'ml-rp') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>ml-rp"><?php echo JText::_('New Mail Message Recevied'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'fd-bk') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>fd-bk"><?php echo JText::_('Feedback Email To User'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'no-rp') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>no-rp"><?php echo JText::_('User Reply On Closed Ticket'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'd-us-da-ad') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>d-us-da-ad"><?php echo JText::_('Erase user data request for admin'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'd-us-da') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>d-us-da"><?php echo JText::_('Erase user request data'); ?></a></span>
                <span class="js-email-menu-link <?php if ($this->templatefor == 'u-da-de') echo 'selected'; ?>"><a class="js-email-link" href="<?php echo $link; ?>u-da-de"><?php echo JText::_('User data deleted'); ?></a></span>
            </div>
            <!-- copid  -->
            <div class="js-email-body">
                <div class="js-form-wrapper">
                    <div class="a-js-form-title"><?php echo JText::_('Subject'); ?></div>
                    <div class="a-js-form-field"><input class="inputbox required" type="text" name="subject" id="subject" size="135" maxlength="255" value="<?php if (isset($this->template)) echo $this->template->subject; ?>" /></div>
                </div>
                <div class="js-form-wrapper">
                    <div class="a-js-form-title"><?php echo JText::_('Body'); ?></div>
                    <div class="a-js-form-field"><?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); if (isset($this->template)) echo $editor->display('body', $this->template->body, '', '300', '60', '20', false); else echo $editor->display('body', '', '', '300', '60', '20', false); ?></div>
                </div>
                <div class="js-email-parameters">
                    <span class="js-email-parameter-heading"><?php echo JText::_('Parameters') ?></span>
                    <?php
                    if ($this->templatefor == 'ew-tk') {
                        ?>
                        <span class="js-email-paramater">{USERNAME} : <?php echo JText::_('Username'); ?></span>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{HELP_TOPIC} : <?php echo JText::_('Help Topic'); ?></span>
                        <span class="js-email-paramater">{EMAIL} : <?php echo JText::_('Email'); ?></span>
                        <span class="js-email-paramater">{MESSAGE} : <?php echo JText::_('Message'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                       <?php foreach ($this->ufields as $field ) {
                                if($field->userfieldtype != 'file'){ ?>
                                    <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                        <?php   }
                            }
                    }elseif ($this->templatefor == 'sntk-tk') {
                        ?>
                        <span class="js-email-paramater">{USERNAME} : <?php echo JText::_('Username'); ?></span>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{HELP_TOPIC} : <?php echo JText::_('Help Topic'); ?></span>
                        <span class="js-email-paramater">{EMAIL} : <?php echo JText::_('Email'); ?></span>
                        <span class="js-email-paramater">{MESSAGE} : <?php echo JText::_('Message'); ?></span>
                        <span class="js-email-paramater">{TICKETURL} : <?php echo JText::_('Ticket URL'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                 if($field->userfieldtype != 'file'){ ?>
                                     <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                         <?php   }
                             }
                    } elseif ($this->templatefor == 'ew-md') {
                        ?>
                        <span class="js-email-paramater">{DEPARTMENT_TITLE} : <?php echo JText::_('Department title'); ?></span>
                        <?php
                    } elseif ($this->templatefor == 'ew-gr') {
                        ?>
                        <span class="js-email-paramater">{GROUP_TITLE} : <?php echo JText::_('Group Title'); ?></span>
                        <?php
                    } elseif ($this->templatefor == 'ew-sm') {
                        ?>
                        <span class="js-email-paramater">{STAFF_MEMBER_NAME} : <?php echo JText::_('Staff member name'); ?></span>
                        <?php
                    } elseif ($this->templatefor == 'ew-ht') {
                        ?>
                        <span class="js-email-paramater">{HELPTOPIC_TITLE} : <?php echo JText::_('Help topic title'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT_TITLE} : <?php echo JText::_('Department title'); ?></span>
                        <?php
                    } elseif ($this->templatefor == 'rs-tk') {
                        ?>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{STAFF_MEMBER_NAME} : <?php echo JText::_('Staff member name'); ?></span>
                        <span class="js-email-paramater">{TICKETURL} : <?php echo JText::_('Ticket URL'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                 if($field->userfieldtype != 'file'){ ?>
                                     <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                         <?php   }
                             }
                    } elseif ($this->templatefor == 'cl-tk') {
                        ?>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{TICKETURL} : <?php echo JText::_('Ticket URL'); ?></span>
                        <span class="js-email-paramater">{FEEDBACKURL} : <?php echo JText::_('Feedback URL'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                if($field->userfieldtype != 'file'){ ?>
                                    <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                        <?php   }
                            }
                    } elseif ($this->templatefor == 'dl-tk') {
                        ?>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <?php
                    } elseif ($this->templatefor == 'mo-tk') {
                        ?>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{TICKETURL} : <?php echo JText::_('Ticket URL'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                if($field->userfieldtype != 'file'){ ?>
                                    <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                        <?php   }
                            }
                    } elseif ($this->templatefor == 'be-tk') {
                        ?>
                        <span class="js-email-paramater">{EMAIL_ADDRESS} : <?php echo JText::_('Email address'); ?></span>
                        <?php
                    } elseif ($this->templatefor == 'be-trtk') {
                        ?>
                        <span class="js-email-paramater">{EMAIL_ADDRESS} : <?php echo JText::_('Email address'); ?></span>
                        <?php
                    } elseif ($this->templatefor == 'dt-tk') {
                        ?>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT_TITLE} : <?php echo JText::_('Department title'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                if($field->userfieldtype != 'file'){ ?>
                                    <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                        <?php   }
                            }
                    } elseif ($this->templatefor == 'ebct-tk') {
                        ?>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{EMAIL_ADDRESS} : <?php echo JText::_('Email address'); ?></span>
                        <span class="js-email-paramater">{TICKETID} : <?php echo JText::_('Ticket Id'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                if($field->userfieldtype != 'file'){ ?>
                                    <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                        <?php   }
                            }

                    } elseif ($this->templatefor == 'ube-tk') {
                        ?>
                        <span class="js-email-paramater">{EMAIL_ADDRESS} : <?php echo JText::_('Email address'); ?></span>
                        <?php
                    } elseif ($this->templatefor == 'rsp-tk') {
                        ?>
                        <span class="js-email-paramater">{USERNAME} : <?php echo JText::_('Username'); ?></span>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{EMAIL} : <?php echo JText::_('Email'); ?></span>
                        <span class="js-email-paramater">{MESSAGE} : <?php echo JText::_('Message'); ?></span>
                        <span class="js-email-paramater">{TICKETURL} : <?php echo JText::_('Ticket URL'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                 if($field->userfieldtype != 'file'){ ?>
                                     <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                         <?php   }
                             }
                    } elseif ($this->templatefor == 'rpy-tk') {
                        ?>
                        <span class="js-email-paramater">{USERNAME} : <?php echo JText::_('Username'); ?></span>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{EMAIL} : <?php echo JText::_('Email'); ?></span>
                        <span class="js-email-paramater">{MESSAGE} : <?php echo JText::_('Message'); ?></span>
                        <span class="js-email-paramater">{TICKETURL} : <?php echo JText::_('Ticket URL'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                 if($field->userfieldtype != 'file'){ ?>
                                     <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                         <?php   }
                             }
                    } elseif ($this->templatefor == 'tk-ew-ad') {
                        ?>
                        <span class="js-email-paramater">{USERNAME} : <?php echo JText::_('Username'); ?></span>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{EMAIL} : <?php echo JText::_('Email'); ?></span>
                        <span class="js-email-paramater">{MESSAGE} : <?php echo JText::_('Message'); ?></span>
                        <span class="js-email-paramater">{TICKETURL} : <?php echo JText::_('Ticket URL'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                 if($field->userfieldtype != 'file'){ ?>
                                     <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                         <?php   }
                             }
                    } elseif ($this->templatefor == 'lk-tk') {
                        ?>
                        <span class="js-email-paramater">{USERNAME} : <?php echo JText::_('Username'); ?></span>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{EMAIL} : <?php echo JText::_('Email'); ?></span>
                        <span class="js-email-paramater">{TICKETURL} : <?php echo JText::_('Ticket URL'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                 if($field->userfieldtype != 'file'){ ?>
                                     <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                         <?php   }
                             }
                    } elseif ($this->templatefor == 'ulk-tk') {
                        ?>
                        <span class="js-email-paramater">{USERNAME} : <?php echo JText::_('Username'); ?></span>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{EMAIL} : <?php echo JText::_('Email'); ?></span>
                        <span class="js-email-paramater">{TICKETURL} : <?php echo JText::_('Ticket URL'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                 if($field->userfieldtype != 'file'){ ?>
                                     <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                         <?php   }
                             }
                    } elseif ($this->templatefor == 'minp-tk') {
                        ?>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{TICKETURL} : <?php echo JText::_('Ticket URL'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                 if($field->userfieldtype != 'file'){ ?>
                                     <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                         <?php   }
                             }
                    } elseif ($this->templatefor == 'pc-tk') {
                        ?>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKINGID} : <?php echo JText::_('Tracking ID'); ?></span>
                        <span class="js-email-paramater">{PRIORITY_TITLE} : <?php echo JText::_('Priority'); ?></span>
                        <span class="js-email-paramater">{TICKETURL} : <?php echo JText::_('Ticket URL'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                 if($field->userfieldtype != 'file'){ ?>
                                     <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                         <?php   }
                             }
                    } elseif ($this->templatefor == 'ml-ew') {
                        ?>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{STAFF_MEMBER_NAME} : <?php echo JText::_('Staff member name'); ?></span>
                        <span class="js-email-paramater">{MESSAGE} : <?php echo JText::_('Message'); ?></span>
                        <?php
                    } elseif ($this->templatefor == 'ml-rp') {
                        ?>
                        <span class="js-email-paramater">{SUBJECT} : <?php echo JText::_('Subject'); ?></span>
                        <span class="js-email-paramater">{STAFF_MEMBER_NAME} : <?php echo JText::_('Staff member name'); ?></span>
                        <span class="js-email-paramater">{MESSAGE} : <?php echo JText::_('Message'); ?></span>
                        <?php
                    } elseif ($this->templatefor == 'fd-bk') {
                        ?>
                        <span class="js-email-paramater">{USER_NAME} : <?php echo JText::_('Username'); ?></span>
                        <span class="js-email-paramater">{TICKET_SUBJECT} : <?php echo JText::_('Ticket Subject'); ?></span>
                        <span class="js-email-paramater">{TRACKING_ID} : <?php echo JText::_('Ticket Tracking ID'); ?></span>
                        <span class="js-email-paramater">{CLOSE_DATE} : <?php echo JText::_('Close Date'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                 if($field->userfieldtype != 'file'){ ?>
                                     <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                         <?php   }
                             }
                    } elseif ($this->templatefor == 'no-rp') {
                        ?>
                        <span class="js-email-paramater">{TICKET_SUBJECT} : <?php echo JText::_('Ticket Subject'); ?></span>
                        <span class="js-email-paramater">{DEPARTMENT} : <?php echo JText::_('Department'); ?></span>
                        <span class="js-email-paramater">{PRIORITY} : <?php echo JText::_('Priority'); ?></span>
                        <?php foreach ($this->ufields as $field ) {
                                 if($field->userfieldtype != 'file'){ ?>
                                     <span class="js-email-paramater">{<?php echo $field->field;?>} : <?php echo JText::_($field->fieldtitle); ?></span>
                         <?php   }
                             }
                    }elseif($this->templatefor == 'd-us-da'){ ?>
                        <span class="js-email-paramater">{USERNAME} : <?php echo JText::_('Username'); ?></span>
                    <?php
                    }elseif($this->templatefor == 'd-us-da-ad'){ ?>
                        <span class="js-email-paramater">{USERNAME} : <?php echo JText::_('Username'); ?></span>
                    <?php
                    }elseif($this->templatefor == 'u-da-de'){ ?>
                        <span class="js-email-paramater">No params</span>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <!-- End Copied -->
            <input type="hidden" name="check" value="post"/>
            <?php if (isset($this->template)) {if (($this->template->created == '0000-00-00 00:00:00') || ($this->template->created == '')) $curdate = date('Y-m-d H:i:s'); else $curdate = $this->template->created; } else $curdate = date('Y-m-d H:i:s'); ?>
            <input type="hidden" name="created" value="<?php echo $curdate; ?>" />
            <input type="hidden" name="c" value="emailtemplate" />
            <input type="hidden" name="uid" value="<?php echo $this->uid; ?>" />
            <input type="hidden" name="id" value="<?php echo $this->template->id; ?>" />
            <input type="hidden" name="templatefor" value="<?php echo $this->template->templatefor; ?>" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="task" value="saveemailtemplate" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
