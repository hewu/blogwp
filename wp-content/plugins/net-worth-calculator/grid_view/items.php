<?php function grid_view_items($json_url, $id, $name, $mmyyyy, $username) { 
	$ucid = ucfirst($id);
	$ucname = ucfirst($name);
	$out = <<<EOT
<script>
jQuery("#$name").jqGrid({
	caption: '$ucname',
	height: 'auto',
	url: '$json_url?view_post&$name&mmyyyy=$mmyyyy&username=$username',
	datatype: 'json',
	colNames: ['ID', '$ucid', 'Amount'],
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
			name: '$id',
			index: '$id',
			width: 100,
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
			width: 120,
			sortable: false,
			editable: true,
			formatter: 'currency',
			formatoptions: {prefix:"$"},
		}
	],
	viewrecords: true,
	forceFit: true,
	footerrow: true,
	userDataOnFooter: true,
	gridComplete: function() {
		var userdata = jQuery("#$name").jqGrid('getGridParam', 'userData');
		jQuery("#$name").jqGrid('setCaption',"$ucname for "+userdata.month);
		jQuery("#$name").setCell('private', 1, '', {'color': '#999999'});
		jQuery("#$name").setCell('private', 2, '', {'color': '#999999'});
	}, 
});
</script>
EOT;
	return $out;
} ?>