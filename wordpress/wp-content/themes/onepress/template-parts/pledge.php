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
		$tier_count = $wpdb->get_var("SELECT COUNT(*) FROM proj_tiers WHERE proj_id = $proj_ID ORDER BY proj_tier_amount");
		
		for ($i = 0; $i < $tier_count; $i++)	$proj_tier[$i] = 0;

		if (isset($_POST['proj-tier']) && sizeof($_POST['proj-tier']) != 0)	foreach ($_POST['proj-tier'] as $i)		if($i != 0)	$proj_tier[$i-1] = 1;
		
		$proj_tier = json_encode($proj_tier);

		global $wpdb;
		$result = $wpdb->get_row("SELECT * FROM projects WHERE proj_id='$proj_ID'", ARRAY_A)
		$proj_fund = $wpdb->get_var("SELECT SUM(fund_given) FROM user_actions WHERE proj_ID='$proj_ID'");
		$proj_fund = $proj_fund + $pledge_amount;
		$wpdb->update('projects', array('proj_fund' => $proj_fund), array( 'proj_ID' => $proj_ID ));
		
		$current_user = wp_get_current_user();
		$pledger = $current_user->display_name;
		$pledger_ID = $current_user->ID;

				
		$pledged = $wpdb->get_row("SELECT * FROM user_actions WHERE user_ID='$pledger_ID' AND proj_ID='$proj_ID'", ARRAY_A);
		
		date_default_timezone_set('Asia/Manila');
		$action_date = date('Y/m/d H:i:s', time());
		if(empty($pledged)){
			$wpdb->insert('user_actions', 
      			array(
      					'user' => $pledger,
      					'user_ID' => $pledger_ID,
      					'proj_title' => $proj_title,
      					'proj_ID' => $proj_ID,
      					'fund_given' => $pledge_amount,
      					'proj_tier' => $proj_tier,
      					'user_comment' => $user_comment,
      					'action_date' => $action_date
      				)
      			);
		}
		else{
			$action_date = $pledged['action_date'];
			if($user_comment == '')		$user_comment = $pledged['user_comment'];
			else if ($pledged['user_comment'] != $user_comment){
				date_default_timezone_set('Asia/Manila');
				$action_date = date('Y/m/d H:i:s', time());
			}
			if ($pledge_amount == 0)
				$wpdb->delete('user_actions', array('user_ID' => $pledger_ID, 'proj_ID' => $proj_ID));
			else
				$wpdb->update('user_actions', 
  					array('fund_given' => $pledge_amount, 'user_comment' => $user_comment, 'action_date' => $action_date, 'proj_tier' => $proj_tier,),
  					array('user_ID' => $pledger_ID, 'proj_ID' => $proj_ID));
		}
			
      	header("Location: {$_SERVER['HTTP_REFERER']}");
	}
	die();
?>


