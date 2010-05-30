<?php function grid_edit_items($json_url, $id, $name) { ?>
<script>
//var lastSel<?php echo $name; ?>;
jQuery("#<?php echo $name; ?>").jqGrid({
	caption: '<?php echo ucfirst($name); ?> for January 2010',
	height: 'auto',
	url: '<?php echo $json_url; ?>?view&<?php echo $name; ?>&month=',
	datatype: 'json',
	colNames: ['ID', 'x', '<?php echo ucfirst($id); ?>', 'Amount', 'Private'],
	colModel: [
		{
			name: 'id',
			index: 'id',
			width: 50,
			hidden: true,
			sortable: false,
			editable: false
		},
		{
			name: 'remove',
			index: 'remove',
			width: 20,
			sortable: false,
			editable: false
		},
		{
			name: '<?php echo $id; ?>',
			index: '<?php echo $id; ?>',
			width: 150,
			sortable: false,
			editable: true,
			formatter: function(cellvalue, options, rowObject) {
				if(cellvalue !== '') {
					return cellvalue.substr(0,1).toUpperCase() + cellvalue.substr(1,cellvalue.length);
				}
				return cellvalue;
			}
		},
		{
			name: 'amount',
			index: 'amount',
			width: 150,
			sortable: false,
			editable: true,
			formatter: 'currency',
			formatoptions: {prefix:"$"},
		},
		{
			name: 'private',
			index: 'private',
			width: 80,
			sortable :false,
			editable :true,
			edittype: 'checkbox',
			editoptions: {value:"1:0"},
			formatter:'checkbox'
		},
	],
	viewrecords: true,
	editurl: '<?php echo $json_url; ?>?modify&<?php echo $name; ?>',
	forceFit: true,
	cellEdit: true,
	cellurl: '<?php echo $json_url; ?>?modify&<?php echo $name; ?>',
	cellSubmit: 'remote',
	footerrow: true,
	userDataOnFooter: true,
	//onSelectRow: function(id) {
	//	if(id && id !== lastSel<?php echo $name; ?>) {
	//		jQuery('#<?php echo $name; ?>').restoreRow(lastSel<?php echo $name; ?>);
	//		lastSel<?php echo $name; ?> = id;
	//	}
	//	jQuery('#<?php echo $name; ?>').editRow(id, true);
	//},
	afterEditCell: function(rowid,name,val,iRow,iCol) {
		if(name == 'private') {
			jQuery('.edit-cell > input[id='+iRow+'_'+name+']').click();
			jQuery('#<?php echo $name; ?>').jqGrid('saveCell', iRow, iCol);
		} else {
			jQuery('.edit-cell > input[id='+iRow+'_'+name+']').select();
		}
		jQuery("input[name='<?php echo $id; ?>']").autocomplete("<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/net-worth-calculator/autocomplete.php?type=<?php echo $id; ?>", {
			width: 260
		});
	},
	afterSaveCell : function(rowid,name,val,iRow,iCol) {
		if(iCol == 3) {
			var total = 0;
			var ids = jQuery("#<?php echo $name; ?>").jqGrid('getDataIDs');
			for(var i in ids) {
				total = total + parseFloat(jQuery('#<?php echo $name; ?>').jqGrid('getCell', ids[i], 3));
			}
			jQuery('#<?php echo $name; ?>').jqGrid('footerData', 'set', {amount:total});
			jQuery('#months').trigger('reloadGrid');
		}
	},
	beforeSubmitCell : function(rowid,name,val,iRow,iCol) {
		var row = jQuery('#<?php echo $name; ?>').jqGrid('getCell', rowid, 'id');
		return {rowid:row};
	},
	afterSubmitCell : function(response,rowid,name,val,iRow,iCol) {
		var json = eval('(' + response.responseText + ')');
		if(rowid == '_empty' && json.success) {
			jQuery('#<?php echo $name; ?>').jqGrid('setCell', rowid, 'id', json.id);
		}
		if(name == 'amount' && rowid == '_empty') {
			jQuery('#<?php echo $name; ?>').trigger('reloadGrid');
		}
		return [json.success, json.message];
	},
	gridComplete: function() {
		var ids = jQuery("#<?php echo $name; ?>").jqGrid('getDataIDs');
		for(var i = 0; i < ids.length; i++) {
			remove = "<input style='height:19px;width:21px;padding:0;margin: 0 -6px;' type='button' value='x' onclick=\"jQuery('#<?php echo $name; ?>').jqGrid('delGridRow',"+ids[i]+",{reloadAfterSubmit: true,afterSubmit: function() {jQuery('#months').trigger('reloadGrid'); return [true,''];}});\" />";
			if(ids[i] !== '_empty') {
				jQuery("#<?php echo $name; ?>").jqGrid('setRowData',ids[i],{remove:remove});
			}
		}
		var userdata = jQuery("#<?php echo $name; ?>").jqGrid('getGridParam', 'userData');
		jQuery("#<?php echo $name; ?>").jqGrid('setCaption',"<?php echo ucfirst($name); ?> for "+userdata.month);
		jQuery("#<?php echo $name; ?>").jqGrid('setGridParam',{url:"<?php echo $json_url; ?>?view&<?php echo $name; ?>&month="+userdata.monthid,page:1,cellurl:"<?php echo $json_url; ?>?modify&<?php echo $name; ?>&month="+userdata.monthid,editurl:"<?php echo $json_url; ?>?modify&<?php echo $name; ?>&month="+userdata.monthid});
		jQuery("#<?php echo $name; ?>").jqGrid('editCell', ids.length-1, 2, true);
		jQuery("#copy-link-data").unbind('click');
		jQuery("#copy-link-graph").unbind('click');
		jQuery("#copy-link-data").click(function() {
			copy_link_data(userdata.mmyyyy);
		});
		jQuery("#copy-link-graph").click(function() {
			copy_link_graph(userdata.mmyyyy);
		});
	}, 
});
</script>
<?php } ?>