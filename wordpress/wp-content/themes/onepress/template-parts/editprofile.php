<?php
/** Template Name: Edit Profile */
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

	get_header();
	$layout = onepress_get_layout();
?>

<link rel="stylesheet" type="text/css" href="../wp-content/themes/onepress/assets/css/login.css?ver=<?php echo rand(111,999)?>">

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

					<?php echo do_shortcode('[wppb-edit-profile]'); ?>

				</main><!-- #main -->
			</div><!-- #primary -->

            <?php if ( $layout != 'no-sidebar' ) { ?>
                <?php get_sidebar(); ?>
            <?php } ?>

		</div><!--#content-inside -->
	</div><!-- #content -->

<?php get_footer(); ?>
