<?php
	/*Template Name: Edit Project Template*/
	/**
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 * @package OnePress
	 */
	
	$hostlink = 'http://'.$_SERVER['HTTP_HOST'];
	if($hostlink == 'http://localhost')	$hostlink .= '/wordpress';

	if (isset($_GET['edit']))		$proj_id = htmlspecialchars($_GET['edit']);
	if(!is_numeric($proj_id))	redirect();

	global $wpdb;
	$query = "SELECT * FROM projects WHERE proj_id='$proj_id'";
	$result = $wpdb->get_row($query, ARRAY_A);
	$url = $hostlink;
	
	if(!isset($result)) 								redirect();
	if($result['proj_user_ID'] != $current_user->ID)	redirect();
	if($current_user->suspended)						redirect();

	get_header();		#HEADER - PASSED ALL REDIRECTION TESTS

	function redirect(){
		global $hostlink;
		header('Location: '.$hostlink);
		die();
	}
?>

<?php 	
	$proj_title = stripcslashes($result['proj_title']);
	$proj_goal = $result['proj_goal'];
	$proj_user_ID = $result['proj_user_ID'];
	$proj_info = stripcslashes($result['proj_info']);
	$proj_user = wp_get_current_user()->display_name;
    $current_user = wp_get_current_user();
    $curr_user_ID = $current_user->ID;
	$proj_deadline = $result['proj_deadline'];

	$funddate = "This project's current deadline is on ".date_format(date_create_from_format('Y-m-d', $proj_deadline), 'm-d-Y');
	$proj_ID = $result['proj_id'];
	$proj_fund = $result['proj_fund'];
	$fundtext = "This project's current fund pledged is P".number_format($proj_fund)."";
	$proj_image = $result['proj_image'];
	$imgloc = $hostlink."/wp-content/uploads/users/".$proj_user_ID."/".$proj_image;
	$imagetext = '<br><img src = "'. $imgloc.'" alt="'.$proj_image.'" id=\"contentimg\" width="50%"><br><br>';

	$result = $wpdb->get_results("SELECT * FROM proj_tiers WHERE proj_id='$proj_ID';", ARRAY_A);
	$projtiers = '';
	if(sizeof($result) != 0){
		$projtiers = '<th>Tier Amount</th><th>Tier Slots</th><th>Tier Description</th><th></th>';
		foreach ($result as $tier) {
			$projtiers = $projtiers.'<tr>
			<td><input type="number" name="proj-tier[AMOUNT][]" value="'.$tier['proj_tier_amount'].'" required min="1"/></td>
			<td><input type="number" name="proj-tier[SLOTS][]" value="'.$tier['proj_tier_slot'].'" min="0"/></td>
			<td><textarea name="proj-tier[TEXT][]" id="proj-info" cols="30" rows="1">'.stripcslashes($tier['proj_tier_desc']).'</textarea></td>
			<td><a href="javascript:void(0);" onclick="remove(this)" id="remtier">Remove Tier</a></td>
			</tr>';
		}
	}

    $currdate = date_default_timezone_set('Asia/Manila');
    $currdate = date('Y-m-d');
    $mindate = $proj_deadline;
    if($currdate < $mindate) $mindate = $currdate;
?>	

<div id="content" class="site-content">
	<div class="page-header">
		<div class="container">
			<h1 class="entry-title">Edit A Project</h1>			
		</div>
	</div>

	<div id="content-inside" class="container no-sidebar">
		<main id="main" class="site-main" role="main">
			<div class="entry-content"></div>

		<form action="edit-project-processing" method="post" class="wpcf7-form" onSubmit="return submitted()" enctype="multipart/form-data" id="mainForm">
			<p><label> Project Name<br />
    		<span class="proj-name">
    			<input type="text" name="proj-name" size="40" value="<?= $proj_title?>"id="proj-name" required/><span id="titlealert"></span>
			</span></label></p>

			<p><label> Goal Amount (Minimum of P10K, Maximum of P10M)<br />
			<span class="goal-amount">
				<input type="number" name="goal-amount" id="goal-amount" value=<?= $proj_goal?> min="10000" max="10000000" onkeyup="slidechange(this.value)" required/></br>	
				<input type="range" name="goal-range" id="goal-range" value=<?= $proj_goal?>  min="10000" max="10000000" oninput="goalchange(this.value)"  onchange="slidechange(this.value)" required/>
			</span></label><span><?= $fundtext?></span></p>

			<p><label> Project Deadline<br />
			<span class="goal-deadline">
				<input type="date" name="proj-deadline" id="proj-deadline" required value=<?= $proj_deadline?> min=<?= $mindate?> max="2099-12-31"/>
			</span></label><span><?= $funddate?></span></p>

			<p><label> Project Information<br />
			<span class="proj-info">
				<textarea name="proj-info" id="proj-info" cols="40" rows="10" required><?= $proj_info?></textarea>
			</span><span id="infoalert"></span></label></p>

			<label>Project Tiers<label>[OPTIONAL] You can add at most 10 project tiers.<br>
			<span id="tierstiers">
				<table id="tierstable" style="width:auto;"><?= $projtiers?></table>		
			</span></label></label>

			<p><label> Current Project Photo
			<span id="imageshow"><?= $imagetext?></span>
			<p><label> Upload a new project photo(jpg/jpeg/gif/png, max 7MB)<br>
			<span class="proj-image"><input type="file" name="proj-image" id="proj-image" size="40"  accept="image/jpeg,image/gif,image/png,image/pjpeg" onchange="verifyMe(this)"/><br>
			<span id="FileError"></span></span></label></p>
			<span id="imgcontainer2"></span>
			
			<p><input type="submit" id="submitbtn" value="Submit" class="wpcf7-submit" /></p>
			<input type="hidden" name="proj-id" value="<?= $proj_ID?>">
			<div id="submitted"></div>
		</form><br>

		</main><!-- #main -->
	</div><!--#content-inside -->
</div><!-- #content -->

<footer style="clear:both;display: block">
	<?php get_footer();?>
</footer>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" rel="Stylesheet" type="text/css" />
<script>

	if(document.getElementById("tierstable").childElementCount == 1)
		tier = document.getElementById("tierstable").childNodes[0].childElementCount-1;
	else tier = 0;
	limit = 10;
        document.getElementById("proj-deadline").max = "2099-12-31";
        
        var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));
        if(isSafari){
                $('#proj-deadline').datepicker({
                        dateFormat: 'dd-mm-yy',
                        showButtonPanel: true,
                        changeMonth: true,
                        changeYear: true,
                        yearRange: '2018:2100',
                        minDate: new Date()
                    });
              $('#proj-deadline').prop('readonly', true);
        }

	window.onload=function(){
		if(tier < limit)	addtierbutton();
	}

	function slidechange(newvalue){
		document.getElementById("goal-range").value = newvalue;
	}

	function goalchange(newvalue){
		document.getElementById("goal-amount").value = newvalue;
	}

	function addtierbutton(){
		addtier = '<a href="javascript:void(0)" id="addtiers">CLICK THIS TO ADD TIERS<br><br></a>';
		a = document.getElementById("tierstiers").innerHTML;
		document.getElementById("tierstiers").innerHTML = addtier + a;
		document.getElementById("addtiers").onclick = addingtiers;
	}

	function addingtiers(){
		if(tier < limit){
			var newtier;
			if(tier == 0){
				newtier = document.getElementById('tierstable').insertRow(0);
				newtier.innerHTML = "<th>Tier Amount</th><th>Tier Slots</th><th>Tier Description</th><th></th>";
			}
			newtier = document.getElementById('tierstable').insertRow(tier+1);
			tieramt = '<input type="number" name="proj-tier[AMOUNT][]" required min="1">';
			tierslot = '<input type="number" name="proj-tier[SLOTS][]" min="0">';
			tiertxt = '<textarea name="proj-tier[TEXT][]" id="proj-info" cols="30" rows="1" required></textarea>';
			tierrem = '<a href="javascript:void(0)" onclick="remove(this)" id="remtier">Remove Tier</a>';
			newtier.innerHTML = "<tr><td>" + tieramt + "</td><td>" + tierslot +"</td><td>" + tiertxt + "</td><td>" + tierrem + "</td></tr>";
			tier++;
			if (tier==limit)	this.parentNode.removeChild(this);
		} 
	}

	function remove(removetier){
		a = removetier.parentNode.parentNode;
		a.parentNode.removeChild(a);
		tier--;	
		if(tier==limit-1)		addtierbutton();
		if(tier==0)				removetierheader(removetier);
	}

	function removetierheader(){
		a = document.getElementById('tierstable').childNodes[0];
		document.getElementById('tierstable').removeChild(a);
	}

	function verifyMe(){
		var oFile = document.getElementById("proj-image").files[0]; 
		if (oFile != null){
        if (oFile.size > 2097152*3.5) // 2*4 mb for bytes.
        {
        	document.getElementById("imageshow").innerHTML = '<?php echo $imagetext; ?>';
            document.getElementById("FileError").innerHTML ='<span role="alert" class="wpcf7-not-valid-tip">FILE SUBMITTED SIZE ISHOULD NOT EXCEED 7MB.</span>';
            return "FileSize";
        }
        else if (oFile.type == "image/jpeg"||oFile.type == "image/gif"||oFile.type == "image/png"||oFile.type == "image/pjpeg"){
        	document.getElementById("FileError").innerHTML = "";	
        	document.getElementById("imageshow").innerHTML = "";
        }
        else {
        	document.getElementById("imageshow").innerHTML = '<?php echo $imagetext; ?>';
        	document.getElementById("FileError").innerHTML ='<span role="alert" class="wpcf7-not-valid-tip">FILE SUBMISSION SHOULD ONLY BE OF TYPE JPG/JPEG/GIF/PNG.</span>';
            return "FileType";	
        }}else{
        	document.getElementById("imageshow").innerHTML = '<?php echo $imagetext; ?>';
        }
	}
	
	function submitted(){
		var x = 0;
		if (verifyMe() == "FileSize" || verifyMe() == "FileType" ){
			x = 1;
		}else document.getElementById("FileError").innerHTML = "";
		if(x == 1){
			document.getElementById("submitted").innerHTML ='<div class="wpcf7-response-output wpcf7-display-none wpcf7-validation-errors" role="alert" style="display: block;">One or more fields have an error. Please check and try again.</div>';			
			return false;
		}else{
			document.getElementById("submitted").innerHTML='<div class="wpcf7-response-output wpcf7-display-none wpcf7-mail-sent-ok" role="alert" style="display: block;">Your project is now being processed.</div>';
			return true;
		}
	}
</script>