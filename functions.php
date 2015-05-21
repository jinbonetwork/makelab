<?php
if(!class_exists('ML_App')):
class ML_App {
	var $version = '0.9';
	var $passive = true;
	var $root_dir = '';
	var $file_path = '';
	var $component_base = '';
	var $component_dir_name = 'components';
	var $shared_base = '';
	var $shared_dir_name = 'jframework';
	var $url_base = '';
	public static $instance;
	public static function instance(){
		if(is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function __construct(){
		$this->root_dir = dirname(__FILE__);
		$this->file_path = $this->root_dir.'/'.basename(__FILE__);
		$this->component_base = $this->root_dir.'/'.$this->component_dir_name;
		$this->shared_base = $this->root_dir.'/'.$this->shared_dir_name;
		$this->url_base = untrailingslashit(get_stylesheet_directory_uri());
	}
	public function init(){
		if('make'===get_template()){
			$this->passive = false;
		}
		load_plugin_textdomain('makelab',null,$this->root_dir.'/languages/');
		add_action('after_setup_theme',array($this,'load_shared_functions'));
		add_action('after_setup_theme',array($this,'load_components'));
	}
	public function load_shared_functions(){
		$file = $this->root_dir.'/resources.php';
		if(file_exists($file)){
			require_once $file;
		}
	}
	public function load_components(){
		$components = array(
			'classifier' => array(
				'slug' => 'classifier',
				'conditions' => array(
					false === $this->passive,
					defined('TTFMAKE_VERSION')&&true===version_compare(TTFMAKE_VERSION,'1.0.6','>='),
				),
			),
		);
		foreach($components as $id => $component){
			if(!in_array(false,$component['conditions'])){
				$file = $this->component_base.'/'.$component['slug'].'/'.$component['slug'].'.php';
				if(file_exists($file)){
					require_once $file;
				}
			}
		}
	}
}
endif;

if(!function_exists('ml_get_app')):
function ml_get_app(){
	return ML_App::instance();
}
endif;

ml_get_app()->init();
?>
