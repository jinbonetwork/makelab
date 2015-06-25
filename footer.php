</div><!--/.wrap-->
</div><!--/#site-content-->

<?php
	ttfmake_maybe_show_site_region( 'footer' );

	/*
	echo implode(PHP_EOL,array(
		'<footer id="global-footer">',
		'<div class="container">',
		'<div id="global-sitemap">', file_get_contents(dirname(__FILE__).'/jframework/html/global-footer/sitemap.html'), '</div>',
		'<div id="global-contact">', file_get_contents(dirname(__FILE__).'/jframework/html/global-footer/contact.html'), '</div>',
		'<div id="global-license">', file_get_contents(dirname(__FILE__).'/jframework/html/global-footer/license.html'), '</div>',
		'</div>',
		'</footer>',
	));
	*/
?>

</div><!--/#site-wrapper-->
<?php wp_footer(); ?>
</body>
</html>
