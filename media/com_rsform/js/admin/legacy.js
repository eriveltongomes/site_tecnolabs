jQuery(function ($) {
	function isLegacyLayout(value)
	{
		var legacyLayouts = [
			'inline', '2lines', '2colsinline', '2cols2lines',
			'inline-xhtml', '2lines-xhtml'
		];

		return legacyLayouts.indexOf(value) > -1;
	}

	function onLegacyLayoutChange(value)
	{
		if (isLegacyLayout(value))
		{
			$('#gridlayoutdiv').hide();
			$('#legacyComponentsTable').show();

			$('#gridlayoutdiv input[name="cid[]"]').attr('name', 'cid_disabled[]');

			legacyOrderingEnable();
		}
		else
		{
			$('#gridlayoutdiv').show();
			$('#legacyComponentsTable').hide();

			$('#gridlayoutdiv input[name="cid_disabled[]"]').attr('name', 'cid[]');

			legacyOrderingDisable();
		}
	}

	$('input[name=FormLayoutName]').change(function() {
		onLegacyLayoutChange(this.value);
	});

	onLegacyLayoutChange($('input[name=FormLayoutName]:checked').val());

	$('[name=GridLayout]').on('gridlayout.changed', function(){
		var layout = jQuery('input[name=FormLayoutName]:checked').val();

		if (isLegacyLayout(layout)) {
			return;
		}

		// Let's reorder fields
		var table = jQuery('#componentPreview > tbody'),
			table_rows = table.children('tr');

		// Remove current rows
		table.children('tr').remove();

		// Start reordering based on Grid position
		jQuery('#rsfp-grid-row-container').find('input[data-rsfpgrid]').each(function(index, input) {
			var row = table_rows.find('input#preview-id-' + jQuery(input).val()).closest('tr');
			table.append(row);
		});

		onLegacyLayoutChange(layout);
	});
});

function legacyOrderingEnable()
{
	var $table = jQuery('#componentPreview');
	$table.find('tbody').tableDnD({
		onDragClass: 'rsform_dragged',
		onDragStop : function (table, row) {
			tidyOrder(true);
		}
	});

	$table.find('.order').show();
}

function legacyOrderingDisable()
{
	var $table = jQuery('#componentPreview');
	$table.find('tbody').tableDnDDestroy();
	$table.find('.order').hide();
}

function tidyOrder(update_php)
{
	if (!update_php)
		update_php = false;

	stateLoading();

	var params = [];

	var must_update_php = update_php;
	var orders = document.getElementsByName('order[]');
	var cids = document.getElementsByName('cid[]');

	for (var i = 0; i < orders.length; i++)
	{
		params.push('cid[' + cids[i].value + ']=' + parseInt(i + 1));

		if (orders[i].value !== i + 1)
			must_update_php = true;

		orders[i].value = i + 1;
	}

	if (update_php && must_update_php)
	{
		var xml = buildXmlHttp();

		var url = 'index.php?option=com_rsform&task=components.saveordering';
		xml.open("POST", url, true);

		params = params.join('&');

		xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		xml.send(params);
		xml.onreadystatechange = function () {
			if (xml.readyState == 4) {
				autoGenerateLayout();

				stateDone();
			}
		}
	}
	else
	{
		stateDone();
	}
}

function listItemTask(cb, task)
{
	stateLoading();

	var xml = buildXmlHttp();
	var url = 'index.php?option=com_rsform&task=' + task + '&format=raw&randomTime=' + Math.random();
	var formId = document.getElementById('formId').value;
	var theId;

	xml.open("POST", url, true);

	var params = [];
	params.push('i=' + cb);
	params.push('componentId=' + document.getElementById(cb).value);
	params.push('formId=' + formId);
	params = params.join('&');

	//Send the proper header information along with the request
	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	switch (task) {
		case 'components.unpublish':
		case 'components.publish':
			theId = 'publish' + cb;
			break;

		case 'components.unsetrequired':
		case 'components.setrequired':
			theId = 'required' + cb;
			break;
	}

	// Add unpublished class to grid
	if (task.indexOf('components.') > -1)
	{
		if (task === 'components.unpublish')
		{
			jQuery('#rsfp-grid-field-id-' + document.getElementById(cb).value).addClass('rsfp-grid-unpublished-field');
		}
		else
		{
			jQuery('#rsfp-grid-field-id-' + document.getElementById(cb).value).removeClass('rsfp-grid-unpublished-field');
		}
	}

	xml.send(params);
	xml.onreadystatechange = function()
	{
		if (xml.readyState === 4)
		{
			var cell = document.getElementById(theId);
			jQuery(cell).html(xml.responseText);

			stateDone();

			if (document.getElementById('FormLayoutAutogenerate1').checked === true)
			{
				generateLayout(false);
			}
		}
	}
}

function legacyListItemTask(cb, task)
{
	if (task == 'orderdown' || task == 'orderup')
	{
		var currentRow = jQuery('#componentPreview #' + cb).closest('tr');
		if (task == 'orderdown')
		{
			try { currentRow.insertAfter(currentRow.next()); }
			catch (dnd_e) { }
		}
		if (task == 'orderup')
		{
			try { currentRow.insertBefore(currentRow.prev()); }
			catch (dnd_e) { }
		}

		tidyOrder(true);
	}
}

function saveorder(num, task)
{
	tidyOrder(true);
}

function legacyRemoveComponent(formId, componentId)
{
	stateLoading();

	// Build URL to post to
	var url = 'index.php?option=com_rsform&task=components.remove&randomTime=' + Math.random();

	// Build data array
	var data = {
		'ajax'  : 1,
		'cid[]' : componentId,
		'formId': formId
	};

	jQuery.post(url, data, function (response, status, jqXHR) {
		RSFormPro.Grid.deleteField(componentId);

		if (!response.submit) {
			jQuery('#rsform_submit_button_msg').show();
		}

		// Remove row
		var table = document.getElementById('componentPreview');
		var rows = document.getElementsByName('previewComponentId');
		for (var i = 0; i < rows.length; i++) {
			if (rows[i].value == componentId) {
				table.deleteRow(i);
			}
		}

		tidyOrder(true);

		stateDone();
	}, 'json');
}