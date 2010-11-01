<?php
function data_analytics(){
	global $wpdb;
	global $va_lang;
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
						GROUP BY ip
				) AS cookie_c
				ORDER BY cookie_count DESC Limit 0,10';
	$results_cookie = $wpdb->get_results($sql);
	
	## show visitors analytics
	$sql='SELECT * FROM '.WP_VA_VISITORS_TABLE;
	$results_visitors = $wpdb->get_results($sql);
	
	## show visitors analytics-2
	$sql='SELECT ip, max(time_visited) as time_visited FROM '.WP_VA_VISITORS_TABLE.' GROUP BY ip';
	$results_visitors_ip = $wpdb->get_results($sql);
	
	## show visitors analytics-3
	$sql='SELECT cookie, max(time_visited) as time_visited FROM '.WP_VA_VISITORS_TABLE.' GROUP BY cookie';
	$results_visitors_cookie = $wpdb->get_results($sql);
	
	foreach($results_url as $r){
		  $hot_url[]	=	$r->url;
		  $hot_url_count[]	=	$r->url_count;
	}
	foreach($results_referer as $r){
			if(empty($r->referer)) $r->referer='direct';
			$hot_refer[]	=	cut_str_2($r->referer,35);
		  $hot_refer_count[]	=	$r->referer_count;
	}
	foreach($results_out_referer as $r){
		if(empty($r->out_referer)) $r->out_referer='direct';
			$hot_out_refer[]	=	cut_str_2($r->out_referer,35);
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
  
  $va_stat=va_get_analytics($results_visitors);
  $va_stat_ip=va_get_analytics($results_visitors_ip);
  $va_stat_cookie=va_get_analytics($results_visitors_cookie);
	echo '<table class="widefat" cellspacing="0">';
 	echo '<thead><tr>';
	echo '<th>Hot Url</th><th>Count</th><th>Hot Entrance</th><th>Count</th><th>Hot Out Refer</th><th>Count</th>';
	echo '</tr></thead>';
	for($i=0;$i<10;$i++){
	  echo '<tr><td><a href="'.get_bloginfo('home').$hot_url[$i].'" target="_blank">'.$hot_url[$i].'</a></td><td>'.$hot_url_count[$i].'</td><td><a href="'.($hot_refer[$i]=='direct' ? get_bloginfo('home') : $hot_refer[$i]).'" target="_blank">'.$hot_refer[$i].'</a></td><td>'. $hot_refer_count[$i].'</td><td><a href="'.($hot_out_refer[$i]=='direct' ? get_bloginfo('home') : $hot_out_refer[$i]).'" target="_blank">'.$hot_out_refer[$i].'</a></td><td>'. $hot_out_refer_count[$i].'</td></tr>';
	}
	echo '<thead><tr>';
	echo '<th>Hot IPs</th><th>Count</th><th> Time  Span </th><th>Hot cookies</th><th>Time  Span</th><th>Count</th>';
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
	echo '<th>Time Span</th><th>Recent Hour</th><th>Recent Day</th><th>Recent Week</th><th>Recent Month</th><th>Recent Year</th>';
	echo '</tr></thead>';
	echo '<tr>';
	echo '<td>Active Visits</td>';
	echo '<td>'.$va_stat[0].'</td>';
	echo '<td>'.$va_stat[1].'</td>';
	echo '<td>'.$va_stat[2].'</td>';
	echo '<td>'.$va_stat[3].'</td>';
	echo '<td>'.$va_stat[4].'</td>';
	echo '</tr>';
	
	echo '<tr>';
	echo '<td>Active IPs</td>';
	echo '<td>'.$va_stat_ip[0].'</td>';
	echo '<td>'.$va_stat_ip[1].'</td>';
	echo '<td>'.$va_stat_ip[2].'</td>';
	echo '<td>'.$va_stat_ip[3].'</td>';
	echo '<td>'.$va_stat_ip[4].'</td>';
	echo '</tr>';
	
	echo '<tr>';
	echo '<td>Active Cookies</td>';
	echo '<td>'.$va_stat_cookie[0].'</td>';
	echo '<td>'.$va_stat_cookie[1].'</td>';
	echo '<td>'.$va_stat_cookie[2].'</td>';
	echo '<td>'.$va_stat_cookie[3].'</td>';
	echo '<td>'.$va_stat_cookie[4].'</td>';
	echo '</tr>';
	
	echo '</table>';

}
?>