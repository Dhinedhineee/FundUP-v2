<?php
	/*Template Name: Edit Project Processing Template*/
	/**
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 * @package OnePress
	 */
?>

<?php 

	if(!IsSet($_SERVER['HTTP_REFERER'])){
		echo "HOWDY, PLEDGER :D NOW YOU SEE ME, NOW YOU DON'T ;D";
		echo "<br><br><br><br>WOAHHHHHHHHHHH WHY ARE YOU ACCESSING THISZZZZZZZ!!?!?!";
		echo "<br><br>죽을래요?";
		echo "<br><br><br><br>Bye~~~~";
		header('Location: http://localhost/wordpress');
	}
	
	echo $_POST['proj-name'];
	echo "<br>";
	echo $_POST['goal-amount'];
	echo "<br>";
	echo $_POST['proj-info'];
	echo "<br>";
	//echo $_FILES['proj-image']['name'];
		//Add the allowed mime-type files to an "allowed" array 
			$allowed = array("image/jpeg", "image/gif","image/png");
			if(isset($_FILES['proj-image']['name'])){
			
			   //If filetypes allowed types are found, continue to check filesize:
			  if($_FILES["proj-image"]["size"] < 10000000){
			  		

			  		if($_FILES["proj-image"]["size"] == 0 && $_FILES['proj-image']['error'] != 4){
			  			//return "wew";
			  			$error = $_FILES['proj-image']['error'];
			  			return $error;
			  		}else if($_FILES["proj-image"]["size"] == 0 && $_FILES['proj-image']['error'] == 4){
			  			echo "NO FILE UPLOADED";
			  			return;
			  		}
				  	//Check uploaded file type is in the above array (therefore valid)  
			    	if(in_array($_FILES["proj-image"]["type"], $allowed)) {
				    //if both files are below given size limit, allow upload
				    //Begin filemove here....
				    //alert("g lang");
				    	echo "approved file";
			    	}else {
			    		echo "wrong type of file";
			    	}
				}else {
					echo "size exceeded";
				}
			}else {
				echo 'NOPE no files';
			}
	
?>