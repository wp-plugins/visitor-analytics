<?php
function make_table($results,$results_count,$page_no=1){
	global $va_lang;
	$timezone = va_get_timezone();
	$va_per_page = get_option('va_per_page', TRUE);
	$per_page = $va_per_page[0];
	if(empty($per_page)) $per_page = 20;
	echo '<div style="width:100%;background-color:#ffffff">';	
	echo '<table class="widefat" cellspacing="0"><thead>';
	//make_stat_chart($results,true);
	echo '</thead></table>';
	echo '<table class="widefat" cellspacing="0"><thead>
		<tr>
		<th width=20%>'.$va_lang['menu_time'].'</th>
		<th width=20%>'.$va_lang['menu_url'].'</td>
		<th width=20%>'.$va_lang['menu_referer'].'</th>
		<th width=15%>'.$va_lang['menu_ip'].'</td>
		<th width=25%>'.$va_lang['menu_fingerprint'].'</th>	
		<th width=5%>'.$va_lang['menu_stalker'].'</th>
		<th width=5%>'.$va_lang['menu_agent'].'</th>
		</tr></thead>';
	echo '<tbody>';
	$show_flag = 0;
	foreach($results as $r){
		$show_flag = $show_flag + 1;
		if($show_flag > (($page_no-1)*$per_page) and $show_flag <= ($page_no*$per_page)){
		   echo '<tr><td>';
		   echo va_get_localtime($r->time_visited, $timezone).'</td>';
		   if (strlen($r->url)>25)
		   	echo '<td><a href="'.$r->url.'" title="'.$r->url.'" target="_blank">'.substr($r->url,0,25).'...</a></td>';
		   else
		   	echo '<td><a href="'.$r->url.'" title="'.$r->url.'" target="_blank">'.$r->url.'</a></td>';
		   if ($r->referer ==''){
		   	echo '<td>direct</td>';
		   }else{
		   	echo '<td><a target="_blank" href="'.$r->referer.'" title="'.$r->referer.'">'.get_referer_domain($r->referer).'</a></td>';
		   }
		   $cuva_ip_address='http://images.ideashot.com/special_use/ip/ip.php?action=getip&ip_url='.$r->ip;
		   echo '<td><a title="'.$cuva_ip_address.'" href="'.VA_ADMIN_URL.'&va_filter=ip&va_ip='.$r->ip.'">'.$r->ip.'</a><a href="'.$cuva_ip_address.'" target="_blank" ><img src="'.VA_BASEFOLDER.'img/questionmark-icon.gif"/></a></td>';
		   echo '<td><a href="'.VA_ADMIN_URL.'&va_filter=fingerprint&va_cookie='.$r->cookie.'" title="'.$va_lang['title_cookie'].'">'.$r->cookie.'</a></td>';
		   echo '<td>';
		   switch($r->access){
		   	case 'y':
		   		echo '<img src="'.VA_BASEFOLDER.'img/granted.png" title="'.$va_lang['menu_granted'].'"/>';
		   		break;
		   	case 'n':
		   		echo '<img src="'.VA_BASEFOLDER.'img/denied.png" title="'.$va_lang['menu_denied'].'"/>';
		   		break;
		   	case 'g':
				  echo '<img src="'.VA_BASEFOLDER.'img/outmail.png" title="'.$va_lang['menu_access_greet'].'"/>';
				  break;
			  case 'g_s':
				  echo '<img src="'.VA_BASEFOLDER.'img/inmail.png" title="'.$va_lang['menu_access_send'].'"/>';
				  break;
		  }
		  echo '&nbsp;<a href="'.VA_ADMIN_URL.'&va_page=stalkers&va_id='.$r->stalker_id.'&va_name='.urlencode($r->name).'"
			title="'.$va_lang['title_stalker'].'">'.$r->name.'</a>';
		  if ($r->comment_id > -1){
			  echo ' <a href="'.$r->referer.'" title="'.strip_tags($r->comment_content).'" target="_blank">
			  <img src="'.VA_BASEFOLDER.'img/comment-icon.gif"/></a>';
		  }
		  echo '</td>';
		  echo '<td><a id ="short_'.$r->visit_id.'" href="#" title="'.$r->agent.'" onClick="toggleAgent('.$r->visit_id.')">'.$r->browser.'</a></td>';
		
		  echo '</tr></tbody>';
	 }
	}
  if(ceil($results_count/$per_page)!=0){
	  echo '<tfoot><tr>';
	  echo '<th colspan="2"></th><th colspan="1"><a href="'.VA_ADMIN_URL;
	  if($_GET['va_page']){
	  			echo '&va_page='.$_GET['va_page'];
	  }
	  if($_GET['va_filter']){
	  			echo '&va_filter='.$_GET['va_filter'];
	  }
	  if($_GET['va_cookie']){
	  			echo '&va_cookie='.$_GET['va_cookie'];
	  }
	  if($_GET['va_ip']){
	  			echo '&va_ip='.$_GET['va_ip'];
	  }
	  if($_GET['va_id']){
	  			echo '&va_id='.$_GET['va_id'];
	  }
	  
	  echo '&page_no='.((($page_no-1)==0) ? 1 : ($page_no-1)).'" > << PrePage</a></th>';
	  echo '<th colspan="1" align="center">Current: '.$page_no.'/'.ceil($results_count/$per_page).'</th>';
	  echo '<th colspan="1" align="center"><a href="'.VA_ADMIN_URL;
	  if($_GET['va_page']){
	  			echo '&va_page='.$_GET['va_page'];
	  }
	  if($_GET['va_filter']){
	  			echo '&va_filter='.$_GET['va_filter'];
	  }
	  if($_GET['va_cookie']){
	  			echo '&va_cookie='.$_GET['va_cookie'];
	  }	
	  if($_GET['va_ip']){
	  			echo '&va_ip='.$_GET['va_ip'];
	  }	
	  if($_GET['va_id']){
	  			echo '&va_id='.$_GET['va_id'];
	  }
	  
	  echo '&page_no=';
	  if($page_no == ceil($results_count/$per_page)){
	  		echo $page_no;
	  }else{ 
	  		echo $page_no+1 ;
	  } 
	  echo '" >NextPage >></a></th>';
	  echo '<th colspan="2"></th></tr></tfoot>';
	}
	echo '</table></div>';

}
function make_stat_chart($results, $isJQuery){
	global $va_lang;
	$timezone = va_get_timezone();
	for($i=0;$i<=23;$i++){
		$hourStat[$i] = 0;
		$dayStat[$i] = 0;
	}
	$referrerStat[direct] = 0;
	$referrerStat[itself] = 0;
	$referrerStat[referred] = 0;
	$homeurl = get_bloginfo('wpurl');
	
	foreach($results as $r){
		$dateTime2 = new DateTime($r->time_visited);
		$dateTime2->setTimezone(new DateTimeZone($timezone));
		$dateTime1 = new DateTime(date('Y-m-d H:i:s'));
		$dateTime1->setTimezone(new DateTimeZone($timezone));
		$date1 =  strtotime($dateTime1->format("Y-m-d 0:0:0"));
		$date2 =  strtotime($dateTime2->format("Y-m-d H:i:s"));
		$diff = 22-floor(($date1 -$date2)/(60*60*24));
		if ($diff>=0 && $diff<=23){
			$dayStat[$diff] +=1;
		}
		$hourStat[$dateTime2->format("H")] += 1; 
		if(!isset($osStat[$r->os])){
			$osStat[$r->os] = 1;
		}else{
			$osStat[$r->os]++;
		}
		if(!isset($browserStat[$r->browser])){
			$browserStat[$r->browser] = 1;
		}else{
			$browserStat[$r->browser]++;
		}
		 
		if($r->referer == ""){
			$referrerStat[direct]++;
		}elseif(strpos($r->referer,$homeurl)!==false){
			$referrerStat[itself]++;
		}else{
			$referrerStat[referred]++;
		}
	}
	$hourLabel ="";
	$dayLabel = "";
	for($i=0;$i<24;$i++){
		$hourLabel .= $i;
		$dayLabel .= (23-$i);
		if ($i!=23){
			$hourLabel .= ',';
			$dayLabel .= ',';
		}
	}
	echo '<table width="100%" cellspacing="1"><tr>';
	for($i=0;$i<24;$i++){
		echo '<td valign="bottom" border="1"><table><tr><td>'.$hourStat[$i].'</td></tr><tr><td style="height:'. $hourStat[$i] . 'px; width:15px; background-color:#517CA1; line-height:1px; font-size:1px;"></td></tr><tfoot><th>'.$i.'</th></tfoot></table></td>';
	}
	for($i=0;$i<24;$i++){
		echo '<td valign="bottom" border="1"><table><tr><td>'.$dayStat[$i].'</td></tr><tr><td style="height:'. $dayStat[$i] . 'px; width:15px; background-color:#517CA1; line-height:1px; font-size:1px;"></td></tr><tfoot><th>'.$i.'</th></tfoot></table></td>';
	}
	echo '</tr></table>';

}

function getRealIp(){
  if (!empty($_SERVER['HTTP_CLIENT_IP'])){
      $ip=$_SERVER['HTTP_CLIENT_IP'];
  }else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
  }else{
      $ip=$_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}

function getOS($agent){
	$output='unknow';
	$oss = array('blackberry'=>'BlackBerry','wordpress'=>'WordPress','android'=>'Android','nokia'=>'Nokia', 'win'=> 'Windows',  'iphone'=>'iPhone', 'mac'=>'Mac','linux'=>'Linux');
	$agent = strtolower($agent);
	foreach ($oss as $key=>$os){
			if(strpos($agent,$key)>-1){
					$output	=	$os;
					break;
			}
	}
}

function getBrowser($agent){
	$browsers = array('ucweb'=>'UCWeb','wordpress'=>'WP','msie'=>'IE' ,'firefox'=>'Firefox','chrome'=> 'Chrome', 'opera'=>'Opera', 'apple'=>'Safari', 'safari'=>'Safari');
	$agent = strtolower($agent);
	foreach ($browsers as $key=>$browser)
			if(strpos($agent,$key)>-1)
				return $browser;
	return 'unknown';
}

function should_record($agent,$url,$referer){
	
	$should_record=true;
	if(is_bot($agent)){
			$should_record=false;
	}
	
	$url_destinations=("wp-admin/,wp-content/,wp-includes/,wiki/");
	$url_destination = explode(',',$url_destinations);
	
	foreach ($url_destination as $url_d){
			if (strpos($url,$url_d)>-1 || strpos($referer,$url_d)>-1){
				 	$should_record=false;
				 	break;
			}				
	}
	return $should_record;
}

if(!function_exists(is_bot)){
	function is_bot($agent) {
		
		$is_bot = false;		
		if ($agent ==''){
			$is_bot = true;
		}
		if (strlen($agent) < 50){
			$is_bot = true;
		}
		$bots = array('Google Bot' => 'google', 'MSN Bot' => 'msn', 'Baidu Bot' => 'baidu', 'YaHoo Bot' => 'yahoo', 'SoSo Bot' => 'soso', 'Sogou Bot' => 'sogou', 'Spider Bot' => 'spider', 'Bot' => 'bot', 'Search Bot' => 'search');
		foreach ($bots as $name => $lookfor) { 
			if (stristr($agent, $lookfor) !== false) { 
					$is_bot = true;
					break;
			}
		}
		return $is_bot;
	}
}

function is_ban() {
		
	$is_ban = false;		
	$ip = getRealIp();
	$va_ban_ips = get_option('va_ban_ips', TRUE);
	$ban_ips = $va_ban_ips[0];
	$ips = explode(',',$ban_ips);
	foreach ($ips as $ip_block){
			$ip_block =  trim($ip_block,"*");
			
			if(empty($ip_block)){
					break;
			}
			if (strpos($ip, $ip_block) === 0){
					$is_ban = true;
					break;
			}
	}
	return $is_ban;
}

function va_get_localtime($utcTime,$timeZone){
	$dateTime = new DateTime($utcTime);
	$dateTime->setTimezone(new DateTimeZone($timeZone));
	return $dateTime->format("m-d H:i:s");
}

function va_get_localtime_2($utcTime,$timeZone){
	$dateTime = new DateTime($utcTime);
	$dateTime->setTimezone(new DateTimeZone($timeZone));
	return $dateTime->format("Y-m-d");
}

function get_referer_domain($url){
	$pieces = explode("/", $url);
	$site = explode("/", get_bloginfo('home'));
	
	if (count($pieces)<3){
		return 'direct';
	}else if($pieces[2] == $site[2]){
		return 'inner';
	}else{
		return $pieces[2];
	}
}

function get_prev_date($days){
	return date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$days,date("Y")));
}

function log_comment($commentId){
	record_db($commentId);
}

function process_record(){	
	record_db(-1);
	
}

function record_db($commentId){
	
	global $wpdb;
	$access=1;
	$ip = getRealIp();
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$os = getOs($_SERVER['HTTP_USER_AGENT']);
	$referer = $_SERVER['HTTP_REFERER'];
	$browser = getBrowser($_SERVER['HTTP_USER_AGENT']);
	$url = $_SERVER['REQUEST_URI'];
	$cookie = $_COOKIE["Vistor_Analytics"];
	if ($cookie==''){
		$cookie = md5(time(). rand(0,99999999));
		setcookie("Vistor_Analytics", $cookie, time()+3600*24*3650,"/");
	}
	if(should_record($agent,$url,$referer)){
			$sql = "insert into ".WP_VA_VISITORS_TABLE." (access, agent,  time_visited , cookie, url, ip, browser, os, referer, comment_id) 
		values('$access','".$_SERVER['HTTP_USER_AGENT']."','".gmdate("Y-n-d H:i:s")."','$cookie','$url','$ip','$browser','$os','$referer','$commentId')";

			$wpdb->query($sql);  
  }
  
  $access=visitor_filter(); 
	if ($access=='n'){
		echo '<html>
					<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
					<body><div style="font-family: Garamond; font-size: 20px; "><p align="center" ><B>You Are Banned ! Pls Contact Suny Tse (message@ziming.org).</B></p></div></body>
					</html>';
		die();
	}elseif ($access=='g'){
		die();
	}
}

function visitor_filter(){

	if(is_ban()){
		return 'n';
	}
	$cookie = $_COOKIE["Vistor_Analytics"];
	if (!empty($cookie)){
		global $wpdb;
		global $va_lang;
		$sql = "select ".WP_VA_TABLE.".id, greet_text,is_greet,is_block from
			".WP_VA_TABLE." join ".WP_VA_FP_TABLE."
			on ".WP_VA_TABLE.".id = ".WP_VA_FP_TABLE.".stalker_id
			where cookie='$cookie'";
		$results = $wpdb->get_results($sql);
		if(count($results)>0){
			if ($results[0]->is_block=='y'){
				return 'n';
			}
			if ($results[0]->is_greet=='y'){
				if (!empty($_POST['va_response'])){					
					$sql ="update ".WP_VA_TABLE." set is_greet='n', response_text='".$_POST['va_response']."', response_time='".gmdate("Y-n-d H:i:s")."'
						where id='".$results[0]->id."'";
					$wpdb->query($sql);
					return 'g_s';
				}else{
					echo "
					<html>
					<head>
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
					<link rel='stylesheet' type='text/css' media='screen' href='".VA_BASEFOLDER."notice.css' />
					</head>
					<body>
					<form name='form1' method='post' action='".$_SERVER['REQUEST_URI']."'>
					<div id='notice'>
          <ul class='form'>
          	<li>
          		<label for='message'>".$results[0]->greet_text."</label>
          	</li>
            <li>
              <textarea id='va_response' name='va_response' rows='' cols=''></textarea>
            </li>
            <li class='submit'>
              <input id='send_contact' type='submit' value='' onfocus='this.blur();'/>
            </li>
          </ul>
   			 	</div>
					</form></body></html>";
					return 'g';
				}
			}
		}
	}
	return 'y';
}
function va_get_timezone(){
	$timezone = get_option('timezone_string');
	if (empty($timezone)){
		$x = floor(get_option('gmt_offset'));
		if ($x>0)
			$x="+$x";
		$timezone = "Etc/GMT$x";
	}
	return $timezone;
}

function va_get_analytics($results){
	  if(!count($results)) return 'no data';
	  $hour_stat[0]=0;
	  $day_stat[0]=0;
	  $week_stat[0]=0;
	  $month_stat[0]=0;
	  $year_stat[0]=0;
		foreach($results as $r){
				$timezone = va_get_timezone();
				$dateTime1 = new DateTime(date('Y-m-d H:i:s'));
				$dateTime2 = new DateTime($r->time_visited);
				$dateTime1->setTimezone(new DateTimeZone($timezone));
				$dateTime2->setTimezone(new DateTimeZone($timezone));
				
				$date1 =  strtotime($dateTime1->format("Y-m-d 0:0:0"));
				$date1_1 =  strtotime($dateTime1->format("Y-m-d H:i:s"));
				$date2 =  strtotime($dateTime2->format("Y-m-d H:i:s"));

				$diff_y = floor(($date1 - $date2)/(60*60*24*365));
				if($diff_y < 5){
					 $year_stat[$diff_y] +=1;
					 $diff_m = floor(($date1 - $date2)/(60*60*24*30));
						if($diff_m < 12){
							 $month_stat[$diff_m] +=1;
					 		 $diff_w = floor(($date1 - $date2)/(60*60*24*7));
							 if($diff_w < 4){
							 		$week_stat[$diff_w] +=1;
							 		$diff_d = floor(($date1 - $date2)/(60*60*24));
									if($diff_d < 30){
										$day_stat[$diff_d] +=1;
										$diff_h = floor(($date1_1 - $date2)/(60*60)); //latest hour , $hour_stat[0]
										if($diff_h < 24){
											 $hour_stat[$diff_h] +=1; //here i only record the latest 24 hours
										}
									}
							 }
						}
				 }
			}		
			$va_stat[0]=$hour_stat[0];
			$va_stat[1]=$day_stat[0];
			$va_stat[2]=$week_stat[0];
			$va_stat[3]=$month_stat[0];
			$va_stat[4]=$year_stat[0];
      return $va_stat;
}

function va_time_diff($time1, $time2){
	global $va_lang;
	$diff = floor($time1 - $time2);
	if ($diff<1)
		return $va_lang['timediff_now'];
	if ($diff<60)
		return $diff.$va_lang['timediff_seconds'];
	else
		$diff = floor($diff/60);
		if ($diff<60)
			return $diff.$va_lang['timediff_minutes'];
		else
			$diff = floor($diff/60);
			if ($diff<24)
				return $diff.$va_lang['timediff_hours'];
			else
				$diff = floor($diff/24);
				if ($diff<7)
					return $diff.$va_lang['timediff_days'];
				else
					$diff = floor($diff/7);
					return $diff.$va_lang['timediff_weeks'];
}

if(!function_exists('cut_str_2')) {
	function cut_str_2($text, $length = 0) {
		if (defined('MB_OVERLOAD_STRING')) {
		  $text = @html_entity_decode($text, ENT_QUOTES, get_option('blog_charset'));
		 	if (mb_strlen($text) > $length) {
				return htmlentities(mb_substr($text,0,$length), ENT_COMPAT, get_option('blog_charset')).'...';
		 	} else {
				return htmlentities($text, ENT_COMPAT, get_option('blog_charset'));
		 	}
		} else {
			$text = @html_entity_decode($text, ENT_QUOTES, get_option('blog_charset'));
		 	if (strlen($text) > $length) {
				return htmlentities(substr($text,0,$length), ENT_COMPAT, get_option('blog_charset')).'...';
		 	} else {
				return htmlentities($text, ENT_COMPAT, get_option('blog_charset'));
		 	}
		}
	}
}
?>