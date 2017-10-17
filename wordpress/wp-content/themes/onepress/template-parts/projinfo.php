<?php
	/*Template Name: Project Info Template*/
	/**
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 * @package OnePress
	 */
	if (isset($_GET['view']))		
		$proj_title = htmlspecialchars($_GET['view']);
	else $proj_title = NULL;
?>

<?php
	#DATABASE INTEGRATION
	global $wpdb;
	$result = $wpdb->get_row("SELECT * FROM projects WHERE proj_title='$proj_title'", ARRAY_A);
	//var_dump($result);		#for debugging
		if(!isset($result)){
			header('Location: http://localhost/wordpress');die();
		} else {
			$proj_user = $result['proj_user'];
			$proj_goal = $result['proj_goal'];
			$proj_fund = $result['proj_fund'];
			$proj_image = $result['proj_image'];
			$proj_info = $result['proj_info'];
		}
	$proj_info = str_replace("\n", "<br><br>", $proj_info);			//TEXT PARAGRAPH LAYOUT

	#Header Setup
	get_header();



	$layout = onepress_get_layout();
	echo onepress_breadcrumb();
?>

<link rel="stylesheet" type="text/css" href="../wp-content/themes/onepress/assets/css/projstyles.css?ver=<?php echo rand(111,999)?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<div id="goal">		
		<div id="contentgoal">
			<?php 	if(IsSet($proj_title))	echo "<p><h2 style='color:#7b1113;'>$proj_title</h2></p>";
					else 					echo "<h2 style='color:#7b1113;'>Project name not found</h2>";
					if(IsSet($proj_user))	echo "<p><h4>by $proj_user</h4></p>";
					else 					echo "<p><h4>User not found</h4></p>";
					if(IsSet($proj_image)){
						$imgloc = "/wordpress/wp-content/uploads/users/".$proj_image;
						echo '<img src = "'. $imgloc.'" alt="'.$proj_image.'" id=\"contentimg\" >';
					}
					else 					echo '<img src ="#" alt="No image available for this project." id=\"contentimg\" >';
					if(IsSet($proj_info))	echo "<p>$proj_info</p>";
					else 					echo "<p>Project information not found</p>";
			?>
		</div>

		<div id="sidebarprojinfo">
			<div id="asidegoal" style="font-size: 15px; letter-spacing:1px;">
				<p style="color:#7b1113;"><b>Goal PHP</b></p>
				<?php 
					if(IsSet($proj_goal))	{echo "<span>P</span>";
											echo "<span style='float:right; letter-spacing:2px;  overflow-wrap:break-word;'>".number_format($proj_goal)."</span>";}
					else 					echo "<p>Goal amount not set</p>";
				?>		
				<br><p style="color:#7b1113;"><b>Raised PHP</b></p>
				<?php 
					if(IsSet($proj_fund))	{echo "<span>P</span>";
											echo "<span style='float:right; letter-spacing:2px;  overflow-wrap:break-word;'>".number_format($proj_fund)."</span>";}
					else 					echo "<p>Amount raised not defined</p>";
				?>
			</div><br><br>
			<div id="asidedonor"  style="text-transform:none;">
				<div id="donatewidget">
				<hr><h2 class="widget-title" style="font-weight: 700; text-align:center;color:#7b1113;font-size: 18px;">WANT TO DONATE?</h2><hr>
				<?php
				if (!is_user_logged_in() ){
					echo "<p style='text-transform:none; text-align:center; color:black;'>
						You need to be a registered user to donate. Click here to 
						<a href='http://localhost/wordpress/signup/'><strong>register</strong></a> or 
						<a href='http://localhost/wordpress/login/''><strong>sign in</strong></a>.
						</p>";
				} else {
					$current_user = wp_get_current_user();
					$query = "SELECT SUM(fund_given) FROM user_actions WHERE proj_title='$proj_title' AND user='$current_user->user_login'";
					$user_donate = $wpdb->get_var($query);
					
					if(!IsSet($user_donate)) 	$user_donate = 0;
					echo "<p style='text-align:center; font-size: 12px; text-transform:none;'>You currently have pledged P$user_donate in the project!</p>";
					if($user_donate > 0)	echo "<p style='text-align:center; font-size:13px; text-transform:none;'><strong>WANT TO DONATE AGAIN?</strong></p>";	
					echo "<form action='pledge-processing' method='post'>
								<input type='hidden' name='proj_title' value='$proj_title'>
						        <label for='pledge'><strong>PLEDGE AMOUNT:</strong></label>
						        <input type='number' id='pledge' name='pledge_amount' min='1' style='width:100%;' required>
						        <br><label for='comment'><strong>ANY COMMENTS? (Optional)</strong></label>
        						<textarea id='comment' name='user_comment' style='width:100%;'></textarea>	
						    <div style='font-size:10px;'>
						    	<input type='checkbox' name='Anonymous' value='Yes'/>Check this box if you want to be Anonymous :)
							</div>
							<br>
						    <div style='text-align:center;'>
  								<button class='btn btn-secondary-outline btn-lg' type='submit' style='background-color:#7b1113; color:white;'>Donate!</button>
							</div></form>";
					}?><br><hr></div>
				<br><h5 style="font-size: 18px; color:#7b1113;">PLEDGERS' LIST</h5>
				<ul><?php
						$result = $wpdb->get_results("SELECT * FROM user_actions WHERE proj_title='$proj_title'", ARRAY_A);
						if(IsSet($result))
							foreach ($result as $list) {
								if($list['anon'] == 1)	$pledger = "Anonymous";
								else 					$pledger = $list['user'];
								$pledge_amount = $list['fund_given'];  #OPTIONAL PLEDGE AMOUNT DISPLAY  
								echo '<li>'.$pledger.' - P'.number_format($pledge_amount).'</li> ';
							}					
						else echo "<p>Be the first to pledge!</p>";
				?></ul>
			</div>
		</div>
		
		<div id="comments">
			<hr>	
			<p style="font-weight: 600; font-size: 18px; color:#7b1113;">PLEDGER'S COMMENTS</p>
			<?php
				$result = $wpdb->get_results("SELECT * FROM user_actions WHERE proj_title='$proj_title'", ARRAY_A);
				if(IsSet($result))
					foreach ($result as $list) {
						$user_comment = $list['user_comment'];
						$action_date = $list['action_date'];
						if($user_comment != ''){
							echo '<div id="pledgecomment">';
							echo '<hr>';
								if($list['anon'] == 1)	$pledger = "Anonymous";
								else 					$pledger = $list['user'];	
								echo '<div id="pledgename">';
									echo "$pledger";	
								echo '</div>';
								echo '<div id="pledgedate">';
									date_default_timezone_set('Asia/Manila');
									$hey = new DateTime($action_date);
									$localtime = new DateTime();
									$et = $localtime->diff(new DateTime($action_date));
									if($et->y > 0)			{$p=$et->y; echo $p.' year';}
									else if($et->m > 0)		{$p=$et->m; echo $p.' month';}
									else if($et->d > 0)		{$p=$et->d; echo $p.' day';}
									else if($et->h > 0)		{$p=$et->h; echo $p.' hour';}
									else if($et->i > 0)		{$p=$et->i; echo $p.' minute';}
									else 					{$p=$et->s; echo $p.' second';}
									if($p>1)				echo 's';	echo ' ago<br>';							
								echo '</div>';
								echo '<div id="pledgecomdet">';
									echo $user_comment;
								echo '</div>';
							echo '</div>';
						}
					}					
				else echo "<p>No comments yet!</p>";

				echo '<hr>';
			?>
		</div>	
	</div>
<br>
<footer style="clear:both;display: block">
	<?php get_footer();?>
</footer>