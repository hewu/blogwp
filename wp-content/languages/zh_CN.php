<?php
// Make the admin interface better look for Chinese.
function cn_admin_fonttweak() { ?>
<style type="text/css">

.postbox p, .postbox ul, .postbox ol, .postbox blockquote, #wp-version-message { font-size: 12px; }
#dashboard_quick_press #media-buttons {	font-size: 12px;}
#dashboard_recent_drafts h4 abbr { font-size: 12px;}
#the-comment-list .comment-item p.row-actions {	font-size: 12px;}
#wpcontent select {font: 12px/20px "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;}
#wphead h1 span {font-size: 12px;}
#adminmenu .wp-submenu a {font: normal 12px/18px "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;}
#the-comment-list td.comment p {font-size: 12px;}
#screen-meta a.show-settings {	font-size: 12px;}
#favorite-actions a {font-size: 12px;}
.form-table td, #wpbody-content .describe td { font-size: 12px;}
#poststuff .inside, #poststuff .inside p {	font-size: 12px;}
#misc-publishing-actions { font-size: 12px;}
label {	font-size: 12px; }
.subsubsub {font-size: 12px;}
.widefat td, .widefat th {font-size: 12px;}

.submit input,
.button,
.button-primary,
.button-secondary,
.button-highlighted,
#postcustomstuff .submit input {
	font-size: 12px !important;
}

</style>
<?php
}
add_action('admin_head', 'cn_admin_fonttweak');
?>