<?php
function filter_fingerprint($cookie,$page_no = 1){
	global $wpdb;
	if(!empty($_POST)){
		if (!empty($_POST['va_remove'])){
			$sql = "delete from ".WP_VA_FP_TABLE." where stalker_id ='".$_POST['va_stalkerId']."' and cookie='$cookie'";
			$wpdb->query($sql);
		}
		if (!empty($_POST['va_add'])){
			$sql = "insert into ".WP_VA_FP_TABLE." (stalker_id,cookie,flag) values ('".$_POST['va_stalkers']."','$cookie','cookie')";
			$wpdb->query($sql);
		}
		if (!empty($_POST['va_create'])){
			$sql = "insert into ".WP_VA_TABLE." (name) values ('".$_POST['va_name']."')";
			$wpdb->query($sql);
			$sql = "select id from ".WP_VA_TABLE." order by id desc limit 0,1";
			$results = $wpdb->get_results($sql);
			$sql = "insert into ".WP_VA_FP_TABLE." (stalker_id,cookie,flag) 
				values ('".$results[0]->id."','$cookie','cookie')";
			$wpdb->query($sql);
		}
		
	}
	
	$sql = "select  ".WP_VA_VISITORS_TABLE.".access, ".WP_VA_VISITORS_TABLE.".id as visit_id, ".WP_VA_VISITORS_TABLE.".browser, ".WP_VA_VISITORS_TABLE.".os, ".WP_VA_VISITORS_TABLE.".referer,".WP_VA_VISITORS_TABLE.".comment_id, 
		".WP_VA_VISITORS_TABLE.".agent, ".WP_VA_VISITORS_TABLE.".time_visited, ".WP_VA_VISITORS_TABLE.".cookie, ".WP_VA_VISITORS_TABLE.".url, ".WP_VA_VISITORS_TABLE.".ip,
		".WP_VA_TABLE.".id as stalker_id, ".WP_VA_TABLE.".name, ".WP_VA_TABLE.".is_stat,
		".$wpdb->prefix."comments.comment_content  
		from
		".WP_VA_VISITORS_TABLE." left join   ".WP_VA_FP_TABLE." on ".WP_VA_VISITORS_TABLE.".cookie= ".WP_VA_FP_TABLE.".cookie or ".WP_VA_VISITORS_TABLE.".ip= ".WP_VA_FP_TABLE.".ip
		left join ".WP_VA_TABLE." on ".WP_VA_TABLE.".id = ".WP_VA_FP_TABLE.".stalker_id 
		left join ".$wpdb->prefix."comments on ".WP_VA_VISITORS_TABLE.".comment_id = ".$wpdb->prefix."comments.comment_ID
	    where ".WP_VA_VISITORS_TABLE.".cookie = '".$cookie."' order by ".WP_VA_VISITORS_TABLE.".time_visited desc";
	$results = $wpdb->get_results($sql);
	$results_count=$wpdb->num_rows;
	$stalkerId = $results[0]->stalker_id;
	$stalkerName = $results[0]->name;
	
	echo '<form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo '<table style="width:100%;"><tr><td style="vertical-align:top;">';
	if ($stalkerId>0){
		echo sprintf(__('This is %s\' fingerprint.','visitor-analytics'),"<a href='".VA_ADMIN_URL."&va_page=stalkers&va_id=$stalkerId&va_name=$stalkerName'>$stalkerName</a>");
		echo "<input type='submit' value ='".__('Remove','visitor-analytics')."' name='va_remove' style='width:60px;height:25px;'/>
		<input type='text' value ='$stalkerId' name='va_stalkerId' style='visibility:hidden;width:0px;'/>";		
	}else{
		$sql = "select * from ".WP_VA_TABLE."";
		$data = $wpdb->get_results($sql);
		echo "<li>".__('Define a new visitor','visitor-analytics').":<input type='text'' size='10' name ='va_name'>
		<input type='submit'  name='va_create' value ='".__('Create','visitor-analytics')."' style='width:60px;height:25px;'/></li>";
		echo '<li>'.__('Add to defined visitor','visitor-analytics').':<select name="va_stalkers">';
		foreach($data as $d){
			echo '<option value="'.$d->id.'">'.$d->name.'</option>';
		}
		echo "</select><input type='submit' name='va_add' value ='".__('Add','visitor-analytics')."' style='width:40px;height:25px;'/></li>";

	}

	echo '</td>';
	echo '</tr></table></form>';
	make_table($results,$results_count,$page_no);
	
}
?>