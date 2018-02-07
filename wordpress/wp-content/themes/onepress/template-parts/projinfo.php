<?php
	/*Template Name: Project Info Template*/
	/**
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 * @package OnePress
	 */

	$hostlink = 'http://'.$_SERVER['HTTP_HOST'];
	if($hostlink == 'http://localhost')	$hostlink .= '/wordpress';

	if (isset($_GET['view']))		
		$proj_id = htmlspecialchars($_GET['view']);

	if(!is_numeric($proj_id)){
		header('Location: '.$hostlink);die();
	}
        
	#DATABASE INTEGRATION
	global $wpdb;
	$query = "SELECT * FROM projects WHERE proj_id='$proj_id'";
	$result = $wpdb->get_row($query, ARRAY_A);
	//var_dump($result);		#for debugging
		if(!isset($result)){
			header('Location: '.$hostlink);die();
		} else {
			$proj_title = $result['proj_title'];
			$proj_user = $result['proj_user'];
			$proj_user_ID = $result['proj_user_ID'];
			$proj_goal = $result['proj_goal'];
			$proj_deadline = $result['proj_deadline'];
			$proj_fund = $wpdb->get_var("SELECT SUM(fund_given) FROM user_actions WHERE proj_title='$proj_title'");
			$proj_image = $result['proj_image'];
			$proj_info = $result['proj_info'];
			$user_ID = $result['proj_user_ID'];
			$proj_finished = 1;
		}
	$proj_info = str_replace("\n", "<br>", $proj_info);			//TEXT PARAGRAPH LAYOUT

	#HEADER SETUP
	get_header();
	$layout = onepress_get_layout();
	echo onepress_breadcrumb();
?>

<link rel="stylesheet" type="text/css" href="../wp-content/themes/onepress/assets/css/projstyles.css?ver=<?php echo rand(111,999)?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<div id="goal">		
		<div id="contentgoal">
			<?
				if(IsSet($proj_title))	echo "<p><h2>$proj_title</h2></p>";
				else 					echo "<h2>Project name not found</h2>";
				if(IsSet($proj_user))	echo "<p><h4>by <a href='".$hostlink."/user-profile/?view=$user_ID' style='color:#7b1113;' >$proj_user</a></h4></p>";
				else 					echo "<p><h4>User not found</h4></p>";
				if(IsSet($proj_image)){
					$imgloc = $hostlink."/wp-content/uploads/users/".$proj_user_ID."/".$proj_image;
					echo '<img src = "'. $imgloc.'" alt="'.$proj_image.'" id=\"contentimg\" >';
				}
				else 					echo '<img src ="#" alt="No image available for this project." id=\"contentimg\" >';
				if(IsSet($proj_info))	echo "<p>".stripcslashes($proj_info)."</p>";
				else 					echo "<p>Project information not found</p>";

				$query = "SELECT * FROM proj_tiers WHERE proj_id = $proj_id ORDER BY proj_tier_amount";
				$results = $wpdb->get_results($query);
				if(IsSet($results) && sizeof($results)){
					$backernum = backersnum($results, $wpdb, $proj_id);
					echo '<hr><p style="color: #7b1113; font-weight:600; font-size: 18px;">PROJECT TIERS</p>';
					echo "<div id='projtiers'><table>";
					for ($i = 0; $i <= sizeof($results)-1; $i++){
						echo "<td>";
						echo "<p style='color:#7b1113; font-size: 20px;'>".'P '.number_format($results[$i]->proj_tier_amount)."</p>";
						echo stripcslashes($results[$i]->proj_tier_desc);
						echo "<br><br><p style='color:#7b1113;'>Backers: ";
						echo (isSet($backernum[$i])) ? $backernum[$i]:0;
						echo "</p></td>";	
					}		
					echo "</table></div>";
				}
			?>
		</div>

		<div id="sidebarprojinfo">
			<div id="asidegoal">
				<p style="color:#7b1113;"><b>Goal PHP</b></p>
				<?
					if(IsSet($proj_goal))	{echo "<span>P</span>";
											echo "<span style='float:right; letter-spacing:2px;  overflow-wrap:break-word;'>".number_format($proj_goal)."</span>";}
					else 					echo "<p>Goal amount not set</p>";
				?>		
				<br><p style="color:#7b1113;"><b>Raised PHP</b></p>
				<?
					echo "<span>P</span>";
					echo "<span style='float:right; letter-spacing:2px;  overflow-wrap:break-word;'>".number_format($proj_fund)."</span>";
				?>
				<?
					if(isSet($proj_deadline)){
						date_default_timezone_set('Asia/Manila');
						$hey = new DateTime($proj_deadline);
						$localtime = new DateTime();
						$localtime->add(DateInterval::createFromDateString('yesterday'));
						$et = $hey->diff($localtime);
						$deadline = $et->format('%R%a')*-1;
						if($et->invert == 1){
							echo "<br><br><hr><p style='text-align: center; color:#7b1113;'><b>This project will end ";
							if($deadline == 0)	echo "today";
							else{
								echo "in ".($deadline)." day";
								if ($deadline > 1) echo "s";
							}
							$proj_finished = 0;
						}
						else {
							echo "<br><br><hr><p style='text-align: center; color:#7b1113;'><b>This project has ended ";
							if ($deadline == 0)			echo "1 day ago";
							else 						echo (($deadline-1)*-1). " days ago"; 							
						}
					}else {
						echo "<br><br><hr><p style='text-align: center; color:#7b1113;'><b>Project deadline is not set</b>";
					}
					echo ".</b></p>";
				?>
			</div>
			
			<br>
			<div id="asidedonor">
				<div id="donatewidget">
				<?
				global $user_tier, $user_pledge;
                                $current_user = wp_get_current_user();
                                $query = "SELECT SUM(fund_given) FROM user_actions WHERE proj_id='$proj_id' AND user_ID='$current_user->ID'";
				$user_pledge = $wpdb->get_var($query);
                                
				if (!$proj_finished) echo '<br><hr><h2 class="widget-title">WANT TO DONATE?</h2><hr>';
				if ($proj_finished){
					if (is_user_logged_in()){		
						if(IsSet($user_pledge)){
							echo "<br><hr><p style='text-align:center; font-size: 12px; text-transform:none;'>You had pledged P".number_format($user_pledge);
							if(isSet($user_tier) && $user_tier) echo " and had backed tier level $user_tier";
							echo " in the project! Thank you for your support!</p>";
						}
					}
				}
				else if (!is_user_logged_in()){
					echo "<p style='text-transform:none; text-align:center; color:black;'>
						You need to be a registered user to donate. Click here to 
						<a href='".$hostlink."/signup/'><strong>register</strong></a> or 
						<a href='".$hostlink."/login/'><strong>sign in</strong></a>.
						</p>";
				} else {
					echo "<p style='text-align:center; font-size: 12px; text-transform:none;'>You currently have pledged P".number_format($user_pledge);
					if($user_tier) echo " and have backed tier level $user_tier";
					echo " in the project!</p>";
					if($user_pledge)	echo "<p style='text-align:center; font-size:13px; text-transform:none;'><strong>WANT TO DONATE AGAIN?</strong></p>";	
					echo "<form action='pledge-processing' method='post'>
								<input type='hidden' name='proj_ID' value='$proj_id'>
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
  								<button class='btn btn-secondary-outline btn-lg' type='submit' id='dbutton' style='background-color:#7b1113; color:white;'>Donate!</button>
							</div></form>
							<p id='ptcontainer'></p>";
					}?>
					<script>
						document.getElementById("dbutton").addEventListener("click", thankyou);
						function thankyou(){
                                                        if(document.getElementById("pledge").value != '')
                                                        document.getElementById("ptcontainer").innerHTML = "<p id='pledgethanks'>THANK YOU FOR YOUR DONATION!</p>";
                                                }
					</script>
					<br><hr></div>
				<br><h5>PLEDGERS' LIST</h5>
				<ul><?
						$pledgecount = 0;
						$result = $wpdb->get_results("SELECT * FROM user_actions WHERE proj_ID='$proj_id';", ARRAY_A);
						if(IsSet($result))
							foreach ($result as $list) {
								$pledgecount++;
								if($list['anon'] == 1)	$pledger = "Anonymous";
								else 					$pledger = $list['user'];
								$pledge_amount = $list['fund_given']; 
								echo '<li>'.$pledger.' - P'.number_format($pledge_amount).'</li> ';
							}					
						if(!$pledgecount) echo "<p>No pledgers yet. Be the first to pledge!</p>";
				?></ul>
			</div>
		</div>
		
		<div id="projcomments">
			<hr><p style="color: #7b1113;">PLEDGERS' COMMENTS</p>
			<?
				$commentcount = 0;
				if(IsSet($result)){
					foreach ($result as $list) {
						$user_comment = stripcslashes($list['user_comment']);
						$action_date = $list['action_date'];
						if($user_comment != ''){
							$commentcount++;
							echo '<div id="pledgecomment">';	echo '<hr>';
								echo '<div id="pledgename" style="color: #7b1113;">';
									if($list['anon'] == 1)	echo "Anonymous";
									else{
										$pledger = $list['user'];	
										$user_ID = $list['user_ID'];	
										echo "<a href='".$hostlink."/user-profile/?view=$user_ID'>$pledger</a>";	
									}
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
				}	
				if(!$commentcount){
					echo "<p style='padding-left:20px;'>";
					if ($proj_finished) 	echo "This project has no comments.</p>";
					else   					echo "No comments yet. Please pledge first to be able to leave comments!</p>";
				}	
				echo '<hr>';
			?>
		</div>	
	</div>
<br>

<?
	function backersnum($results, $wpdb, $proj_id){
		global $user_tier, $user_pledge;
		$user_tier = 0;
		$user_pledge = 0;
		$query2 = "SELECT * FROM user_actions WHERE proj_id = $proj_id ORDER BY user_ID";
		$results2 = $wpdb->get_results($query2);
		if(sizeof($results2)){
			$ctr = 0;
			foreach($results2 as $pledger){
				$check = 0;
				if($ctr){
					$ind = array_search($pledger->user_ID, array_column($backer, 'user_ID'));
					if((!$ind && $backer[$ind]['user_ID'] == $pledger->user_ID) || $ind){
						$backer[$ind]['pledge'] += $pledger->fund_given;
							$check = 1;
					}
				}
				if(!$check)
				{
					$backer[$ctr]['user_ID'] = $pledger->user_ID;
					$backer[$ctr]['pledge'] = $pledger->fund_given;
					$ctr++;
				}	
			}
			if (is_user_logged_in()){
				$current_user = wp_get_current_user();
				$query = "SELECT SUM(fund_given) FROM user_actions WHERE proj_ID='$proj_id' AND user_ID='$current_user->ID'";
				$user_pledge = $wpdb->get_var($query);
				if(!IsSet($user_pledge)) 	$user_pledge = 0;
			}
			for ($i = 0; $i <= sizeof($results)-1; $i++){
				$curr = $results[$i]->proj_tier_amount;
				if($i != sizeof($results)-1)	$next = $results[$i+1]->proj_tier_amount;
				else 							$next = 10000000000;
				$backernum[$i] = sizeof(array_filter(array_column($backer, 'pledge'), function ($var) use ($curr, $next) {return ( $var >= $curr && $var < $next);}));
				if(is_user_logged_in() && tierlevel($user_pledge, $curr, $next))		$user_tier = $i+1;
			}	
		}else return null;
		return $backernum;
	}

	function tierlevel($var, $curr, $next){
		return ($var >= $curr && $var < $next);
	}
?>
<footer style="clear:both;display: block">
	<? get_footer();?>
</footer>