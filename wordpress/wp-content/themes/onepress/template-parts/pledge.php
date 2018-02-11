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
		$proj_fund = $wpdb->get_var("SELECT SUM(fund_given) FROM proj_pledges WHERE proj_ID='$proj_ID'");
		$proj_fund = $proj_fund + $pledge_amount;
		$wpdb->update('projects', array('proj_fund' => $proj_fund), array( 'proj_ID' => $proj_ID ));
		
		$current_user = wp_get_current_user();
		$pledger = $current_user->display_name;
		$pledger_ID = $current_user->ID;

		
		if(!empty($user_comment))
			$wpdb->insert('proj_comments', 
      			array(
      					'user' => $pledger,
      					'user_ID' => $pledger_ID,
      					'proj_ID' => $proj_ID,
      					'user_comment' => $user_comment
      				)
      			);
		
		$pledged = $wpdb->get_results("SELECT * FROM proj_pledges WHERE user_ID='$pledger_ID' AND proj_ID='$proj_ID'");
		if(empty($pledged)){
			$wpdb->insert('proj_pledges', 
      			array(
      					'user' => $pledger,
      					'user_ID' => $pledger_ID,
      					'proj_ID' => $proj_ID,
      					'proj_title' => $proj_title,
      					'fund_given' => $pledge_amount
      				));
		}
		else{
			if ($pledge_amount == 0)
				$wpdb->delete('proj_pledges', array('user_ID' => $pledger_ID, 'proj_ID' => $proj_ID));
			else
				$wpdb->update('proj_pledges', 
  					array('fund_given' => $pledge_amount),
  					array('user_ID' => $pledger_ID, 'proj_ID' => $proj_ID));
		}
			
      	header("Location: {$_SERVER['HTTP_REFERER']}");
	}
	die();
?>
