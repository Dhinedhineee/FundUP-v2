<?php 

	/*Template Name: Pledgers List Template*/
	/**
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 * @package OnePress
	 */

	//Automatically gets the root directory of the file. This is helpful for changing website addresses or testing in the localhost.
	$hostlink = 'http://'.$_SERVER['HTTP_HOST'];
	if ($hostlink == 'http://localhost')		$hostlink .= '/wordpress';

	if (isset($_GET['view']))		$proj_ID = htmlspecialchars($_GET['view']);
	if (!is_numeric($proj_ID))		redirect();

	global $wpdb;
	$query = "SELECT * FROM projects WHERE proj_id='$proj_ID'";
	$result = $wpdb->get_row($query, ARRAY_A);
	$url = $hostlink;
	
	if(!isset($result)) 								redirect();
	if($result['proj_user_ID'] != $current_user->ID)	redirect();
	if($current_user->suspended)						redirect();
	if($wpdb->get_var("SELECT suspended FROM wp_users WHERE ID=".$result['proj_user_ID'].))	redirect();

	#checks user login, user proj creator, input check, user not suspended
	get_header();		#HEADER - PASSED ALL REDIRECTION TESTS

	function redirect(){
		global $hostlink;
		header('Location: '.$hostlink);
		die();
	}
?>



<?php 
	global $wpdb;
	
	$proj_link = $hostlink."/projinfo/?view=".$proj_ID;
	$proj_title = $wpdb->get_var("SELECT proj_title FROM projects WHERE proj_id='$proj_ID'"); 
	$pledgers = $wpdb->get_results("SELECT * FROM user_actions WHERE proj_ID='$proj_ID'");
	
	foreach($pledgers as $a)
		$a->contact = $wpdb->get_var("SELECT user_email FROM wp_users WHERE ID='$a->user_ID'");
	

	$proj_tiers = $wpdb->get_results("SELECT * FROM proj_tiers WHERE proj_ID='$proj_ID' ORDER BY proj_tier_amount");
	$nopledge = '';
	$sortpledgers = '';
	$tieronly = '';
	$pledgerslist = '';

	if(count($pledgers) != 0){
		$sortpledgers = sortlist();
		$tieronly = choosetieronly();
		$pledgerslist = pledgerslisttext();
	}else {
		$nopledge = '<h3>This project has no pledgers yet.</h3>';
	}
?>

<link rel="stylesheet" type="text/css" href="../wp-content/themes/onepress/assets/css/pledgers.css?ver=<?php echo rand(111,999)?>">
<html>
<body>
<div id="content" class="site-content">
	<div class="page-header">
		<div class="container">
			<h1 class="entry-title"><a href=<?= $proj_link?> style="color:#7b1113;"><?= $proj_title?></a> Pledgers' List</h1>			
		</div>
	</div>
	<div id="content-inside" class="container no-sidebar">
		<main id="main" class="site-main" role="main">
			<div class="entry-content"></div>

			<!--CONTENT HERE -->
			<div id="no_pledgers">
				<?= $nopledge?>	
			</div>

			<div id="pledger_sort">
				<?= $sortpledgers?>
			</div>

			<div id="proj_tiers">
				<?= $tieronly?>
			</div>

			<div id="pledgers">
				<?= $pledgerslist?>	
			</div>
			<!--END OF CONTENT HERE -->
		</main>
	</div>
</div>


<?php 
	function pledgerslisttext(){
		global $proj_tiers, $pledgers, $hostlink, $userlink;
		$pledgerslist = '';
		$pledgerslist .='
		<table id="pledgelist">
			<thead>
				<th>Pledger</th>
				<th>Fund Given</th>
				'.(count($proj_tiers) > 0 ? '<th>Tier Pledged</th>':'').'
				<th>Project Comment</th>
				<th>Contact Details</th>
				<th>Date Pledged</th>
			</thead><tbody>
		';
				
		foreach($pledgers as $a){
			$pledgerslist .= "<tr class='pledger_row'>";
			$userlink = $hostlink."/user-profile/?view=".$a->user_ID;

			$pledgerslist .= "<td class='pledger_name'><a href=".$userlink.">".$a->user."</a></td>";
			$pledgerslist .= "<td class='fund_given'>P ".number_format($a->fund_given)."</td>";
			$pledgerslist .= "<input type='hidden' class='fund_given_amt' value='".$a->fund_given."'>";
			

			if (count($proj_tiers) > 0){
				#remove if project has no project tiers.
				if($a->proj_tier == null){
					$pledgerslist .= "<td class='pledger_tier'>No project tier chosen.</td>";
					$pledgerslist .= "<input type='hidden' class='pledger_tier_2' value='0'>";
				}
				else{	
					$user_tier = json_decode($a->proj_tier);
					$user_tier_str = '';
					$count = 0;
					for ($j = 0; $j < count($proj_tiers); $j++){
						$check = array_search((int)$proj_tiers[$j]->proj_tier_amount, $user_tier);
						if ($check === 0 || $check){
							$user_tier[$check] = ($j+1);	
							$count = 1;
						}
					}

					if ($count == 0){
						$pledgerslist .= "<td class='pledger_tier'>No project tier chosen.</td>";
						$pledgerslist .= "<input type='hidden' class='pledger_tier_2' value='0'>";
					} else {
						$user_tier_str = substr(json_encode($user_tier), 1, -1);
						$pledgerslist .= "<td class='pledger_tier'>$user_tier_str</td>";
						$pledgerslist .= "<input type='hidden' class='pledger_tier_2' value='".json_encode($user_tier)."'>";
					}
				} 							
			}

			if($a->user_comment == '')	$pledgerslist .= "<td class='pledger_comment'>No comment.</td>";
			else 						$pledgerslist .= "<td class='pledger_comment'>".stripcslashes($a->user_comment)."</td>";
			
			$pledgerslist .= "<td class='pledger_contact'>".$a->contact."</td>";
			$date = new DateTime($a->action_date);
			$pledgerslist .= "<td class='pledger_date'>".date_format($date, 'F j, Y')."</td>";
			$pledgerslist .= "<input type='hidden' class='pledger_time' value='".$a->action_date."'>";
			$pledgerslist .= "</tr>";
		}

		$pledgerslist .= '</tbody></table>';
		return $pledgerslist;
	}

	function sortlist(){
		global $proj_tiers;

		return "
			<h3>Sort by: </h3>
			<input type='checkbox' onchange=sort_date() id='pledgedate'>Date Pledged</input><br>
			<input type='checkbox' onchange=sort_amount() id='amtfund'>Amount</input><br>".
			(count($proj_tiers) > 0 ? "<input type='checkbox' onchange=sort_tiers() id='sorttiers'>Tier</input><br><br>":'<br><br>');
	}

	function choosetieronly(){
		global $proj_tiers;
		$tieronly = '';

		if(count($proj_tiers) == 0) return $tieronly;
		$tieronly .= "<h4>Show pledgers only on: </h4>";
		$tieronly .= "<input type='checkbox' class='tieronly' value=-1 onchange=notier()>No pledged tier.</input><br>";
		for($i = 0; $i < count($proj_tiers); $i++){
			$tieronly .= "<input type='checkbox' class='tieronly' value='".($i+1)."' onchange=tieronly()>Tier Level ".($i+1)." (P ".number_format($proj_tiers[$i]->proj_tier_amount).")</input><br>";
		}
		return $tieronly.'<br>';
	}

?>
<br><br>
</body>
<footer style="clear:both;display: block">
	<?php get_footer();?>
<footer>
</html>


<script>
	function notier(){
		tiers = document.getElementsByClassName("tieronly");

		for (i = 1; i < tiers.length; i++)
			if (tiers[i].checked == true)	tiers[i].checked = false;
		
		table = document.getElementById("pledgelist");
		rows = table.getElementsByClassName("pledger_row");
		for (i = 0;  i < rows.length; i++)		rows[i].style.display = '';
		if (tiers[0].checked == false)			return;

		for (i = 0;  i < rows.length; i++){
			pledgetier = JSON.parse(rows[i].getElementsByClassName("pledger_tier_2")[0].value);
			if (pledgetier != 0)		rows[i].style.display = 'none';
		}
	}

	function tieronly(){
		tiers = document.getElementsByClassName("tieronly");

		checklist = []
		check1 = 0;
		for (i = 1; i < tiers.length; i++){
			if (tiers[i].checked == true){
				checklist[i] = 1;
				check1 = 1;
			}
			else 							checklist[i] = 0;
		}

		if(check1 == 1)			tiers[0].checked = false;

		table = document.getElementById("pledgelist");
		rows = table.getElementsByClassName("pledger_row");
		for (i = 0;  i < rows.length; i++)		rows[i].style.display = '';
		if(check1 == 0)	return;
		
		for (i = 0;  i < rows.length; i++){
			pledgetier = JSON.parse(rows[i].getElementsByClassName("pledger_tier_2")[0].value);
			if (pledgetier == 0)		rows[i].style.display = 'none';
			else {
				check = 0;
				for (j = 0; j < pledgetier.length; j++){
					if (checklist[pledgetier[j]] == 1){
						check = 1;
						break;
					}
				}
				if (check == 0)			rows[i].style.display = 'none';
			}
		}
	}

	function sort_date(){
		if (document.getElementById("pledgedate").checked == false)		return;
		document.getElementById("sorttiers").checked = false;
		document.getElementById("amtfund").checked = false;

		var switching, shouldSwitch, i, x, y, rows;
		table = document.getElementById("pledgelist");
		switching = true;
		while (switching) {
			switching = false;
			rows = table.getElementsByClassName("pledger_row");		
			for (i = 0; i < rows.length-1; i++){
				shouldSwitch = false;
				x = rows[i].getElementsByClassName("pledger_time")[0];
				y = rows[i+1].getElementsByClassName("pledger_time")[0];
				if (new Date(x.value) < new Date(y.value)){
					shouldSwitch = true;
					break;
				}
			}
			if (shouldSwitch) {
				rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      			switching = true;
			}
		}
	}

	function sort_amount(){
		if (document.getElementById("amtfund").checked == false)		return;
		document.getElementById("sorttiers").checked = false;
		document.getElementById("pledgedate").checked = false;

		var switching, shouldSwitch, i, x, y, rows;
		table = document.getElementById("pledgelist");
		switching = true;
		while (switching) {
			switching = false;
			rows = table.getElementsByClassName("pledger_row");		
			for (i = 0; i < rows.length-1; i++){
				shouldSwitch = false;
				x = rows[i].getElementsByClassName("fund_given_amt")[0];
				y = rows[i+1].getElementsByClassName("fund_given_amt")[0];
				if (parseInt(x.value) < parseInt(y.value)){
					shouldSwitch = true;
					break;
				}
			}
			if (shouldSwitch) {
				rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      			switching = true;
			}
		}
	}

	function sort_tiers(){
		if (document.getElementById("sorttiers").checked == false)		return;
		document.getElementById("pledgedate").checked = false;
		document.getElementById("amtfund").checked = false;

		var switching, shouldSwitch, i, x, y, rows;
		table = document.getElementById("pledgelist");
		switching = true;
		while (switching) {
			switching = false;
			rows = table.getElementsByClassName("pledger_row");		
			break2 = false;
			for (i = 0; i < rows.length-1; i++){
				shouldSwitch = false;
				x = JSON.parse(rows[i].getElementsByClassName("pledger_tier_2")[0].value);
				y = JSON.parse(rows[i+1].getElementsByClassName("pledger_tier_2")[0].value);
				if ((x == 0 && y != 0) || x.length < y.length){
					shouldSwitch = true;
					break;
				} else if (x.length == y.length) {
					for (j = 0; j < x.length; j++){
						if (x[j] < y[j]){
							shouldSwitch = true;
							break2 = true;
							break;	
						}
					}
				}
				if (break2)	break;
			}
			if (shouldSwitch) {
				rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      			switching = true;
			}
		}
	}
</script>