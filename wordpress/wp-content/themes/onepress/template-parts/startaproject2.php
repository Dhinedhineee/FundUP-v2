<?php
/** Template Name: Start a Project 3*/
/**
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package OnePress
 */
?>

<?php
/*
	if(!IsSet($_SERVER['HTTP_REFERER'])){
		header('Location: http://localhost/wordpress');
		die();
	}
*/
	get_header();
	$layout = onepress_get_layout();
?>

<div id="content" class="site-content">

	<div class="page-header">
		<div class="container">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</div>
	</div>

	<?php echo onepress_breadcrumb(); ?>

	<div id="content-inside" class="container <?php echo esc_attr( $layout ); ?>">
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

				<form action="start-a-project-processing" method="post" class="wpcf7-form demo" onSubmit="return submitted()" enctype="multipart/form-data" id="mainForm">

				<p><label> Project Name<br />
		    	<span class="wpcf7-form-control-wrap proj-name"><input type="text" name="proj-name" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-invalid="false" id="proj-name" required/>
				<span id="titlealert"></span></span> </label></p>

				<p><label> Goal Amount<br />
				<span class="wpcf7-form-control-wrap goal-amount"><input type="number" name="goal-amount" value="'.do_shortcode("[CF7_PROJ_GOAL key='edit']").'" class="wpcf7-form-control wpcf7-number wpcf7-validates-as-required wpcf7-validates-as-number" aria-required="true" aria-invalid="false" min="1" required/></span></label><span></span></p>

				<?php 
					$mindate = date_default_timezone_set('Asia/Manila');
					$mindate = date('Y-m-d');
					echo '
					<p><label> Project Deadline<br />
					<span class="wpcf7-form-control-wrap goal-amount"><input type="date" name="proj-deadline" class="wpcf7-form-control wpcf7-number wpcf7-validates-as-required wpcf7-validates-as-number" aria-required="true" aria-invalid="false" required min="'.$mindate.'"/></span></label><span></span></p>';
				?>

				<p><label> Project Information<br />
				<span class="wpcf7-form-control-wrap proj-info"><textarea name="proj-info" id="proj-info" cols="40" rows="10" class="wpcf7-form-control wpcf7-textarea wpcf7-validates-as-required" required aria-invalid="false"></textarea>
				<span id="infoalert"></span></span> </label></p>

				<p><label> Upload a photo (jpg/jpeg/gif/png, max 7MB)<br /><span class="wpcf7-form-control-wrap image"><input type="file" name="proj-image" id="proj-image" size="40" class="wpcf7-form-control wpcf7-file wpcf7-validates-as-required" aria-required="true" aria-invalid="false" accept="image/jpeg,image/gif,image/png,image/pjpeg" onchange="verifyMe(this)" required/><br><span id="FileError"></span></span></label></p>
				
				<p><input type="submit" id="submitbtn" value="Submit" class="wpcf7-form-control wpcf7-submit" /></p>
				</form>
				<br>

				<script>
					function verifyMe(){
						alert('verifying...');
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
					</script>

				<?php 

					#DO THE PROCESSING HERE
					##echo do_shortcode('[contact-form-7 id="297" title="Create Project"]'); 

				?>	
					
			</main><!-- #main -->
		</div><!-- #primary -->

        <?php if ( $layout != 'no-sidebar' ) { ?>
            <?php get_sidebar(); ?>
        <?php } ?>

	</div><!--#content-inside -->
</div><!-- #content -->

<?php get_footer(); ?>
