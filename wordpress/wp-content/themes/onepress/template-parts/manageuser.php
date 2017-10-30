<?php
/**Template Name: Manage User */
/**
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package OnePress
 */
?>

<?php
	$current_user = wp_get_current_user();
	$user_ID = $current_user->ID;
	get_header();
	$layout = onepress_get_layout();
	echo onepress_breadcrumb();
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
					<?php echo '<a href="http://localhost/wordpress/user-profile/?view='.$user_ID.'">View Profile</a><br />' ?>
					<?php echo '<a href="http://localhost/wordpress/edit-profile">Edit Profile</a><br />' ?>

				</main>
			</div>

            <?php if ( $layout != 'no-sidebar' ) { ?>
                <?php get_sidebar(); ?>
            <?php } ?>

		</div>
	</div>

<?php get_footer(); ?>
