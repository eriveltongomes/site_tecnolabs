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
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');
global $mainframe;
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
                        <li><?php echo JText::_('Outbox'); ?></li>
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
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Outbox'); ?></h1>          
        </div>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm">
            <div id="js-tk-filter">
                <div class="tk-search-value"><input type="text" placeholder="<?php echo JText::_('Subject'); ?>" name="filter_subject" id="filter_subject" value="<?php if (isset($this->lists['subject'])) echo $this->lists['subject']; ?>" class="text_area"/></div>
                <div class="tk-search-value"><?php if (isset($this->lists['start_date'])) echo JHTML::_('calendar', $this->lists['start_date'], 'filter_start_date', 'startdate', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19')); else echo JHTML::_('calendar', '', 'filter_start_date', 'startdate', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19')); ?></div>
                <div class="tk-search-value"><?php if (isset($this->lists['end_date'])) echo JHTML::_('calendar', $this->lists['end_date'], 'filter_end_date', 'enddate', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19')); else echo JHTML::_('calendar', '', 'filter_end_date', 'enddate', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19')); ?></div>
                <div class="tk-search-button">
                    <button class="js-form-search" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
                    <button class="js-form-reset" onclick="document.getElementById('filter_subject').value = ''; document.getElementById('startdate').value = ''; document.getElementById('enddate').value = ''; document.getElementById('filter_type').value = ''; this.form.submit();"><?php echo JText::_('Reset'); ?></button>
                </div>
            </div>
            <div class="js-col-md-12 js-ticket-box-shadow" id="mail-admin-links">
                <?php if ($this->isstaff == true) { ?> <a href="index.php?option=com_jssupportticket&c=mail&layout=Inbox"><img alt="Inbox" src="components/com_jssupportticket/include/images/inboxadmin.png"> <?php if ($this->unreadmessages >= 1) {$Inbox = $this->unreadmessages; } else {$Inbox = $this->totalinboxmessages; } echo JText::_('Inbox') . "&nbsp;(" . $Inbox . ")"; ?> </a> <?php } else { ?> <a href="" onClick="return false;"><img alt="Inbox" src="components/com_jssupportticket/include/images/inboxadmin.png"><?php echo JText::_('Inbox'); ?></a> <?php } ?>

                <?php if ($this->isstaff == true) { ?> <a href="index.php?option=com_jssupportticket&c=mail&layout=outbox"><img alt="Inbox" src="components/com_jssupportticket/include/images/outboxadmin.png"> <?php echo JText::_('Outbox') . "&nbsp;(" . $this->outboxmessages . ")"; ?></a> <?php } else { ?> <a href="" onClick="return false;"><img alt="Inbox" src="components/com_jssupportticket/include/images/outboxadmin.png"><?php echo JText::_('Outbox'); ?></a> <?php } ?>
                
                <?php if ($this->isstaff == true) { ?> <a href="index.php?option=com_jssupportticket&c=mail&layout=formmessage"><img alt="Inbox" src="components/com_jssupportticket/include/images/add_icon.png"> <?php echo JText::_('Compose'); ?></a> <?php } else { ?> <a href="" onClick="return false;"><img alt="Inbox" src="components/com_jssupportticket/include/images/add_icon.png"><?php echo JText::_('Compose'); ?></a> <?php } ?>
                
                <?php if ($this->isstaff == false) { ?>
                    <font color="orangered">[<?php echo JText::_('To Use This Feature You Must Be Staff Memeber'); ?>]</font>
                <?php } ?>                
            </div>
            <?php
            if (!(empty($this->messages)) && is_array($this->messages)) {  ?>
                    <table id="js-table" class="js-ticket-box-shadow">
                        <thead>
                        <tr>
                            <th class="center"><?php echo JText::_("S.No"); ?></th>
                            <th><?php echo JText::_("Subject"); ?></th>
                            <th><?php echo JText::_("To"); ?></th>
                            <th><?php echo JText::_("Created"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $k = 0;
                            foreach ($this->messages AS $message) {
                                $checked = JHTML::_('grid.id', $i, $message->id);
                                $link = 'index.php?option=' . $this->option . '&c=mail&task=showmessage&cid[]=' . $message->id; ?>
                                <tr>
                                    <td class="center"><?php echo $k + 1 + $this->pagination->limitstart; ?></td>
                                    <td><a  href="<?php echo $link; ?>"> <?php echo $message->subject; ?></a></td>
                                    <td><?php echo $message->staffname; ?></td>
                                    <td><?php echo JHtml::_('date',$message->created,$this->config['date_format']); ?></td>
                                </tr>
                            <?php
                                $i++;
                                $k++;
                            } ?>
                        </tbody>
                    </table>
                <div class="js-row js-tk-pagination js-ticket-pagination-shadow">
                    <?php echo $this->pagination->getListFooter(); ?>
                </div>
            <?php 
            }else{
                messagesLayout::getRecordNotFound();
            } ?>
            <input type="hidden" name="c" value="mail" />
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
            <input type="hidden" name="layout" value="outbox" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <?php echo JHtml::_( 'form.token' ); ?>
        </form>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
<script type="text/javascript">
    var headertext = [],
    headers = document.querySelectorAll("#js-table th"),
    tablerows = document.querySelectorAll("#js-table th"),
    tablebody = document.querySelector("#js-table tbody");

    for(var i = 0; i < headers.length; i++) {
      var current = headers[i];
      headertext.push(current.textContent.replace(/\r?\n|\r/,""));
    } 
    for (var i = 0, row; row = tablebody.rows[i]; i++) {
      for (var j = 0, col; col = row.cells[j]; j++) {
        col.setAttribute("data-th", headertext[j]);
      } 
    }
</script>
