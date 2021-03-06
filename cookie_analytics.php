<?php
function cookie_analytics($page_no){
	if(!empty($_POST)){
		if (is_numeric($_POST['va_all_time'])){
			$va_all_time[] = floor($_POST['va_all_time']);
		}
		if (is_numeric($_POST['va_all_visit'])){
			$va_all_visit[] = floor($_POST['va_all_visit']);
		}
		update_option('va_all_time', $va_all_time);
		update_option('va_all_visit', $va_all_visit);
	}
	if($_GET['va_cookie']){
				echo '&va_cookie='.$_GET['va_cookie'];
	}
	global $wpdb;
	$va_options = get_option('va_options', TRUE);
	$va_all_time = get_option('va_all_time', TRUE);
	$va_all_visit = get_option('va_all_visit', TRUE);
	$per_page = $va_options['per_page'];
	$all_time = $va_all_time[0];
	$all_visit = $va_all_visit[0];
	if(empty($per_page)) $per_page = 20;
	$timezone = va_get_timezone();
	$sql="select * from (select * from (select cookie as coo, count(cookie) as co,  max(time_visited) as max_time,min(time_visited) as min_time 
												from ".WP_VA_VISITORS_TABLE."
												 group by ".WP_VA_VISITORS_TABLE.".cookie
												) as cookie_count
										where cookie_count.co>".$all_visit." order by co desc
				) as c
				left join ".WP_VA_FP_TABLE." as f on f.cookie=c.coo and f.flag='cookie'
				left join ".WP_VA_TABLE." as a on a.id=f.stalker_id";
	$results = $wpdb->get_results($sql);
	echo '<form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo "<p align=\"left\">".sprintf(__('Filter : Only show these which more than %s days','visitor-analytics'),"<input type='text' size='3' name='va_all_time' value='".$all_time."'/>").", ";
	echo sprintf(__('and visits greater than %s times','visitor-analytics'),"<input type='text' size='3' name='va_all_visit' value='".$all_visit."'/>")." ";
	echo "<input type='submit' value ='".__('Refresh','visitor-analytics')."' style='width:70px;height:25px;'/></p></form>";
	echo '<table class="widefat" cellspacing="0"><thead>
		<tr><th>'.__('Cookie','visitor-analytics').'</th><th>'.__('Name','visitor-analytics').'</th><th>'.__('Visit count','visitor-analytics').'</th>
		<th>'.__('Frenquency','visitor-analytics').'</th><th>'.__('Date Span','visitor-analytics').'</th><th>'.__('Time Span','visitor-analytics').'</th></tr><tbody>';
	$isData = false;
	$show_flag = 0;
	foreach($results as $r){
		$isData = true;
		$timeSpan = ceil((strtotime($r->max_time) - strtotime($r->min_time))/(3600*24));
		if ($timeSpan < $all_time)
			continue;
		$show_flag = $show_flag + 1;
		if($show_flag > (($page_no-1)*$per_page) and $show_flag <= ($page_no*$per_page)){
		  $frequency =  floor($r->co/$timeSpan);
		  $likelihood = 255-($timeSpan + $r->co)*15;
		  if ($likelihood<=0)
		  	$likelihood = "00";
		  elseif ($likelihood<=15)
		  	$likelihood = "0".dechex($likelihood);
		  else 
		  	$likelihood = dechex($likelihood);
		  if(empty($r->name)) $r->name = __('undefined','visitor-anaytics');
		  echo '<tr style="color:#ff'.$likelihood.'00">';
		  echo '<td><a href="'.VA_ADMIN_URL.'&va_filter=fingerprint&va_cookie='.$r->coo.'">'.$r->coo.'</a></td>';
		  echo '<td>'.$r->name.'</td>';
		  echo '<td>'.$r->co.'</td>';
		  echo '<td>'.$frequency.' visits/day</td>';
		  echo '<td>'.$timeSpan.' days</td>';
		  echo '<td>'.va_get_localtime($r->min_time, $timezone).' ~ '.va_get_localtime($r->max_time, $timezone).'</td>';
		  echo '</tr>';
		}
	}
	echo '</tbody>';
	if(ceil($results_count/$per_page)!=0){
	   echo '<tfoot><tr>';
	   echo '<th colspan="1"></th><th colspan="1"><a href="'.VA_ADMIN_URL.'&va_page=cookie_analytics';	
	   echo '&page_no='.((($page_no-1)==0) ? 1 : ($page_no-1)).'" > <<'. __('PrePage','visitor-analytics').'</a></th>';
	   echo '<th colspan="2" align="center"></th>';
	   echo '<th colspan="1" align="center"><a href="'.VA_ADMIN_URL.'&va_page=cookie_analytics';	
	   echo '&page_no=';
	   if($page_no == ceil($results_count/$per_page)){
	   		echo $page_no;
	   }else{ 
	   		echo $page_no+1 ;
	   } 
	   echo '" >'. __('NextPage','visitor-analytics').' >></a></th>';
	   echo '<th colspan="1"></th></tr></tfoot>';
	 }
	 echo '</table>';
	 if(!$isData) echo  __('No Data','visitor-analytics');
}
?>