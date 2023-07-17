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

<div id="js-tk-admin-wrapper" style="padding-left:0px;">
    <div id="js-tk-cparea" style="padding:0px 10px; min-height:auto;">
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text"><?php echo $this->ticketdetail->subject; ?></h1>
        </div>
        <form action="index.php" class="jsstadmin-data-wrp" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
        <div id="js-tk-ticket-detail">
            <div class="js-tkt-det-left">
                <div class="js-tkt-det-cnt js-tkt-det-info-wrp" id="">
                    <div class="js-tkt-det-user">
                        <?php if($this->ticketdetail->status != 5){ ?>
                        <div class="js-tkt-det-user-image">
                            <img class="requester-image" src="components/com_jssupportticket/include/images/user.png">
                        </div>
                        <div class="js-tkt-det-user-cnt">
                            <div class="js-tkt-det-user-data name">
                                <?php echo $this->ticketdetail->name; ?>
                            </div>
                            <div class="js-tkt-det-user-data email">
                                <?php echo $this->ticketdetail->email; ?>
                            </div>
                            <div class="js-tkt-det-user-data number">
                                <?php if ($this->ticketdetail->phone) { ?>
                                    <?php echo $this->ticketdetail->phone; ?>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="js-tkt-det-other-tkt">
                        <a href="javascript:void(0)" class="js-tkt-det-other-tkt-btn">
                            <?php echo JText::_('View').' '.JText::_('all tickets').' '.JText::_('by').' '; ?>
                            <?php echo $this->ticketdetail->name; ?>
                        </a>
                    </div>
                    <div class="js-tkt-det-tkt-msg">
                        <p><?php echo $this->ticketdetail->message; ?></p>
                    </div>
                </div>


                <!-- internal note -->
                <?php if($this->ticketnotes){ ?>
                    <div class="js-tk-subheading">
                        <?php echo JText::_('Internal notes'); ?>
                    </div>
                    <?php jimport('joomla.filter.output');
                        foreach ($this->ticketnotes as $row) { ?>
                            <div id="js-ticket-threads">
                                <div class="js-tk-pic">
                                    <?php if ($row->staffphoto) { ?>
                                        <img  src="<?php echo JURI::root(). $this->config['data_directory'] . "/staffdata/staff_" . $row->staffid . "/" . $row->staffphoto; ?>" />
                                    <?php } else { ?>
                                        <img src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                    <?php } ?>
                                </div>
                                <div class="js-tk-message">
                                    <div class="js-ticket-thread-data">
                                        <span class="js-ticket-thread-person">
                                            <?php echo $row->staffname; ?>
                                        </span>
                                    </div>
                                    <?php if($row->title){ ?>
                                        <div class="js-ticket-thread-data">
                                            <span class="js-ticket-thread-note">
                                                <?php echo $row->title; ?>
                                            </span>
                                        </div>
                                    <?php } ?>
                                    
                                    <div class="js-ticket-thread-data note-msg">
                                        <?php echo $row->note; ?>
                                        <?php  if($row->filesize > 0 && !empty($row->filename)){
                                            $notepath = 'index.php?option=com_jssupportticket&c=note&task=getdownloadbyid&id='.$row->id.'&' . JSession::getFormToken() . '=1';
                                                echo '<div class="js_ticketattachment">'.'<span class="js_ticketattachment_fname">'
                                                . $row->filename.'</span>' . '&nbsp;&nbsp;
                                                <a class="button" target="_blank" href="'.$notepath.'">'.JText::_('Download').'</a>
                                                </div>';
                                        }?>
                                    </div>
                                    <div class="js-ticket-thread-cnt-btm">
                                            <span class="js-ticket-thread-date"><?php $replyby = JHtml::_('date',$row->created,"l F d, Y, H:i:s"); echo ' ( '. $replyby.' )'; ?></span>
                                            <?php 
                                                $hours = floor($row->usertime / 3600);
                                                $mins = floor($row->usertime / 60 % 60);
                                                $secs = floor($row->usertime % 60);
                                                $time = JText::_('Time Taken').':&nbsp;'.sprintf('%02d:%02d:%02d', $hours, $mins, $secs); 
                                            ?>
                                            <span class="js-ticket-thread-time"><?php echo $time; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <div class="js-tk-subheading">
                        <?php echo JText::_('Ticket Thread'); ?>
                    </div>
                <div id="js-ticket-threads">
                    <div class="js-tk-pic">
                        <img src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                    </div>
                    <div class="js-tk-message">
                        <div class="js-ticket-thread-data">
                            <span class="js-ticket-thread-person">
                                <?php echo $this->ticketdetail->name; ?>
                            </span>
                        </div>
                        <div class="js-ticket-thread-data">
                            <span class="js-ticket-thread-email">
                                <?php echo $this->ticketdetail->email; ?>                
                            </span>
                        </div>
                        <div class="js-ticket-thread-data note-msg">
                            <?php echo $this->ticketdetail->message; ?>
                            <?php
                            if (isset($this->ticketattachment[0]->filename)) {
                                foreach ($this->ticketattachment as $attachment) {
                                    echo '<div class="js_ticketattachment">';
                                        $path = 'index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$attachment->attachmentid.'&' . JSession::getFormToken() . '=1';
                                        echo "<img src='components/com_jssupportticket/include/images/clip.png'><a target='_blank' href=" . $path . ">"
                                        . $attachment->filename . "&nbsp(" . round($attachment->filesize, 2) . " KB)" . "</a>";
                                    echo "</div>";
                                }
                            } ?>
                        </div>
                        <div class="js-ticket-thread-cnt-btm">
                            <div class="js-ticket-thread-date">
                                <?php $replyby = JHtml::_('date',$this->ticketdetail->created,"l F d, Y, H:i:s"); echo ' ( '. $replyby.' )'; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- left bar end -->
            </div>
            <div class="js-tkt-det-right">
                <div  class="js-tkt-det-cnt js-tkt-det-tkt-info">
                    <?php if ($this->ticketdetail->lock == 1) { ?>
                        <div class="js-tkt-det-status" style="background-color: darkred;"><?php echo JText::_('Lock'); ?></div>
                    <?php } elseif ($this->ticketdetail->status == 0) { ?>
                        <div class="js-tkt-det-status" style="background-color: #9ACC00;"><?php echo JText::_('New'); ?></div>
                    <?php } elseif ($this->ticketdetail->status == 1) { ?>
                        <div class="js-tkt-det-status" style="background-color: orange;"><?php echo JText::_('Waiting reply'); ?></div>
                    <?php } elseif ($this->ticketdetail->status == 2) { ?>
                        <div class="js-tkt-det-status" style="background-color: #FF7F50;"><?php echo JText::_('In progress'); ?></div>
                    <?php } elseif ($this->ticketdetail->status == 3) { ?>
                        <div class="js-tkt-det-status" style="background-color: #507DE4;"><?php echo JText::_('Replied'); ?></div>
                    <?php } elseif ($this->ticketdetail->status == 4) { ?>
                        <div class="js-tkt-det-status" style="background-color: #CB5355;"><?php echo JText::_('Close'); ?></div>
                    <?php } elseif ($this->ticketdetail->status == 5) { ?>
                        <div class="js-tkt-det-status" style="background-color: #ee1e22;"><?php echo JText::_('Close due to merged'); ?></div>
                    <?php } ?>
                    <div class="js-tkt-det-info-cnt">
                    <div class="js-tkt-det-info-data">
                        <span class="js-title"><?php echo JText::_('Created'); ?>&nbsp;:</span>
                        <span class="js-value"><?php echo JHTML::_('date',strtotime($this->ticketdetail->created),'y-m-d H:i:s'); ?></span>
                    </div>
                    <div class="js-tkt-det-info-data">
                        <span class="js-title"><?php echo JText::_('Ticket Id'); ?>&nbsp;:</span>
                        <span class="js-value"><?php echo $this->ticketdetail->ticketid; ?></span>
                    </div>
                    <div class="js-tkt-det-info-data">
                        <span class="js-title"><?php echo JText::_('Last Reply'); ?>&nbsp;:</span>
                        <span class="js-value"><?php if ($this->ticketdetail->lastreply == '' || $this->ticketdetail->lastreply == '0000-00-00 00:00:00') echo JText::_('Not given'); else echo JHtml::_('date',$this->ticketdetail->lastreply,$this->config['date_format']); ?></span>
                    </div>
                    <div class="js-tkt-det-info-data">
                        <span class="js-title"><?php echo JText::_('Due Date'); ?>&nbsp;:</span>
                        <span class="js-value"><?php if ($this->ticketdetail->duedate == '' || $this->ticketdetail->duedate == '0000-00-00 00:00:00') echo JText::_('Not given'); else echo JHtml::_('date',$this->ticketdetail->duedate,$this->config['date_format']); ?></span>
                    </div>
                    <div class="js-tkt-det-info-data">
                        <span class="js-title"><?php echo JText::_('Help Topic'); ?>&nbsp;:</span>
                        <span class="js-value"><?php echo JText::_($this->ticketdetail->helptopic); ?></span>
                    </div>
                    <div class="js-tkt-det-info-data">
                        <span class="js-title"><?php echo JText::_('Department'); ?>&nbsp;:</span>
                        <span class="js-value"><?php echo JText::_($this->ticketdetail->departmentname); ?></span>
                    </div>
                    <?php
                        $customfields = getCustomFieldClass()->userFieldsData(1);
                        foreach ($customfields as $field) {
                            if($field->userfieldtype != 'termsandconditions'){  
                                echo getCustomFieldClass()->showCustomFields($field, 3 , $this->ticketdetail->params , $this->ticketdetail->id);
                            }
                        }
                    ?>
                    </div>
                </div>
                <div class="js-tkt-det-cnt js-tkt-det-tkt-prty">
                    <div class="js-tkt-det-hdg">
                        <div class="js-tkt-det-hdg-txt">
                            <?php echo JText::_('Priority'); ?>
                        </div>
                        <?php /* <a title="Change" href="#" class="js-tkt-det-hdg-btn" id="chng-prority">
                            <?php echo JText::_('Change'); ?>
                        </a> */ ?>
                    </div>
                    <div class="js-tkt-det-tkt-prty-txt" style="color:#FFFFF;background:<?php echo $this->ticketdetail->prioritycolour; ?>;"><?php echo JText::_($this->ticketdetail->priority); ?>
                    </div>
                </div>
                <div class="js-tkt-det-cnt js-tkt-det-tkt-assign">
                    <div class="js-tkt-det-hdg">
                        <div class="js-tkt-det-hdg-txt">
                             <?php echo JText::_('Ticket Assign and Transfer'); ?>
                        </div>
                    </div>
                    <div class="js-tkt-det-tkt-asgn-cnt">
                        <div class="js-tkt-det-hdg">
                            <div class="js-tkt-det-hdg-txt">
                            <?php if ($this->ticketdetail->firstname != "") { ?>
                                <?php echo JText::_('Ticket assigned to'); ?>
                                <?php }else{ ?>
                                    <?php echo JText::_('Not assigned to staff'); ?>
                                <?php } ?>
                            </div>
                            <?php /* <?php if($this->ticketdetail->status != 5){ ?>
                            <a title="Change" href="#" class="js-tkt-det-hdg-btn" id="asgn-staff">
                                <?php echo JText::_('Change'); ?>
                            </a>
                            <?php } ?> */ ?>
                        </div>
                        <div class="js-tkt-det-info-wrp">
                            <?php if ($this->ticketdetail->firstname != "") { ?>
                            <div class="js-tkt-det-user">
                                <div class="js-tkt-det-user-image">
                                    <img alt="staff photo" src="components/com_jssupportticket/include/images/user.png">
                                </div>
                                <div class="js-tkt-det-user-cnt">
                                    <div class="js-tkt-det-user-data">
                                        <?php if ($this->ticketdetail->firstname != "") {
                                            echo $this->ticketdetail->firstname . " " . $this->ticketdetail->lastname;
                                        } ?>
                                    </div>
                                    <div class="js-tkt-det-user-data">
                                        <?php if ($this->ticketdetail->email != "") {
                                            echo $this->ticketdetail->email;
                                        }?>
                                    </div>
                                    <div class="js-tkt-det-user-data"></div>
                                </div>
                            </div>
                            <?php }?>
                            <div class="js-tkt-det-trsfer-dep">
                                <div class="js-tkt-det-trsfer-dep-txt">
                                    <?php echo JText::_('Department').': '; ?>
                                    <?php if ($this->ticketdetail->departmentname != "") {
                                        echo $this->ticketdetail->departmentname;
                                    } ?>
                                </div>
                                <?php /* <?php if($this->ticketdetail->status != 5){ ?>
                                <a title="Change" href="#" class="js-tkt-det-hdg-btn" id="chng-dept">
                                    <?php echo JText::_('Change'); ?>
                                </a>
                                <?php } ?> */ ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </form>

    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
<script type="text/javascript">
    var printpage = '<?php if (isset($this->print)) echo $this->print; else echo 0; ?>';
    if (printpage == 1) {
        window.onload = function () {
            window.print();
            //window.close();
        }
    }
</script>
