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
<div class="js-row js-null-margin">
    <?php
    $isstaff = $this->user->getIsStaff();
    $per_ticket = true;
    $isstaffdisable = true;
    if($isstaff){
        $per_granted = ($this->id == '') ? $this->ticket_permissions['Add Ticket'] : $this->ticket_permissions['Edit Ticket'];
        $per_ticket = ($per_granted == 1) ? true : false;
        $isstaffdisable = !($this->user->getIsStaffDisable());
    }else{
        if($this->config['visitor_can_create_ticket'] != 1){
            if($this->user->getIsGuest()){
                $per_ticket = false;
            }
        }
    }
if($this->config['offline'] != '1'){       
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/ticket-formticket.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }?>
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
                            <?php echo JText::_('Submit Ticket'); ?>
                        </li>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php
    if($per_ticket){
        if($isstaffdisable){ ?>
            <?php
            JHTML::_('behavior.formvalidator');
            /*
            JHtml::_('stylesheet', 'system/calendar-jos.css', array('version' => 'auto', 'relative' => true), $attribs);
            JHtml::_('script', $tag . '/calendar.js', array('version' => 'auto', 'relative' => true));
            JHtml::_('script', $tag . '/calendar-setup.js', array('version' => 'auto', 'relative' => true));
            */
            $document = JFactory::getDocument();
            $document->addScript('administrator/components/com_jssupportticket/include/js/file/file_validate.js');
            JText::script('JS_ERROR_FILE_SIZE_TO_LARGE');
            JText::script('JS_ERROR_FILE_EXT_MISMATCH');
            ?>
            <div id="userpopupblack" style="display:none;"></div>
            <div id="userpopup" style="display:none;">
               <div class="jsst-popup-header">
                    <div class="popup-header-text"><?php echo JText::_('Select user'); ?></div><div class="popup-header-close-img"></div>
                </div>
                <div class="js-ticket-popup-search-wrp">
                    <form id="userpopupsearch">
                        <div class="js-ticket-search-top">
                            <div class="js-ticket-search-left">
                                <div class="js-ticket-search-fields-wrp">
                                    <input class="js-ticket-search-input-fields" type="text" name="username" id="username" placeholder="<?php echo JText::_('Username'); ?>" />
                                    <input class="js-ticket-search-input-fields" type="text" name="name" id="name" placeholder="<?php echo JText::_('Name'); ?>" />
                                    <input class="js-ticket-search-input-fields" type="text" name="emailaddress" id="emailaddress" placeholder="<?php echo JText::_('Email'); ?>" />
                                </div>
                            </div>
                            <div class="js-ticket-search-right">
                                <div class="js-ticket-search-btn-wrp">
                                    <input value="<?php echo JText::_('Search'); ?>" type="submit" class="js-ticket-search-btn">
                                    <input type="submit" class="js-ticket-reset-btn" onclick="document.getElementById('name').value = '';document.getElementById('username').value = ''; document.getElementById('emailaddress').value = '';" value="<?php echo JText::_('Reset'); ?>" />
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
                
        <?php if(!empty($this->config['new_ticket_message'])){ ?>
            <div class="js-col-xs-12 js-col-md-12 js-ticket-form-instruction-message">
                <?php echo $this->config['new_ticket_message']; ?>
            </div>
        <?php } ?>
        <div id="js-tk-formwrapper">
            <?php if(count($this->fieldsordering) > 0){ ?>
                <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm" >
                    <?php 
                    $fieldcounter = 0;
                    $i = 0;
                    $j = 0;
                    foreach($this->fieldsordering AS $field) {
                        switch($field->field){
                            case 'users':
                                if($this->user->getIsStaff()){
                                    if ($field->published == 1) {
                                        if($fieldcounter % 2 == 0){
                                            if($fieldcounter != 0){
                                                echo '</div>';
                                            }
                                            echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                        }
                                        $fieldcounter++;
                                        ?>
                                        <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                            <div class="js-form-title"><label for="email"><?php echo JText::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                            <div class="js-ticket-from-field">
                                                <?php if (isset($this->editticket->id)) { ?>
                                                    <div class="js-ticket-select-user-field">
														<input type="text" class="js-ticket-form-field-input <?php if($field->required == 1) echo ' required'; ?>" value="<?php if(isset($this->data['username-text'])) echo $this->data['username-text']; else echo $this->editticket->name; ?>" id="username-text" name="username-text" readonly="readonly" />
                                                    </div>
                                                    <div class="js-ticket-select-user-btn">
                                                        <a href="#" id="userpopup"><?php echo JText::_('Select User'); ?></a>
                                                    </div>
                                                
                                                <?php } else { ?>
                                                    <div class="js-ticket-select-user-field">
                                                        <input type="text" class="js-ticket-form-field-input <?php if($field->required == 1) echo ' required'; ?>" value="<?php if(isset($this->data['username-text'])) echo $this->data['username-text']; ?>" id="username-text" name="username-text" readonly="readonly" />
                                                    </div>
                                                    <div class="js-ticket-select-user-btn">
                                                        <a href="#" id="userpopup"><?php echo JText::_('Select User'); ?></a>
                                                    </div>
                                                <?php } ?> 
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                break;
                            case 'email':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    $readonly = '';
                                    if(isset($field->readonly) && $field->readonly == 1){
                                        $readonly = 'readonly';
                                    }
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="email"><?php echo JText::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                        <div class="js-form-value"><input class="js-form-input-field required validate-email" <?php echo $readonly;?> type="text" name="email" id="email" size="40" maxlength="255" value="<?php if(isset($this->data['email'])) echo $this->data['email']; elseif (isset($this->editticket->email)) echo $this->editticket->email;elseif (isset($this->email)) echo $this->email; ?>" /></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'fullname':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="name"><?php echo JText::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                        <div class="js-form-value"><input class="js-form-input-field required" type="text" name="name" id="name"size="40" maxlength="255" value="<?php if(isset($this->data['name'])) echo $this->data['name']; elseif (isset($this->editticket->ticketname)) echo $this->editticket->ticketname; elseif (isset($this->name)) echo $this->name; ?>" /></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'phone':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="phone"><?php echo JText::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                        <div class="js-form-value"><input class="js-form-input-field <?php if($field->required == 1) echo ' required'; ?>" type="text" name="phone" id="phone" size="40" maxlength="255" value="<?php if(isset($this->data['phone'])) echo $this->data['phone']; else echo isset($this->editticket->phone) ? $this->editticket->phone : ''; ?>" /></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'phoneext':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="phoneext"><?php echo JText::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                        <div class="js-form-value"><input class="js-form-input-field <?php if($field->required == 1) echo ' required'; ?>" type="text" name="phoneext" id="phoneext" size="5" maxlength="255" value="<?php if(isset($this->data['phoneext'])) echo $this->data['phoneext']; else echo isset($this->editticket->phoneext) ? $this->editticket->phoneext : ''; ?>" /></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'department':
                                if ($field->published == 1){
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="departmentid"><?php echo JText::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                        <div class="js-form-value"><?php echo $this->lists['departments']; ?></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'helptopic':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="helptopicid"><?php echo JText::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                        <div class="js-form-value" id="helptopic"><?php echo $this->lists['helptopic']; ?></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'priority':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="priorityid"><?php echo JText::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                        <div class="js-form-value"><?php echo $this->lists['priorities']; ?></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'subject':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-12 js-col-xs-12  js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="subject"><?php echo JText::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                        <div class="js-form-value"><input class="js-form-input-field required" type="text" name="subject" id="subject" size="40" maxlength="255" value="<?php if(isset($this->data['subject'])) echo $this->subject; elseif (isset($this->editticket->subject)) echo $this->editticket->subject; ?>" /></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'premade':
                                if($isstaff){
                                    if ($field->published == 1) {
                                        if (!isset($this->editticket->id)) {
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                         echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                        }
                                        $fieldcounter++;
                                        ?>
                                        <div class="js-col-md-12 js-col-xs-12 js-margin-bottom js-padding-null">
                                            <div class="js-form-title">
                                                <label for="premadeid"><?php echo JText::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                                            </div>
                                            <div class="js-form-value">
                                                <div class="js-append-premade" id="premades"><?php echo $this->lists['premade']; ?></div>
                                                <div class="js-append-premadecheck">
                                                    <input type="checkbox" name="append" id="append" />
                                                    <label for="append"><?php echo JText::_('Append'); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                break;
                            case 'issuesummary':
                                if ($field->published == 1) {
                                    if($fieldcounter != 0){
                                        echo '</div>';
                                    }
                                     echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                <div class="js-col-md-12 js-col-xs-12 js-margin-bottom js-padding-null">
                                    <div class="js-form-title"><label for="issuesummary"><?php echo JText::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                    <div class="js-form-value">
                                        <?php
                                            if(isset($this->editticket)) $message = $this->editticket->message; else $message = '';
                                            $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);
                                            echo $editor->display('message', $message, '550', '300', '60', '20', false);
                                        ?></div>
                                </div>
                                <?php
                                break;
                            case 'attachments':
                                $flag = false;
                                if($isstaff){
                                    if($this->ticket_permissions['Attachment'] == 1)
                                        $flag = true;
                                }else{
                                    $flag = true;
                                }
                                if($flag){
                                    if ($field->published == 1) {
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                         echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                            
                                    }
                                        $fieldcounter++;
                                        ?>
                                        <div class="js-col-md-12 js-col-xs-12 js-margin-bottom js-padding-null js-attachment-wrp">
                                            <div class="js-form-title"><?php echo JText::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></div>
                                            <?php
                                            if(isset($this->attachments) && is_array($this->attachments) && count($this->attachments) > 0){
                                                $attachmentreq = '';
                                            }else{
                                                $attachmentreq = $field->required == 1 ? 'required' : '';
                                            }
                                            ?>
                                            <div class="js-form-value js-attachment-files-wrp">
                                                <div id="js-attachment-files" class="js-attachment-files">
                                                    <span class="js-attachment-file-box">
                                                        <input type="file" class="js-form-input-field-attachment <?php echo $attachmentreq; ?>" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
                                                        <span class='js-attachment-remove'></span>
                                                    </span>
                                                </div>
                                                <div id="js-attachment-option">
                                                    <?php echo JText::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo JText::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?></small>
                                                </div>
                                                <span id="js-attachment-add"><?php echo JText::_('Add more'); ?></span>
                                            </div>
                                        </div>
                                        <?php
                                }
                                break;
                            case 'internalnotetitle':
                                if($isstaff){
                                    if ($this->ticket_permissions['Post Internal Note'] == 1) {
                                        if ($field->published == 1) {
                                            if($fieldcounter != 0){
                                                echo '</div>';
                                                }
                                                echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                                
                                        }
                                        $fieldcounter++;
                                        ?>
                                        <div class="js-col-md-12 js-col-xs-12 js-margin-bottom js-padding-null">
                                            <div class="js-form-title"><label for="internalnotetitle"><?php echo JText::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                            <div class="js-form-value"><input class="js-form-input-field <?php if($field->required == 1) echo ' required'; ?>" type="text" <?php if (isset($this->editticket->id)) { ?> placeholder="<?php echo JText::_('Reason for edit'); ?>" <?php } ?> name="internalnotetitle" id="internalnotetitle" size="40" maxlength="255" value="<?php if(isset($this->data['internalnotetitle'])) echo $this->data['internalnotetitle']; ?>" /></div>
                                        </div>
                                        <div class="js-col-md-12 js-col-xs-12 js-padding-null js-margin-top">
                                            <div class="js-form-title"><label for="message"><?php echo JText::_('Internal Note'); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                            <div class="js-form-value"><?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('internalnote', '', '550', '300', '60', '20', false); ?></div>
                                        </div>
                                            <?php
                                        }
                                    }
                                break;
                            case 'status':
                                if($isstaff){ /*
                                    if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                    }
                                    $fieldcounter++;
                                        ?>
                                        <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                            <div class="js-form-title"> <label><?php echo JText::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                            <div class="js-form-value">
                                                <div class="js-col-xs-6 tk_form_radiobox_wraper">
                                                    <input id="active" class="tk_form_chkbox" type="radio" value="0" name="status"<?php if (isset($this->editticket->status)) { if ($this->editticket->status == 0) echo "checked=''"; } else echo "checked=''"; ?> />
                                                    <label for="active" class="tk_form_chkbox_label"><?php echo JText::_('Active'); ?></label>
                                                </div>
                                                <div class="js-col-xs-6 tk_form_radiobox_wraper">
                                                    <input id="disable" class="tk_form_chkbox" type="radio" value="" name="status"<?php if (isset($this->editticket->status)) { if ($this->editticket->status == '') echo "checked=''"; } ?> />
                                                    <label for="disable" class="tk_form_chkbox_label" ><?php echo JText::_('Disabled'); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    */
                                }
                                break;
                            case 'assignto':
                                if($isstaff){
                                    if($this->ticket_permissions['Assign Ticket To Staff'] == 1){
                                        if($field->published == 1){
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                    }
                                    $fieldcounter++;
                                            ?>
                                        <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                            <div class="js-form-title"><label for="staffid"><?php echo JText::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                            <div class="js-form-value"><?php echo $this->lists['assignto']; ?></div>
                                        </div>
                                            <?php
                                        }
                                    }
                                }
                                break;
                            case 'duedate':
                                if($isstaff){
                                    if ($this->ticket_permissions['Duedate Ticket'] == 1) {
                                        if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    if(isset($this->data['duedate']))
                                        $duedate = $this->data['duedate'];
                                    elseif(isset($this->editticket->duedate))
                                        $duedate = $this->editticket->duedate;
                                    else
                                        $duedate = '';
                                            ?>
                                            <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                                <div class="js-form-title"><label for="ticket_duedate"><?php echo JText::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                                <div class="js-form-value">
                                                   <?php
                                                    if($field->required == 1) $required = "required"; else $required = '';
                                                    echo JHTML::_('calendar', $duedate, 'duedate', 'ticket_duedate', $js_dateformat, array('class' => ''.$required, 'size' => '10', 'maxlength' => '19'));
                                                   ?>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                    }
                                }
                                break;
                            default:
                                $params = NULL;
                                $id = NULL;
                                $isadmin = false;
                                if(isset($this->editticket)){
                                    $id = $this->editticket->id; 
                                    $params = $this->editticket->params; 
                                }else{
    								if(isset($this->custom_params))
    									$params = $this->custom_params;
    								else
    									$params = '';
                                }
                                switch ($field->size) {
                                    case '100':
                                        
                                            if($fieldcounter != 0){
                                                echo '</div>';
                                            }
                                            echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                       
                                        $fieldcounter++;
                                        echo getCustomFieldClass()->formCustomFields($field , $id , $params , $isadmin);
                                    break;
                                
                                    case '50':
                                        if($fieldcounter % 2 == 0){
                                            if($fieldcounter != 0){
                                                echo '</div>';
                                            }
                                            echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                        }
                                        $fieldcounter++;
                                        echo getCustomFieldClass()->formCustomFields($field , $id , $params , $isadmin );
                                    break;
                                }
                            break;
                        }
                    }

                    if($fieldcounter != 0){
                        echo '</div>';
                    }                


                    if($this->user->getIsGuest()){
                        if ($this->config['show_captcha_visitor_form_ticket'] == 1) {
							$notinvisible = 1;
							if($this->config['captcha_selection'] == 1){
								$joomla_captcha = JFactory::getConfig()->get('captcha');
								if ( $joomla_captcha == 'recaptcha_invisible') {
									$captcha_plugin = JFactory::getConfig()->get('captcha');
									$captcha = JCaptcha::getInstance($captcha_plugin);
									$field_id = 'recaptchainvb';
									print $captcha->display($field_id, $field_id, 'g-recaptcha');
									$notinvisible = 0;
								}
							}
							if($notinvisible == 1){
							?>
                            <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null js-captcha-wrp">
                                <div class="js-col-md-12 js-col-xs-12">
                                    <div class="js-form-title js-captch-title">
                                        <label id="captchamsg" for="captcha"><?php echo JText::_('Captcha'); ?> <span style="color:red;">*</span></label>
                                    </div>
                                    <div class="js-form-value js-captcha-value">
                                        <?php
                                        if($this->config['captcha_selection'] == 1){
											if ( $joomla_captcha == 'recaptcha') { // 2.0
												$captcha = JCaptcha::getInstance('recaptcha', array('namespace' => 'dynamic_recaptcha_1' ));
											    echo $captcha->display('recaptcha', 'recaptcha', 'required');
											}
                                        }else{
                                            echo $this->captcha;
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
							<?php 
							}
							?>
                    <?php
                        }
                    }?>

                    <div class="js-form-submit-btn-wrp">
                        <input type="submit" class="js-save-button" name="submit_app" id="submit_app_button" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Submit Ticket'); ?>" />
                        <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo $this->Itemid; ?>" class="js-ticket-cancel-button"><?php echo JText::_('Cancel'); ?></a>
                        <?php
                        if($isstaff){
                            $link = "index.php?option=com_jssupportticket&c=ticket&layout=myticketsstaff&Itemid=" . $this->Itemid;
                        }else{
                            $link = "index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=" . $this->Itemid;
                        }

                        ?>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php if (isset($this->editticket)) echo $this->editticket->id; ?>" />
                    <input type="hidden" name="isoverdue" id="isoverdue" value="<?php if (isset($this->editticket)) echo $this->editticket->isoverdue; ?>" />
                    <input type="hidden" name="ticketid" id="ticketid" value="<?php if (isset($this->editticket)) echo $this->editticket->ticketid; ?>" />
                    <input type="hidden" name="uid" id="uid" value="<?php if (isset($this->editticket)) echo $this->editticket->uid;?>" />
                    <input type="hidden" name="c" id="c" value="ticket" />
                    <input type="hidden" name="task" id="task" value="saveticket" />
                    <input type="hidden" name="view" id="view" value="ticket" />
                    <input type="hidden" name="layout" id="layout" value="formticket" />
                    <input type="hidden" name="check" id="check" value="" />
                    <input type="hidden" name="option" id="option" value="<?php echo $this->option; ?>" />
                    <input type="hidden" name="created" id="created" value="<?php if (isset($this->editticket)) echo $this->editticket->created; else echo $curdate = date('Y-m-d H:i:s'); ?>"/>
                    <input type="hidden" name="Itemid" id="Itemid" id="Itemid" value="<?php echo $this->Itemid; ?>" />
                    <input type="hidden" name="update" id="update" value="<?php if (isset($this->editticket)) echo $update = date('Y-m-d H:i:s'); ?>"/>
                    <?php echo JHtml::_('form.token'); ?>
                </form>
            <?php }else{
                messageslayout::getPermissionNotAllow();
            } ?>
        </div>
        <?php
        }else{
            messageslayout::getStaffDisable(); //staff disabled
        }
    }else{
        if($this->user->getIsGuest()){ // user is guest
            messageslayout::getUserGuest('formticket',$this->Itemid);
        }else{
            messageslayout::getPermissionNotAllow(); //permission not granted
        }
    }
}else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End ?>
<script type="text/javascript">
        function validate_form(f) {
            if (document.formvalidator.isValid(f)) {
                if(isTinyMCE()){
                    var issuesummary = tinyMCE.get('message').getContent();
                }else{
                    var issuesummary = jQuery('textarea#message').val();
                }
                if (typeof issuesummary !== 'undefined' && issuesummary !== null) {
                    if (issuesummary == '') {
                        alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
                        return false;
                    }
                }
                f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken(); else echo JSession::getFormToken(); ?>';//send token
            } else {
                alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
                return false;
            }
            return true;
        }
        jQuery("#js-attachment-add").click(function () {
            var obj = this;
            var current_files = jQuery('input[name="filename[]"]').length;
            var total_allow =<?php echo $this->config['noofattachment']; ?>;
            var append_text = "<span class='js-attachment-file-box'><input name='filename[]' class='js-form-input-field-attachment' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlenght='30' /><span  class='js-attachment-remove'></span></span>";
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

        function gethelptopicandpremade(help,pre,depid) {
            var link = 'index.php?option=com_jssupportticket&c=ticket&task=listhelptopicandpremade&<?php echo JSession::getFormToken(); ?>=1';
            jQuery.post(link, {val: depid}, function (data) {
                if (data) {
                    helptopics = JSON.parse(data);
                    jQuery('div#'+help).html(helptopics.helptopic);
                    jQuery('div#'+pre).html(helptopics.premade);
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
        function getpremade(src, id, append) {
            var link = 'index.php?option=com_jssupportticket&c=ticket&task=getpremadeforinternalnote&<?php echo JSession::getFormToken(); ?>=1';
            jQuery.post(link, {val: id}, function (data) {
                if (data) {
                    if (append == true) {
                        if(isTinyMCE()){
                            var content = tinyMCE.get('message').getContent();
                        }else{
                            var content = jQuery('textarea#message').getContent();
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
                            jQuery('textarea#message').val(content);
                        }
                    }
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
                    getUserRemainMaxtickets(id);
                });
            });
        }
        function updateuserlist(pagenum){
            jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1", {userlimit:pagenum}, function (data) {
                if(data){
                    jQuery("div#records").html("");
                    jQuery("div#records").html(data);
                    setUserLink();
                }
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

            jQuery("div.popup-header-close-img").click(function (e) {
                jQuery("div#userpopup").slideUp('slow');
                setTimeout(function () {
                    jQuery("div#userpopupblack").hide();
                }, 700);
            });
            getUserRemainMaxtickets();
        });
        function getDataForDepandantField(parentf, childf, type) {
            if (type == 1) {
                var val = jQuery("select#" + parentf).val();
            } else if (type == 2) {
                var val = jQuery("input[name=" + parentf + "]:checked").val();
            }
            jQuery.post('index.php?option=com_jssupportticket&c=ticket&task=datafordepandantfield&<?php echo JSession::getFormToken(); ?>=1', {fvalue: val, child: childf}, function (data) {
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

        jQuery('#adminForm').submit(function() {
            jQuery('#submit_app_button').attr('disabled',true);
        });

        function getUserRemainMaxtickets(uid = 0){
            jQuery.post('index.php?option=com_jssupportticket&c=ticket&task=getuserremainmaxticket&<?php echo JSession::getFormToken(); ?>=1', {uid:uid}, function (data) {
                if (data) {
                    jQuery("#js-tk-formwrapper").before(data);
                }
            });
        }

    </script>
</div>
