<?php
/**
 * @package Make
 */
$current_view = ttfmake_get_view();
if(ml_get_app()->get_date_option($current_view)!='none'):
	$published_date = ml_get_app()->get_published_date();
	$published_date = $published_date&&!is_singular()?'<a href="'.get_permalink().'" rel="bookmark">'.$published_date.'</a>':$published_date;
	$modified_date = ml_get_app()->get_modified_date();
?>
<time class="entry-date published" datetime="<?php the_time('c'); ?>"><label class="date"><?php _e('Published date',TEXTDOMAIN); ?></label><span class="split">:</span><span class="date"><?php echo $published_date; ?></span></time>
<?php
$show_modified_date_key = "layout-{$current_view}-post-date-show-modified-date";
if(get_theme_mod($show_modified_date_key,ttfmake_get_default($show_modified_date_key))):
?>
<time class="entry-date modified" datetime="<?php the_modified_time('c'); ?>"><label class="date"><?php _e('Modified date',TEXTDOMAIN); ?></label><span class="split">:</span><span class="date"><?php echo $modified_date; ?></span></time>
<?php
endif;
endif;
?>
