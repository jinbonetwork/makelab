<?php
/**
 * @package Make
 */

// Footer Options
$jframework_footer = get_theme_mod('footer-jframework-footer',false);
$footer_text   = get_theme_mod( 'footer-text', false );
$footer_credit = apply_filters( 'make_show_footer_credit', true );
$footer_layout = (int) get_theme_mod( 'footer-layout', ttfmake_get_default( 'footer-layout' ) );
$sidebar_count = (int) get_theme_mod( 'footer-widget-areas', ttfmake_get_default( 'footer-widget-areas' ) );
$social_links  = ttfmake_get_social_links();
$show_social   = (int) get_theme_mod( 'footer-show-social', ttfmake_get_default( 'footer-show-social' ) );

// Test for enabled sidebars that contain widgets
$has_active_sidebar = false;
if ( $sidebar_count > 0 ) {
	$i = 1;
	while ( $i <= $sidebar_count ) {
		if ( is_active_sidebar( 'footer-' . $i ) ) {
			$has_active_sidebar = true;
			break;
		}
		$i++;
	}
}
?>

<?php
if($sidebar_count||$footer_text||$footer_credit||$show_social):
?>
<footer id="site-footer" class="site-footer footer-layout-<?php echo esc_attr( $footer_layout ); ?>" role="contentinfo">
	<div class="container">
		<?php // Footer widget areas
		if ( true === $has_active_sidebar ) : ?>
		<div class="footer-widget-container columns-<?php echo esc_attr( $sidebar_count ); ?>">
			<?php
			$current_sidebar = 1;
			while ( $current_sidebar <= $sidebar_count ) :
				get_sidebar( 'footer-' . $current_sidebar );
				$current_sidebar++;
			endwhile; ?>
		</div>
		<?php endif; ?>

		<?php // Footer text and credit line
		get_template_part( 'partials/footer', 'credit' ); ?>

		<?php ttfmake_maybe_show_social_links( 'footer' ); ?>

	</div><!--/.container-->
</footer>
<?php
endif;
?>

<?php
$custom_footer = get_theme_mod('footer-custom-footer-file-path','');
if($custom_footer):
$custom_footer = ml_get_app()->root_dir."/custom/{$custom_footer}";
if(file_exists($custom_footer)){
	echo file_get_contents($custom_footer);
}
endif;
?>

<?php
if($jframework_footer){
	$pattern = array(
		'columns-container' => 'columns-container columns-4',
	);
	echo str_replace(array_keys($pattern),array_values($pattern),JFTemplates::getFooter());
}
?>
