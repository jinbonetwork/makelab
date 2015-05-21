<?php
function makelab_init(){
	require_once ml_get_app()->shared_base.'/jframework.php';	
	JFResources::initMap();
	JFResources::$index['jquery']['js'] = array();
}
add_action('init','makelab_init');

function makelab_enqueue_scripts(){
	wp_enqueue_style('makelab-style',get_stylesheet_directory_uri().'/style.css',array(),ml_get_app()->version);
	wp_enqueue_script('makelab-script',get_stylesheet_directory_uri().'/script.js',array('jquery'),ml_get_app()->version,true);
}
add_action('wp_enqueue_scripts','makelab_enqueue_scripts');

function makelab_footer(){
	JFTemplates::printHead(array(
		'wow',
		'skrollr',
		'jframework',
	));
}
add_action('wp_footer','makelab_footer');

function makelab_admin_enqueue_scripts(){
	wp_enqueue_style('makelab-admin-style',get_stylesheet_directory_uri().'/admin-style.css',array(),ml_get_app()->version);
	wp_enqueue_script('makelab-admin-script',get_stylesheet_directory_uri().'/admin-script.js',array('ttfmake-builder/js/views/section.js'),ml_get_app()->version,true);
}
add_action('admin_enqueue_scripts','makelab_admin_enqueue_scripts');

function makelab_admin_footer(){
	JFTemplates::printHead(array(
		'fancybox',
	));
}
add_action('admin_footer','makelab_admin_footer');
?>
