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
?>

<script language=Javascript>
    function confirmdelete() {
        if (confirm("<?php echo JText::_('Are you sure to delete'); ?>") == true) {
            return true;
        } else
            return false;
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
                        <li><?php echo JText::_('Premade'); ?></li>
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
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Premade'); ?></h1>
            <?php $link = 'index.php?option='.$this->option.'&c=premade&task=editpremade&cid[]=""'; ?>
            <a class="tk-heading-addbutton" href="<?php echo $link; ?>">
                <img class="js-heading-addimage" src="components/com_jssupportticket/include/images/plus.png">
                <?php echo JText::_('Add Premade Message'); ?>
            </a>            
        </div>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm">
            <div id="js-tk-filter">
                <div class="tk-search-value"><input type="text" placeholder="<?php echo JText::_('Title'); ?>" name="filter_dp_title" id="filter_dp_title" value="<?php if (isset($this->lists['title'])) echo $this->lists['title']; ?>"/></div>
                <div class="tk-search-value"><?php echo $this->lists['departments']; ?></div>
                <div class="tk-search-value"><?php echo $this->lists['status']; ?></div>
                <div class="tk-search-button">
                    <button class="js-form-search" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
                    <button class="js-form-reset" onclick="document.getElementById('filter_dp_title').value = ''; document.getElementById('filter_dp_departmentid').value = ''; document.getElementById('filter_dp_statusid').value = ''; this.form.submit();"><?php echo JText::_('Reset'); ?></button>
                </div>
            </div>
            <?php
            if (!(empty($this->premade)) && is_array($this->premade)) {  ?>
                    <table id="js-table" class="js-ticket-box-shadow">
                        <thead>
                        <tr>
                            <th class="center"><?php echo JText::_("S.No"); ?></th>
                            <th><?php echo JText::_("Title"); ?></th>
                            <th><?php echo JText::_("Department"); ?></th>
                            <th class="center"><?php echo JText::_("Status"); ?></th>
                            <th class="center"><?php echo JText::_("Last Updated"); ?></th>
                            <th class="center"><?php echo JText::_("Action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $k = 0;
                            foreach ($this->premade AS $row) {
                                $checked = JHTML::_('grid.id', $i, $row->id);
                                if($row->isenabled == 1) $icon_status = 'good.png'; else $icon_status = 'close.png';
                                $editlink = 'index.php?option='.$this->option.'&c=premade&task=editpremade&cid[]=' . $row->id;
                                $deletelink = 'index.php?option='.$this->option.'&c=premade&task=removedepartmentpremade&cid[]='.$row->id.'&'. JSession::getFormToken() .'=1'; ?>
                                <tr>
                                    <td class="center"><?php echo $k + 1 + $this->pagination->limitstart; ?></td>
                                    <td><a href="<?php echo $editlink;?>"><?php echo $row->title; ?></a></td>
                                    <td><?php echo JText::_($row->departmentname); ?></td>
                                    <td class="center"><img src="components/com_jssupportticket/include/images/<?php echo $icon_status; ?>"></td>
                                    <td class="center"><?php if ($row->update == "" || $row->update == "0000-00-00 00:00:00") echo JText::_('Not updated'); else echo JHtml::_('date',$row->update,$this->config['date_format']);?></td>
                                    <td class="center">
                                        <a class="js-tk-button" href="<?php echo $editlink; ?>">
                                            <img src="components/com_jssupportticket/include/images/edit.png">                     
                                        </a>&nbsp;
                                        <a class="js-tk-button" onclick="return confirmdelete()" href="<?php echo $deletelink; ?>">
                                            <img src="components/com_jssupportticket/include/images/delete.png">
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                                $k++;
                            } ?>
                        </tbody>
                    </table>
                <div class="js-tk-pagination js-ticket-pagination-shadow">
                    <?php echo $this->pagination->getListFooter(); ?>
                </div>
            <?php 
            }else{
                messagesLayout::getRecordNotFound();
            } ?>
            
            <input type="hidden" name="option" value="<?php echo $this->option; ?>"/>
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>"/>
            <input type="hidden" name="c" value="premade"/>
            <input type="hidden" name="layout" value="departmentspremade"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
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
