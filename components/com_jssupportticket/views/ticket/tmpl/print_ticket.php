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
JText::script('Error file size too large');
JText::script('Error file extension mismatch');
?>
<div class="js-row js-null-margin">
<?php
if(isset($this->perm_not_allowed) && $this->perm_not_allowed == 2 ){
    messageslayout::getPermissionNotAllow(); //permission not granted
}elseif(isset($this->perm_not_allowed) && $this->perm_not_allowed == 3 ){
    messageslayout::getUserGuest($this->layoutname,$this->Itemid); //visitor trying to view ticket that belongs to logged in user.
}elseif(isset($this->perm_not_allowed) && $this->perm_not_allowed == 4 ){
    messageslayout::getUserNotAllowedToViewTicket(); //permission not granted
}else{
$isstaff = $this->user->getIsStaff();
$per_viewticket = true;
$isstaffdisable = true;
$per_ticketmerge = false;
if($isstaff){
    $per_viewticket = ($this->ticket_permissions['View Ticket'] == 1) ? true : false;
    $isstaffdisable = !($this->user->getIsStaffDisable());
    $per_ticketmerge = ($this->ticket_permissions['Ticket Merge'] == 1) ? true : false;
}
if($this->config['offline'] != '1'){
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/ticket-ticketdetail.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    $document->addStyleSheet('components/com_jssupportticket/include/css/color.php');
    if($per_viewticket){
        if($isstaffdisable){
            if($this->ticketdetail){
            $document = JFactory::getDocument();
            $document->addScript('administrator/components/com_jssupportticket/include/js/jquery_idTabs.js');
            $document->addScript('administrator/components/com_jssupportticket/include/js/file/file_validate.js');
            $document->addScript('components/com_jssupportticket/include/js/timer.jquery.js');
            JText::script('JS_ERROR_FILE_SIZE_TO_LARGE');
            JText::script('JS_ERROR_FILE_EXT_MISMATCH'); ?>
        <?php 
            $yesno = array(
            '0' => array('value' => '1',
                'text' => JText::_('Yes')),
            '1' => array('value' => '0',
                'text' => JText::_('No')),);
             $time_confilct_combo = JHTML::_('select.genericList', $yesno, 'time-confilct-combo', 'class="inputbox" ' . '', 'value', 'text', '');
        ?>
        <div id="tk-detail-wraper">
            <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
                <div id="message"></div>
                <div id="tk_detail_content_wraper">
                    <div class="js-col-md-12 js-ticket-detail-wrapper"> <!-- Ticket Detail Data Top -->
                        <div class="js-ticket-detail-box"><!-- Ticket Detail Box -->
                            <div class="js-ticket-detail-left"><!-- Left Side Image -->
                                <div class="js-tkt-det-cnt js-tkt-det-info-wrp">
                                    <div class="js-tkt-det-user">
                                        <div class="js-ticket-user-img-wrp">
                                            <img class="js-ticket-staff-img" src="components/com_jssupportticket/include/images/user.png" alt="<?php echo JText::_('New Ticket'); ?>" />
                                        </div>
                                        <div class="js-tkt-det-user-cnt">
                                            <div class="js-ticket-user-name-wrp">
                                                <?php echo $this->ticketdetail->name; ?>
                                            </div>
                                            <div class="js-ticket-user-subject-wrp">
                                                <?php echo $this->ticketdetail->subject; ?>
                                            </div>
                                            <div class="js-ticket-user-email-wrp">
                                                <?php echo $this->ticketdetail->email; ?>
                                            </div>
                                            <div class="js-ticket-user-email-wrp">
                                                <?php echo $this->ticketdetail->phone; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="js-tkt-go-to-all-wrp">
                                        <a class="js-tkt-go-to-all" href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=<?php echo $this->Itemid; ?>">     <?php echo JText::_('Show All').' '.JText::_('Tickets'); ?>
                                        </a>
                                    </div>
                                    <div class="js-tkt-det-tkt-msg">
                                        <?php echo $this->ticketdetail->message; ?>
                                    </div>
                                </div>


                    <!-- Internal Notes Portion -->
                    <?php
                    if (!empty($this->ticketnotes)) { ?>
                        <div class="js-ticket-post-reply-wrapper"><!-- Ticket Post Replay -->
                            <div class="js-ticket-thread-heading"><?php echo JText::_('Internal Note'); ?></div>
                            <?php foreach ($this->ticketnotes AS $row) {  ?>
                                <div class="js-ticket-detail-box js-ticket-post-reply-box"><!-- Ticket Detail Box -->
                                    <div class="js-ticket-detail-left js-ticket-white-background"><!-- Left Side Image -->
                                        <div class="js-ticket-user-img-wrp">
                                            <?php if ($row->staffphoto) { ?>
                                                <img  class="js-ticket-staff-img" src="<?php echo JURI::root(). $this->config['data_directory'] . "/staffdata/staff_" . $row->staffid . "/" . $row->staffphoto; ?>" />
                                            <?php } else { ?>
                                                <img class="js-ticket-staff-img" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                            <?php } ?>                                        
                                        </div>
                                        <?php $name=$row->staffname;
                                            if($row->staffid == 0){
                                                $name = $row->from;
                                            } ?>
                                    </div>
                                    <div class="js-ticket-detail-right js-ticket-background"><!-- Right Side Ticket Data -->
                                        <div class="js-ticket-rows-wrapper">
                                            <div class="js-ticket-rows-wrp">
                                                <div class="js-ticket-field-value name">
                                                   <?php echo $name; ?>
                                                </div>
                                            </div>
                                            <div class="js-ticket-rows-wrp" >
                                                
                                                <div class="js-ticket-row">
                                                    <div class="js-ticket-field-value">
                                                       <?php echo $row->title; ?>
                                                    </div>
                                                </div>
                                                <div class="js-ticket-row">
                                                    <div class="js-ticket-field-value">
                                                       <?php echo $row->note; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php 
                                            if($row->filesize > 0 && !empty($row->filename)){ 
                                                $notepath = 'index.php?option=com_jssupportticket&c=note&task=getdownloadbyid&id='.$row->id.'&'. JSession::getFormToken() . '=1'; ?>
                                                <div class="js-ticket-attachments-wrp">
                                                    <div class="js_ticketattachment">
                                                        <span class="js-ticket-download-file-title">
                                                            <?php echo $row->filename;?>
                                                        </span>
                                                        <a class="js-download-button" target="_blank" href="<?php echo $notepath;?>">
                                                            <img class="js-ticket-download-img" src="components/com_jssupportticket/include/images/download.png">
                                                        </a> 
                                                    </div>
                                                </div>            
                                            <?php } ?>
                                        </div>
                                        <div class="js-ticket-time-stamp-wrp">
                                            <span class="js-ticket-ticket-created-date">
                                                <?php echo JHtml::_('date',$row->created,"l F d, Y");?>
                                            </span>

                                            <div class="js-ticket-edit-options-wrp">
                                                <?php 
                                                    $hours = floor($row->usertime / 3600);
                                                    $mins = floor($row->usertime / 60 % 60);
                                                    $secs = floor($row->usertime % 60);
                                                    $time = jText::_('Time Taken').':&nbsp;'.sprintf('%02d:%02d:%02d', $hours, $mins, $secs); 
                                                    ?>
                                                <span class="js-ticket-thread-time"><?php echo $time; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            <?php } ?>
                        </div> 
                    <?php } ?>

                    <!-- Ticket Post Replay -->
                    <div class="js-ticket-post-reply-wrapper">
                        <div class="js-ticket-thread-heading"><?php echo JText::_('Ticket Thread'); ?></div> <!-- Heading -->
                        <div class="js-ticket-thread internal-note"><!-- Ticket Detail Box -->
                            <div class="js-ticket-user-img-wrp"><!-- Left Side Image -->
                                <div class="js-ticket-user-img-wrp">
                                     <img class="js-ticket-staff-img" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                </div>
                            </div>
                            <div class="js-ticket-thread-cnt"><!-- Right Side Ticket Data -->
                                <div class="js-ticket-user-name-wrp">
                                    <?php echo $this->ticketdetail->name; ?>
                                </div>
                                <div class="js-ticket-user-email-wrp">
                                    <?php echo $this->ticketdetail->email; ?>
                                </div>
                                <div class="js-ticket-user-email-wrp">
                                    <?php echo $this->ticketdetail->message; ?>
                                </div>
                                <?php
                                if (isset($this->ticketattachment[0]->filename)) { ?>
                                    <div class="js-ticket-attachments-wrp">
                                        <?php foreach ($this->ticketattachment as $attachment) {
                                            echo '
                                                <div class="js_ticketattachment">
                                                    <span class="js-ticket-download-file-title">
                                                        ' . $attachment->filename . ' ( ' . $attachment->filesize . ' ) ' . '
                                                    </span> 
                                                    <a class="js-download-button" target="_blank" href="index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$attachment->attachmentid.'&' . JSession::getFormToken() . '=1">
                                                        <img class="js-ticket-download-img" src="components/com_jssupportticket/include/images/download.png">
                                                    </a>  
                                                </div>';
                                        }
                                        echo'
                                            <a class="js-all-download-button" target="_blank" href="index.php?option=com_jssupportticket&c=ticket&task=downloadall&id='.$attachment->id.'&'. JSession::getFormToken() .'=1">
                                                '. JText::_('Download All') . '</a>
                                                ';?>

                                       </div>
                                <?php     
                                } ?>
                                <div class="js-ticket-time-stamp-wrp">
                                    <span class="js-ticket-ticket-created-date">
                                        <?php echo JHtml::_('date',$this->ticketdetail->created,"l F d, Y");?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        


                        <!-- Ticket Replies -->
                        <?php  if (!empty($this->ticketreplies)) { ?>
                            <?php $i = 0;
                            foreach ($this->ticketreplies AS $row) {
                                $i++;
                                if($row->staffid <> 0) {
                                    $staffname = $row->staffname;
                                }else{
                                    $staffname = $row->name;
                                } ?>
                                <div class="js-ticket-detail-box js-ticket-post-reply-box"><!-- Ticket Detail Box -->
                                    <div class="js-ticket-detail-left js-ticket-white-background"><!-- Left Side Image -->
                                        <div class="js-ticket-user-img-wrp">
                                            <?php if ($row->staffphoto) { ?>
                                                <img  class="js-ticket-staff-img" src="<?php echo JURI::root(). $this->config['data_directory'] . "/staffdata/staff_" . $row->staffid . "/" . $row->staffphoto; ?>" />
                                            <?php } else { ?>
                                                <img class="js-ticket-staff-img" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="js-ticket-detail-right js-ticket-background"><!-- Right Side Ticket Data -->
                                        <div class="js-ticket-rows-wrapper">
                                        <div class="js-ticket-user-name-wrp">
                                           <?php echo $staffname; ?>
                                        </div>
                                        <div class="js-ticket-user-email-wrp">
                                            <?php if ($row->ticketviaemail == 1) { ?>
                                                <?php echo JText::_('Created via email'); ?>
                                            <?php } ?>
                                        </div>
                                            <div class="js-ticket-rows-wrp" >
                                                <div class="js-ticket-row">
                                                    <div class="js-ticket-field-value">
                                                        <?php $message = $row->message;
                                                        if($row->mergemessage == 1){
                                                            $message = str_replace("cid[]=","id=",$message);
                                                            $message = str_replace("layout=ticketdetails","layout=ticketdetail",$message);
                                                        } ?>
                                                        <?php echo html_entity_decode($message); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if($isstaff){ ?>
                                                <div class="js-ticket-edit-options-wrp">
                                                    <?php if($row->staffid != 0){ 
                                                        $hours = floor($row->usertime / 3600);
                                                        $mins = floor($row->usertime / 60 % 60);
                                                        $secs = floor($row->usertime % 60);
                                                        $time = jText::_('Time Taken').':&nbsp;'.sprintf('%02d:%02d:%02d', $hours, $mins, $secs); 
                                                        ?>
                                                    <?php }?>
                                                    <span class="js-ticket-thread-time"><?php echo $time; ?></span>  
                                                </div>
                                            <?php } ?>
                                            <?php if (!empty($row->attachments)) { ?>
                                                 <?php if (isset($row->attachments)) { ?>
                                                    <div class="js-ticket-attachments-wrp">
                                                        <?php foreach ($row->attachments as $attachment) {
                                                            $path = 'index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$attachment->attachmentid.'&'. JSession::getFormToken() . '=1';
                                                            echo ' <div class="js_ticketattachment">
                                                                        <span class="js-ticket-download-file-title">'
                                                                            . $attachment->filename . "&nbsp(" . round($attachment->filesize, 2) . " KB)";
                                                                    echo '</span>
                                                                        <a class="js-download-button" target="_blank" href="' . $path . '">
                                                                            <img class="js-ticket-download-img" src="components/com_jssupportticket/include/images/download.png">
                                                                        </a> 
                                                                </div>';            
                                                        }
                                                            echo'
                                                                <a class="js-all-download-button" target="_blank" href="index.php?option=com_jssupportticket&c=ticket&task=downloadallforreply&id='.$row->id.'&'. JSession::getFormToken() .'=1">
                                                                '. JText::_('Download All') . '</a>';?>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                        <div class="js-ticket-time-stamp-wrp">
                                            <span class="js-ticket-ticket-created-date">
                                                 <?php echo JHtml::_('date',$row->created,"l F d, Y");?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div>
                            </div>
                            <div class="js-ticket-detail-right"><!-- Right Side Ticket Data -->
                                <div class="js-ticket-rows-wrp js-tkt-detail-cnt" >

                                        <!-- Status box -->
                                        <?php
                                            if ($this->ticketdetail->lock == 1) {
                                                $color = "#5bb12f;";
                                                $ticketmessage = JText::_('Lock');
                                            } elseif ($this->ticketdetail->status == 0) {
                                                $color = "#5bb12f;";
                                                $ticketmessage = JText::_('Open');
                                            } elseif ($this->ticketdetail->status == 1) {
                                                $color = "#28abe3;";
                                                $ticketmessage = JText::_('On Waiting');
                                            } elseif ($this->ticketdetail->status == 2) {
                                                $color = "#69d2e7;";
                                                $ticketmessage = JText::_('In Progress');
                                            } elseif ($this->ticketdetail->status == 3) {
                                                $color = "#FFB613;";
                                                $ticketmessage = JText::_('Replied');
                                            } elseif ($this->ticketdetail->status == 4) {
                                                $color = "#ed1c24;";
                                                $ticketmessage = JText::_('Closed');
                                            } elseif ($this->ticketdetail->status == 5) {
                                                $color = "#dc2742;";
                                                $ticketmessage = JText::_('Closed and Merged');
                                            }
                                        ?>
                                        <div class="js-tkt-det-status" style="background-color:<?php echo $color;?>; box-shadow:<?php echo $boxshadow;?>;">
                                            <?php echo $ticketmessage; ?>
                                        </div>
                                    <div class="js-tkt-det-info-cnt">
                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                                <?php echo JText::_('Department'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                                <?php echo $this->ticketdetail->departmentname; ?>
                                            </div>
                                        </div>
                                        <?php if($this->ticketdetail->ticketviaemail == 1 && $isstaff){ ?>
                                            <div class="js-ticket-row">
                                                <div class="js-ticket-field-title">
                                                    <?php echo JText::_('Ticket Email'); ?>&nbsp;:
                                                </div>
                                                <div class="js-ticket-field-value">
                                                    <?php echo $this->ticketdetail->departmentname; ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                               <?php echo JText::_('Created'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                                <?php
                                                    $startTimeStamp = strtotime($this->ticketdetail->created);
                                                    $endTimeStamp = strtotime("now");
                                                    $timeDiff = abs($endTimeStamp - $startTimeStamp);
                                                    $numberDays = $timeDiff / 86400;  // 86400 seconds in one day
                                                    // and you might want to convert to integer
                                                    $numberDays = intval($numberDays);
                                                    if ($numberDays != 0 && $numberDays == 1) {
                                                        $day_text = JText::_('Day');
                                                    } elseif ($numberDays > 1) {
                                                        $day_text = JText::_('Days');
                                                    } elseif ($numberDays == 0) {
                                                        $day_text = JText::_('Today');
                                                    }
                                                ?>
                                                <?php
                                                    if ($numberDays == 0) {
                                                        echo $day_text;
                                                    } else {
                                                        echo $numberDays . ' ' . $day_text . ' ';
                                                        echo JText::_('Ago');
                                                    }
                                                ?>
                                                <?php echo JHtml::_('date',$this->ticketdetail->created,"d F, Y"); ?>
                                            </div>
                                        </div>
                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                               <?php echo JText::_('Ticket ID'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                               <?php echo $this->ticketdetail->ticketid; ?>
                                            </div>
                                        </div>
                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                               <?php echo JText::_('Last Reply'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                               <?php if ($this->ticketdetail->lastreply == '' || $this->ticketdetail->lastreply == '0000-00-00 00:00:00') {echo JText::_('No Last Reply'); } else {echo JHtml::_('date',$this->ticketdetail->lastreply,"d F, Y"); } ?>
                                            </div>
                                        </div>
                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                                <?php echo JText::_('Help Topic'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                                <?php echo $this->ticketdetail->helptopic; ?>
                                            </div>
                                        </div>
                                        <?php if(isset($this->time_taken)){ ?>
                                            <div class="js-ticket-row">
                                                <div class="js-ticket-field-title">
                                                     <?php echo JText::_('Total Time Taken'); ?>&nbsp;:
                                                </div>
                                                <div class="js-ticket-field-value">
                                                    <?php 
                                                    $time = $this->time_taken;
                                                        $hours = floor($time / 3600);
                                                        $mins = floor($time / 60 % 60);
                                                        $secs = floor($time % 60);
                                                        echo jText::_(''). sprintf('%02d:%02d:%02d', $hours, $mins, $secs); 
                                                    ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php 
                                        $customfields = getCustomFieldClass()->userFieldsData(1);
                                        if(!empty($customfields)){ ?>
                                            <div class="js-ticket-row">
                                                <?php
                                                    foreach ($customfields as $field) {
                                                        if($field->userfieldtype =='termsandconditions'){}
                                                        else{                                                            $array =  getCustomFieldClass()->showCustomFields($field, 5, $this->ticketdetail->params , $this->ticketdetail->id);
                                                            if(!empty($array)){ ?>
                                                                <div class="js-ticket-row">
                                                                    <div class="js-ticket-field-title">
                                                                        <?php echo JText::_($array['title']); ?>&nbsp;:
                                                                    </div>
                                                                    <div class="js-ticket-field-value">
                                                                        <?php echo JText::_($array['value']); ?>
                                                                    </div>
                                                                </div>

                                                    <?php   }
                                                        }
                                                    }
                                                ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="js-ticket-rows-wrp  js-tkt-detail-cnt">
                                    <div class="js-tkt-det-hdg">
                                        <div class="js-tkt-det-hdg-txt">
                                            <?php echo JText::_('Priority'); ?>&nbsp;:
                                        </div>
                                    </div>
                                    <div class="js-ticket-field-value js-ticket-priorty" style="background:<?php echo $this->ticketdetail->prioritycolour; ?>; color:#ffffff;">
                                       <?php echo jText::_($this->ticketdetail->priority); ?>
                                    </div>
                                </div>
                                <div class="js-ticket-right-bottom"><!-- Right Side Bottom Data -->
                                    <div class="js-ticket-rows-wrp  js-tkt-detail-cnt">
                                        <div class="js-tkt-det-hdg">
                                            <div class="js-tkt-det-hdg-txt">
                                                <?php echo JText::_('Assigned To'); ?>&nbsp;:
                                            </div>
                                        </div>
                                        <div class="js-ticket-field-value">
                                            <div class="js-tkt-det-hdg">
                                                <div class="js-tkt-det-hdg-txt">
                                                    <?php echo JText::_('Ticket assigned to'); ?>
                                                </div>
                                            </div>
                                            <div class="js-tkt-det-user">
                                                <div class="js-tkt-det-user-image">
                                                   <?php if ($this->ticketdetail->staffphoto != "") { ?>
                                                        <img  class="js-ticket-staff-img" src="<?php echo JURI::root(). $this->config['data_directory'] . "/data/staff_" . $this->ticketdetail->staffid . "/" . $this->ticketdetail->staffphoto; ?>" />
                                                    <?php } else { ?>
                                                        <img class="js-ticket-staff-img" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                                    <?php } ?>
                                                </div>
                                                <div class="js-ticket-subject-link">
                                                    <?php
                                                        if ($this->ticketdetail->firstname != "") {
                                                            echo $this->ticketdetail->firstname . " " . $this->ticketdetail->lastname;
                                                        }?>        
                                                </div>
                                                <div class="js-ticket-subject-link">
                                                    <?php
                                                        if ($this->ticketdetail->email != "")
                                                        {
                                                            echo $this->ticketdetail->email;
                                                        }?>       
                                                </div>
                                            </div>  
                                        </div>
                                        <div class="js-tkt-det-trsfer-dep">
                                            <div class="js-tkt-det-trsfer-dep-txt">
                                                <span class="js-tkt-det-trsfer-dep-txt-tit">
                                                    <?php echo JText::_('Department').' :'; ?>
                                                </span>
                                                <?php
                                                    if ($this->ticketdetail->departmentname != "") {
                                                            echo $this->ticketdetail->departmentname;
                                                        } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="email" value="<?php echo $this->ticketdetail->email; ?>" />
                <input type="hidden" name="email_ban" id="email_ban" value="<?php echo (isset($this->isemailban)) ? $this->isemailban : ''; ?>" />
                <input type="hidden" id="staffid" name="staffid" value="<?php echo ($isstaff) ? $this->user->getStaffId() : '' ;?>" />

                <input type="hidden" name="callaction" id="callaction" value="" />
                <input type="hidden" name="callfrom" id="callfrom" value="" />
                <input type="hidden" name="view" value="ticket" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                <input type="hidden" name="lastreply" value="<?php echo $this->ticketdetail->lastreply; ?>" />
                <input type="hidden" name="id" value="<?php echo $this->ticketdetail->id; ?>" />
                <input type="hidden" name="ticketid" value="<?php echo $this->ticketdetail->ticketid; ?>" />
                <input type="hidden" name="hash" value="<?php echo $this->ticketdetail->hash; ?>" />
                <input type="hidden" name="layout" value="tickets" />
                <input type="hidden" id="task" name="task" value="actionticket" />
                <input type="hidden" name="c" value="ticket" />
                <input type="hidden" name="created" value="<?php echo $curdate = date('Y-m-d H:i:s'); ?>"/>
                <?php echo JHtml::_('form.token'); ?>
            </form>
            <div id="popup-record-data" style="display:inline-block;width:100%;"></div>
        </div>
<?php
            }else{
                messageslayout::getRecordNotFound(); //No Record
            }
        }else{
            messageslayout::getStaffDisable(); //staff disabled
        }
    }else{
        messageslayout::getPermissionNotAllow(); //permission not granted
    }
}else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End ?>
    <script language="Javascript">
        var printpage = '<?php if (isset($this->print)) echo $this->print; else echo 0; ?>';
        if (printpage == 1) {
            window.onload = function () {
                window.print();
                //window.close();
            }
        }
    </script>
</div>
<?php } ?>
