<?php
function va_option(){
	global $wpdb;
	global $va_lang;
	if (!empty($_POST)){
		if (!empty($_POST["va_perpage"])){
			$va_per_page[] = $_POST["va_per_page"];
			update_option('va_per_page', $va_per_page);
		}
		if (!empty($_POST["va_ban_ips"])){
			$va_ban_ips[] = $_POST["va_ban_ips"];	
			update_option('va_ban_ips', $va_ban_ips);		
		}
	}
	$va_per_page = get_option('va_per_page', TRUE);
	$va_ban_ips = get_option('va_ban_ips', TRUE);
	$per_page = $va_per_page[0];
	$ban_ips = $va_ban_ips[0];
	echo '<form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo "<p>Numbers to show  perpage :<input type='text' size='10' name='va_per_page' value='".$per_page."'/> ";
	echo "<input type='submit' value='Update'/>";
	echo "</form>"; 
	
	echo '<form name="form2" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo '<p>Ip Ban List: <input type="submit" value="Update" /></p>';
	echo '<textarea style="width:400px;height:60px;" row="5" name="va_ban_ips" >'.$ban_ips.'</textarea>';
	echo "</form>"; 
}
?>