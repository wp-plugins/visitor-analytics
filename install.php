<?php
function visitor_analytics_install(){
	global $wpdb;
	$sql = "CREATE TABLE `".WP_VA_TABLE."` (
  			`id` int(10) NOT NULL auto_increment,
  			`name` varchar(50) NOT NULL default '0',
  			`is_stat` char(10)  NOT NULL  default 'y',
  			`is_greet` char(10) NOT NULL  default 'n',
  			`is_block` CHAR(10) not NULL DEFAULT 'n',
  			`greet_text` text,
  			`greet_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  			`response_text` text,
  			`response_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  			PRIMARY KEY  (`id`)
		)";
	$wpdb->get_results($sql);
	
	$sql = "CREATE TABLE `".WP_VA_FP_TABLE."` (
  			`id` int(10) unsigned NOT NULL auto_increment,
  			`stalker_id` int(10) NOT NULL,
  			`flag` varchar(10) NOT NULL,
  			`cookie` varchar(32) NOT NULL,
  			`ip` varchar(15) NOT NULL,
  			PRIMARY KEY  (`id`),
  			KEY `stalker` (`stalker_id`),
  			KEY `cookie` (`cookie`)
		)";
	$wpdb->get_results($sql);
	
	$sql = "CREATE TABLE `".WP_VA_VISITORS_TABLE."` (
			`id` int(10) NOT NULL auto_increment,
  			`agent` text,
		  	`time_visited` timestamp NOT NULL default CURRENT_TIMESTAMP,
		  	`cookie` varchar(32) NOT NULL,
		  	`url` varchar(100) NOT NULL,
		  	`ip` varchar(15) NOT NULL,
		  	`browser` varchar(15) NOT NULL,
		  	`os` varchar(15) NOT NULL,
		  	`referer` varchar(100) NOT NULL,
		  	`comment_id` int(10) default NULL,
		  	`access` varchar(5) default 'y',
  		PRIMARY KEY  (`id`),
  		KEY `cookie` (`cookie`)
		)";
	$wpdb->get_results($sql);
	
	$va_per_page[]=30;
	$va_all_time[]=1;
	$va_all_visit[]=2;
	$va_search['fp']='';
	$va_search['agent']='';
	$va_search['ip']='';
	$va_search['days']='';
	$va_ban_ips[]='';
	
	if(!update_option('va_per_page', $va_per_page)){
    		add_option('va_per_page',$va_per_page);
  }
  if(!update_option('va_all_time', $va_all_time)){
    		add_option('va_all_time',$va_all_time);
  }
  if(!update_option('va_all_visit', $va_all_visit)){
    		add_option('va_all_visit',$va_all_visit);
  }
  if(!update_option('va_search',$va_search)){
    		add_option('va_search',$va_search);
  }
  if(!update_option('va_ban_ips',$va_ban_ips)){
    		add_option('va_ban_ips',$va_ban_ips);
  }

}

function visitor_analytics_menu() {
	
	add_dashboard_page( 'Visitor Analytics',  'Analytics', 'manage_options', 'visitor-analytics',  'visitor_analytics');
	
}
?>