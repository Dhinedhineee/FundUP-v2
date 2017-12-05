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
	$info2 = $wpdb->get_row("SELECT meta_value FROM wp_usermeta WHERE user_id=$user_ID AND meta_key='description'", ARRAY_A);

	if(!isset($info)){
		header('Location: http://localhost/wordpress');
		die();
	} else {
		$user_name = $info['display_name'];
		$user_email = $info['user_email'];
		$user_website = $info['user_url'];
		$user_bio = $info2['meta_value'];
		$projects = $wpdb->get_results("SELECT * FROM projects WHERE proj_user='$user_name'", ARRAY_A);
		//$user_image = $wpdb->get_var("SELECT filepath FROM wp_wfu_log WHERE userid=$user_ID");
	}

	get_header();
	$layout = onepress_get_layout();
	echo onepress_breadcrumb();
?>

<link rel="stylesheet" type="text/css" href="../wp-content/themes/onepress/assets/css/userprofile.css?ver=<?php echo rand(111,999)?>">

	<div id="content" class="site-content">

		<?php echo onepress_breadcrumb(); ?>

		<div id="content-inside" class="container <?php echo esc_attr( $layout ); ?>">
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">

					<?php echo '<h1>'.$user_name.'</h1>' ?>
					<?php
						//if ($user_image) {
						//	$img = "/wordpress".$user_image;
						//} else {
						//	$img = "/wordpress/wp-content/default/default.png";
						//}
						//echo '<img src="'.$img.'" alt="'.$user_image.'" class="userimg" />';
					?>
					<?php echo "Email: $user_email<br />" ?>
					<?php if ($user_website) echo "Website: $user_website<br /><br />" ?>
					<?php if ($user_bio) echo "<h3>About Me</h3> <div class=\"bio\">$user_bio</div><br />" ?>

					<h3 class="label">Projects</h2>
					<?php
						if ($projects) {
							echo '<table class="projects">';
							foreach ($projects as $project) {
								echo '<tr><td class="title"><a href="http://localhost/wordpress/projinfo/?view='.$project['proj_title'].'">'.$project['proj_title'].'</a></td><td class="money">Amount raised: P'.$project['proj_fund'].'</td><td class="goal">Goal amount: P'.$project['proj_goal'].'</td></tr>';
							}
							echo '</table>';
						} else {
							echo "No projects yet.<br />";
						}
					?>
					
				</main>
			</div>

            <?php if ( $layout != 'no-sidebar' ) { ?>
                <?php get_sidebar(); ?>
            <?php } ?>

		</div>
	</div>

<?php get_footer(); ?>
