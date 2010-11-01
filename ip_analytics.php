<?php
function ip_analytics($page_no){
	if(!empty($_POST)){
		if (is_numeric($_POST['va_all_time'])){
			$va_all_time = floor($_POST['va_all_time']);
		}
		if (is_numeric($_POST['va_all_visit'])){
			$va_all_visit = floor($_POST['va_all_visit']);
		}
		update_option('va_all_time', $va_all_time);
		update_option('va_all_visit', $va_all_visit);
	}
	if($_GET['va_ip']){
				echo '&va_ip='.$_GET['va_ip'];
	}
	global $wpdb;
	global $va_lang;
	$va_per_page = get_option('va_per_page', TRUE);
	$va_all_time = get_option('va_all_time', TRUE);
	$va_all_visit = get_option('va_all_visit', TRUE);
	$per_page = $va_per_page[0];
	$all_time = $va_all_time[0];
	$all_visit = $va_all_visit[0];
	if(empty($per_page)) $per_page = 20;
	$timezone = va_get_timezone();
	$sql="select * from (select * from (select ip as ip_source, count(ip) as ip_count,  max(time_visited) as max_time,min(time_visited) as min_time 
												from ".WP_VA_VISITORS_TABLE."
												 group by ".WP_VA_VISITORS_TABLE.".ip
												) as ip_count_temp_table
										where ip_count_temp_table.ip_count>".	$all_visit." order by ip_count desc
				) as c
				left join ".WP_VA_FP_TABLE." as f on f.ip=c.ip_source and f.flag='ip'
				left join ".WP_VA_TABLE." as a on a.id=f.stalker_id";
	$results = $wpdb->get_results($sql);
	$results_count=$wpdb->num_rows;
	echo '<form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo "<p align=\"left\">".sprintf($va_lang['va_span_option'],"<input type='text' size='3' name='va_all_time' value='".$all_time."'/>").", ";
	echo sprintf($va_lang['va_visits_option'],"<input type='text' size='3' name='va_all_visit' value='".$all_visit."'/>")." ";
	echo "<input type='submit' value ='".$va_lang['va_refresh']."' style='width:70px;height:25px;'/></p></form>";
	echo '<table class="widefat" cellspacing="0"><thead>
		<tr><th>'.$va_lang['menu_ip'].'</th><th>'.$va_lang['menu_stalker'].'</th><th>'.$va_lang['va_visits'].'</th>
		<th>'.$va_lang['va_frequency'].'</th><th>'.$va_lang['va_time_span'].'</th><th>'.$va_lang['menu_time'].'</th></tr><tbody>';
	$isData = false;
	$show_flag = 0;
	foreach($results as $r){
		$isData = true;

		$timeSpan = ceil((strtotime($r->max_time) - strtotime($r->min_time))/(3600*24));
		if ($timeSpan < $all_time)
		   continue;
		
		$show_flag = $show_flag + 1;
		if($show_flag > (($page_no-1)*$per_page) and $show_flag <= ($page_no*$per_page)){
		   $frequency =  floor($r->ip_count/$timeSpan);
		   $likelihood = 255-($timeSpan + $r->ip_count)*15;
		   if ($likelihood<=0)
		   	$likelihood = "00";
		   elseif ($likelihood<=15)
		   	$likelihood = "0".dechex($likelihood);
		   else 
		   	$likelihood = dechex($likelihood);
		   if(empty($r->name)) $r->name = $va_lang['menu_undefine'];
		   echo '<tr style="color:#ff'.$likelihood.'00">';
		   echo '<td><a href="'.VA_ADMIN_URL.'&va_filter=ip&va_ip='.$r->ip_source.'">'.$r->ip_source.'</a></td>';
		   echo '<td>'.$r->name.'</td>';
		   echo '<td>'.$r->ip_count.'</td>';
		   echo '<td>'.$frequency.' visits/day</td>';
		   echo '<td>'.$timeSpan.' days</td>';
		   echo '<td>'.va_get_localtime($r->min_time, $timezone).' ~ '.va_get_localtime($r->max_time, $timezone).'</td>';
		   echo '</tr>';

		}
	}
	echo '</tbody>';
	if(ceil($results_count/$per_page)!=0){
	   echo '<tfoot><tr>';
	   echo '<th colspan="2"></th><th colspan="1"><a href="'.VA_ADMIN_URL.'&va_page=ip_analytics';	
	   echo '&page_no='.((($page_no-1)==0) ? 1 : ($page_no-1)).'" > << PrePage</a></th>';
	   echo '<th colspan="1" align="center"></th>';
	   echo '<th colspan="1" align="center"><a href="'.VA_ADMIN_URL.'&va_page=ip_analytics';	
	   echo '&page_no=';
	   if($page_no == ceil($results_count/$per_page)){
	   		echo $page_no;
	   }else{ 
	   		echo $page_no+1 ;
	   } 
	   echo '" >NextPage >></a></th>';
	   echo '<th colspan="1"></th></tr></tfoot>';
	 }
	   echo '</table>';
	   if(!$isData) echo $va_lang['va_no_data'];	
}
?>