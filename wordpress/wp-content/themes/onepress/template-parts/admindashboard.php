<?php
/**Template Name: Admin Dashboard */
/**
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package OnePress
 */
?>

<?php
	if(!IsSet($_SERVER['HTTP_REFERER'])){
		header('Location: http://localhost/wordpress');
		die();
	}

	$current_user = wp_get_current_user();
	$user_ID = $current_user->ID;

	if ($user_ID != 0) {
		$user_data = get_userdata($user_ID);
		$user_role = implode(', ', $user_data->roles);
	} else {
		$user_role = 'no user';
	}

	get_header();
	$layout = onepress_get_layout();
	echo onepress_breadcrumb();
?>

<link rel="stylesheet" type="text/css" href="../wp-content/themes/onepress/assets/css/admindashboard.css?ver=<?php echo rand(111,999)?>">

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

					<?php
						if ($user_role != 'administrator') {
							redirect('http://localhost/wordpress');
						} else {
							echo '<a href="http://localhost/wordpress/admin-dashboard/users">Show list of users</a><br />';
							echo '<a href="http://localhost/wordpress/admin-dashboard/projects">Show list of projects</a><br />';
							echo '<a href="http://localhost/wordpress/signup">Add a user</a><br />';
                            echo '<a href="http://localhost/wordpress/add-admin">Add an administrator</a><br />';
						}
					?>

				</main><!-- #main -->
			</div><!-- #primary -->

            <?php if ( $layout != 'no-sidebar' ) { ?>
                <?php get_sidebar(); ?>
            <?php } ?>

		</div><!--#content-inside -->
	</div><!-- #content -->
	
<?php get_footer(); ?>

<?php 

function redirect($url){
		$string = '<script type="text/javascript">';
	    $string .= 'window.location = "' . $url . '"';
	    $string .= '</script>';
	    echo $string;
	    die();
	}

?>