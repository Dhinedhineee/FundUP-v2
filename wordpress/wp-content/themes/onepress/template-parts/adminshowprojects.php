<?php
/**Template Name: Admin Show Projects */
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

	global $wpdb;
	$projects = $wpdb->get_results("SELECT * FROM projects", ARRAY_A);

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
							echo '<a href="http://localhost/wordpress/admin-dashboard"><< Back</a><br /><br />';
							echo '<h2 class="label">List of Projects</h2>';
							if ($projects) {
								echo '<table class="display">';
								foreach ($projects as $project) {
									echo '<tr>
											<td class="name">
												'.stripcslashes($project['proj_title']).' by ' .$project['proj_user']. '
											</td>
											<td class="options">
												<a href="http://localhost/wordpress/projinfo/?view=' .$project['proj_id']. '">[View]</a>
											</td>
											<td class="options">
												<form method="post" action="/wordpress/wp-content/themes/onepress/template-parts/delete.php">
													<input type="hidden" name="ID" value="'.$project['proj_id'].'" />
													<input type="hidden" name="type" value="project" />
													<input type="submit" value="[Delete]" />
												</form>
											</td>
										</tr>';
								}
								echo '</table>';
							} else {
								echo 'There are no projects yet.<br />';
							}
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