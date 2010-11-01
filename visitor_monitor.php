<?php
function visitor_monitor($page_no=1){
	global $wpdb;
	global $va_lang;
	$timezone = va_get_timezone();

	$sql="select * from ".WP_VA_TABLE." 
		left join 
		(select stalker_id, count(stalker_id) as v_count from ".WP_VA_FP_TABLE."
		join ".WP_VA_VISITORS_TABLE." on ".WP_VA_FP_TABLE.".cookie = ".WP_VA_VISITORS_TABLE.".cookie or ".WP_VA_FP_TABLE.".ip = ".WP_VA_VISITORS_TABLE.".ip
		group by stalker_id) as stalker_count
		on ".WP_VA_TABLE.".id = stalker_count.stalker_id
		left join 
		(select stalker_id, flag as defined_flag from ".WP_VA_FP_TABLE." group by stalker_id) as defined_flag_temp_table
		on ".WP_VA_TABLE.".id = defined_flag_temp_table.stalker_id
		left join 
		(select stalker_id, count(stalker_id) as c_count from ".WP_VA_FP_TABLE."
		where flag='cookie' group by stalker_id) as cookie_count
		on ".WP_VA_TABLE.".id = cookie_count.stalker_id
		left join 
		(select stalker_id, count(stalker_id) as i_count from ".WP_VA_FP_TABLE."
		where flag='ip' group by stalker_id) as ip_count
		on ".WP_VA_TABLE.".id = ip_count.stalker_id
		left join 
		(	select max(time_visited) as last_visit_time, ".WP_VA_FP_TABLE.".stalker_id from ".WP_VA_VISITORS_TABLE." join
			".WP_VA_FP_TABLE." on ".WP_VA_VISITORS_TABLE.".cookie = ".WP_VA_FP_TABLE.".cookie or ".WP_VA_FP_TABLE.".ip = ".WP_VA_VISITORS_TABLE.".ip
			group by ".WP_VA_FP_TABLE.".stalker_id
		) as last_visit
		 on ".WP_VA_TABLE.".id = last_visit.stalker_id
		";
	$results = $wpdb->get_results($sql);
	if(empty($results)){
		echo $va_lang['stalkers_no_data'];
		return true;
	}
	echo '<form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo '<table class="widefat" cellspacing="0"><thead><tr>
		<th>'.$va_lang['stalkers_name'].'</th>
		<th>'.$va_lang['stalkers_visits'].'</th>
		<th>'.$va_lang['stalkers_ip'].'</th>
		<th>'.$va_lang['stalkers_fp'].'</th>
		<th>'.$va_lang['stalkers_flag'].'</th>
		<th>'.$va_lang['stalkers_is_stats'].'</th>
		<th>'.$va_lang['stalkers_is_block'].'</th>
		<th>'.$va_lang['stalkers_last_visit'].'</th>
		<th>'.$va_lang['stalkers_greet'].'</th>
		<th>'.$va_lang['stalkers_detail'].'</th>
		
		</tr></thead>';
	foreach($results as $r){
		$v_count = empty($r->v_count)? 0:$r->v_count;
		$c_count = empty($r->c_count)? 0:$r->c_count;
		$i_count = empty($r->i_count)? 0:$r->i_count;
		echo "<tr>
		<td><a href='".VA_ADMIN_URL."&va_page=visitor_tracker&va_id=".$r->id."'>".$r->name."</a></td>
		<td>$v_count</td><td>$i_count</td><td>$c_count</td><td>$r->defined_flag</td><td>";
		
		if ($r->is_stat=='y')
			echo "<div style='color:#00ff00'>YES</div>";
		else
			echo "<div style='color:#ff0000'>NO</div>";
		echo "</td><td>";
		if ($r->is_block=='y')
			echo '<img src="'.VA_BASEFOLDER.'img/denied.png" title="'.$va_lang['menu_denied'].'"/>';
		else
			echo '<img src="'.VA_BASEFOLDER.'img/granted.png" title="'.$va_lang['menu_granted'].'"/>';
		echo "</td><td>".va_time_diff(time(),strtotime($r->last_visit_time))."</td><td>";
		if ($r->is_greet=='y'){
			//out mail
			echo '<img src="'.VA_BASEFOLDER.'img/outmail.png"/>';
			if(strlen($r->greet_text)<100){
				echo strip_tags($r->greet_text);				
			}else{
				echo substr(strip_tags($r->greet_text),0,100);
			}
		}else if($r->greet_time < $r->response_time){
			//inmail
			echo '<img src="'.VA_BASEFOLDER.'img/inmail.png"/>';
			if(strlen($r->response_text)<100){
				echo strip_tags($r->response_text);				
			}else{
				echo substr(strip_tags($r->response_text),0,100);
			}
		}else{
			echo '-----';
		}
		
		echo "</td>";
		echo "<td><a href='".VA_ADMIN_URL."&va_page=visitor_tracker&va_id=".$r->id."'>".$va_lang['stalkers_detail']."</a></td>";
		echo "</tr>";
	}
	echo '</table></form>';
}
?>