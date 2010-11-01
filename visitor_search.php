<?php
function visitor_search($page_no=1){
	global $wpdb;
	global $va_lang;
	if (!empty($_POST)){
		if (!empty($_POST["va_fp"])){
			$whereClauses[] = " ".WP_VA_VISITORS_TABLE.".cookie = '".$_POST["va_fp"]."' ";
			$va_search['fp'] = $_POST["va_fp"];
		}
		if (!empty($_POST["va_agent"])){
			$whereClauses[] = " ".WP_VA_VISITORS_TABLE.".agent like '%".$_POST["va_agent"]."%' ";
			$va_search['agent'] = $_POST["va_agent"];
		}
		if (!empty($_POST["va_ip"])){
			$whereClauses[] = " ".WP_VA_VISITORS_TABLE.".ip like '".trim($_POST["va_ip"],"*")."%' ";
			$va_search['ip'] = $_POST["va_ip"];
		}
		if (!empty($_POST["va_days"])){
			$whereClauses[] = " ".WP_VA_VISITORS_TABLE.".time_visited<'".get_prev_date($_POST["va_days"])."' ";
		  $va_search['days'] = $_POST["va_days"];
		}
		update_option('va_search', $va_search);

		if (!empty($whereClauses)){
			$whereClause = " where ".implode(' or ', $whereClauses);
			if (!empty($_POST["va_delete"])){
				$sql = "delete from ".WP_VA_VISITORS_TABLE." ".$whereClause;
				$wpdb->query($sql);
			}
		}
	}
	
	$va_search = get_option('va_search', TRUE);
	
	$search_fp = $va_search['fp'];
	$search_agent = $va_search['agent'];
	$search_ip = $va_search['ip'];
	$search_days = $va_search['days'];
	
	if(!empty($search_fp))	$whereClauses[]=" ".WP_VA_VISITORS_TABLE.".cookie = '".$search_fp."' ";
	if(!empty($search_agent)) $whereClauses[]=" ".WP_VA_VISITORS_TABLE.".agent like '%".$search_agent."%' ";
	if(!empty($search_ip)) $whereClauses[]=" ".WP_VA_VISITORS_TABLE.".ip like '".trim($search_ip,"*")."%' ";
	if(!empty($search_days)) $whereClauses[]=" ".WP_VA_VISITORS_TABLE.".time_visited<'".get_prev_date($search_days)."' ";
	if (!empty($whereClauses)){
			$whereClause = " where ".implode(' or ', $whereClauses);
	}
	
	echo '<script LANGUAGE="JavaScript">function confirmSubmit(){
		return confirm("Are you sure to delete these?")?true:false;
		}</script>';
	echo $va_lang['delete_title'].":";
	echo '<form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo "<table><tr><td>".$va_lang['menu_fingerprint']." = <input type='text' size='50' name='va_fp' value='".$search_fp."'/> </td>";
	echo "<td>".$va_lang['menu_agent']." like : <input type='text' size='50' name='va_agent' value='".$search_agent."'/></td></tr>";
	echo "<tr><td>IP like: <input type='text' size='50' name='va_ip' value='".$search_ip."'/></td>";
	echo "<td>".sprintf($va_lang['delete_days'],"<input type='text' size='5' name='va_days' value='".$search_days."'/>")."</td></tr></table>";
	echo "<input type='submit' style='width:150px;height:25px;' value='".$va_lang['delete_search']."'/>";
	//if (empty($whereClause))
	//	return true;
	echo "<input type='submit' name ='va_delete' style='width:150px;height:25px;' value='".$va_lang['delete_delete']."' onClick='return confirmSubmit();'/>";	
	echo "</form>"; 
	echo "<br>";
	$sql = "select ".WP_VA_VISITORS_TABLE.".id as visit_id, ".WP_VA_VISITORS_TABLE.".browser, ".WP_VA_VISITORS_TABLE.".os, ".WP_VA_VISITORS_TABLE.".referer,".WP_VA_VISITORS_TABLE.".comment_id, 
		".WP_VA_VISITORS_TABLE.".agent, ".WP_VA_VISITORS_TABLE.".time_visited, ".WP_VA_VISITORS_TABLE.".cookie, ".WP_VA_VISITORS_TABLE.".url, ".WP_VA_VISITORS_TABLE.".ip,
		".WP_VA_TABLE.".id as stalker_id, ".WP_VA_TABLE.".name, ".WP_VA_TABLE.".is_stat,
		".$wpdb->prefix."comments.comment_content  
		from
		".WP_VA_VISITORS_TABLE." left join   ".WP_VA_FP_TABLE." on ".WP_VA_VISITORS_TABLE.".cookie= ".WP_VA_FP_TABLE.".cookie 
		left join ".WP_VA_TABLE." on ".WP_VA_TABLE.".id = ".WP_VA_FP_TABLE.".stalker_id
		left join ".$wpdb->prefix."comments on ".WP_VA_VISITORS_TABLE.".comment_id = ".$wpdb->prefix."comments.comment_ID ".$whereClause."		
		order by ".WP_VA_VISITORS_TABLE.".time_visited desc";
		
	$results = $wpdb->get_results($sql);
	$results_count=$wpdb->num_rows;
	echo "<div>".$va_lang['delete_count'].": ".count($results)."</div>";
	make_table($results,$results_count,$page_no);
}
?>