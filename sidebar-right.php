<?php
/**
 * @package Make
 */

/**
 * Filter the sidebar ID to allow developers to programmatically change the sidebar displayed.
 *
 * @since 1.2.3.
 *
 * @param string    $footer_id    The ID of the current footer being generated.
 */
$sidebar_id = apply_filters( 'make_sidebar_right', 'sidebar-right' );
$sidebar_id = esc_attr( $sidebar_id );
?>
<section id="sidebar-right" class="widget-area sidebar <?php echo $sidebar_id; ?> <?php echo ( is_active_sidebar( $sidebar_id ) ) ? 'active' : 'inactive'; ?>" role="complementary">
<div class="wrap">
	<?php if ( ! dynamic_sidebar( $sidebar_id ) ) : ?>
		&nbsp;
	<?php endif; ?>
</div><!--/.wrap-->
</section>
