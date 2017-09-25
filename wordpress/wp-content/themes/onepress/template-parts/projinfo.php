<?php
/*
Template Name: Project Info Template
*/

/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package OnePress
 */

get_header();
$layout = onepress_get_layout();
if (isset($_GET['view'])){
 		$proj_name = $_GET['view'];
}
?>

<?php 
#THIS IS THE STANDARD LAYOUT FILE PREVIEW FOR PROJECT INFORMATIONS
global $proj_user, $proj_name, $proj_info, $proj_pic;
$proj_name = "An Airplane Up Above the Sky";
$proj_user = "Dhinedhineee";
$proj_fund = "99999999999";
$proj_raised = "60511971551912530";
$proj_info = "
				*This is the standard layout file preview for the display of the project information module. No copyright intended on the picture and the poem used in this random page.

				I that am lost, oh who will find me?
				Deep down below the old beech tree
				Help succour me now the east winds blow
				Sixteen by six, brother, and under we go!

				Without your love, he’ll be gone before
				Save pity for strangers, show love the door.
				My soul seek the shade of my willow’s bloom
				Inside, brother mine –
				Let Death make a room.

				Be not afraid to walk in the shade
				Save one, save all, come try!
				My steps – five by seven
				Life is closer to Heaven
				Look down, with dark gaze, from on high.

				Before he was gone – right back over my (h)ill
				Who now will find him?
				Why, nobody will
				Doom shall I bring to him, I that am queen
				Lost forever, nine by nineteen.

				(Poem by Eurus H.)
			";
$proj_info = str_replace("\n", "<br>", $proj_info);
?>

<?php echo onepress_breadcrumb(); ?>
<link href="../wp-content/themes/onepress/assets/css/projstyles.css" type="text/css" rel="stylesheet" />

	<div class="goal">		
		<div class="contentgoal">
				<?php 
					global $proj_user, $proj_name, $proj_info, $proj_pic;
					if(IsSet($proj_name))	echo "<p><h2>$proj_name</h2></p>";
					else 					echo "<h2>Project name not found</h2>";
					if(IsSet($proj_user))	echo "<p><h4>by $proj_user</h4></p>";
					else 					echo "<p><h4>User not found</h4></p>";
					if(IsSet($proj_pic))	echo "<img src=\"localhost/wordpress/wp-content/uploads/2017/09/$proj_pic\" title=\"The proj. photo\" id=\"contentimg\" />";
					else 					echo "<img src=\"../wp-content/uploads/2017/09/banner.jpg\" title=\"The proj. photo\" id=\"contentimg\" />";
					if(IsSet($proj_info))	echo "<p>$proj_info</p>";
					else 					echo "<p>Project information not found</p>";
				?>
		</div>
		
		<div class="sidebarprojinfo">
			<div class="asidegoal">
				<p style="color:black"><b>Goal PHP</b></p>
				<?php 
					global $proj_fund;
					if(IsSet($proj_fund))	{echo "<span style='color:black'>P</span>";
											echo "<span style='float:right'>$proj_fund</span>";}
					else 					echo "<p style='color:black'>Goal amount not set</p>";
				?>		
				<p style="color:black"><br><b>Raised PHP</b></p>
				<?php 
					global $proj_raised;
					if(IsSet($proj_raised))	{echo "<span style='color:black'>P</span>";
											echo "<span style='float:right'>$proj_raised</span>";}
					else 					echo "<p style='color:black'>Amount raised not defined</p>";
				?>
			</div>
			<br><br>
			<div class="asidedonor">
				<h2 class="widget-title" style="font-weight: 700;font-size: 15px;">WANT TO DONATE?</h2>
				<hr>
				<h5>DONORS LIST</h5>
				<ul>
					<li>S.H</li>
					<li>M.H</li>
					<li>Anonymous</li>
				</ul>
			</div>
		</div>
	</div>
	
	<footer style="clear:both;display: block">
	<?php get_footer();?>
	</footer>
