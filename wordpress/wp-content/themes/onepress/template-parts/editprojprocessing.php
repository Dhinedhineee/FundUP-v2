<?php
	/*Template Name: Edit Project Processing Template*/
	/**
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 * @package OnePress
	 */

	#HEADER 
	get_header();
	$layout = onepress_get_layout();
	echo onepress_breadcrumb();
	echo "<br><br><br>";

?>

<div id="content" class="site-content">
	<div class="page-header">
		<div class="container">
			<h1 class="entry-title">Edit A Project</h1>
		</div><!-- container -->
	</div><!-- page-header -->
	<div class="container">
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
	
<?
	$hostlink = 'http://'.$_SERVER['HTTP_HOST'];
	if($hostlink == 'http://localhost')	$hostlink .= '/wordpress';

	if(!IsSet($_SERVER['HTTP_REFERER']) || !IsSet($_POST['proj-name'])){
		//Unknown site access
		header('Location: '.$hostlink);
		die();
	}
	$proj_deadline = htmlspecialchars($_POST['proj-deadline']);
	$proj_title = htmlspecialchars($_POST['proj-name']);
	$proj_goal = htmlspecialchars($_POST['goal-amount']);
	$proj_info = htmlspecialchars($_POST['proj-info']);
	$proj_ID = htmlspecialchars($_POST['proj-id']);

	global $wpdb, $current_user_name;
	$current_user = wp_get_current_user();
	$current_user_name = $current_user->display_name;

	if($_FILES['proj-image']['size'] != 0){
		fileupload();
		$proj_image = $_FILES['proj-image']['name'];
	}else 	$proj_image = $wpdb->get_var("SELECT proj_image FROM projects WHERE proj_id='$proj_ID'");
	
	$wpdb->update( 
		'projects', 
		array( 
			'proj_goal' => $proj_goal,
			'proj_deadline' => $proj_deadline,
			'proj_info' => $proj_info,
			'proj_image' => $proj_image
		), 
		array( 'proj_id' => $proj_ID, 'proj_user' => $current_user_name)
	);

	/*ARE PROJECT HAVING THE SAME NAMES ALLOWED?
	if($proj_title != $origprojname){
		//echo "NOT SAME";
		$checkdup = $wpdb->get_var("SELECT * FROM projects WHERE proj_title='$proj_title'");
		if(isset($checkdup)){
			display("Sorry. Project name already taken!");
			fileerror();
		}
	}*/

	$wpdb->update( 
		'projects', array( 'proj_title' => $proj_title ), array( 'proj_id' => $proj_ID )
	);

	$wpdb->update( 
		'user_actions', array( 'proj_title' => $proj_title,),  array( 'proj_ID' => $proj_ID )
	);

	$wpdb->delete('proj_tiers', array( 'proj_ID' => $proj_ID ));
	if(IsSet($_POST['proj-tier']['AMOUNT'])){
		//var_dump($_POST['proj-tier']['TEXT']);
		//var_dump($_POST['proj-tier']['AMOUNT']);
	
		for ($i = 0; $i < sizeof($_POST['proj-tier']['AMOUNT']); $i++){
			$proj_tier_desc = htmlspecialchars($_POST['proj-tier']['TEXT'][$i]);
			$wpdb->insert( 
				'proj_tiers', 
				array( 
					'proj_ID' => $proj_ID,
					'proj_tier_amount' => $_POST['proj-tier']['AMOUNT'][$i], 
					'proj_tier_desc' => $proj_tier_desc
				), 
				array('%d','%d','%s') 
			);		
		}
	}

	$url = $hostlink.'/projinfo/?view='.$proj_ID;
	display("Your project was successfully processed. <br> Redirecting to project page...");
	redirect($url);

	function fileupload(){
		global $current_user_name;
		$current_user = wp_get_current_user();
		$current_user_name = $current_user->display_name;
		$current_user_ID = $current_user->ID;
		$upload_dir = wp_upload_dir();
	    $users_folder_dir = $upload_dir['basedir'].'/users';
	       
	    if (isset($current_user_name) && !empty($users_folder_dir)){
	        $user_dirname = $users_folder_dir.'/'.$current_user_ID.'/';
	        if (!file_exists($user_dirname))	wp_mkdir_p($user_dirname);
			$target_file = $user_dirname . basename($_FILES["proj-image"]["name"]);
	    }
		
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image
		if(isset($_FILES["proj-image"])) {
		    $check = getimagesize($_FILES["proj-image"]["tmp_name"]);
		    if($check !== false) {
		        $uploadOk = 1;
		    } else {
		        display("File is not an image.");
		        $uploadOk = 0;
		    }
		}
		
		// Check if file already exists
		if (file_exists($target_file)) {
		    //return 0;
		}

		// Check file size
		if ($_FILES["proj-image"]["size"] > 2097152*3.5) {
		    display("Sorry, your file is too large.");
		    $uploadOk = 0;
		}

		// Allow certain file formats
		if($imageFileType != "JPG" && $imageFileType != "jpg" && $imageFileType != "PNG" && $imageFileType != "png" && $imageFileType != "JPEG" && $imageFileType != "jpeg"
		&& $imageFileType != "GIF" && $imageFileType != "gif" && $uploadOk != 0) {
		    display("The project photo is of type ".$imageFileType.". Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
		    $uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		    display("Sorry, there is an error uploading your file.");
		    fileerror();

		// if everything is ok, try to upload file
		} else {
		    if (move_uploaded_file($_FILES["proj-image"]["tmp_name"], $target_file)) {
		        //return 1;
		    } else {
		       display("Sorry, there is an error uploading your file.");
			   fileerror();  
		    }
		}
	}

	function fileerror(){
		display("Redirecting to previous page...");
		$url = "{$_SERVER['HTTP_REFERER']}";
		redirect($url);
	}

	//redirect($url);
	function redirect($url){
		$string = '<script type="text/javascript">';
	    $string .= 'setTimeout(function(){window.location = "' . $url . '";}, 5	);';
	    $string .= '</script>';
	    echo $string;
	    die();
	}

	function display($msg){	
		echo "<h2>".$msg."</h2>";
	}
?>
			</main><!-- #main -->
		</div><!-- #primary -->
	</div><!-- #container -->
</div><!-- #content -->


<!-- #footer is not sent because of automatic redirection -->
<footer style="clear:both;display: block">
	<? get_footer();?>
</footer>