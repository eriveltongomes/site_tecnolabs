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
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/jssupportticketdefault.css');
$document->addStyleSheet('components/com_jssupportticket/include/css/color.php');
$document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/staff-users.css', 'text/css');
$language = JFactory::getLanguage();
$document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
if($language->isRTL()){
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
}

if (JVERSION < 3) {
    JHtml::_('behavior.mootools');
    $document->addScript('administrator/components/com_jssupportticket/include/js/jquery.js');
} else {
    JHtml::_('bootstrap.framework');
    JHtml::_('jquery.framework');
}
?>
<div id="tk_detail_wraper">
    <form action="index.php?option=com_jssupportticket&c=staff&layout=users&tmpl=component" method="post" name="adminForm" id="adminForm">
        <div id="tk_heading">
            <span id="tk_heading_text"><?php echo JText::_('Select user'); ?></span>
        </div>
        <hr class="tk_sepr_hr_line">

        <div id="tk_search">
            <div id="tk_search_data" >
                <label id="tk_search_title"><?php echo JText::_('Name'); ?>:</label>
                <input type="text" name="searchname" class="tk_staff_search" id="searchname" size="10" value="<?php if (isset($this->lists['searchname'])) echo $this->lists['searchname']; ?>" class="text_area" />&nbsp;
                <label id="tk_search_title"><?php echo JText::_('Username'); ?>:</label>
                <input type="text" name="searchusername" class="tk_staff_search" id="searchusername" size="10" value="<?php if (isset($this->lists['searchusername'])) echo $this->lists['searchusername']; ?>" class="text_area" />&nbsp;
                <button class="tk_dft_btn" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>&nbsp;
                <button class="tk_dft_btn" onclick="document.getElementById('searchname').value = '';
                        document.getElementById('searchusername').value = '';
                        this.form.submit();"><?php echo JText::_('Reset'); ?></button>
            </div>
        </div>    
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
    </form>
    <div id="tk_mt_tabs_bottom_line_full"></div>
    <div id="tk_user_wraper">
        <div id="tk_user_heading">
            <span class="tk_user_small_heading"><?php echo JText::_('Num'); ?></span>
            <span class="tk_user_small_heading tk_user_left_border">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
            </span>
            <span class="tk_user_large_heading tk_user_left_border"><?php echo JText::_('Name'); ?></span>
            <span class="tk_user_large_heading tk_user_left_border"><?php echo JText::_('Username'); ?></span>
            <span class="tk_user_medium_heading tk_user_left_border"><?php echo JText::_('Id'); ?></span>
        </div>
        <div id="tk_user_data_wraper">
            <table class="tk_user_table" cellpadding="1"  >
                <tbody>
                    <?php
                    for ($i = 0, $n = count($this->items); $i < $n; $i++) {
                        $row = & $this->items[$i];
                        $img = $row->block ? 'publish_x.png' : 'tick.png';
                        $task = $row->block ? 'unblock' : 'block';
                        $alt = $row->block ? JText::_('Enabled') : JText::_('Blocked');
                        ?>
                        <tr class="">
                            <td class="tk_user_small_heading">
                                <?php echo $i + 1 + $this->pagination->limitstart; ?>
                            </td>
                            <td class="tk_user_small_heading">
                                <?php echo JHTML::_('grid.id', $i, $row->id); ?>
                            </td>
                            <td class="tk_user_large_heading"><a onclick="window.parent.setuser('<?php echo $row->username; ?>', '<?php echo $row->id; ?>');
                                                                 " ><?php echo $row->name; ?></a></td>
                            <td class="tk_user_large_heading"><?php echo $row->username; ?>	</td>
                            <td class="tk_user_medium_heading"><?php echo $row->id; ?>	</td>

                        </tr>
                    <?php } ?>    
                </tbody>
            </table>
        </div>
    </div>   
</div>   
<form action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=staff&layout=users&tmpl=component'); ?>" method="post">
    <div class="pagination">
        <div id="ticket_paginationlinks" >
            <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
        <br clear="all">
        <div> <?php echo JText::_('Display #'); ?>
            <?php echo $this->pagination->getLimitBox(); ?>
        </div>
        <div  style="text-align: right">
            <?php echo $this->pagination->getResultsCounter(); ?>
        </div>
    </div>
</form>
