<style>
div.item {
    vertical-align: top;
    display: inline-block;
    text-align: center;
    width: 360px;
}
img {
    background-color: grey;
}

a {
    text-decoration: none;
}
h2 {
    text-align: center;
}
  /* Other style */
  body {
    padding: 2%;
  }


</style>
<?php
$id       = get_theme_mod( 'onepress_features_id', esc_html__('features', 'onepress') );
$disable  = get_theme_mod( 'onepress_features_disable' ) == 1 ? true : false;
$title    = get_theme_mod( 'onepress_features_title', esc_html__('Features', 'onepress' ));
$subtitle = get_theme_mod( 'onepress_features_subtitle', esc_html__('Why choose Us', 'onepress' ));
if ( onepress_is_selective_refresh() ) {
    $disable = false;
}
$data  = onepress_get_features_data();
if ( !$disable && !empty( $data ) ) {
    $desc = get_theme_mod( 'onepress_features_desc' );
?>
<?php if ( ! onepress_is_selective_refresh() ){ ?>
<section id="<?php if ( $id != '') echo $id; ?>" <?php do_action('onepress_section_atts', 'features'); ?>
         class="<?php echo esc_attr(apply_filters('onepress_section_class', 'section-features section-padding section-meta onepage-section', 'features')); ?>">
<?php } ?>
    <?php do_action('onepress_section_before_inner', 'features'); ?>
    <div class="<?php echo esc_attr( apply_filters( 'onepress_section_container_class', 'container', 'features' ) ); ?>">
        <?php if ( $title ||  $subtitle || $desc ){ ?>
        <div class="section-title-area">
            <?php if ($subtitle != '') echo '<h5 class="section-subtitle">' . esc_html($subtitle) . '</h5>'; ?>
            <?php if ($title != '') echo '<h2 class="section-title">' . esc_html($title) . '</h2>'; ?>
            <?php if ( $desc ) {
                echo '<div class="section-desc">' . apply_filters( 'onepress_the_content', wp_kses_post( $desc ) ) . '</div>';
            } ?>
        </div>
        <?php } ?>
        <div class="section-content">
            <div class="row">
              <?php
              $link = mysqli_connect("localhost", "root", "", "wordpress");

              /* check connection */
              if (mysqli_connect_errno()) {
                  printf("Connect failed: %s\n", mysqli_connect_error());
                  exit();
              }

              $query = "SELECT * FROM projects ORDER by proj_goal-proj_fund";

              if ($result = mysqli_query($link, $query)) {
                  $i=0;
                  /* fetch associative array */
                  while (($row = mysqli_fetch_assoc($result)) && $i<3 ) {
                  printf ("<div class=\"item\">
                  <a href=\"http://localhost/wordpress/projinfo/?view=%s\">
                  <span class=\"icon-image\"><img src=\"../wp-content/uploads/2017/09/%s.jpg\"></span>
                  %s <br> by %s
                  </a>
                  <p style=\"overflow: hidden; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical;\">%s</p>
                  </div>", $row["proj_title"],$row["proj_image"],$row["proj_title"],$row["proj_user"],$row["proj_info"]);
                  $i++;
                  }

                  /* free result set */
                  mysqli_free_result($result);
              }

              /* close connection */
              mysqli_close($link);
              ?>
            </div>
        </div>
    </div>
    <?php do_action('onepress_section_after_inner', 'features'); ?>

<?php if ( ! onepress_is_selective_refresh() ){ ?>
</section>
<?php } ?>
<?php } ?>
