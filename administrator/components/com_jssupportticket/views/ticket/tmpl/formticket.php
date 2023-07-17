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
jimport('joomla.html.pane');
JHTML::_('behavior.formvalidator');
/*
JHtml::_('stylesheet', 'system/calendar-jos.css', array('version' => 'auto', 'relative' => true), $attribs);
JHtml::_('script', $tag . '/calendar.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', $tag . '/calendar-setup.js', array('version' => 'auto', 'relative' => true));
*/
$document = JFactory::getDocument();
$document->addScript('components/com_jssupportticket/include/js/file/file_validate.js');
JText::script('Error file size too large');
JText::script('Error file extension mismatch');

$dash = '-';
$dateformat = $this->config['date_format'];
$firstdash = strpos($dateformat, $dash, 0);
$firstvalue = substr($dateformat, 0, $firstdash);
$firstdash = $firstdash + 1;
$seconddash = strpos($dateformat, $dash, $firstdash);
$secondvalue = substr($dateformat, $firstdash, $seconddash - $firstdash);
$seconddash = $seconddash + 1;
$thirdvalue = substr($dateformat, $seconddash, strlen($dateformat) - $seconddash);
$js_dateformat = '%' . $firstvalue . $dash . '%' . $secondvalue . $dash . '%' . $thirdvalue;

?>

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'saveticket' || task == 'saveticketandnew' || task == 'saveticketsave') {
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

    function validate_form(f)
    {
        if (document.formvalidator.isValid(f)) {
            f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken(); else echo JSession::getFormToken(); ?>';//send token
        } else {
            alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
        return true;
    }

        /*
    function validate_duedate(){
        var date_start_make = new Array();
        var split_start_value = new Array();
        var start_string = document.getElementById("ticket_duedate").value;
            var format_type = document.getElementById("js_dateformat").value;
            var current_date = document.getElementById("current_date").value;
            if (format_type == 'd-m-Y') {
                split_start_value = start_string.split('-');

                date_start_make['year'] = split_start_value[2];
                date_start_make['month'] = split_start_value[1];
                date_start_make['day'] = split_start_value[0];


            } else if (format_type == 'm-d-Y') {
                split_start_value = start_string.split('-');
                date_start_make['year'] = split_start_value[2];
                date_start_make['month'] = split_start_value[0];
                date_start_make['day'] = split_start_value[1];


            } else if (format_type == 'Y-m-d') {

                split_start_value = start_string.split('-');

                date_start_make['year'] = split_start_value[0];
                date_start_make['month'] = split_start_value[1];
                date_start_make['day'] = split_start_value[2];


            }

            var duedate = new Date(date_start_make['year'], date_start_make['month'] - 1, date_start_make['day']);
             console.log(duedate);
             console.log(current_date);


        return false;
    }
*/
</script>
<div id="userpopupblack" style="display:none;"></div>
<div id="userpopup" style="display:none;">
    <div class="">
        <form id="userpopupsearch">
            <div class="search-center">
                <div class="search-center-heading"><?php echo JText::_('Select user'); ?><span class="close"></span></div>
                <div class="js-col-md-12">
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="username" id="username" placeholder="<?php echo JText::_('Username'); ?>" />
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="name" id="name" placeholder="<?php echo JText::_('Name'); ?>" />
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="emailaddress" id="emailaddress" placeholder="<?php echo JText::_('Email Address'); ?>"/>
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value-button">
                        <div class="js-button">
                            <input class="js-button-search" type="submit" value="<?php echo JText::_('Search'); ?>" />
                        </div>
                        <div class="js-button">
                            <input class="js-button-reset" type="submit" onclick="document.getElementById('name').value = '';document.getElementById('username').value = ''; document.getElementById('emailaddress').value = '';" value="<?php echo JText::_('Reset'); ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="records">
        <div id="records-inner">
            <div class="js-staff-searc-desc">
                <?php echo JText::_('Use Search Feature To Select The User'); ?>
            </div>
        </div>
    </div>
</div>
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
                        <li><?php echo JText::_('Submit Ticket'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo JText::_('Create Ticket'); ?></h1></div>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
            <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <?php
            $count = count($this->fieldsordering);
            $i = 0; // for userfield numbering
                if($count>0){
            foreach ($this->fieldsordering AS $field) { ?>
                <?php switch ($field->field) {
                        case 'users':
                            if ($field->published == 1) {  ?>
                                <div class="js-form-wrapper">
                                    <div class="js-title"><label for="email"><?php echo JText::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                    <div class="js-value">
                                        <?php if (isset($this->editticket->uid) && $this->editticket->uid!=0) {?>
                                            <div id="username-div"><input type="text" class="<?php if($field->required == 1) echo ' required'; ?>" value="<?php if(isset($this->data['username-text'])) echo $this->data['username-text']; else echo $this->editticket->name; ?>" id="username-text" name="username-text" readonly="readonly" /></div>
                                            <?php } else {
                                            ?>
                                            <div id="username-div"></div><input type="text" class="<?php if($field->required == 1) echo ' required'; ?>" value="<?php if(isset($this->data['username-text'])) echo $this->data['username-text']; ?>" id="username-text" name="username-text" readonly="readonly" /><a href="#" id="userpopup"><?php echo JText::_('Select User'); ?></a>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                        break;
                    case 'email': ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                            <div class="js-title">
                                    <label for="email"><?php echo JText::_($field->fieldtitle); ?>:&nbsp;<font color="red">*</font></label>
                            </div>
                            <div class="js-value">
                                <input class="inputbox required validate-email" type="text" id="email" name="email" size="40" maxlength="255" value="<?php if(isset($this->data['email'])) echo $this->data['email']; elseif (isset($this->editticket)) echo $this->editticket->email; ?>" />
                            </div>
                        </div>
                        <?php
                    } ?>
                    <?php break;
                    case 'fullname':
                        ?>
                    <?php if ($field->published == 1) { ?>
                            <div class="js-form-wrapper">
                                <div class="js-title">
                                    <label for="name"><?php echo JText::_($field->fieldtitle); ?>:&nbsp;<font color="red">*</font></label>
                                </div>
                                <div class="js-value">
                                    <input class="inputbox required" type="text" name="name" id="name" size="40" maxlength="255" value="<?php if(isset($this->data['name'])) echo $this->data['name']; elseif (isset($this->editticket)) echo $this->editticket->ticketname; ?>" />
                                </div>
                            </div>
                        <?php } ?>
                        <?php break;
                    case 'phone':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                                <div class="js-title">
                                    <label for="phone"><?php echo JText::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                                </div>
                                <div class="js-value">
                                    <input class="inputbox <?php if($field->required == 1) echo ' required'; ?>" type="text" name="phone" id="phone" size="40" maxlength="255" value="<?php if(isset($this->data['phone'])) echo $this->data['phone']; elseif (isset($this->editticket)) echo $this->editticket->phone; ?>" />
                                </div>
                        </div>
                        <?php } ?>
                        <?php break;
                    case 'phoneext':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                                <div class="js-title">
                                    <label for="phoneext"><?php echo JText::_($field->fieldtitle); ?>:&nbsp;</label>
                                </div>
                                <div class="js-value">
                                    <input class="inputbox" type="text" name="phoneext" id="phoneext" size="5" maxlength="255" value="<?php if(isset($this->data['phoneext'])) echo $this->data['phoneext']; elseif (isset($this->editticket)) echo $this->editticket->phoneext; ?>" />
                                </div>
                        </div>
                        <?php } ?>
                        <?php break;
                    case 'department':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                            <div class="js-title">
                                <label for="departmentid"><?php echo JText::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                            </div>
                            <div class="js-value js-export-row-alue">
                                <?php echo $this->lists['departments']; ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php break;
                    case 'helptopic':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                            <div class="js-title">
                                <label for="helptopicid"><?php echo JText::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                            </div>
                            <div class="js-value js-export-row-alue select-field-null-margin" id="helptopic">
                                <?php echo $this->lists['helptopic']; ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php break;
                    case 'priority':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                            <div class="js-title">
                                <label for="priorityid"><?php echo JText::_($field->fieldtitle); ?>:&nbsp;<font color="red">*</font></label>
                            </div>
                            <div class="js-value js-export-row-alue select-field-null-margin">
                                <?php echo $this->lists['priorities']; ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php break;
                    case 'subject':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                        <div class="js-title">
                            <label for="subject"><?php echo JText::_($field->fieldtitle); ?>:&nbsp;<font color="red">*</font></label>
                        </div>
                        <div class="js-value">
                            <input style="width:100%" class="inputbox required" type="text" name="subject" id="subject" size="40" maxlength="255" value="<?php if(isset($this->data['subject'])) echo $this->data['subject']; elseif (isset($this->editticket)) echo $this->editticket->subject; ?>" />
                        </div>
                        </div>
                            <?php } ?>
                                <?php break;
                    case 'premade':
                                ?>
                        <?php if ($field->published == 1) { ?>
                            <?php //if (!isset($this->editticket)) { ?>
                              <div class="js-form-wrapper">
                                <div class="js-title">
                                    <label for="premadeid"><?php echo JText::_($field->fieldtitle); ?>:&nbsp;</label>
                                </div>
                                <div class="js-value" id="premades">
                                    <?php echo $this->lists['premade']; ?>
                                </div>
                        </div>
                            <?php //} ?>
                        <?php } ?>
                    <?php break; ?> <?php
                case 'issuesummary': ?>
                    <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper fullwidth">
                        <div class="js-title"><?php echo JText::_('Canned Response'); ?>
                            </div>
                            <div class="js-value"><div class="js-form-append">
                            <input type="checkbox" name="append" id ="append" /><label for="append"><?php echo JText::_('Append'); ?></label></div></div>
                        </div>
                        <div class="js-form-wrapper fullwidth">
                            <div class="js-title">
                                <label for="message"><?php echo JText::_($field->fieldtitle); ?>:&nbsp;<font color="red">*</font></label>
                            </div>
                            <div class="js-value">
                            <?php
                                if(isset($this->editticket)) $message = $this->editticket->message; else $message = '';
                                $editor = JFactory::getConfig()->get('editor');
                                $editor = JEditor::getInstance($editor);
                                echo $editor->display('message', $message, '', '300', '60', '20', false);
                            ?>
                        </div>
                                </div>
                        <?php } ?>
                    <?php break; ?> <?php

                case 'attachments': ?>
                    <?php if ($field->published == 1) { ?>
                    <div class="js-form-wrapper fullwidth">
                    <div class="js-title">
                        <label for="attachment"><?php echo JText::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                        </div>
                        <?php 
                        if(isset($this->attachments) && count($this->attachments) > 0){
                            $attachmentreq = '';
                        }else{
                            $attachmentreq = $field->required == 1 ? 'required' : '';
                        }
                        ?>
                        <div class="js-value">
                            <div id="js-attachment-files" class="js-attachment-files">
                                <span class="js-value-attachment-text">
                                    <input type="file" class="inputbox <?php echo $attachmentreq; ?>" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
                                    <span class='js-attachment-remove'></span>
                                </span>
                            </div>
                        <div id="js-attachment-option">
                            <span class="js-attachment-ins">
                                <small><?php echo JText::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo JText::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?></small>
                            </span>
                            <span id="js-attachment-add"><?php echo JText::_('Add Files'); ?></span>
                        </div>
                            <?php
                            if (!empty($this->attachments)) {
                                $ticketid = isset($this->editticket->id) ? $this->editticket->id : '' ;
                                foreach ($this->attachments AS $attachment) {
                                    echo '
                                        <div class="js_ticketattachment">' . $attachment->filename . ' ( ' . $attachment->filesize . ' ) ' . '<a href="index.php?option=com_jssupportticket&c=ticket&task=deleteattachment&id=' . $attachment->id . '&ticketid=' . $ticketid. '&'.JSession::getFormToken().'=1">' . JText::_('Delete Attachment') . '</a></div>';
                                    }
                                }
                                ?>

                        </div>
                        </div>
                                    <?php } ?>
                                    <?php break;
                    case 'internalnotetitle':
                                    ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                                <div class="js-title">
                                    <label for="internalnotetitle"><?php echo JText::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                                    </div>
                                    <div class="js-value">
                                        <input class="inputbox <?php if($field->required == 1) echo ' required'; ?>" type="text" name="internalnotetitle" id="internalnotetitle" size="40" maxlength="255" value="<?php if(isset($this->data['internalnotetitle'])) echo $this->data['internalnotetitle']; ?>" />
                                    </div>
                                </div>
                                <div class="js-form-wrapper fullwidth">
                                    <div class="js-title" >
                                        <label for="message"><?php echo JText::_('Internal Note'); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                                    </div>
                                    <div class="js-value" id="internal_note">
                                        <?php
                                            $editor = JFactory::getConfig()->get('editor');
                                            $editor = JEditor::getInstance($editor);
                                            echo $editor->display('internalnote', '', '550', '300', '60', '20', false); ?>
                                    </div>
                                </div>
                                    <?php } ?>
                                    <?php break;
                    case 'assignto':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                        <div class="js-title">
                            <label for="staffid"> <?php echo JText::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                        </div>
                        <div class="js-value js-export-row-alue select-field-null-margin" id="assignto">
                            <?php echo $this->lists['assignto']; ?>
                        </div>
                    </div>
                        <?php } ?>
                        <?php break;
                    case 'duedate':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                        <div class="js-title">
                            <label for="ticket_duedate"><?php echo JText::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                        </div>
                        <div class="js-value">
                        <?php
                        if(isset($this->data['duedate']))
                            $duedate = $this->data['duedate'];
                        elseif(isset($this->editticket->duedate))
                            $duedate = $this->editticket->duedate;
                        else
                            $duedate = '';
                        if($field->required == 1) $required = "required"; else $required = '';
                        if (isset($this->editticket)) { //edit
                            echo JHTML::_('calendar', $duedate, 'duedate', 'ticket_duedate', $js_dateformat, array('class' => 'inputbox '.$required, 'size' => '10', 'maxlength' => '19')); ?>
                        <?php
                            }else {
                                echo JHTML::_('calendar', $duedate, 'duedate', 'ticket_duedate', $js_dateformat, array('class' => 'inputbox '.$required, 'size' => '10', 'maxlength' => '19'));
                            } ?>
                        </div>
                                </div>
                            <?php } ?>
                            <?php break;
                    case 'status':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper fullwidth">
                        <div class="js-title">
                            <label for="active"> <?php echo JText::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                        </div>
                        <div class="js-value-radio-btn">
                            <div class="jsst-formfield-status-radio-button-wrap">
                            <input type="radio" id="open" value="0" name="status"<?php if (isset($this->editticket)) {if ($this->editticket->status == 0) echo "checked=''"; }else{ echo "checked=''";} ?> /><label for="open"><?php echo JText::_('Open'); ?></label>
                            </div>
                            <div class="jsst-formfield-status-radio-button-wrap">
                            <input type="radio" id="close" value="4" name="status"<?php if (isset($this->editticket)) {if ($this->editticket->status == 4) echo "checked=''"; } ?> /><label for="close"><?php echo JText::_('Close'); ?></label>
                            </div>
                            <div class="jsst-formfield-status-radio-button-wrap">
                            <input type="radio" id="waitinadminreply" value="1" name="status"<?php if (isset($this->editticket)) {if ($this->editticket->status == 1) echo "checked=''"; } ?> /><label for="waitinadminreply"><?php echo JText::_('Waiting for admin/staff reply'); ?></label>
                            </div>
                            <div class="jsst-formfield-status-radio-button-wrap">
                            <input type="radio" id="waitincustomerreply" value="3" name="status"<?php if (isset($this->editticket)) {if ($this->editticket->status == 3) echo "checked=''"; } ?> /><label for="waitincustomerreply"><?php echo JText::_('Waiting for customer reply'); ?></label></div>
                        </div>
                                </div>
                            <?php } ?>
                            <?php
                            break;
                    default:
                        $params = NULL;
                        $id = NULL;
                        $isadmin = true;
                        $j = 0;
                        if(isset($this->editticket)){
                            $id = $this->editticket->id; 
                            $params = $this->editticket->params; 
                        }
                        echo getCustomFieldClass()->formCustomFields($field , $id , $params ,$isadmin );
                        break;
            }
                ?>
            <?php }  // end of fieldsordering foreach 

        }else{
            messageslayout::getPermissionNotAllow(); //permission not granted
        }?>
                
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Submit Ticket'); ?>" /></div></div>

                <input type="hidden" name="id" id="id" value="<?php if (isset($this->editticket)) echo $this->editticket->id; ?>" />
                <input type="hidden" name="isoverdue" id="isoverdue" value="<?php if (isset($this->editticket)) echo $this->editticket->isoverdue; ?>" />
                <input type="hidden" name="ticketid" id="ticketid" value="<?php if (isset($this->editticket)) echo $this->editticket->ticketid; ?>" />
                <input type="hidden" name="c" id="c" value="ticket" />
                <input type="hidden" name="task" id="task" value="saveticket" />
                <input type="hidden" name="uid" id="uid" value="<?php if(isset($this->editticket)) echo $this->editticket->uid; ?>" />
                <input type="hidden" name="view" id="view" value="ticket" />
                <input type="hidden" name="layout" id="layout" value="formticket" />
                <input type="hidden" name="check" id="check" value="" />
                <input type="hidden" name="option" id="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="created" id="created" value="<?php if (isset($this->editticket)) echo $this->editticket->created; else echo $curdate = date('Y-m-d H:i:s'); ?>"/>
                <input type="hidden" name="update" id="update" value="<?php if (isset($this->editticket)) echo $update = date('Y-m-d H:i:s'); ?>"/>
                <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
<script type="text/javascript">
    function gethelptopicandpremade(src,src1, val) {
        jQuery('div#'+src).html("Loading...");
        jQuery('div#'+src1).html("Loading...");
        jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=listhelptopicandpremade&<?php echo JSession::getFormToken(); ?>=1",{val:val},function(data){
            if(data){
                var obj = eval("(" + data + ")");
                jQuery('div#'+src).html(obj.helptopic); //retuen value
                jQuery('div#'+src1).html(obj.premade); //retuen value
            }
        });
    }

    function isTinyMCE(){
        is_tinyMCE_active = false;
        if (typeof(tinyMCE) != "undefined") {
            if(tinyMCE.editors.length > 0){
                is_tinyMCE_active = true;
            }
        }
        return is_tinyMCE_active;
    }

    function getpremade(src, val, append) {
        jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getpremadeforinternalnote&<?php echo JSession::getFormToken(); ?>=1",{val:val},function(data){
            if(data){
                if (append == true) {
                    if(isTinyMCE()){
                        var content = tinyMCE.get('message').getContent();                        
                    }else{
                        var content = jQuery('textarea#message').val();
                    }
                    content = content + data;
                    if(isTinyMCE()){
                        tinyMCE.get('message').execCommand('mceSetContent', false, content);                        
                    }else{
                        jQuery('textarea#message').val(content);
                    }

                } else {
                    if(isTinyMCE()){
                        tinyMCE.get('message').execCommand('mceSetContent', false, data);                        
                    }else{
                        jQuery('textarea#message').val(data);
                    }
                }
            }
        });
    }

    jQuery("#js-attachment-add").click(function () {
        var obj = this;
        var current_files = jQuery('input[name="filename[]"]').length;
        var total_allow =<?php echo $this->config['noofattachment']; ?>;
        var append_text = "<span class='js-value-attachment-text'><input name='filename[]' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlenght='30' /><span  class='js-attachment-remove'></span></span>";
        if (current_files < total_allow) {
            jQuery(".js-attachment-files").append(append_text);
        } else if ((current_files === total_allow) || (current_files > total_allow)) {
            alert("<?php echo JText::_('File upload limit exceed'); ?>");
            jQuery(obj).hide();
        }
    });

    jQuery(document).delegate(".js-attachment-remove", "click", function (e) {
        var current_files = jQuery('input[name="filename[]"]').length;
        if(current_files!=1)
            jQuery(this).parent().remove();
        var current_files = jQuery('input[name="filename[]"]').length;
        var total_allow =<?php echo $this->config['noofattachment']; ?>;
        if (current_files < total_allow) {
            jQuery("#js-attachment-add").show();
        }
    });
    function updateuserlist(pagenum){
        var name = jQuery("input#name").val();
        var username = jQuery("input#username").val();
        var emailaddress = jQuery("input#emailaddress").val();
        jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1", {name:name,username:username,emailaddress:emailaddress,userlimit:pagenum}, function (data) {
            if(data){
                jQuery("div#records").html("");
                jQuery("div#records").html(data);
                setUserLink();
            }
        });
    }
    function setUserLink() {
        jQuery("a.js-userpopup-link").each(function () {
            var anchor = jQuery(this);
            jQuery(anchor).click(function (e) {
                var id = jQuery(this).attr('data-id');
                var name = jQuery(this).html();
                var email = jQuery(this).attr('data-email');
                var displayname = jQuery(this).attr('data-name');
                jQuery("input#username-text").val(name);
                if(jQuery('input#name').val() == ''){
                    jQuery('input#name').val(displayname);
                }
                if(jQuery('input#email').val() == ''){
                    jQuery('input#email').val(email);
                }
                jQuery("input#uid").val(id);
                jQuery("div#userpopup").slideUp('slow', function () {
                    jQuery("div#userpopupblack").hide();
                });
            });
        });
    }
        jQuery(document).ready(function () {
            jQuery("a#userpopup").click(function (e) {
                e.preventDefault();
                jQuery("div#userpopupblack").show();
                jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1",{},function(data){
                  if(data){
                    jQuery('div#records').html("");
                    jQuery('div#records').html(data);
                    setUserLink();
                  }
                });
                jQuery("div#userpopup").slideDown('slow');
            });
            jQuery("form#userpopupsearch").submit(function (e) {
                e.preventDefault();
                var name = jQuery("input#name").val();
                var username = jQuery("input#username").val();
                var emailaddress = jQuery("input#emailaddress").val();
                jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1",{name: name, emailaddress: emailaddress,username:username}, function (data) {
                    if (data) {
                        jQuery("div#records").html(data);
                        setUserLink();
                    }
                });//jquery closed
            });
            jQuery("span.close, div#userpopupblack").click(function (e) {
                jQuery("div#userpopup").slideUp('slow', function () {
                    jQuery("div#userpopupblack").hide();
                });

            });
        });
		function getDataForDepandantField(parentf, childf, type) {
			if (type == 1) {
				var val = jQuery("select#" + parentf).val();
			} else if (type == 2) {
				var val = jQuery("input[name=" + parentf + "]:checked").val();
			}
			jQuery.post('index.php?option=com_jssupportticket&c=userfields&task=datafordepandantfield&<?php echo JSession::getFormToken(); ?>=1', {fvalue: val, child: childf}, function (data) {
				if (data) {
					console.log(data);
					var d = jQuery.parseJSON(data);
					jQuery("select#" + childf).replaceWith(d);
				}
			});
		}

		function deleteCutomUploadedFile (field1) {
			jQuery("input#"+field1).val(1);
			jQuery("span."+field1).hide();
			
		}        
</script>
