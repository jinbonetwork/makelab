<?php
global $jf_resources;
$jf_resources = array();

function makelab_init(){
	require_once ml_get_app()->shared_base.'/jframework.php';	
	JFResources::initMap();
	JFResources::$exception = array('jquery');

	wp_register_script('ace',ml_get_app()->url_base.'/contrib/ace/1.1.01/src-min-noconflict/ace.js',array(),'1.1.01',false);

	wp_register_style('jsoneditor',ml_get_app()->url_base.'/contrib/jsoneditor/4.1.3/dist/jsoneditor.min.css',array(),'4.1.3',false);
	wp_register_script('jsoneditor',ml_get_app()->url_base.'/contrib/jsoneditor/4.1.3/dist/jsoneditor.js',array(),'4.1.3',false);

	wp_register_style('makelab-admin',ml_get_app()->url_base.'/admin-style.css',array(),ml_get_app()->version,false);
	wp_register_script('makelab-admin',ml_get_app()->url_base.'/admin-script.js',array('ttfmake-builder/js/views/section.js'),ml_get_app()->version,true);

	wp_register_style('makelab',ml_get_app()->url_base.'/style.css',array(),ml_get_app()->version,false);
	wp_register_script('makelab',ml_get_app()->url_base.'/script.js',array('jquery'),ml_get_app()->version,true);
}
add_action('init','makelab_init');

function makelab_enqueue_scripts(){
	global $jf_resources;
	$jf_resources = array_merge($jf_resources,array(
		'wow.js',
		'skrollr',
		'jframework',
	));
	wp_enqueue_style('makelab');
	wp_enqueue_script('makelab');
}
add_action('wp_enqueue_scripts','makelab_enqueue_scripts');

function makelab_admin_enqueue_scripts(){
	wp_enqueue_style('makelab-admin');
	wp_enqueue_script('makelab-admin');
}
add_action('admin_enqueue_scripts','makelab_admin_enqueue_scripts');

function makelab_print_footer(){
	global $jf_resources;
	if(!empty($jf_resources)){
		JFTemplates::printHead($jf_resources);
	}
}
add_action('wp_footer','makelab_print_footer');
add_action('admin_footer','makelab_print_footer');
?>
