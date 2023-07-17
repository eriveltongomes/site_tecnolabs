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

<table width="100%">
    <tr>
        <td width="100%" valign="top">

            <form action="index.php?option=com_jssupportticket&c=staff&view=staff&layout=users&tmpl=component" method="post" name="adminForm" id="adminForm">
                <tr>
                    <td width="100%">
                        <strong><?php echo JText::_(''); ?></strong>
                    </td>
                    <td nowrap>
<?php echo JText::_('Name'); ?>:
                        <input type="text" name="searchname" id="searchname" value="<?php if (isset($this->lists['searchname'])) echo $this->lists['searchname']; ?>" class="text_area" onchange="document.adminForm.submit();" />
                        &nbsp;</td>
                    <td nowrap >
<?php echo JText::_('Username'); ?>:
                        <input type="text" name="searchusername" id="searchusername" size="15" value="<?php if (isset($this->lists['searchusername'])) echo $this->lists['searchusername']; ?>" class="text_area" onchange="document.adminForm.submit();" />
                        &nbsp;</td>

                    <td>
                        <button onclick="document.getElementById('searchname').value = '';
                                document.getElementById('searchusername').value = '';
                                document.getElementById('searchrole').value = '';
                                this.form.submit();"><?php echo JText::_('Reset'); ?></button>
                    </td>
                </tr>
                <table class="adminlist" cellpadding="1">
                    <thead>
                        <tr>
                            <th width="2%" class="title">
<?php echo JText::_('Num'); ?>
                            </th>
                            <th width="3%" class="title">
                                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
                            </th>
                            <th width="15%" class="title"><?php echo JText::_('Name'); ?></th>
                            <th width="15%" class="title" ><?php echo JText::_('Username'); ?></th>
                            <th width="4%" class="title" nowrap="nowrap"><?php echo JText::_('Id'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="10">
<?php echo $this->pagination->getListFooter(); ?>
                            </td>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $k = 0;
                        for ($i = 0, $n = count($this->items); $i < $n; $i++) {
                            $row = & $this->items[$i];
                            $img = $row->block ? 'publish_x.png' : 'tick.png';
                            $task = $row->block ? 'unblock' : 'block';
                            $alt = $row->block ? JText::_('Enabled') : JText::_('Blocked');
                            $link = 'index.php?option=com_jsautoz&view=vehicle&layout=formdeller&userid=' . $row->id;
                            ?>
                            <tr class="<?php echo "row$k"; ?>">
                                <td>
                                    <?php echo $i + 1 + $this->pagination->limitstart; ?>
                                </td>
                                <td><?php echo JHTML::_('grid.id', $i, $row->id); ?></td>
                                <td><a onclick="window.parent.setuser('<?php echo $row->username; ?>', '<?php echo $row->id; ?>');
                                        " ><?php echo $row->name; ?></a></td>
                                <td><?php echo $row->username; ?>	</td>
                                <td><?php echo $row->id; ?>	</td>

                            </tr>
                            <?php
                            $k = 1 - $k;
                        }
                        ?>
                    </tbody>
                </table>


                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="c" value="staff" />
                <input type="hidden" name="view" value="staff" />
                <input type="hidden" name="layout" value="users" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
                <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
                <?php echo JHTML::_('form.token'); ?>
            </form>
        </td>
    </tr>

</table>										
