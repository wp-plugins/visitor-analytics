<?php
function data_analytics(){
	global $wpdb;
	$timezone = va_get_timezone();
	## Hot url
	$sql='SELECT * FROM (
						SELECT  url , COUNT( url ) AS url_count
						FROM  '.WP_VA_VISITORS_TABLE.'
						GROUP BY url
				) AS url_c
				ORDER BY url_count DESC Limit 0,10';
	$results_url = $wpdb->get_results($sql);
	## Hot refer
	$sql='SELECT * FROM (
						SELECT  referer , COUNT( referer ) AS referer_count
						FROM  '.WP_VA_VISITORS_TABLE.'
						GROUP BY referer
				) AS referer_c
				ORDER BY referer_count DESC Limit 0,10';
	$results_referer= $wpdb->get_results($sql);
	## Hot out refer
	$sql="SELECT * FROM (
						SELECT  referer as out_referer, COUNT( referer ) AS out_referer_count
						FROM  ".WP_VA_VISITORS_TABLE."
						where referer not like '%".get_bloginfo('home')."%'
						GROUP BY referer
				) AS out_referer_c
				ORDER BY out_referer_count DESC Limit 0,10";
	$results_out_referer= $wpdb->get_results($sql);
	## Hot ip
	$sql='SELECT * FROM (
						SELECT  ip , COUNT( ip ) AS ip_count, max(time_visited) as max_time,min(time_visited) as min_time
						FROM  '.WP_VA_VISITORS_TABLE.'
						GROUP BY ip
				) AS ip_c
				ORDER BY ip_count DESC Limit 0,10';
	$results_ip = $wpdb->get_results($sql);
	
	## Hot cookie
	$sql='SELECT * FROM (
						SELECT  cookie , COUNT( cookie ) AS cookie_count, max(time_visited) as max_time,min(time_visited) as min_time
						FROM  '.WP_VA_VISITORS_TABLE.'
						GROUP BY cookie
				) AS cookie_c
				ORDER BY cookie_count DESC Limit 0,10';
	$results_cookie = $wpdb->get_results($sql);
	
	## show visitors analytics
	$sql='SELECT * FROM '.WP_VA_VISITORS_TABLE.' order by time_visited asc';
	$results_visitors = $wpdb->get_results($sql);
	
	## show visitors analytics-2
	$sql='SELECT ip, max(time_visited) as time_visited FROM '.WP_VA_VISITORS_TABLE.' GROUP BY ip order by time_visited asc';
	$results_visitors_ip = $wpdb->get_results($sql);
	
	## show visitors analytics-3
	$sql='SELECT cookie, max(time_visited) as time_visited FROM '.WP_VA_VISITORS_TABLE.' GROUP BY cookie order by time_visited asc';
	$results_visitors_cookie = $wpdb->get_results($sql);
	
	foreach($results_url as $r){
		  $hot_url_title[]	=	cut_str_2($r->url,35);
		  $hot_url[]	=	$r->url;
		  $hot_url_count[]	=	$r->url_count;
	}
	foreach($results_referer as $r){
			if(empty($r->referer)) $r->referer='direct';
			$hot_refer_title[]	=	cut_str_2($r->referer,35);
			$hot_refer[] = $r->referer;
		  $hot_refer_count[]	=	$r->referer_count;
	}
	foreach($results_out_referer as $r){
		if(empty($r->out_referer)) $r->out_referer='direct';
			$hot_out_refer_title[]	=	cut_str_2($r->out_referer,35);
			$hot_out_refer[]	=	$r->out_referer;
		  $hot_out_refer_count[]	=	$r->out_referer_count;
	}
	foreach($results_ip as $r){
			$hot_ip[]	=	$r->ip;
		  $hot_ip_count[]	=	$r->ip_count;
		  $hot_ip_max_time[] = va_get_localtime_2($r->max_time, $timezone);
		  $hot_ip_min_time[] = va_get_localtime_2($r->min_time, $timezone);
		  
	}
	foreach($results_cookie as $r){
			$hot_cookie[]	=	$r->cookie;
		  $hot_cookie_count[]	=	$r->cookie_count;
		  $hot_cookie_max_time[] = va_get_localtime_2($r->max_time, $timezone);
		  $hot_cookie_min_time[] = va_get_localtime_2($r->min_time, $timezone);
	}
  
  $va_stat = va_get_analytics($results_visitors);
  $va_stat_ip = va_get_analytics($results_visitors_ip);
  $va_stat_cookie = va_get_analytics($results_visitors_cookie);
  
	echo '<table class="widefat" cellspacing="0">';
 	echo '<thead><tr>';
	echo '<th>'.__('Hot Url','visitor-analytics').'</th><th>'.__('Count','visitor-analytics').'</th><th>'.__('Hot Entrance','visitor-analytics').'</th><th>'.__('Count','visitor-analytics').'</th><th>'.__('Hot Out Refer','visitor-analytics').'</th><th>'.__('Count','visitor-analytics').'</th>';
	echo '</tr></thead>';
	for($i=0;$i<10;$i++){
	  echo '<tr><td><a href="'.get_bloginfo('home').$hot_url[$i].'" target="_blank">'.$hot_url_title[$i].'</a></td><td>'.$hot_url_count[$i].'</td><td><a href="'.($hot_refer[$i]=='direct' ? get_bloginfo('home') : $hot_refer[$i]).'" target="_blank">'.$hot_refer_title[$i].'</a></td><td>'. $hot_refer_count[$i].'</td><td><a href="'.($hot_out_refer[$i]=='direct' ? get_bloginfo('home') : $hot_out_refer[$i]).'" target="_blank">'.$hot_out_refer_title[$i].'</a></td><td>'. $hot_out_refer_count[$i].'</td></tr>';
	}
	echo '<thead><tr>';
	echo '<th>'.__('Hot IPs','visitor-analytics').'</th><th>'.__('Count','visitor-analytics').'</th><th>'.__('Time Span','visitor-analytics').'</th><th>'.__('Hot cookies','visitor-analytics').'</th><th>'.__('Time Span','visitor-analytics').'</th><th>'.__('Count','visitor-analytics').'</th>';
	echo '</tr></thead>';
	for($i=0;$i<10;$i++){
	  echo '<tr><td><a href="'.VA_ADMIN_URL.'&va_filter=ip&va_ip='.$hot_ip[$i].'" target="_blank">'.$hot_ip[$i].'</a></td>
	            <td>'.$hot_ip_count[$i].'</td>
	            <td>'.$hot_ip_min_time[$i].' ~ '.$hot_ip_max_time[$i].'</td>
	            <td><a href="'.VA_ADMIN_URL.'&va_filter=fingerprint&va_cookie='.$hot_cookie[$i].'" target="_blank">'.$hot_cookie[$i].'</a></td>
	            <td>'.$hot_cookie_min_time[$i].' ~ '.$hot_cookie_max_time[$i].'</td>
	            <td>'.$hot_cookie_count[$i].'</a></td></tr>';
	}
	echo '<thead><tr>';
	echo '<th>'.__('Time Span','visitor-analytics').'</th><th>'.__('Recent Hour','visitor-analytics').'</th><th>'.__('Recent Day','visitor-analytics').'</th><th>'.__('Recent Week','visitor-analytics').'</th><th>'.__('Recent Month','visitor-analytics').'</th><th>'.__('Recent Year','visitor-analytics').'</th>';
	echo '</tr></thead>';
	echo '<tr>';
	echo '<td>'.__('Active Visits','visitor-analytics').'</td>';
	echo '<td>'.$va_stat['hour'].'</td>';
	echo '<td>'.$va_stat['day'].'</td>';
	echo '<td>'.$va_stat['week'].'</td>';
	echo '<td>'.$va_stat['month'].'</td>';
	echo '<td>'.$va_stat['year'].'</td>';
	echo '</tr>';
	
	echo '<tr>';
	echo '<td>'.__('Active IPs','visitor-analytics').'</td>';
	echo '<td>'.$va_stat_ip['hour'].'</td>';
	echo '<td>'.$va_stat_ip['day'].'</td>';
	echo '<td>'.$va_stat_ip['week'].'</td>';
	echo '<td>'.$va_stat_ip['month'].'</td>';
	echo '<td>'.$va_stat_ip['year'].'</td>';
	echo '</tr>';
	
	echo '<tr>';
	echo '<td>'.__('Active Cookies','visitor-analytics').'</td>';
	echo '<td>'.$va_stat_cookie['hour'].'</td>';
	echo '<td>'.$va_stat_cookie['day'].'</td>';
	echo '<td>'.$va_stat_cookie['week'].'</td>';
	echo '<td>'.$va_stat_cookie['month'].'</td>';
	echo '<td>'.$va_stat_cookie['year'].'</td>';
	echo '</tr>';
	
	echo '</table>';

}
?>