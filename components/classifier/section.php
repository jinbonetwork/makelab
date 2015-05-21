<?php
if(!class_exists('ML_Section_Classifier')):
class ML_Section_Classifier {
	private static $instance;
	public static function instance(){
		if(is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function __construct() {
		add_action('admin_enqueue_scripts',array($this,'admin_enqueue_scripts'));
		add_action('ttfmake_builder_section_footer_links',array($this,'builder_section_footer_links'));
	}
	public function admin_enqueue_scripts($hook_suffix){
		if(!in_array($hook_suffix,array('post.php','post-new.php'))||'page'!==get_post_type()){
			return;
		}
		wp_enqueue_script(
			'ml-classify-section-utilities',
			ml_get_classifier()->url_base.'/js/utilities.js',
			array(
				'jquery',
				'ttfmake-builder',
			),
			ml_get_app()->version,
			true
		);
		wp_enqueue_script(
			'ml-classify-section',
			ml_get_classifier()->url_base.'/js/section.js',
			array(
				'jquery',
				'ttfmake-builder',
				'ml-classify-section-utilities',
			),
			ml_get_app()->version,
			true
		);
		wp_localize_script(
			'ml-classify-section',
			'mlClassifySection',
			array(
				'nonce' => wp_create_nonce('classify'),
				'defaultError' => __('An unexpected error occurred.','makelab'),
			)
		);
		wp_enqueue_style(
			'ml-classify-section',
			ml_get_classifier()->url_base.'/css/section.css',
			array(),
			ml_get_app()->version
		);
	}
	public function builder_section_footer_links($links){
		$links[0] = array(
			'class' => 'ml-classify-section',
			'href'  => '#',
			'label' => __('Classify','makelab'),
			'title' => __('Classify section','makelab'),
		);

		return $links;
	}
}
endif;

if (!function_exists('ml_get_section_classifier')):
function ml_get_section_classifier(){
	return ML_Section_Classifier::instance();
}
endif;

ml_get_section_classifier();

function ml_recursive_stripslashes(&$item,$key){
	$item = stripslashes($item);
}
?>
