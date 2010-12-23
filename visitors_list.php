<?php
function visitor_list($page_no) {
	global $wpdb;

	$sql = "select ".WP_VA_VISITORS_TABLE.".access, ".WP_VA_VISITORS_TABLE.".id as visit_id, ".WP_VA_VISITORS_TABLE.".browser, ".WP_VA_VISITORS_TABLE.".os, ".WP_VA_VISITORS_TABLE.".referer,".WP_VA_VISITORS_TABLE.".comment_id, 
		".WP_VA_VISITORS_TABLE.".agent, ".WP_VA_VISITORS_TABLE.".time_visited, ".WP_VA_VISITORS_TABLE.".cookie, ".WP_VA_VISITORS_TABLE.".url, ".WP_VA_VISITORS_TABLE.".ip,
		".WP_VA_TABLE.".id as stalker_id, ".WP_VA_TABLE.".name, ".WP_VA_TABLE.".is_stat,
		".$wpdb->prefix."comments.comment_content  
		from
		".WP_VA_VISITORS_TABLE." left join   ".WP_VA_FP_TABLE." on ".WP_VA_VISITORS_TABLE.".cookie= ".WP_VA_FP_TABLE.".cookie 
		left join ".WP_VA_TABLE." on ".WP_VA_TABLE.".id = ".WP_VA_FP_TABLE.".stalker_id
		
		left join ".$wpdb->prefix."comments on ".WP_VA_VISITORS_TABLE.".comment_id = ".$wpdb->prefix."comments.comment_ID";
	$sql.="	and  (".WP_VA_TABLE.".is_stat='y' or ".WP_VA_TABLE.".is_stat is null) 
		order by ".WP_VA_VISITORS_TABLE.".time_visited desc ";
	
	$results = $wpdb->get_results($sql);
	$results_count=$wpdb->num_rows;

	if (empty($results)){
		echo '<div><p>'.__('No Data','visitor-analyrics').'</p></div>';
	}else{
		make_table($results,$results_count,$page_no);
	}

}
?>