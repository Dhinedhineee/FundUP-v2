<?php
	/*Template Name: Start a Project Processing Template*/
	/**
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 * @package OnePress
	 */
	$hostlink = 'http://'.$_SERVER['HTTP_HOST'];
	if($hostlink == 'http://localhost')	$hostlink .= '/wordpress';

	if(!IsSet($_SERVER['HTTP_REFERER'])){
		//Unknown site access
		header('Location: '.$hostlink);
		die();
	}
	#HEADER 
	get_header();
	$layout = onepress_get_layout();
	echo onepress_breadcrumb();
	echo "<br><br><br>";

?>

<div id="content" class="site-content">
	<div class="page-header">
		<div class="container">
			<h1 class="entry-title">Start A Project</h1>
		</div><!-- container -->
	</div><!-- page-header -->
	<div class="container">
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

<?php
	/*
	var_dump($_POST['proj-deadline']);
	var_dump($_POST['proj-name']);
	var_dump($_POST['goal-amount']);
	var_dump($_POST['proj-info']);
	var_dump($_POST['proj-tier']['TEXT']);
	var_dump($_POST['proj-tier']['AMOUNT']);
	var_dump($_FILES["proj-image"]['name']);
	*/
	###############START OF PROCESSING HERE########################


	global $wpdb, $current_user_name;
	$current_user = wp_get_current_user();

	$proj_title = htmlspecialchars($_POST['proj-name']);
	$proj_user = $current_user->display_name;
	$proj_user_ID = $current_user->ID;
	$proj_goal = htmlspecialchars($_POST['goal-amount']);
	$proj_deadline = htmlspecialchars($_POST['proj-deadline']);
        if(date_create_from_format('d-m-Y', $proj_deadline)){
           $proj_deadline = date_create_from_format('d-m-Y', $proj_deadline);
           $proj_deadline = date_format($proj_deadline, "Y-m-d");
        }
	$proj_info = htmlspecialchars($_POST['proj-info']);
	//var_dump($_FILES['proj-image']);
	fileupload();
	$proj_image = $_FILES['proj-image']['name'];

	$wpdb->insert(
		'projects', 
		array( 
			'proj_title' => $proj_title, 
			'proj_user' => $proj_user, 
			'proj_user_ID' => $proj_user_ID,
			'proj_goal' => $proj_goal,
			'proj_deadline' => $proj_deadline,
			'proj_image' => $proj_image,
			'proj_info' => $proj_info,
			'proj_date' => date('Y-m-d H:i:s')
		)
	);

	$query = "SELECT * FROM projects WHERE proj_title='$proj_title'";
	$result = $wpdb->get_row($query, ARRAY_A);
	$proj_ID = $result['proj_id'];
	//var_dump($result);

	#################PROJECT TIERS HERE############################
	if(IsSet($_POST['proj-tier']) && sizeof($_POST['proj-tier']['AMOUNT']) != 0){
		for ($i = 0; $i < sizeof($_POST['proj-tier']['AMOUNT']); $i++){
			$proj_tier_slot = null;
			if(IsSet($_POST['proj-tier']['SLOTS'][$i]))	$proj_tier_slot = $_POST['proj-tier']['SLOTS'][$i];
			$proj_tier_desc = htmlspecialchars($_POST['proj-tier']['TEXT'][$i]);
			$wpdb->insert( 
				'proj_tiers', 
				array( 
					'proj_ID' => $proj_ID,
					'proj_tier_slot' => $proj_tier_slot,
					'proj_tier_amount' => $_POST['proj-tier']['AMOUNT'][$i], 
					'proj_tier_desc' => $proj_tier_desc
				)
			);		
		}
	}

	$query = "SELECT * FROM proj_tiers WHERE proj_ID='$proj_ID'";
	$result = $wpdb->get_results($query);
	//var_dump($result);

	#################IMAGE PROCESSING HERE########################
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
		die();
		redirect($url);
	}


	
	#################END OF PROCESSING HERE########################

	$url = $hostlink.'/projinfo/?view='.$proj_ID;
	display("Your project was successfully created. <br> Redirecting to project page...");
	redirect($url);
	#################END OF REDIRECTION HERE########################

	function redirect($url){
		$string = '<script type="text/javascript">';
	    $string .= 'setTimeout(function(){window.location = "' . $url . '";}, 5);';
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
	<?php get_footer();?>
</footer>