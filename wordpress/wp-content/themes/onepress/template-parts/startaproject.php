<?php
/** Template Name: Start a Project*/
/**
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package OnePress
 */

	$hostlink = 'http://'.$_SERVER['HTTP_HOST'];
	if($hostlink == 'http://localhost')	$hostlink .= '/wordpress';

	if(!is_user_logged_in() || wp_get_current_user()->suspended){
		header('Location: '.$hostlink);
		die();
	}

	get_header();
	$layout = onepress_get_layout();
?>

<div id="content" class="site-content">
	<div class="page-header">
		<div class="container">
			<h1 class="entry-title">Start a Project</h1>			
		</div>
	</div>

	<div id="content-inside" class="container no-sidebar">
			<main id="main" class="site-main" role="main">
				<div class="entry-content"></div>

	<form action="start-a-project-processing" method="post" class="wpcf7-form" onSubmit="return submitted()" enctype="multipart/form-data" id="mainForm">
	
	<p><label> Project Name<br>
	<span class="proj-name-field"><input type="text" required name="proj-name" size="40" id="proj-name"/>
	<span id="titlealert"></span></span></label></p>

	<p><label> Goal Amount (Minimum of P10K, Maximum of P10M)<br />
	<span class="goal-amount"><input type="number" required name="goal-amount" id="goal-amount" min="10000" max="10000000"
	 value="10000"/></span><br>
	<span class="goal-amount"><input type="range" name="goal-range" id="goal-range" value="10000" class="wpcf7-form-control wpcf7-number wpcf7-validates-as-required wpcf7-validates-as-number" aria-required="true" aria-invalid="false" min="10000" max="10000000" oninput="goalchange(this.value)" onchange="slidechange(this.value)" required/>
	</label></p>
	
				
	
	<?php
		$mindate = date_default_timezone_set('Asia/Manila');
		$mindate = date('Y-m-d');
		echo '
		<p><label> Project Deadline<br />
		<input type="date" name="proj-deadline" aria-required="true" aria-invalid="false" required min="'.$mindate.'" value="'.$mindate.'"/></span></label><span></span></p>';
	?>

	<p><label> Project Information<br>
	<textarea name="proj-info" id="proj-info" cols="40" rows="10" required></textarea>
	<span id="infoalert"></span></label></p>

	<p><label>Project Tiers<label>[OPTIONAL] You can add at most 10 project tiers.<br>
		<Span id="tierstiers">
			<table id="tierstable" style="width:auto;"></table>		
		</span><span id="tieralert"></span></label>
	</label></p>

	<p><label> Project Photo<label> Upload a photo (jpg/jpeg/gif/png, max 7MB)<br><span class="wpcf7-form-control-wrap image"><input type="file" name="proj-image" id="proj-image" size="40" accept="image/jpeg,image/gif,image/png,image/pjpeg" onchange="verifyMe(this)" required/><br><span id="FileError"></span></span></label>	
	</label></p>
				
	<p><input type="submit" id="submitbtn" value="Submit"/></p>
	<div id="submitted"></div>
	
	</form><br>

<script>
	tier = 0;
	limit = 10;

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
		addtier = '<a href="javascript:void(0)" id="addtiers">CLICK THIS TO ADD TIERS</a>';
		a = document.getElementById("tierstiers").innerHTML;
		document.getElementById("tierstiers").innerHTML = a + addtier;
		document.getElementById("addtiers").onclick = addingtiers;
	}

	function addingtiers(){
		if(tier < limit){
			var newtier;
			if(tier == 0){
				newtier = document.getElementById('tierstable').insertRow(tier);
				newtier.innerHTML = "<th>Tier Amount</th><th>Tier Slots</th><th>Tier Description</th><th></th>"
			}
			newtier = document.getElementById('tierstable').insertRow(tier);
			tieramt = '<input type="number" name="proj-tier[AMOUNT][]" required min="1">';
			tierslot = '<input type="number" name="proj-tier[SLOTS][]">';
			tiertxt = '<textarea name="proj-tier[TEXT][]" id="proj-info" cols="30" rows="1" required></textarea>';
			tierrem = '<a href="javascript:void(0)" onclick="remove(this)" id="remtier">Remove Tier</a>';

			newtier.innerHTML = "<td>" + tieramt + "</td><td>" + tierslot +"</td><td>" + tiertxt + "</td><td>" + tierrem + "</td>";
			tier++;
			if (tier==limit)	this.parentNode.removeChild(this);
		} 
	}

	function remove(removetier){
		a = removetier.parentNode.parentNode;
		a.parentNode.removeChild(a);
		tier--;	
		if(tier==limit-1)		addtierbutton();
		if(tier==0)		removetierheader(removetier);
	}

	function removetierheader(){
		a = document.getElementById('tierstable').childNodes[0];
		document.getElementById('tierstable').removeChild(a);
	}

	function submitted(){
		var x = 0;
		if (document.getElementById("proj-name").value == "" || document.getElementById("proj-name").value.replace(/\s+/g, '') == ""){
			document.getElementById("titlealert").innerHTML ='<span role="alert" class="wpcf7-not-valid-tip">This field is required.</span>';
			x = 1;
		}else document.getElementById("titlealert").innerHTML = "";
		if (document.getElementById("proj-info").value == "" || document.getElementById("proj-info").value.replace(/\s+/g, '') == ""){
			document.getElementById("infoalert").innerHTML ='<span role="alert" class="wpcf7-not-valid-tip">This field is required.</span>';
			x = 1;
		}else document.getElementById("infoalert").innerHTML = "";
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

	function verifyMe(proj_file){
		var oFile = document.getElementById("proj-image").files[0]; 
		//alert(oFile.size);
		//alert(oFile.type);

		if (oFile != null){
	        if (oFile.size > 2097152*3.5) // 2*4 mb for bytes.
	        {
	            document.getElementById("FileError").innerHTML ='<span role="alert" class="wpcf7-not-valid-tip">FILE SUBMITTED SIZE ISHOULD NOT EXCEED 7MB.</span>';
	            return "FileSize";
	        }
	        else if (oFile.type == "image/jpeg"||oFile.type == "image/gif"||oFile.type == "image/png"||oFile.type == "image/pjpeg"){
	        	document.getElementById("FileError").innerHTML = "";	
	        	//alert("CORRECT FILE");
	        }
	        else {
	        	document.getElementById("FileError").innerHTML ='<span role="alert" class="wpcf7-not-valid-tip">FILE SUBMISSION SHOULD ONLY BE OF TYPE JPG/JPEG/GIF/PNG.</span>';
	            return "FileType";	
	        }
    	}else{
        	document.getElementById("FileError").innerHTML = '<span role="alert" class="wpcf7-not-valid-tip">THIS FIELD IS REQUIRED</span>';
        }
	}


</script>

			</main><!-- #main -->
	</div><!--#content-inside -->
</div><!-- #content -->

<footer style="clear:both;display: block">
	<?php get_footer();?>
</footer>