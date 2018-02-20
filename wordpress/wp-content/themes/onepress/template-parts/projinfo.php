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
			$proj_goal = $result['proj_goal'];
			$proj_deadline = $result['proj_deadline'];
			$proj_fund = $wpdb->get_var("SELECT SUM(fund_given) FROM user_actions WHERE proj_ID='$proj_id'");
			$proj_image = $result['proj_image'];
			$proj_info = $result['proj_info'];
			$proj_user_ID = $result['proj_user_ID'];
			$proj_finished = 1;
		}
	$proj_info = str_replace("\n", "<br>", $proj_info);			//TEXT PARAGRAPH LAYOUT

	#SUSPENDED ACCOUNT
	$result = $wpdb->get_var("SELECT suspended FROM wp_users WHERE ID='$proj_user_ID'");
	if($result){
		header('Location: '.$hostlink);die();
	}

	#HEADER SETUP
	get_header();
	$layout = onepress_get_layout();
	echo onepress_breadcrumb();
?>

<link rel="stylesheet" type="text/css" href="../wp-content/themes/onepress/assets/css/projstyles.css?ver=<?php echo rand(111,999)?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<div id="goal">		
		<div id="contentgoal">
			<?php
				if(IsSet($proj_title))	echo "<p><h2>$proj_title</h2></p>";
				else 					echo "<h2>Project name not found</h2>";
				if(IsSet($proj_user))	echo "<p><h4>by <a href='".$hostlink."/user-profile/?view=$proj_user_ID' style='color:#7b1113;' >$proj_user</a></h4></p>";
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
						if($backernum[$i] == null) 		$numbacker = 0;
						else 							$numbacker = $backernum[$i];
						echo "<br><br><p style='color:#7b1113;'>Backers: ".$numbacker."<br>";
						//echo (isSet($backernum[$i])) ? 
						if ($results[$i]->proj_tier_slot != null) echo "Slots remaining: ".($results[$i]->proj_tier_slot-$numbacker)."</p>";
						else echo "Unlimited slots available.</p>";
						echo "</td>";	
					}		
					echo "</table></div>";
				}
			?>
		</div>

		<div id="sidebarprojinfo">
			<div id="asidegoal">
				<p style="color:#7b1113;"><b>Goal PHP</b></p>
				<?php
					if(IsSet($proj_goal))	{echo "<span>P</span>";
											echo "<span style='float:right; letter-spacing:2px;  overflow-wrap:break-word;'>".number_format($proj_goal)."</span>";}
					else 					echo "<p>Goal amount not set</p>";
				?>		
				<br><p style="color:#7b1113;"><b>Raised PHP</b></p>
				<?php
					echo "<span>P</span>";
					echo "<span style='float:right; letter-spacing:2px;  overflow-wrap:break-word;'>".number_format($proj_fund)."</span>";
				?>
				<?php
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
			<?php
                $current_user = wp_get_current_user();
                if (!$proj_finished and $current_user->ID != $proj_user_ID) echo '<br><hr><h2 class="widget-title">WANT TO DONATE?</h2><hr>';
                if (!is_user_logged_in() and !$proj_finished){
					echo "<p style='text-transform:none; text-align:center; color:black;'>
						You need to be a registered user to donate. Click here to 
						<a href='".$hostlink."/signup/'><strong>register</strong></a> or 
						<a href='".$hostlink."/login/'><strong>sign in</strong></a>.
						</p>";
				} 
                else if(is_user_logged_in() and $current_user->ID != $proj_user_ID){
	                //$query = "SELECT fund_given FROM user_actions WHERE proj_id='$proj_id' AND user_ID='$current_user->ID'";
	                global $tier_count;
					$user_pledge = $wpdb->get_row("SELECT * FROM user_actions WHERE proj_id='$proj_id' AND user_ID='$current_user->ID'");
					$user_tier_arr = null;
					if(sizeof($user_pledge) != 0){
						$user_pledge_amt = $user_pledge->fund_given;
						$user_tier = usertierprint($user_pledge);
						$user_tier_arr = usertier($user_pledge);
					}
					if ($proj_finished){
						if (is_user_logged_in()){		
							if(sizeof($user_pledge) != 0){
								echo "<br><hr><p style='text-align:center; font-size: 12px; text-transform:none;'>You had pledged P".number_format($user_pledge_amt);
								if($user_tier != null) echo " and had backed tier $user_tier";
								echo " in the project! Thank you for your support!</p>";
							}
						}
					}else {
						$nopledge = 1;
						if(sizeof($user_pledge) != 0){
							echo "<p style='text-align:center; font-size: 12px; text-transform:none;'>You currently have pledged P".number_format($user_pledge_amt);
							if($user_tier != null) echo " and had backed tier $user_tier";
							echo " in the project!</p>";
							echo "<p style='text-align:center; font-size:13px; text-transform:none;'><strong>WANT TO CHANGE YOUR DONATION?</strong></p>";	
							$nopledge = 0;
						}
						
						$tier_array = $wpdb->get_results("SELECT * FROM proj_tiers WHERE proj_id = $proj_id ORDER BY proj_tier_amount", ARRAY_A);
						$pledgemin = "<input type='number' id='pledge' name='pledge_amount' onkeyup='choosetiers()' min='".$nopledge."' style='width:100%;' required>";
						$projtiers='';
						if(sizeof($tier_array) != 0)	$projtiers = chooseprojtiers($wpdb, $proj_id);

						echo "<form action='pledge-processing' method='post'>
									<input type='hidden' name='proj_ID' value='$proj_id'>
									<input type='hidden' name='proj_title' value='$proj_title'>
									<input type='hidden' name='proj_tier' value='".json_encode($user_tier_arr)."'>
							        <label for='pledge'><strong>PLEDGE AMOUNT:</strong></label>".$pledgemin."
							        <div id='choosetiers'>".$projtiers."</div>
							        <br><label for='comment'><strong>ANY COMMENTS? (Optional)</strong></label>
	        						<textarea id='comment' name='user_comment' style='width:100%;'></textarea>
								<br><br>
							    <div style='text-align:center;'>
	  								<button class='btn btn-secondary-outline btn-lg' type='submit' id='dbutton' style='background-color:#7b1113; color:white;'>Donate!</button>
								</div></form>
								<p id='ptcontainer'></p>";
					}
				}?><br></div>
				<?php
					if (is_user_logged_in() and wp_get_current_user()->ID == $proj_user_ID){
						echo "<hr><br><h5>PLEDGERS' LIST</h5>";
						echo "<ul>";
						$result = $wpdb->get_results("SELECT * FROM user_actions WHERE proj_ID='$proj_id'", ARRAY_A);
								$pledgecount = 0;
								if(IsSet($result))
									foreach ($result as $list) {
										$pledgecount++;
										$pledger = $list['user'];
										$pledge_amount = $list['fund_given']; 
										$pledgerlink = "<a href='$hostlink/user-profile/?view=".$list['user_ID']."'style='color:#7b1113;' >$pledger</a>";
										echo '<li>'.$pledgerlink.' - P'.number_format($pledge_amount).'</li> ';
									}					
								if(!$pledgecount) echo "<p>No pledgers yet.</p>";
						echo "</ul>";
				}?>
			</div>
		</div>
		
		<div id="projcomments">
			<hr><p style="color: #7b1113;">PLEDGERS' COMMENTS</p>
			<?php
				$commentcount = 0;
				$result = $wpdb->get_results("SELECT * FROM user_actions WHERE proj_ID='$proj_id'", ARRAY_A);
				if(IsSet($result)){
					foreach ($result as $list) {
						$user_comment = stripcslashes($list['user_comment']);
						$action_date = $list['action_date'];
						if($user_comment != ''){
							$commentcount++;
							echo '<div id="pledgecomment">';	echo '<hr>';
								echo '<div id="pledgename" style="color: #7b1113;">';
								$pledger = $list['user'];	
								$user_ID = $list['user_ID'];	
								echo "<a href='".$hostlink."/user-profile/?view=$user_ID'>$pledger</a>";	
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

<?php

	function backersnum($results, $wpdb, $proj_id){	
		global $tier_count, $tierslots;
		$results = $wpdb->get_results("SELECT * FROM proj_tiers WHERE proj_id = $proj_id ORDER BY proj_tier_amount");
		
		foreach($results as $tier)	$tierslots[] = $tier->proj_tier_slot;
		
		$query2 = "SELECT * FROM user_actions WHERE proj_id = $proj_id ORDER BY user_ID";
		$results2 = $wpdb->get_results($query2);
		$tier_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM proj_tiers WHERE proj_id = $proj_id");
		for ($i = 0; $i < $tier_count; $i++)	$backernum[$i] = 0;

		if(sizeof($results2)){
			foreach($results2 as $pledger){
				if($pledger->proj_tier != null){
					$pledged_tiers = json_decode($pledger->proj_tier);
					for ($i = 0; $i < $tier_count; $i++)	
						if ($pledged_tiers[$i] != 0){
							$backernum[$i]++;
							if($tierslots[$i] != null)	$tierslots[$i]--;
						}
				}
			}
		}else return null;
		return $backernum;
	}

	function usertier($user_pledge){
		global $tier_count;
		//$tier_count = $wpdb->get_var("SELECT COUNT(*) FROM proj_tiers WHERE proj_id = $proj_id");
		if($user_pledge->proj_tier != null)				return json_decode($user_pledge->proj_tier);
		else if ($tier_count == 0)						return null;
		else for($i = 0; $i < $tier_count; $i++)		$user_tier[$i] = 0;
		return $user_tier;
	}

	function usertierprint($user_pledge){
		$pledged_tiers = usertier($user_pledge);
		if($pledged_tiers == null)	return null;
		$tierpledge = array_sum($pledged_tiers);
		if($tierpledge == null)		return null;
		$user_tier = 'level ';
		if($tierpledge > 1)	$user_tier = 'levels ';
		$tierctr = 0;
		for($i = 0; $i < sizeof($pledged_tiers); $i++){
			if($pledged_tiers[$i] != 0){
				if($tierctr >= 1 && $tierpledge > 2) $user_tier .= ', ';
				if($tierctr == $tierpledge-1 && $tierpledge != 1) $user_tier .= ' and ';
				$user_tier .= $i+1;
				$tierctr++;
			}
		}
		return $user_tier;
	}

	function chooseprojtiers($wpdb, $proj_id){
		global $tier_count, $tierslots;
		$results = $wpdb->get_results("SELECT * FROM proj_tiers WHERE proj_id = $proj_id ORDER BY proj_tier_amount");
		if(sizeof($results) != 0){
			$projtiers = '<br><label for="pledge"><strong>CHOOSE A PROJECT TIER (OPTIONAL):</strong></label><div id="projecttiers">';
			echo "<script>
					window.tierslot = ".json_encode($tierslots)."
				</script>";
		}
		for($i = 0; $i < sizeof($results); $i++){
			$projtiers .= "<input type='hidden' class='proj-tier' onchange='tierchange()' name='proj-tier[".$i."]'value='".$results[$i]->proj_tier_amount."'><label for='proj_tier[".$i."]' >Tier Level ".($i+1)." (P ".number_format($results[$i]->proj_tier_amount).")</label><br>";
		}
		return $projtiers."</div>";
	}
?>

<script>
	var pledgeval, tierlist, pledgeval2 = 0;
	if (document.getElementById("dbutton") != null)	document.getElementById("dbutton").addEventListener("click", thankyou);

	function thankyou(){
		pledgeval = document.getElementById("pledge").value;
        if (parseInt(pledgeval) > 0){
        	document.getElementById("ptcontainer").innerHTML = "<p id='pledgethanks'>THANK YOU FOR YOUR DONATION!</p>";
        	tierlist = document.getElementsByClassName("proj-tier");
        	for (var i = 0; i < tierlist.length; i++){
        		if(!tierlist[i].checked || tierlist[i].type=='hidden')	tierlist[i].value = 0;
        		else 													tierlist[i].value = i+1;
        	}
        }
    }

    function choosetiers(){
    	prevval = parseInt(document.getElementById("pledge").value);
    	pledgeval = parseInt(document.getElementById("pledge").value);
    	tiertitle = "<br><label for='pledge'><strong>CHOOSE A PROJECT TIER (Optional):</strong></label>";
    	tierlist = document.getElementsByClassName("proj-tier");
    	
    	for (var i = 0; i < tierlist.length; i++){
			if ((pledgeval-pledgeval2 >= parseInt(tierlist[i].value))|| (tierlist[i].checked && pledgeval >= parseInt(tierlist[i].value))){
    			if(window.tierslot[i] != 0){
		    		tierlist[i].type = 'checkbox';
		    		tierlist[i].innerHTML = 'Tier Level ' + (i+1);
	    		}
    		}	
    		else{
    			tierlist[i].type = 'hidden';
    			if(tierlist[i].checked){
    				tierlist[i].checked = false;
    				tierchange();
    			}
    		}
    	}
    }

    function tierchange(){
    	pledgeval2 = 0
    	tierlist = document.getElementsByClassName("proj-tier");
    	for (var i = 0; i < tierlist.length; i++)	if(tierlist[i].checked)	pledgeval2 += parseInt(tierlist[i].value);
    	choosetiers();
    }

</script>

<footer style="clear:both;display: block">
	<?php get_footer();?>
</footer>