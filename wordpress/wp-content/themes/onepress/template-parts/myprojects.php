<?php /* Template Name: My Projects Template */

get_header();

$layout = onepress_get_layout();
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
                    
                    <?php
                    
                    $args = array(
                        'child_of' => $post->ID,
                        'title_li' => ''
                    );
                    
                    ?>
                    
					<?php  wp_list_pages($args); ?>

				</main><!-- #main -->
			</div><!-- #primary -->


		</div><!--#content-inside -->
	</div><!-- #content -->

<?php get_footer(); ?>