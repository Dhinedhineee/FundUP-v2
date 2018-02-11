<?php
	/*Template Name: Pledge Form Template*/
	/**
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 *
	 * @package OnePress
	 */

	global $wpdb;
	if(!IsSet($_SERVER['HTTP_REFERER'])){
		$hostlink = 'http://'.$_SERVER['HTTP_HOST'];
		if($hostlink == 'http://localhost')	$hostlink .= '/wordpress';
		header('Location: '.$hostlink);
	}else {
		$proj_title = htmlspecialchars($_POST['proj_title']);
		$proj_ID = htmlspecialchars($_POST['proj_ID']);
		$pledge_amount = htmlspecialchars($_POST['pledge_amount']);
		$user_comment = htmlspecialchars($_POST['user_comment']);
		global $wpdb;
		#$result = $wpdb->get_row("SELECT * FROM projects WHERE proj_title='$proj_title'", ARRAY_A);
		$result = $wpdb->get_row("SELECT * FROM projects WHERE proj_id='$proj_ID'", ARRAY_A);
		#$proj_fund = $wpdb->get_var("SELECT SUM(fund_given) FROM user_actions WHERE proj_title='$proj_title'");
		$proj_fund = $wpdb->get_var("SELECT SUM(fund_given) FROM user_actions WHERE proj_ID='$proj_ID'");
		$proj_fund = $proj_fund + $pledge_amount;
		$wpdb->update('projects', array('proj_fund' => $proj_fund), array( 'proj_title' => $proj_title ));
		
		$current_user = wp_get_current_user();
		$pledger = $current_user->display_name;
		$pledger_ID = $current_user->ID;
      	$wpdb->insert('user_actions', 
      			array(
      					'user' => $pledger,
      					'user_ID' => $pledger_ID,
      					'proj_title' => $proj_title,
      					'proj_ID' => $proj_ID,
      					'fund_given' => $pledge_amount,
      					'user_comment' => $user_comment
      				)
      			);
      	header("Location: {$_SERVER['HTTP_REFERER']}");
	}
	die();
?>
