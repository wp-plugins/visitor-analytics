<?php
function visitor_comments($page_no=1){
	global $wpdb;
	global $va_lang;
	$sql = "select ".WP_VA_VISITORS_TABLE.".*, ".WP_VA_TABLE.".name,".WP_VA_TABLE.".id as stalker_id ,".$wpdb->prefix."comments.comment_content from
		".WP_VA_VISITORS_TABLE." left join   ".WP_VA_FP_TABLE." on ".WP_VA_VISITORS_TABLE.".cookie= ".WP_VA_FP_TABLE.".cookie 
		left join ".WP_VA_TABLE." on ".WP_VA_TABLE.".id = ".WP_VA_FP_TABLE.".stalker_id 
		left join ".$wpdb->prefix."comments on ".WP_VA_VISITORS_TABLE.".comment_id = ".$wpdb->prefix."comments.comment_ID
	    where ".WP_VA_VISITORS_TABLE.".comment_id > '-1' 
		order by ".WP_VA_VISITORS_TABLE.".time_visited desc";
	$results = $wpdb->get_results($sql);
	$results_count=$wpdb->num_rows;
	if(empty($results)){
		echo __('No Data','visitor-analytics');
	}else{
		make_table($results,$results_count,$page_no);
	}
}
?>