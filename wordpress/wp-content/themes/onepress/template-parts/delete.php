<?php
	/*Template Name: Delete */
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
		$deltype = $_POST['type'];

		if ($deltype == 'user') {
			$user_ID = $_POST['ID'];
			$user_name = $wpdb->get_var("SELECT display_name FROM wp_users WHERE ID=$user_ID");
			$user_name .= ' [Deleted]';

			$wpdb->delete('wp_users', array( 'ID' => $user_ID ));
			$wpdb->delete('projects', array( 'proj_user_ID' => $user_ID ));
			$wpdb->update('user_actions', array( 'user' => $user_name ), array( 'user_ID' => $user_ID, 'anon' => 1 ));
		} elseif ($deltype == 'project') {
			$proj_id = $_POST['ID'];

			// remove this if proj_id is implemented in user_actions
			$proj_title = $wpdb->get_var("SELECT proj_title FROM projects WHERE proj_id=$proj_id");
			$wpdb->delete('user_actions', array( 'proj_title' => $proj_title ));

			$wpdb->delete('projects', array( 'proj_id' => $proj_id ));
			//$wpdb->delete('user_actions', array( 'proj_id' => $proj_id ));
		}

      	header("Location: {$_SERVER['HTTP_REFERER']}");
	}
	die();
?>
