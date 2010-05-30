function incDecLevelNum(inc)
{
    var current = jQuery('input[name=maxLevelNum]').val() * 1;
    if (current > 1 || inc === true) {
	    var next = (inc === true) ? (current + 1) : (current - 1);
	    jQuery('input[name="maxLevelNum"]').val(next);
	    jQuery('#maxLevelNum').text(next);
        addRemText(inc, next);
	}
}

function hideUnhide(e)
{
	if (jQuery(e).is(':checked'))
		jQuery('tr.hideChapter').show();
	else 
		jQuery('tr.hideChapter').hide();
};

function addRemText(add, current)
{
    if (add === true) {
    	jQuery('#chapterLevelStyleBody').append(
		    jQuery('<tr>').append(
		        jQuery('<td>').text('Level ' + current + ' start:')
		    ).append(
		        jQuery('<td>').append(
		            jQuery('<textarea>').attr('name', 'levelStyleStart[' + current + ']').attr('cols', '27')
		            .attr('rows', '3')
		        )
		    )
		).append(
		    jQuery('<tr>').attr('class', 'alternate').append(
		        jQuery('<td>').text('Level ' + current + ' start:')
		    ).append(
		        jQuery('<td>').append(
		            jQuery('<textarea>').attr('name', 'levelStyleEnd[' + current + ']').attr('cols', '27')
		            .attr('rows', '3')
		        )
		    )
		);
    }
    else {
    	jQuery('#chapterLevelStyleBody tr:last').remove();
    	jQuery('#chapterLevelStyleBody tr:last').remove();
    }
}