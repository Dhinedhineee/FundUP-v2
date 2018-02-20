<?php
	/*Template Name: Suspend */
	/**
	 * @link https://codex.wordpress.org/Template_Hierarchy
	 *
	 * @package OnePress
	 */
?>

<?php //include folder and file clean up
	define('WP_USE_THEMES', false); 
	require_once( $_SERVER['DOCUMENT_ROOT'] . '/wordpress/wp-load.php' );

	global $wpdb;
	if(!IsSet($_SERVER['HTTP_REFERER'])){
		header('Location: http://localhost/wordpress');
	} else {
		$user_ID = $_POST['ID'];
		$sustype = $_POST['type'];

		if ($sustype == 'suspend') {
			$wpdb->update('wp_users', array( 'suspended' => 1 ), array( 'ID' => $user_ID ));
		} elseif ($sustype == 'unsuspend') {
			$wpdb->update('wp_users', array( 'suspended' => 0 ), array( 'ID' => $user_ID ));
		}

      	header("Location: {$_SERVER['HTTP_REFERER']}");
	}
	die();
?>
