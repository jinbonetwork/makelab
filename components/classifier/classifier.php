<?php
if(!class_exists('ML_Classifier')):
class ML_Classifier {
	var $component_slug = 'classifier';
	var $component_root = '';
	var $file_path = '';
	var $url_base = '';
	private static $instance;
	public static function instance(){
		if(is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function __construct() {
		$this->component_root = ml_get_app()->component_base.'/'.$this->component_slug;
		$this->file_path = $this->component_root.'/'.basename(__FILE__);
		$this->url_base = untrailingslashit(get_stylesheet_directory_uri().'/'.ml_get_app()->component_dir_name.'/'.$this->component_slug);
		if(defined('TTFMAKE_VERSION')&&true===version_compare(TTFMAKE_VERSION,'1.0.9','>=')){
			require_once $this->component_root.'/section.php';
		}
	}
}
endif;

if(!function_exists('ml_get_classifier')):
function ml_get_classifier() {
	return ML_Classifier::instance();
}
endif;

ml_get_classifier();
?>
