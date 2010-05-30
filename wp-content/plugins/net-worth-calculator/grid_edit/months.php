<?php function grid_edit_months($json_url) { ?>
<script>
jQuery("#months").jqGrid({
	caption: 'My Net Worth',
	height: 'auto',
	url: '<?php echo $json_url; ?>?view&months',
	datatype: 'json',
	colNames: ['ID', 'Month', 'Net Worth'],
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
			name: 'month',
			index: 'month',
			width: 150,
			sortable: false,
			editable: false
		},
		{
			name: 'networth',
			index: 'networth',
			width: 150,
			sortable: false,
			editable: false,
			formatter: 'currency',
			formatoptions: {prefix:"$"}
		},
	],
	viewrecords: true,
	forceFit: true,
	editurl: '<?php echo $json_url; ?>?modify&months',
	afterSubmit: function(args) {
		alert('test');
	},
	onSelectRow: function(monthid) {
		if(monthid == null) {
			monthid = 0;
			if(jQuery('#assets').jqGrid('getGridParam','records') > 0) {
				jQuery("#assets").jqGrid('setGridParam',{url:"<?php echo $json_url; ?>?view&assets&month="+monthid,page:1,cellurl:"<?php echo $json_url; ?>?modify&assets&month="+monthid,editurl:"<?php echo $json_url; ?>?modify&assets&month="+monthid}).trigger('reloadGrid');
			}
			if(jQuery('#liabilities').jqGrid('getGridParam','records') > 0) {
				jQuery("#liabilities").jqGrid('setGridParam',{url:"<?php echo $json_url; ?>?view&liabilities&month="+monthid,page:1,cellurl:"<?php echo $json_url; ?>?modify&liabilities&month="+monthid,editurl:"<?php echo $json_url; ?>?modify&liabilities&month="+monthid}).trigger('reloadGrid');
			}
		} else if(monthid == '_empty') {
			jQuery("#months").jqGrid('addNewMonth', monthid, false, function(rowid, response) {
				var json = eval('(' + response.responseText + ')');
				jQuery("#months").trigger('reloadGrid');
				//jQuery("#months").setSelection(json.monthid, false);
				jQuery("#assets").jqGrid('setGridParam',{url:"<?php echo $json_url; ?>?view&assets&month="+json.monthid,page:1,cellurl:"<?php echo $json_url; ?>?modify&assets&month="+json.monthid,editurl:"<?php echo $json_url; ?>?modify&assets&month="+json.monthid}).trigger('reloadGrid');
				jQuery("#liabilities").jqGrid('setGridParam',{url:"<?php echo $json_url; ?>?view&liabilities&month="+json.monthid,page:1,cellurl:"<?php echo $json_url; ?>?modify&liabilities&month="+json.monthid,editurl:"<?php echo $json_url; ?>?modify&liabilities&month="+json.monthid}).trigger('reloadGrid');
			});
		} else {
			jQuery("#assets").jqGrid('setGridParam',{url:"<?php echo $json_url; ?>?view&assets&month="+monthid,page:1,cellurl:"<?php echo $json_url; ?>?modify&assets&month="+monthid,editurl:"<?php echo $json_url; ?>?modify&assets&month="+monthid}).trigger('reloadGrid');
			jQuery("#liabilities").jqGrid('setGridParam',{url:"<?php echo $json_url; ?>?view&liabilities&month="+monthid,page:1,cellurl:"<?php echo $json_url; ?>?modify&liabilities&month="+monthid,editurl:"<?php echo $json_url; ?>?modify&liabilities&month="+monthid}).trigger('reloadGrid');
		}
	},
	gridComplete: function() {
		var ids = jQuery("#months").jqGrid('getDataIDs');
		for(var i = 0; i < ids.length; i++) {
			if(ids[i] == '_empty') {
				jQuery("#months").setCell(ids[i], 1, '', {'color': '#aaaaaa'});
				jQuery("#months").setCell(ids[i], 2, '', {'color': '#aaaaaa'});
			} else {
				var value = jQuery("#months").getCell(ids[i], 2);
				if(value > 0) {
					jQuery("#months").setCell(ids[i], 2, '', {'color': 'green'});
					jQuery("#months").setCell(ids[i], 1, '', {'font-weight': 'bold'});
					jQuery("#months").setCell(ids[i], 2, '', {'font-weight': 'bold'});
				} else if(value < 0) {
					jQuery("#months").setCell(ids[i], 2, '', {'color': 'red'});
					jQuery("#months").setCell(ids[i], 1, '', {'font-weight': 'bold'});
					jQuery("#months").setCell(ids[i], 2, '', {'font-weight': 'bold'});
				}
				
			}
		}
		<?php echo ccf_show_graph_swf('managedata', $vars=array( 'no_tags' => 1 ), $width=300, $height=300); ?>
	}
});
</script>
<?php } ?>