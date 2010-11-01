<?php
function visitor_tracker($id,$page_no=1){
	global $va_lang;
	global $wpdb;
	$timezone = va_get_timezone();
	$sql ="select * from ".WP_VA_TABLE." where id ='".$id."'";
	$results_message = $wpdb->get_results($sql);
	$va_submenu='<p><b>Visitor Name: <a href="'.VA_ADMIN_URL.'&va_page=visitor_tracker&va_id='.$id.'">'.$results_message[0]->name.'</a></b>&nbsp;&nbsp;&nbsp;&nbsp;[<a href="'.VA_ADMIN_URL.'&va_page=visitor_tracker&va_id='.$id.'">General</a>&nbsp;|&nbsp;<a href="'.VA_ADMIN_URL.'&va_page=visitor_tracker&va_id='.$id.'&va_submenu=message">Leave Message</a>&nbsp;|&nbsp;<a href="'.VA_ADMIN_URL.'&va_page=visitor_tracker&va_id='.$id.'&va_submenu=option">Option</a>]</p>';

	if(isset($_GET['va_submenu'])){
		if($_GET['va_submenu'] == 'message'){
			if(!empty($_POST)){
					$sql ="update ".WP_VA_TABLE." set is_greet='y', greet_text='".$_POST['va_greet_text']."', greet_time='".gmdate("Y-n-d H:i:s")."' where id=$id"; 
					$wpdb->query($sql);
			}
			$sql ="select * from ".WP_VA_TABLE." where id ='".$id."'";
			$results_message = $wpdb->get_results($sql);
			echo $va_submenu;
			if(!empty($results_message[0]->greet_text)){
				echo '<p> Recent Message</p>' ;
				echo 'You greet <b>'.$results_message[0]->name.'</b>: '.$results_message[0]->greet_text.'&nbsp;&nbsp;&nbsp;['.va_get_localtime($results_message[0]->greet_time, $timezone).']';
				if (!empty($results_message[0]->response_text)){
				 	echo '<p><b>'.$results_message[0]->name.'</b> response to you : '.$results_message[0]->response_text.'&nbsp;&nbsp;&nbsp;['.va_get_localtime($results_message[0]->response_time, $timezone).']</p>';
				}
			}
			echo '<form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
			echo '<p> Greet to <b>'.$results_message[0]->name.':</b></p>';
			echo '<textarea name="va_greet_text" id="va_greet_text" style="width:500px;margin-bottom:5px;" ></textarea><br />';
			echo '<input type="submit" value="'.$va_lang['stalker_update'].'" style="height:25px;width:140px"/>';
			echo '</form>';
		}else if($_GET['va_submenu'] == 'option'){
			if(!empty($_POST)){
				if ($_POST['group_del'] == 'del_yes'){
					$sql = "delete from ".WP_VA_TABLE." where id='$id'";				
					$wpdb->query($sql);
					$sql = "delete from ".WP_VA_FP_TABLE." where stalker_id='$id'";
					$wpdb->query($sql);
				}else if ($_POST['group_v_del'] == 'del_yes'){
					$sql = "delete from ".WP_VA_VISITORS_TABLE." 
					where cookie in (select cookie from ".WP_VA_FP_TABLE." where stalker_id='$id' and flag='cookie')
					 or ip in (select ip from ".WP_VA_FP_TABLE." where stalker_id='$id' and flag='ip')";
					$wpdb->query($sql);				
				}else{
					if ($_POST['group1'] == 'stat_yes')  $isStat ='y';
					elseif ($_POST['group1'] == 'stat_no') $isStat ='n';
					if ($_POST['group_block'] == 'block_no') $isBlock ='n';
					elseif ($_POST['group_block'] == 'block_yes') $isBlock ='y';
					$sql ="update ".WP_VA_TABLE." set name='".$_POST['va_name']."', is_stat ='$isStat', is_block ='$isBlock'  where id=$id"; 
					$wpdb->query($sql);
				}
			}
			$sql ="select * from ".WP_VA_TABLE." where id ='".$id."'";
			$results_option = $wpdb->get_results($sql);
			echo $va_submenu;
			echo '<form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
			echo '<p>'.$va_lang['stalker_name'].':<input name="va_name" type="text" value="'.$results_option[0]->name.'" size="15"/></p>';
			echo '<p>'.$va_lang['stalker_block'].': <input type="radio" name="group_block" value="block_yes" ';
	           if ($results_option[0]->is_block =='y') echo "checked";
	    echo '> Yes <input type="radio" name="group_block" value="block_no" ';
	           if ($results_option[0]->is_block =='n') echo "checked";
	    echo '> No</p>';
	    echo '<p>'.$va_lang['stalker_stat'].': <input type="radio" name="group1" value="stat_yes" ';
	           if ($results_option[0]->is_stat =='y') echo "checked";
	    echo '> Yes <input type="radio" name="group1" value="stat_no" ';
							if ($results_option[0]->is_stat =='n') echo "checked";
			echo '> No</p>';
			echo '<p>Delete this defined visitor: <input type="radio" name="group_del" value="del_yes" > Yes <input type="radio" name="group_del" value="del_no" checked > No</p>';
			echo '<p>Delete visiting log: <input type="radio" name="group_v_del" value="del_yes" > Yes <input type="radio" name="group_v_del" value="del_no" checked > No</p>';
			echo '<input type="submit" value="'.$va_lang['stalker_update'].'" style="height:25px;width:140px"/>';
			echo '</form>';
		}
	}else{
			$sql_1="select * from ".WP_VA_TABLE." ,".WP_VA_FP_TABLE." where ".WP_VA_TABLE.".id=".$id." and ".WP_VA_TABLE.".id=".WP_VA_FP_TABLE.".stalker_id";
			$results_1 = $wpdb->get_results($sql_1);
			
			$sql_2 = "select  ".WP_VA_VISITORS_TABLE.".access, ".WP_VA_VISITORS_TABLE.".id as visit_id, ".WP_VA_VISITORS_TABLE.".browser, ".WP_VA_VISITORS_TABLE.".os, ".WP_VA_VISITORS_TABLE.".referer,".WP_VA_VISITORS_TABLE.".comment_id, 
				".WP_VA_VISITORS_TABLE.".agent, ".WP_VA_VISITORS_TABLE.".time_visited, ".WP_VA_VISITORS_TABLE.".cookie, ".WP_VA_VISITORS_TABLE.".url, ".WP_VA_VISITORS_TABLE.".ip,
				".WP_VA_TABLE.".id as stalker_id, ".WP_VA_TABLE.".name, ".WP_VA_TABLE.".is_stat,
				".$wpdb->prefix."comments.comment_content  
				from
				".WP_VA_VISITORS_TABLE." left join   ".WP_VA_FP_TABLE." on ".WP_VA_VISITORS_TABLE.".cookie= ".WP_VA_FP_TABLE.".cookie  or ".WP_VA_FP_TABLE.".ip = ".WP_VA_VISITORS_TABLE.".ip
				left join ".WP_VA_TABLE." on ".WP_VA_TABLE.".id = ".WP_VA_FP_TABLE.".stalker_id 
				left join ".$wpdb->prefix."comments on ".WP_VA_VISITORS_TABLE.".comment_id = ".$wpdb->prefix."comments.comment_ID
	    	where stalker_id ='$id'
				order by ".WP_VA_VISITORS_TABLE.".time_visited desc";
			$results_2 = $wpdb->get_results($sql_2);
			$results_2_count=$wpdb->num_rows;
			
			$stalkerName = $results_1[0]->name;

			if(empty($results_1)){
				echo $va_lang['stalker_no_data'];
				return;
			}
			echo '<div>';
      echo $va_submenu;
			echo '<b>Defined Cookies :</b> <br />';
			foreach($results_1 as $r){
				if($r->cookie!=''){
					echo '<a href="'.VA_ADMIN_URL.'&filter=fingerprint&cookie='.$r->cookie.'" title="'.$va_lang['title_cookie'].'">'.$r->cookie.'</a> , ';
				}
			}
			echo '<br /><b>Defined IPs :</b><br /> ';
			foreach($results_1 as $r){
				if($r->ip!=''){
					echo '<a href="'.VA_ADMIN_URL.'&filter=ip&ip='.$r->ip.'"  title="'.$va_lang['title_ip'].'">'.$r->ip.'</a><br/>';
				}
			}
			echo '</div>';
			make_table($results_2,$results_2_count,$page_no);
		}
}
?>