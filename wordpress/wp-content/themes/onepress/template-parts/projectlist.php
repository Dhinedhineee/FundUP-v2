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
<style>
#myInput {
    width: 100%; /* Full-width */
    font-size: 16px; /* Increase font-size */
    border: 1px solid #ddd; /* Add a grey border */
		margin-bottom: 10px;
}

#myUL {
    /* Remove default list styling */
    list-style-type: none;
    padding: 0;
    margin: 0;
		overflow: hidden;
}

#myUL li{
	float:left;
	margin: 10px;
	height: 279px;
}

#myUL li a {
    text-decoration: none; /* Remove default text underline */
    display: block; /* Make it into a block element to fill the whole list */
}

#myUL li a:hover:not(.header) {
    background-color: #eee; /* Add a hover effect to all links, except for headers */
}
</style>
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
					<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for projects..">
					<ul id="myUL">
					<?php
						$link = mysqli_connect("localhost", "root", "", "wordpress");

						/* check connection */
						if (mysqli_connect_errno()) {
						    printf("Connect failed: %s\n", mysqli_connect_error());
						    exit();
						}

						$query = "SELECT * FROM projects a JOIN wp_users b WHERE b.display_name = a.proj_user AND b.suspended = 0 ORDER by a.proj_goal-a.proj_fund";

						if ($result = mysqli_query($link, $query)) {

						    /* fetch associative array */
						    while ($row = mysqli_fetch_assoc($result)) {
						    printf ("<li class=\"item\">
						    <a href=\"http://localhost/wordpress/projinfo/?view=%s\">
						    <div class=\"thumbnail\"><img src=\"../wp-content/uploads/users/%s/%s\"></div>
						    %s <br> by %s
						    </a>
						    <p style=\"overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;\">%s</p>
						    </li>", $row["proj_id"],$row["proj_user_ID"],$row["proj_image"],stripcslashes($row["proj_title"]),$row["proj_user"],stripcslashes($row["proj_info"]));
						    }

						    /* free result set */
						    mysqli_free_result($result);
						}

						/* close connection */
						mysqli_close($link);
    				?>
					</ul>
						<script>
function myFunction() {
    // Declare variables
    var input, filter, ul, li, a, i;
    input = document.getElementById('myInput');
    filter = input.value.toUpperCase();
    ul = document.getElementById("myUL");
    li = ul.getElementsByTagName('li');

    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}
</script>

				</main><!-- #main -->
			</div><!-- #primary -->

            <?php if ( $layout != 'no-sidebar' ) { ?>
                <?php get_sidebar(); ?>
            <?php } ?>

		</div><!--#content-inside -->
	</div><!-- #content -->

<?php get_footer(); ?>
