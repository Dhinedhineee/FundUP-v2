<?php
	/*Template Name: Project Info Template*/
	/**
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 * @package OnePress
	 */

	$hostlink = 'http://'.$_SERVER['HTTP_HOST'];
	if ($hostlink == 'http://localhost')		$hostlink .= '/wordpress';
	if (isset($_GET['view']))		$proj_id = htmlspecialchars($_GET['view']);
	if (!is_numeric($proj_id))		redirect();

	#DATABASE ACCESS
	global $wpdb;
	$proj_result = $wpdb->get_row("SELECT * FROM projects WHERE proj_id='$proj_id'", ARRAY_A);

	if (!isset($proj_result))		redirect();
	if ($wpdb->get_var("SELECT suspended FROM wp_users WHERE ID=".$proj_result['proj_user_ID']))	redirect();

	get_header();			#PASSED ALL REDIRECTION TESTS.

	function redirect(){
		header('Location: '.$hostlink);
		die();
	}
?>

<?php
	#PREPROCESSING OF DATA
	$proj_title = $proj_result['proj_title'];
	$proj_user = $proj_result['proj_user'];
	$proj_goal = $proj_result['proj_goal'];
	$proj_deadline = $proj_result['proj_deadline'];
	$proj_image = $proj_result['proj_image'];
	$proj_info = $proj_result['proj_info'];
	$proj_user_ID = $proj_result['proj_user_ID'];
	$proj_info = stripcslashes(str_replace("\n", "<br>", $proj_info));			//TEXT PARAGRAPH LAYOUT
	$user_link = $hostlink."/user-profile/?view=";
	$proj_img_link = $hostlink."/wp-content/uploads/users/".$proj_user_ID."/".$proj_image;

	#DATABASE ACTIONS
	$proj_fund = $wpdb->get_var("SELECT SUM(fund_given) FROM user_actions WHERE proj_ID='$proj_id'");
	$tier_result = $wpdb->get_results("SELECT * FROM proj_tiers WHERE proj_id = $proj_id ORDER BY proj_tier_amount");
	$userpledge_result = $wpdb->get_row("SELECT * FROM user_actions WHERE proj_id='$proj_id' AND user_ID='$current_user->ID'");
	$pledgers_result = $wpdb->get_results("SELECT * FROM user_actions WHERE proj_ID='$proj_id'", ARRAY_A);
	$tier_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM proj_tiers WHERE proj_id = $proj_id");

	#PROJECT PARTS TEXTS
    $proj_finished = 1;  
    $user_pledge_amt = 0;
    $backernames = null;
	$tierdiv = project_tiers();
	$dldiv = project_deadline();
	$pledgerdiv = project_pledgers();
	$pledgeinfodiv = user_pledge_info();
	$pledgeformdiv = project_pledge_form();
	$projcommentsdiv = project_comments();
?>

<!-- START OF HTML -->
<link rel="stylesheet" type="text/css" href="../wp-content/themes/onepress/assets/css/projstyles.css?ver=<?php echo rand(111,999)?>">	
<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<div id="goal">		
		<div id="contentgoal">
			<br><h2><?= $proj_title?></h2>
			<h4>by <a href=<?= $user_link.$proj_user_ID?>><?= $proj_user?></a></h4>
			<img src="<?= $proj_img_link?>" alt="Project image not found." id="contentimg">
			<p><?= $proj_info?></p>
			<div id="projtiers2"><?= $tierdiv?></div>
			<hr>
		</div>

		<div id="sidebarprojinfo">
			<div id="asidegoal">
				<b>Goal PHP</b><br><span>P</span>				
				<span class="amount"><?= number_format($proj_goal)?></span><br><br>
				<b>Raised PHP</b><br><span>P</span>
				<span class="amount"><?= number_format($proj_fund)?></span><br><br>
				<hr><p style="text-align: center; color:#7b1113;"><b><?= $dldiv?></b></p>
			</div>
			<br>
			<div id="asidedonor">
				<div id="donatewidget">
					<div id="pledgeinfo"><?= $pledgeinfodiv?></div>
					<div id="pledgeform"><?= $pledgeformdiv?></div>
				</div>
				<div id="pledger_list"><?= $pledgerdiv?></div>
			</div>
		</div>
		
		<div id="projcomments">
			<p>PLEDGERS' COMMENTS</p>
			<?= $projcommentsdiv?>
		</div>	
	</div>

<br><br>
<!-- END OF HTML -->

<?php
	function project_tiers(){
		global $tier_result, $proj_id, $current_user, $proj_user_ID, $backernames, $user_link;
		if (sizeof($tier_result)){
			$tierdiv = "<hr><p>PROJECT TIERS</p>";
			$tierdiv .= "<div id='projtiers'><table>";
			$tierdiv .= "<col width=25%><col width=40%><col width=35%>";
			$backernum = backersnum($tier_result, $proj_id);
			for ($i = sizeof($tier_result)-1; $i >= 0; $i--){
				$tierdiv .= "<tr>";
				$tierdiv .= "<td style='color:#7b1113; font-size: 20px;'>".'P '.number_format($tier_result[$i]->proj_tier_amount)."</td>";
				$tierdiv .= "<td style='padding: 0px;'>".stripcslashes($tier_result[$i]->proj_tier_desc)."</td>";
				$numbacker = ($backernum[$i] == null ? 0:$backernum[$i]);
				$tierdiv .= "<td class='maroon'>Backers: ".$numbacker."<br>";
				
				if (intval($tier_result[$i]->proj_tier_slot) != 0) $tierdiv .= "Slots remaining: ".($tier_result[$i]->proj_tier_slot-$numbacker);
				else $tierdiv .= "Unlimited slots available.";

				if ($current_user->ID == $proj_user_ID)
					foreach ($backernames[$i] as $backerid => $backer)
						$tierdiv .= '<li><a href='.$user_link.$backerid.'>'.$backer.'</a></li>';	

				$tierdiv .= "</td></tr>";	
			}		
			$tierdiv .= "</table></div>";
			return $tierdiv;
		}else return "";
	}

	function project_deadline(){
		global $proj_deadline, $proj_finished;
		$dldiv = '';
		date_default_timezone_set('Asia/Manila');
		$hey = new DateTime($proj_deadline);
		$localtime = new DateTime();
		$localtime->add(DateInterval::createFromDateString('yesterday'));
		$et = $hey->diff($localtime);
		$deadline = $et->format('%R%a')*-1;
		if($et->invert == 1){
			$dldiv .= "This project will end ".($deadline == 0 ? "today.":"in ".($deadline)." day".($deadline > 1 ? "s.":"."));
			$proj_finished = 0;
		}
		else 	$dldiv .= "This project ended ".($deadline == 0 ? "1 day ago.":(-1*$deadline)." days ago.");
		return $dldiv;
	}

	function project_pledgers(){
		global $pledgers_result, $proj_id, $current_user, $proj_user_ID, $user_link;
		if(is_user_logged_in() and $current_user->ID == $proj_user_ID){
			$pledgerdiv = "<hr><h5>PLEDGERS' LIST</h5><hr>";
			$pledgerdiv .= "<ul>";
			$pledgecount = 0;
			if(IsSet($pledgers_result))
				foreach ($pledgers_result as $list) {
					$pledgecount++;
					$pledger = $list['user'];
					$pledge_amount = $list['fund_given']; 
					$pledgerlink = "<a href=".$user_link.$list['user_ID'].">$pledger</a>";
					$pledgerdiv .= '<li>'.$pledgerlink.' - P'.number_format($pledge_amount).'</li> ';
				}					
			if(!$pledgecount) $pledgerdiv .= "<p>No pledgers yet.</p>";
			$pledgerdiv .= "</ul><hr>";
			return $pledgerdiv;
		} else return '';
	}

	function user_pledge_info(){
		global $proj_finished, $proj_id, $proj_user_ID, $tier_count, $hostlink, $user_tier_arr, $user_pledge_amt, $current_user, $userpledge_result;
		$pledgeinfodiv = '';
		if($current_user->ID != $proj_user_ID){
			$user_tier_arr = null;
			if (!$proj_finished) 	$pledgeinfodiv .= '<br><hr><h2 class="widget-title">WANT TO DONATE?</h2><hr>';
                        else    $pledgeinfodiv .= '<hr>';
			if(sizeof($userpledge_result) != 0){
				$user_pledge_amt = $userpledge_result->fund_given;
				$user_tier = usertierprint($userpledge_result);
				$user_tier_arr = usertier($userpledge_result);
				$pledgeinfodiv .= "<p style='font-size: 12px;'>You ".($proj_finished ? 'had':'have')." pledged P".number_format($user_pledge_amt);
				if ($user_tier != null) 	$pledgeinfodiv .= " and ".($proj_finished ? 'had':'have')." backed tier $user_tier";
				$pledgeinfodiv .= " in the project!".($proj_finished ? " Thank you for your support.<hr>":"")."</p>";
				if (!$proj_finished) 		$pledgeinfodiv .= "<p style='font-size:13px;'><strong>WANT TO CHANGE YOUR DONATION?</strong></p>";	
			}else if (!is_user_logged_in())
				$pledgeinfodiv .= "<p style='color:black;'>
					You need to be a registered user to donate. Click here to 
					<a href='".$hostlink."/signup/'><strong>register</strong></a> or 
					<a href='".$hostlink."/login/'><strong>sign in</strong></a>.
					</p><hr>";
		}
		return $pledgeinfodiv;
	}

	function project_pledge_form(){
		global $proj_finished, $proj_user_ID, $proj_id, $proj_title, $user_tier_arr, $user_pledge_amt, $current_user, $tier_result;
		$pledgeformdiv = '';
		if (!$proj_finished && is_user_logged_in() && $current_user->ID != $proj_user_ID){
			$pledgemin = "<input type='number' id='pledge' name='pledge_amount' onkeyup='choosetiers()' min='".($user_pledge_amt > 0 ? 0:1)."' style='width:100%;' required>";
			$projtiers = (sizeof($tier_result) != 0 ? chooseprojtiers($proj_id):'');
			$pledgeformdiv .= 
				"<form action='pledge-processing' method='post'>
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
				<p id='ptcontainer'></p><br><hr>";
		}
		return $pledgeformdiv;
	}

	function project_comments(){
		global $pledgers_result, $hostlink, $proj_finished, $proj_id;
		$projcommentsdiv = '';
		$commentcount = 0;
		if(IsSet($pledgers_result)){
			foreach ($pledgers_result as $list) {
				$user_comment = stripcslashes($list['user_comment']);
				$action_date = $list['action_date'];
				if($user_comment != ''){
					$commentcount++;
					$projcommentsdiv .= '<div id="pledgecomment">';	$projcommentsdiv .= '<hr>';
					$userlink = "<a href='".$hostlink."/user-profile/?view=".$list['user_ID']."'>".$list['user']."</a>";	
					$projcommentsdiv .= '<div id="pledgename">'.$userlink. '</div><div id="pledgedate">';
						date_default_timezone_set('Asia/Manila');
						$hey = new DateTime($action_date);
						$localtime = new DateTime();
						$et = $localtime->diff(new DateTime($action_date));
						if($et->y > 0)			$p=$et->y.' year';
						else if($et->m > 0)		$p=$et->m.' month';
						else if($et->d > 0)		$p=$et->d.' day';
						else if($et->h > 0)		$p=$et->h.' hour';
						else if($et->i > 0)		$p=$et->i.' minute';
						else 					$p=$et->s.' second';
					$projcommentsdiv .= $p.(intval($p) > 1 ? 's':'').' ago<br>';
					$projcommentsdiv .= '</div><div id="pledgecomdet">'.$user_comment.'</div>';
					$projcommentsdiv .= '</div>';
				}
			}				
		}	
		if(!$commentcount){
			$projcommentsdiv .= "<hr><p style='padding-left:20px; color: #777777'>";
			if ($proj_finished) 	$projcommentsdiv .= "This project has no comments.</p>";
			else   					$projcommentsdiv .= "No comments yet. Please pledge first to be able to leave comments!</p>";
		}	
		$projcommentsdiv .= '<hr>';
		return $projcommentsdiv;
	}

	function backersnum($results, $proj_id){	
		global $tier_count, $tierslots, $tier_result, $pledgers_result, $backernames;
		
		foreach ($tier_result as $tier)		$tierslots[] = ($tier->proj_tier_slot == 0 ? null:$tier->proj_tier_slot);
		for ($i = 0; $i < $tier_count; $i++){
			$backernum[$i] = 0;
			$backernames[$i] = array();
		}

		if (sizeof($pledgers_result)){
			for ($i = 0; $i < sizeof($pledgers_result); $i++){
				if ($pledgers_result[$i] != null){
					$pledged_tiers = json_decode($pledgers_result[$i]['proj_tier']);
					for ($j = 0; $j < $tier_count; $j++)	
						if ($pledged_tiers[$j] != 0){
							$backernames[$j][$pledgers_result[$i]['user_ID']] = $pledgers_result[$i]['user'];
							$backernum[$j]++;
							if($tierslots[$j] != null)	$tierslots[$j]--;
						}
				}
			}
		}else return null;
		return $backernum;
	}

	function usertier($user_pledge){
		global $tier_count;
		if ($user_pledge->proj_tier != null)			return json_decode($user_pledge->proj_tier);
		else if ($tier_count == 0)						return null;
		else for($i = 0; $i < $tier_count; $i++)		$user_tier[$i] = 0;
		return $user_tier;
	}

	function usertierprint($user_pledge){
		$pledged_tiers = usertier($user_pledge);
		if ($pledged_tiers == null)		return null;
		$tierpledge = array_sum($pledged_tiers);
		if ($tierpledge == null)		return null;
		$user_tier = 'level'.($tierpledge > 1 ? 's':'').' ';
		$tierctr = 0;
		for($i = 0; $i < sizeof($pledged_tiers); $i++){
			if($pledged_tiers[$i] != 0){
				if($tierctr >= 1 && $tierpledge > 2) 			  $user_tier .= ', ';
				if($tierctr == $tierpledge-1 && $tierpledge != 1) $user_tier .= ' and ';
				$user_tier .= $i+1;
				$tierctr++;
			}
		}
		return $user_tier;
	}

	function chooseprojtiers($proj_id){
		global $tier_count, $tierslots, $tier_result;
		if(sizeof($tier_result) != 0){
			$projtiers = '<br><label for="pledge"><strong>CHOOSE A PROJECT TIER (OPTIONAL):</strong></label><div id="projecttiers">';
			echo "<script>window.tierslot = ".json_encode($tierslots)."</script>";
		}
		for($i = 0; $i < sizeof($tier_result); $i++)
			$projtiers .= "<input type='hidden' class='proj-tier' onchange='tierchange()' name='proj-tier[".$i."]'value='".$tier_result[$i]->proj_tier_amount."'><label for='proj_tier[".$i."]' >Tier Level ".($i+1)." (P ".number_format($tier_result[$i]->proj_tier_amount).")</label><br>";
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