<?php
/** Template Name: Project List */
/**
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package OnePress
 */
?>

<?php
	get_header();
	$layout = onepress_get_layout();
?>

<link rel="stylesheet" type="text/css" href="../wp-content/themes/onepress/assets/css/projectlist.css?ver=<?php echo rand(111,999)?>">

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
						$link = mysqli_connect("localhost", "root", "", "wordpress");

						/* check connection */
						if (mysqli_connect_errno()) {
						    printf("Connect failed: %s\n", mysqli_connect_error());
						    exit();
						}

						$query = "SELECT * FROM projects ORDER by proj_goal-proj_fund";

						if ($result = mysqli_query($link, $query)) {

						    /* fetch associative array */
						    while ($row = mysqli_fetch_assoc($result)) {
						    printf ("<div class=\"item\">
						    <a href=\"http://localhost/wordpress/projinfo/?view=%s\">
						    <div class=\"thumbnail\"><img src=\"../wp-content/uploads/users/%s/%s\"></div>
						    %s <br> by %s
						    </a>
						    <p style=\"overflow: hidden; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical;\">%s</p>
						    </div>", $row["proj_title"],$row["proj_user_ID"],$row["proj_image"],$row["proj_title"],$row["proj_user"],$row["proj_info"]);
						    }

						    /* free result set */
						    mysqli_free_result($result);
						}

						/* close connection */
						mysqli_close($link);
    				?>

				</main><!-- #main -->
			</div><!-- #primary -->

            <?php if ( $layout != 'no-sidebar' ) { ?>
                <?php get_sidebar(); ?>
            <?php } ?>

		</div><!--#content-inside -->
	</div><!-- #content -->

<?php get_footer(); ?>
