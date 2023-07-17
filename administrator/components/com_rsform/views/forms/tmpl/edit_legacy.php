<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2017 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<table border="0" width="100%" class="adminrsform" id="legacyComponentsTable">
	<tr>
		<td valign="top" class="componentPreview">
			<table border="0" id="componentPreview" class="table table-striped">
				<thead>
				<tr>
					<th class="title" width="1"><input type="hidden" value="-2" name="previewComponentId"/><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this, 'legacycb');"/></th>
					<th class="title"><?php echo JText::_('RSFP_NAME');?></th>
					<th class="title"><?php echo JText::_('RSFP_CAPTION');?></th>
					<th class="title"><?php echo JText::_('RSFP_PREVIEW');?></th>
					<th class="title" width="5">&nbsp;</th>
					<th class="title" width="5">&nbsp;</th>
					<th width="150" class="order nowrap center"><span class="pull-left"><?php echo JText::_('Ordering'); ?></span> <?php echo JHtml::_('grid.order',$this->fields); ?></th>
					<th class="title" width="5"><?php echo JText::_('RSFP_PUBLISHED');?></th>
					<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_COMP_FIELD_REQUIRED');?></th>
					<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_COMP_FIELD_VALIDATIONRULE');?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				$i = 0;
				$n = count($this->fields);
				// hack to show order down icon
				$n++;
				foreach ($this->fields as $field) { ?>
					<tr<?php if ($field->type_id == RSFORM_FIELD_PAGEBREAK) { ?> class="rsform_page"<?php } ?>>
						<td><input type="hidden" id="preview-id-<?php echo $field->id; ?>" name="previewComponentId" value="<?php echo $field->id; ?>" /><?php echo JHtml::_('grid.id', $i, $field->id, false, 'cid', 'legacycb'); ?></td>
						<td><?php echo $field->name; ?></td>
						<td><?php echo $field->caption; ?></td>
						<td><?php echo $this->adjustPreview($field->preview, false); ?></td>
						<td align="center"><button type="button" class="btn btn-secondary" onclick="displayTemplate('<?php echo $field->type_id; ?>','<?php echo $field->id; ?>');"><?php echo JText::_('RSFP_EDIT'); ?></button></td>
						<td align="center"><button type="button" class="btn btn-danger" onclick="if (confirm(Joomla.JText._('RSFP_REMOVE_COMPONENT_CONFIRM').replace('%s', '<?php echo $this->escape($field->name); ?>'))) legacyRemoveComponent('<?php echo $this->form->FormId; ?>','<?php echo $field->id; ?>');"><?php echo JText::_('RSFP_DELETE'); ?></button></td>
						<td class="order center">
							<span><?php echo str_replace('listItemTask', 'legacyListItemTask', $this->pagination->orderUpIcon($i, true, 'orderup', 'JLIB_HTML_MOVE_UP', true, 'legacycb')); ?></span>
							<span><?php echo str_replace('listItemTask', 'legacyListItemTask', $this->pagination->orderDownIcon($i, $n, true, 'orderdown', 'JLIB_HTML_MOVE_DOWN', true, 'legacycb')); ?></span>
							<input type="text" name="order[]" size="5" value="<?php echo $field->ordering; ?>" disabled="disabled" class="width-20 text-area-order" style="text-align:center" />
						</td>
						<td align="center" id="publishlegacycb<?php echo $i; ?>"><?php echo JHtml::_('jgrid.published', $field->published, $i, 'components.', true, 'legacycb'); ?></td>
						<td align="center" id="requiredlegacycb<?php echo $i; ?>"><?php echo !empty($field->hasRequired) ?
								JHtml::_('jgrid.state', array(
									0 => array('setrequired', 'JYES', '', '', false, 'unpublish', 'unpublish'),
									1 => array('unsetrequired', 'JNO', '', '', false, 'publish', 'publish')
								), $field->required, $i, 'components.', true, true, 'legacycb')
								: '-'; ?></td>
						<td align="center"><?php echo !empty($field->validation) ? '<b>' . $field->validation . '</b>' : '-'; ?></td>
					</tr>
					<?php
					$i++;
				}
				?>
				</tbody>
			</table>
		</td>
	</tr>
</table>