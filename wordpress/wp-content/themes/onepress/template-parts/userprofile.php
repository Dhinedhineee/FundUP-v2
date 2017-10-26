<?php
/**Template Name: User Profile */
/**
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package OnePress
 */
?>

<?php
	if (isset($_GET['view']))		
		$user_ID = htmlspecialchars($_GET['view']);
	else
		$user_ID = NULL;

	global $wpdb;
	$info = $wpdb->get_row("SELECT * FROM wp_users WHERE ID=$user_ID", ARRAY_A);
	/* warning message: user does not exist if info = NULL */

	if(!isset($info)){
		header('Location: http://localhost/wordpress');
		die();
	} else {
		$user_name = $info['display_name'];
		$user_email = $info['user_email'];
		/* hardcoded for now, will set up a form for additional information (e.g biography, other contact info, etc.)*/
		$user_image = 'thinking.png';
	}

	get_header();
	$layout = onepress_get_layout();
	echo onepress_breadcrumb();
?>

<link rel="stylesheet" type="text/css" href="../wp-content/themes/onepress/assets/css/userprofile.css">

	<div id="content" class="site-content">

		<?php echo onepress_breadcrumb(); ?>

		<div id="content-inside" class="container <?php echo esc_attr( $layout ); ?>">
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">

					<?php echo '<h1 class="name">'.$user_name.'</h1><br />' ?>
					<?php
						$img = "/wordpress/wp-content/uploads/users/".$user_image;
						echo '<img src="'.$img.'" alt="'.$user_image.'" class="userimg" />';
					?>
					<?php echo "Email: $user_email<br />" ?>

				</main>
			</div>

            <?php if ( $layout != 'no-sidebar' ) { ?>
                <?php get_sidebar(); ?>
            <?php } ?>

		</div>
	</div>

<?php get_footer(); ?>
