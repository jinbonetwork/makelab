<?php
/**
 * @package Make
 */


/**
 * Allow toggling of the footer credit.
 *
 * @since 1.2.3.
 *
 * @param bool    $show    Whether or not to show the footer credit.
 */
$footer_text   = get_theme_mod( 'footer-text', false );
$footer_credit = apply_filters( 'make_show_footer_credit', true );
?>

<?php
if($footer_text || ttfmake_is_preview()):
	echo implode(PHP_EOL,array(
		'<div class="footer-text">',
		ttfmake_sanitize_text($footer_text),
		'</div>',
	));
endif;
?>

<?php if($footer_credit): ?>
<div class="site-info">
	<span class="theme-name">Make: A WordPress template</span>
	<span class="theme-by"><?php _ex( 'by', 'attribution', 'make' ); ?></span>
	<span class="theme-author">
		<a title="The Theme Foundry <?php esc_attr_e( 'homepage', 'make' ); ?>" href="https://thethemefoundry.com/">
			The Theme Foundry
		</a>
	</span>
</div>
<?php endif; ?>
