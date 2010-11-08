<?php
/*
Plugin Name: Visitor Analytics
Plugin URI: http://ziming.org/dev/visitor-analytics
Description: Visitor Depth Analyse and Interaction.
Version: 1.1.5
Author: Suny Tse
Author URI: http://ziming.org
 */
/*  Copyright 2010  Suny Tse  (email : message@ziming.org)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require dirname(__FILE__) . "/install.php";
require dirname(__FILE__) . "/function.php";

register_activation_hook(__FILE__,'visitor_analytics_install');
add_action('init', 'visitor_analytics_textdomain');
add_action('init', 'process_record');
add_action('admin_menu', 'visitor_analytics_menu');
init_lang();
add_action('comment_post', 'log_comment',10,1);

global $table_prefix;
$table_prefix = ( isset( $table_prefix ) ) ? $table_prefix : $wpdb->prefix;
define('WP_VA_TABLE', $table_prefix . 'visitor_analytics');
define('WP_VA_FP_TABLE',  $table_prefix . 'visitor_analytics_fingerprint');
define('WP_VA_VISITORS_TABLE',  $table_prefix . 'visitor_analytics_visitors');
define('VA_BASEFOLDER', get_bloginfo('wpurl').'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/');
define('VA_ADMIN_URL', 'index.php?page=visitor-analytics');

### Create Text Domain For Translations
function visitor_analytics_textdomain() {
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain( 'visitor-analytics','wp-content/plugins/'.$plugin_dir, $plugin_dir);
}
### Init Usage Language
function init_lang(){
	switch (WPLANG){
		case 'zh_CN':
			require 'lang/visitor_analytics_i18n_cn.php';
			break;
		default:
			require 'lang/visitor_analytics_i18n_en.php';
			break;
	}
}

function visitor_analytics(){
	
	$va_menu = '<div><a href="'.VA_ADMIN_URL.'">Recent Visitor</a> > >
	                 <a href="'.VA_ADMIN_URL.'&va_page=cookie_analytics ">Cookie Analytics</a> > >
	                 <a href="'.VA_ADMIN_URL.'&va_page=ip_analytics ">IP Analytics</a> > >
	                 <a href="'.VA_ADMIN_URL.'&va_page=visitor_monitor ">Visitor Analytics</a> > >
	                 <a href="'.VA_ADMIN_URL.'&va_page=visitor_comments ">Visitor Comments</a> > >
	                 <a href="'.VA_ADMIN_URL.'&va_page=visitor_search ">Search & Delete</a> > >
	                   <a href="'.VA_ADMIN_URL.'&va_page=data_analytics ">Data Analytics</a> > >
	                 <a href="'.VA_ADMIN_URL.'&va_page=va_option ">Option</a> > ></div>';

	if(isset($_GET['page_no'])){
							$page_no = $_GET['page_no'];
					}else{
							$page_no = 1;
					}
	
	if(isset($_GET['va_page'])){
		if($_GET['va_page'] == 'visitor_monitor'){
				include 'visitor_monitor.php';
				echo $va_menu;
				visitor_monitor($page_no);
		}else if($_GET['va_page'] == 'cookie_analytics'){
				include 'cookie_analytics.php';
				echo $va_menu;
				cookie_analytics($page_no);
		}else if($_GET['va_page'] == 'ip_analytics'){
				include 'ip_analytics.php';
				echo $va_menu;
				ip_analytics($page_no);
		}else if($_GET['va_page'] == 'visitor_comments'){
				include 'visitor_comments.php';
				echo $va_menu;
				visitor_comments();
		}else if($_GET['va_page'] == 'visitor_tracker'){
				include 'visitor_tracker.php';
				echo $va_menu;
				visitor_tracker($_GET['va_id'],$page_no);
		}else if($_GET['va_page'] == 'visitor_search'){
				include 'visitor_search.php';
				echo $va_menu;
				visitor_search($page_no);
		}else if($_GET['va_page'] == 'va_option'){
				include 'va_option.php';
				echo $va_menu;
				va_option();
		}else if($_GET['va_page'] == 'data_analytics'){
				include 'data_analytics.php';
				echo $va_menu;
				data_analytics();
		}
	}else{
		if(isset($_GET['va_filter'])){
			if($_GET['va_filter'] == 'fingerprint'){
					include 'filter_fingerprint.php';
					echo $va_menu;
					filter_fingerprint($_GET['va_cookie'],$page_no);
			}else if($_GET['va_filter'] == 'ip'){
					include 'filter_ip.php';
					echo $va_menu;
					filter_ip($_GET['va_ip'],$page_no);
			}
		}else{
			include 'visitors_list.php';
			echo $va_menu;
			visitor_list($page_no);
		}
	}
}

?>