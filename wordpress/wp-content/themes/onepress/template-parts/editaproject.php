<?php
	/*Template Name: Edit Project Template*/
	/**
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 * @package OnePress
	 */
	if (isset($_GET['edit']))		
		$proj_title = htmlspecialchars($_GET['edit']);
	else $proj_title = NULL;

	#HEADER SETUP
	get_header();
	$layout = onepress_get_layout();
	echo onepress_breadcrumb();
?>

	<div id="content" class="site-content">

		<div class="page-header">
			<div class="container">
				<h1 class="entry-title">Edit A Project</h1>			</div>
		</div>

		
		<div id="content-inside" class="container no-sidebar">
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">

		<article id="post-338" class="post-338 page type-page status-publish hentry">
	<header class="entry-header">
			</header><!-- .entry-header -->

	<div class="entry-content">

</div>



<?php 
	global $wpdb;
	$query = "SELECT * FROM projects WHERE proj_title='$proj_title'";
	$result = $wpdb->get_row($query, ARRAY_A);
	var_dump($result);		#for debugging
		if(!isset($result)){
			$url = 'http://localhost/wordpress';
        	redirect($url);
		} else {
				$proj_user = $result['proj_user'];
                $current_user = wp_get_current_user();
                $name = $current_user->display_name;
                if($proj_user != $name){
				echo "WHO ARE YOU?!?!? :OOO";
				$url = 'http://localhost/wordpress';
        		redirect($url);
			}
		}

	function redirect($url){
		$string = '<script type="text/javascript">';
	    $string .= 'window.location = "' . $url . '"';
	    $string .= '</script>';
	    echo $string;
	    die();
	}

	$proj_fund = $result['proj_fund'];
	$fundtext = "The project's current fund pledged is P".number_format($proj_fund)."";
	$proj_image = $result['proj_image'];
	$imgloc = "/wordpress/wp-content/uploads/users/".$proj_user."/".$proj_image;
	$imagetext = '<img src = "'. $imgloc.'" alt="'.$proj_image.'" id=\"contentimg\" width="50%"><br>';
	$imageedit = '<p><label> Upload a photo (jpg/jpeg/gif/png, max 10MB)<br /><span class="wpcf7-form-control-wrap image"><input type="file" name="proj-image" id="proj-image" size="40" class="wpcf7-form-control wpcf7-file wpcf7-validates-as-required" aria-required="true" aria-invalid="false" accept="image/jpeg,image/gif,image/png"/><br><span id="FileError"></span></span></label></p>';
?>

<?php echo'
		<form action="edit-page-processing" method="post" class="wpcf7-form demo" onSubmit="return submitted()" enctype="multipart/form-data" id="mainForm">
		<p><label> Project Name<br />
    	<span class="wpcf7-form-control-wrap proj-name"><input type="text" name="proj-name"value="'.$proj_title.'" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-invalid="false" id="proj-name"/>
		<span id="titlealert"></span></span> </label></p>
		<p><label> Goal Amount<br />
		<span class="wpcf7-form-control-wrap goal-amount"><input type="number" name="goal-amount" value="'.do_shortcode("[CF7_PROJ_GOAL key='edit']").'" class="wpcf7-form-control wpcf7-number wpcf7-validates-as-required wpcf7-validates-as-number" aria-required="true" aria-invalid="false" min="1"/></span></label><span>'.$fundtext.'</span></p>
		<p><label> Project Information<br />
		<span class="wpcf7-form-control-wrap proj-info"><textarea name="proj-info" id="proj-info" cols="40" rows="10" class="wpcf7-form-control wpcf7-textarea wpcf7-validates-as-required" aria-invalid="false">'.do_shortcode("[CF7_PROJ_INFO key='edit']").'</textarea>
		<span id="infoalert"></span></span> </label></p>
		<p><label> Project Image <br>
		<span id="imgcontainer">'.$imagetext.'</span> 
		<input type="checkbox" id="checkme" name="Image" value="Pic">Do you want to replace your image?
		<span id="imgcontainer2"></span> <br>
		</label></p>
		<p><input type="submit" id="submitbtn" value="Submit" class="wpcf7-form-control wpcf7-submit" /></p>	
		<div id="submitted"></div>
		</form>
	';
?>

<script type="text/javascript">
	document.getElementById("checkme").addEventListener("change", showme);
	function showme(){
		if (this.checked){
			var imageedit = '<?php echo $imageedit; ?>';
			document.getElementById("imgcontainer2").innerHTML = imageedit;
			document.getElementById("imgcontainer").innerHTML = "";
		}else {
			var imagetext =  '<?php echo $imagetext; ?>';
			document.getElementById("imgcontainer").innerHTML = imagetext;
			document.getElementById("imgcontainer2").innerHTML = "";
		}
	}

	function submitted(){
		//alert(document.getElementById("proj-name").value);
		var x = 0;
		if (document.getElementById("proj-name").value == ""){
			document.getElementById("titlealert").innerHTML ='<span role="alert" class="wpcf7-not-valid-tip">The field is required.</span>';
			x = 1;
		}else document.getElementById("titlealert").innerHTML = "";
		if (document.getElementById("proj-info").value == ""){
			document.getElementById("infoalert").innerHTML ='<span role="alert" class="wpcf7-not-valid-tip">The field is required.</span>';
			x = 1;
		}else document.getElementById("infoalert").innerHTML = "";

		if(x == 1){
			document.getElementById("submitted").innerHTML ='<div class="wpcf7-response-output wpcf7-display-none wpcf7-validation-errors" role="alert" style="display: block;">One or more fields have an error. Please check and try again.</div>';			
			return false;
		}else{
			document.getElementById("submitted").innerHTML='<div class="wpcf7-response-output wpcf7-display-none wpcf7-mail-sent-ok" role="alert" style="display: block;">Your project was successfully edited.</div>';
			return true;
		}

	}
</script>

</div><!-- .entry-content -->
</article><!-- #post-## -->

					
				</main><!-- #main -->
			</div><!-- #primary -->

            
		</div><!--#content-inside -->
	</div><!-- #content -->


<footer style="clear:both;display: block">
	<?php get_footer();?>
</footer>