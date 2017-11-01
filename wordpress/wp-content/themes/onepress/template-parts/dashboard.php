<?php
/**Template Name: Dashboard */
/**
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package OnePress
 */
?>

<?php
	$current_user = wp_get_current_user();
	$user_ID = $current_user->ID;

	global $wpdb;
	$info = $wpdb->get_row("SELECT * FROM wp_users WHERE ID=$user_ID", ARRAY_A);
	$info2 = $wpdb->get_row("SELECT meta_value FROM wp_usermeta WHERE user_id=$user_ID AND meta_key='description'", ARRAY_A);

	$user_name = $info['display_name'];
	$user_bio = $info2['meta_value'];
	/* hardcoded for now*/
	$user_image = 'thinking.png';

	$projects = $wpdb->get_results("SELECT * FROM projects WHERE proj_user='$user_name'", ARRAY_A);
	$pledged = $wpdb->get_results("SELECT * FROM user_actions WHERE user='$user_name'", ARRAY_A);

	get_header();
	$layout = onepress_get_layout();
	echo onepress_breadcrumb();
?>

<link rel="stylesheet" type="text/css" href="../wp-content/themes/onepress/assets/css/dashboard.css?ver=<?php echo rand(111,999)?>">

	<div id="content" class="site-content">

		<?php echo onepress_breadcrumb(); ?>

		<div id="content-inside" class="container <?php echo esc_attr( $layout ); ?>">
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">

					<?php echo '<h1 class="name">Welcome back, '.$user_name.'!</h1>' ?>
					<?php
						$img = "/wordpress/wp-content/uploads/users/".$user_image;
						echo '<img src="'.$img.'" alt="'.$user_image.'" class="userimg" />';
					?>
					<?php echo '<a href="http://localhost/wordpress/user-profile/?view='.$user_ID.'">View Profile</a><br />' ?>
					<a href="http://localhost/wordpress/edit-profile">Edit Profile</a><br />
					<a href="http://localhost/wordpress/change-picture">Change Profile Picture</a><br/ >
					<h2 class="label">My Projects</h2>
					<?php
						if ($projects) {
							echo '<table class="projects">';
							foreach ($projects as $project) {
								echo '<tr><td class="title">[edit link] <a href="http://localhost/wordpress/projinfo/?view='.$project['proj_title'].'">'.$project['proj_title'].'</a></td><td class="money">Amount raised: P'.$project['proj_fund'].'</td><td class="goal">Goal amount: P'.$project['proj_goal'].'</td></tr>';
							}
							echo '</table>';
						} else {
							echo "You don't have any projects yet.<br />";
						}
					?>
					<br />
					<h2 class="label">Projects I Pledged In</h2>
					<?php
						if ($pledged) {
							echo '<table class="projects">';
							foreach ($pledged as $project) {
								echo '<tr><td class="title"><a href="http://localhost/wordpress/projinfo/?view='.$project['proj_title'].'">'.$project['proj_title'].'</a></td><td class="money">Amount pledged: P'.$project['fund_given'].'</td>';
								if ($project['user_comment']) {
									echo '<td class="comment">Comment: '.$project['user_comment'].'</td></tr>';
								} else {
									echo '<td class="comment">You did not comment on this pledge.</td></tr>';
								}
							}
							echo '</table>';
						} else {
							echo "You haven't pledged on any projects yet.<br />";
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
