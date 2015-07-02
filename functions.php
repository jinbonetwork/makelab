<?php
require_once dirname(__FILE__).'/jframework/jframework.php';

if(!class_exists('ML_App')):
class ML_App {

	//---------------------------------------------------------------------------
	//	Environmental variables
	//---------------------------------------------------------------------------
	var $version = '0.9';
	var $passive = true;
	var $root_dir = '';
	var $file_path = '';
	var $component_base = '';
	var $component_dir_name = 'components';
	var $shared_base = '';
	var $shared_dir_name = 'jframework';
	var $url_base = '';
	var $deps_backend = array();
	var $deps_frontend = array();

	//---------------------------------------------------------------------------
	//	Singleton constructor
	//---------------------------------------------------------------------------
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
		$this->shared_url_base = untrailingslashit(get_stylesheet_directory_uri().'/'.$this->shared_dir_name);
		$this->url_base = untrailingslashit(get_stylesheet_directory_uri());
		$this->deps_backend = array(
			'jquery',
			'fancybox',
			'ml',
		);
		$this->deps_frontend = array(
			'jquery',
			//'make',
			'wow.js',
			'skrollr',
			'notosans-subset',
			'notosans-subset-black-and-thin',
			'jframework',
			'maniprop.js',
			'lazydo.js',
			'ml',
		);
	}

	//---------------------------------------------------------------------------
	//	Utilities
	//---------------------------------------------------------------------------
	public function recursive_stripslashes(&$item,$key){
		$item = stripslashes($item);
	}

	public function get_post_meta_json($id,$key){
		$meta = get_post_meta($id,$key,true);
		//return json_decode($meta);
		return json_decode(base64_decode($meta));
	}

	public function update_post_meta_json($id,$key,$value){
		//$meta = json_encode($value);
		$meta = base64_encode(json_encode($value));
		return update_post_meta($id,$key,$meta);
	}

	//---------------------------------------------------------------------------
	//	Initiation
	//---------------------------------------------------------------------------
	public function init(){
		if('make'===get_template()){
			$this->passive = false;
		}
		load_plugin_textdomain('makelab',null,$this->root_dir.'/languages/');
		add_action('after_setup_theme',array($this,'register_resources'));
		add_action('after_setup_theme',array($this,'load_components'));
		add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
		add_action('admin_enqueue_scripts',array($this,'admin_enqueue_scripts'));
		add_filter('script_loader_tag',array($this,'filter_resource_tag'),0,2);
		add_filter('style_loader_tag',array($this,'filter_resource_tag'),0,2);
		add_filter('make_css_add',array($this,'make_css_add'));
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

	//---------------------------------------------------------------------------
	//	Resources
	//---------------------------------------------------------------------------
	public function test_resources(){
		global $wp_styles;
		print_r($wp_styles->registered);
	}

	public function get_resource_map(){
		$map = json_decode(file_get_contents($this->shared_base.'/data/resources.map.json'),true);
		//unset($map['jquery']); // WordPress already has jquery.
		//makelab_test_resources();
		return $map;
	}

	public function filter_resource_tag($tag,$handle){
		if(strpos($tag,'/dummy.')){
			return null;
		}else{
			return $tag;
		}
	}

	public function register_resources(){
		$types = array('css'=>'wp_register_style','js'=>'wp_register_script');

		$map = $this->get_resource_map();
		$url_base = $this->url_base;
		$shared_url_base = $this->shared_url_base;

		foreach($map as $key => $value){
			$version = $value['version']?$value['version']:$this->version;
			$deps = $value['deps'];
			foreach($types as $type => $callback){
				$items = $value[$type];
				$items = !empty($items)?$items:array("{$type}/dummy.{$type}");
				$keyword = '';
				$index = 0;
				foreach($items as $item){
					$eldersister = $keyword?array($keyword):array();
					$keyword = count($items)==$index+1?$key:"{$key}-{$index}";
					$dependancy = array_merge($eldersister,$deps);
					call_user_func($callback,$keyword,"{$shared_url_base}/{$item}",$dependancy,$version);
					$index ++;
				}
			}
		}

		// ace
		wp_register_script('ace',"{$url_base}/contrib/ace/1.1.01/src-min/ace.js",array('jquery'),$this->version);

		// jsoneditor
		wp_register_style('jsoneditor',"{$url_base}/contrib/jsoneditor/4.1.3/dist/jsoneditor.css",array(),$this->version);
		wp_register_script('jsoneditor',"{$url_base}/contrib/jsoneditor/4.1.3/dist/jsoneditor.js",array('jquery'),$this->version);

		// app
		$deps_app = array(
			'jquery',
		);
		wp_register_style('ml',"{$url_base}/css/app.css",$deps_app,$this->version);
		wp_register_script('ml',"{$url_base}/js/app.js",$deps_app,$this->version);

		// admin
		wp_register_style('makelab-admin',"{$url_base}/css/admin.css",$this->deps_backend,$this->version);
		wp_register_script('makelab-admin',"{$url_base}/js/admin.js",array_merge(array('ttfmake-builder/js/views/section.js'),$this->deps_backend),$this->version,true);

		/*
		// parent theme
		wp_register_style('make',"{$url_base}/css/make.css",array(),$this->version);
		wp_register_script('make',"{$url_base}/js/dummy.js",array(),$this->version);
		*/

		// frontend
		wp_register_style('makelab',"{$url_base}/css/style.css",$this->deps_frontend,$this->version);
		wp_register_script('makelab',"{$url_base}/js/script.js",$this->deps_frontend,$this->version,true);
		//makelab_test_resources();

	}

	public function enqueue_custom_resources(){
		$url_base = $this->url_base;
		if(file_exists("{$this->root_dir}/custom/style.css")){
			wp_enqueue_style('makelab-custom',"{$url_base}/custom/style.css",$this->deps_frontend,$this->version);
		}
		if(file_exists("{$this->root_dir}/custom/script.js")){
			wp_enqueue_script('makelab-custom',"{$url_base}/custom/script.js",$this->deps_frontend,$this->version,true);
		}
	}

	public function enqueue_scripts(){
		// dependancies
		foreach($this->deps_frontend as $resource){
			wp_enqueue_style($resource);
			wp_enqueue_script($resource);
		}

		// frontend
		wp_enqueue_style('makelab');
		wp_enqueue_script('makelab');
		//makelab_test_resources();

		// custom
		$this->enqueue_custom_resources();
	}

	public function admin_enqueue_scripts(){
		// dependancies
		foreach($this->deps_backend as $resource){
			wp_enqueue_style($resource);
			wp_enqueue_script($resource);
		}

		// admin
		wp_enqueue_style('makelab-admin');
		wp_enqueue_script('makelab-admin');
		//makelab_test_resources();
	}

	public function make_css_add_selector_patch($selector){
		switch($selector):
		case '.footer-social-links':
			$selector = '.footer-social-links li a';
			break;
		endswitch;
		return $selector;
	}

	public function make_css_add($data){
		$new_selectors = array();
		foreach($data['selectors'] as $selector){
			$new_selectors[] = '#site '.$this->make_css_add_selector_patch($selector);
		}
		$data['selectors'] = $new_selectors;
		return $data;
	}

	public function the_content($content){
		$pattern = array(
		);
		$content = str_replace(array_keys($pattern),array_values($pattern),$content);
		return $content;
	}
}
endif;

//-----------------------------------------------------------------------------
//	Singleton loader
//-----------------------------------------------------------------------------
if(!function_exists('ml_get_app')):
function ml_get_app(){
	return ML_App::instance();
}
endif;
ml_get_app()->init();
?>
