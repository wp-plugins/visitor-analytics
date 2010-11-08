<?php
function va_option(){
	global $wpdb;
	global $va_lang;
	if (!empty($_POST)){
			$va_options['per_page'] = $_POST["va_per_page"];
			$va_options['ip_addr'] = $_POST["va_ip_addr"];
			$va_options['ban_ips'] = $_POST["va_ban_ips"];
			$va_options['ban_message'] = $_POST["va_ban_message"];	
							
			update_option('va_options', $va_options);
	}
	$va_options = get_option('va_options', TRUE);

	echo '<form name="form1" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	echo "<p>Numbers to show  perpage :<input type='text' size='10' name='va_per_page' value='".$va_options['per_page']."'/> </p>";	
	echo '<p>Check IP Address :</p>';
	echo "<input type='text' size='100' name='va_ip_addr' value='".$va_options['ip_addr']."'/> </p>";	
	echo '<p>Ip Ban List: </p>';
	echo '<textarea style="width:400px;height:60px;" row="5" name="va_ban_ips" >'.$va_options['ban_ips'].'</textarea>';
	echo '<p>Ban Message: </p>';
	echo '<textarea style="width:400px;height:60px;" row="5" name="va_ban_message" >'.$va_options['ban_message'].'</textarea>';
	echo '<p><input type="submit" value="Update" /></p>';
	echo "</form>"; 
}
?>