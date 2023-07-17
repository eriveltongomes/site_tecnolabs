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
JHtml::_('bootstrap.tooltip');
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
                        <li><?php echo JText::_('Banlist log'); ?></li>
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
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Banlist log'); ?></h1>
        </div>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm">
            <div id="js-tk-filter">
                <div class="tk-search-value"><input type="text" placeholder="<?php echo JText::_('Email address'); ?>" name="filter_email_address" id="filter_email_address" value="<?php if (isset($this->lists['email_address'])) echo $this->lists['email_address']; ?>" class="text_area"/></div>
                <div class="tk-search-value">
                    <?php
                        if (isset($this->lists['start_date']))
                            echo JHTML::_('calendar', $this->lists['start_date'], 'filter_start_date', 'startdate', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19','placeholder'=>JText::_('Start date')));
                        else
                            echo JHTML::_('calendar', '', 'filter_start_date', 'startdate', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19','placeholder'=>JText::_('Start date')));
                    ?>
                </div>
                <div class="tk-search-value">
                    <?php
                    if (isset($this->lists['end_date']))
                        echo JHTML::_('calendar', $this->lists['end_date'], 'filter_end_date', 'enddate', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19','placeholder'=>JText::_('End date')));
                    else
                        echo JHTML::_('calendar', '', 'filter_end_date', 'enddate', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19','placeholder'=>JText::_('End date')));
                    ?>                                        
                </div>
                <div class="tk-search-button">
                    <button class="js-form-search" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
                    <button class="js-form-reset" type="button" onclick="resetJSForm();"><?php echo JText::_('Reset'); ?></button>
                </div>
            </div>
            <?php
            if (!(empty($this->banlistlog)) && is_array($this->banlistlog)) {  ?>
                    <table id="js-table" class="js-ticket-box-shadow">
                        <thead>
                        <tr>
                            <th class="center"><?php echo JText::_("S.No"); ?></th>
                            <th><?php echo JText::_("Title"); ?></th>
                            <th><?php echo JText::_("Log"); ?></th>
                            <th><?php echo JText::_("Logger"); ?></th>
                            <th><?php echo JText::_("Logger Email"); ?></th>
                            <th><?php echo JText::_("IP Address"); ?></th>
                            <th class="center"><?php echo JText::_("Created"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $k = 0;
                            foreach ($this->banlistlog AS $row) {
                                $checked = JHTML::_('grid.id', $i, $row->id); ?>
                                <tr>
                                    <td class="center"><?php echo $k + 1 + $this->pagination->limitstart; ?></td>
                                    <td><?php echo $row->title; ?></td>
                                    <td><?php echo $row->log; ?></td>
                                    <td><?php echo $row->logger; ?></td>
                                    <td><?php echo $row->loggeremail; ?></td>
                                    <td><?php echo $row->ipaddress; ?></td>
                                    <td class="center"><?php echo JHtml::_('date',$row->created,$this->config['date_format']); ?></td>
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
            <input type="hidden" name="option" value="<?php echo $this->option; ?>"/>
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>"/>
            <input type="hidden" name="c" value="emailbanlistlog"/>
            <input type="hidden" name="layout" value="emailbanlistlog"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
<script type="text/javascript">
function resetJSForm(){
    jQuery('#filter_email_address').val('');
    jQuery('#startdate').val('');
    jQuery('#enddate').val('');
    jQuery('#adminForm').submit();
}
</script>
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
