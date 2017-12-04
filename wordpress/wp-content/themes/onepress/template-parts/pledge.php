<?php
	/*Template Name: Pledge Form Template*/
	/**
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 *
	 * @package OnePress
	 */
?>

<?php 
	global $wpdb;
	if(!IsSet($_SERVER['HTTP_REFERER'])){
		echo "HOWDY, PLEDGER :D NOW YOU SEE ME, NOW YOU DON'T ;D";
		echo "<br><br><br><br>WOAHHHHHHHHHHH WHY ARE YOU ACCESSING THISZZZZZZZ!!?!?!";
		echo "<br><br>죽을래요?";
		echo "<br><br><br><br>Bye~~~~";
		header('Location: http://localhost/wordpress');
	}else {
		$proj_title = htmlspecialchars($_POST['proj_title']);
		$pledge_amount = htmlspecialchars($_POST['pledge_amount']);
		$user_comment = htmlspecialchars($_POST['user_comment']);
		global $wpdb;
		$result = $wpdb->get_row("SELECT * FROM projects WHERE proj_title='$proj_title'", ARRAY_A);
		$proj_fund = $wpdb->get_var("SELECT SUM(fund_given) FROM user_actions WHERE proj_title='$proj_title'");
		$proj_fund = $proj_fund + $pledge_amount;
		$wpdb->update('projects', array('proj_fund' => $proj_fund), array( 'proj_title' => $proj_title ));
		
		$current_user = wp_get_current_user();
		$pledger = $current_user->display_name;
		$pledger_ID = $current_user->ID;
		if(isset($_POST['Anonymous']) && $_POST['Anonymous'] == 'Yes') $anon = 1;
		else 	$anon = NULL;
      	$wpdb->insert('user_actions', 
      			array(
      					'user' => $pledger,
      					'user_ID' => $pledger_ID,
      					'anon' => $anon,
      					'proj_title' => $proj_title,
      					'fund_given' => $pledge_amount,
      					'user_comment' => $user_comment
      				)
      			);
      	header("Location: {$_SERVER['HTTP_REFERER']}");
	}
	die();
?>
