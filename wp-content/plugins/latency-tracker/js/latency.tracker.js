jQuery(document).ready(function($) {
	$("#divLatencyTrackerContent ul").idTabs();
	$("#tblRecentRequests").tablesorter({
		sortList: [[0,1]],
		widgets: ['zebra']
	});
});