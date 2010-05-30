<?php function grid_edit_ordering($json_url, $id, $name) { ?>
<script>
jQuery("#<?php echo $name; ?>").tableDnD({
	scrollAmount: 0,
	onDrop: function(table, row) {
		var rows = table.tBodies[0].rows;
		for(var i = 0; i < rows.length; i++) {
			var id = rows[i].cells[0].innerHTML;
			jQuery("#<?php echo $name; ?>").setCell(id, 'order', i);
			jQuery('#<?php echo $name; ?>').jqGrid('saveOrder', id);
		}
	}
});

jQuery("#<?php echo $name; ?>").jqGrid({
	caption: 'Order of <?php echo ucfirst($name); ?>',
	height: 'auto',
	url: '<?php echo $json_url; ?>?view&<?php echo $name; ?>&order',
	datatype: 'json',
	colNames: ['ID', '<?php echo ucfirst($id); ?>', 'Order'],
	colModel: [
		{
			name: 'id',
			index: 'id',
			width: 50,
			hidden: true,
			sortable: false,
		},
		{
			name: '<?php echo $id; ?>',
			index: '<?php echo $id; ?>',
			width: 150,
			sortable: false,
			formatter: function(cellvalue, options, rowObject) {
				if(cellvalue !== '') {
					return cellvalue.substr(0,1).toUpperCase() + cellvalue.substr(1,cellvalue.length);
				}
				return cellvalue;
			}
		},
		{
			name: 'order',
			index: 'order',
			width: 50,
			sortable: false,
			hidden: true
		},
	],
	viewrecords: true,
	editurl: '<?php echo $json_url; ?>?modify&<?php echo $name; ?>&order',
	forceFit: true,
	gridComplete: function() {
		jQuery("#<?php echo $name; ?>").tableDnDUpdate();
	},
	beforeSaveCell: function() {
		alert('test');
	},
	cellEdit: true,
	cellurl: '<?php echo $json_url; ?>?modify&<?php echo $name; ?>&order',
	cellSubmit: 'remote',
});
</script>
<?php } ?>