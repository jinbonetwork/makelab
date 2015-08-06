<?php
//-----------------------------------------------------------------------------
//	Load Core Library
//-----------------------------------------------------------------------------
define('TEXTDOMAIN','makelab');
require_once dirname(__FILE__).'/jframework/jframework.php';

//-----------------------------------------------------------------------------
//	Build Main Object
//-----------------------------------------------------------------------------
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

	var $date_structure = '<span class="date-format date-format-date">%date%</span>';
	var $time_structure = '<span class="date-format date-format-time">%time%</span>';

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
			'webfonts',
			'jframework',
			'maniprop.js',
			'lazydo.js',
			'turning-sentences.js',
			'ml',
		);
	}

	//---------------------------------------------------------------------------
	//	Utilities
	//---------------------------------------------------------------------------
	public function array_insert($items,$key_position=null,$value_position=null,$insert_value,$insert_key=null){
		$result = array();
		foreach($items as $item_key => $item_value){
			if(is_string($item_key)){
				$result[$item_key] = $item_value;
			}else{
				$result[] = $item_value;
			}
			if($item_key==$key_position||$item_value==$value_position){
				if(is_string($insert_key)){
					$result[$insert_key] = $insert_value;
				}else{
					$result[] = $insert_value;
				}
			}
		}
		return $result;
	}

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
	//	Resources
	//---------------------------------------------------------------------------
	public function test_resources(){
		global $wp_styles;
		print_r($wp_styles->registered);
	}

	public function get_resource_map(){
		$map_path = defined(ML_MAP_PATH)? ML_MAP_PATH : $this->shared_base.'/data/resources.map.json';
		$map = file_exists($map_path)? json_decode(file_get_contents($map_path),true) : array();

		$map_patch_path = defined(ML_MAP_PATCH_PATH)? ML_MAP_PATCH_PATH : null;
		$map_patch = file_exists($map_patch_path)? json_decode(file_get_contents($map_patch_path),true) : array();

		$map = array_merge($map,$map_patch);
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

	//-----------------------------------------------------------------------------
	//	Localization
	//-----------------------------------------------------------------------------
	public function load_textdomain_mofile($mofile,$domain){
		$locale = get_locale();
		switch($domain):
			case 'make':
				$alt_mofile = dirname(__FILE__)."/languages/{$domain}-{$locale}.mo";
				if(defined('TTFMP_VERSION')) call_user_func(array($this,'copy_plugin_mofile'));
			break;
			default:
				$alt_mofile = null;
			break;
		endswitch;
		return file_exists($alt_mofile)?$alt_mofile:$mofile;
	}

	public function copy_plugin_mofile(){
		$locale = get_locale();
		$from = $this->root_dir.'/languages';
		$to = WP_LANG_DIR;
		$domains = array(
			'make-plus' => "{$to}/plugins",
		);
		foreach($domains as $domain => $dirname){
			$mofile = "{$domain}-{$locale}.mo";
			$source = "{$from}/{$mofile}";
			$target = "{$dirname}/{$mofile}";
			if((file_exists($source)&&!file_exists($target))||filemtime($source)>$filemtime($target)){
				if(!file_exists($dirname)){
					mkdir($dirname,0777,true);
				}
				if(!file_exists($target)||filemtime($source)>filemtime($target)){
					copy($source,$target);
					chmod($source,0777);
				}
			}
		}
	}

	//---------------------------------------------------------------------------
	//	Make -- Admin
	//---------------------------------------------------------------------------
	public function tiny_mce_before_init($settings=array()){
		$style_formats = array(
			array(
				'title' => __('Alert',TEXTDOMAIN),
				'block' => 'div',
				'classes' => 'alert alert-success',
				'wrap' => true,
			),
			array(
				'title' => __('Information',TEXTDOMAIN),
				'block' => 'div',
				'classes' => 'alert alert-info',
				'wrap' => true,
			),
			array(
				'title' => __('Warning',TEXTDOMAIN),
				'block' => 'div',
				'classes' => 'alert alert-warning',
				'wrap' => true,
			),
			array(
				'title' => __('Danger',TEXTDOMAIN),
				'block' => 'div',
				'classes' => 'alert alert-danger',
				'wrap' => true,
			),
		);
		$settings['style_formats'] = json_encode(array_merge(json_decode($settings['style_formats']),$style_formats));
		return $settings;
	}

	//---------------------------------------------------------------------------
	//	Make -- Customizer
	//---------------------------------------------------------------------------
	public function make_setting_defaults($defaults){
		return array_merge($defaults,array(
			'header-bar-menu-mobile-label' => __('Quicklinks',TEXTDOMAIN),
			'footer-jframework-footer' => false,
			'footer-custom-footer-file-path' => '',
			'layout-blog-post-date-show-modified-date' => false,
			'layout-archive-post-date-show-modified-date' => false,
			'layout-search-post-date-show-modified-date' => false,
			'layout-post-post-date-show-modified-date' => false,
			'layout-page-post-date-show-modified-date' => false,
		));
	}

	public function make_customizer_general_sections($general_sections){
		$theme_prefix = 'ml_';
		$panel = 'ttfmake_general';

		// header bar menu mobile label
		$general_sections['labels']['options'] = array_merge(array(
			'header-bar-menu-mobile-label' => array(
				'setting' => array(
					'sanitize_callback' => 'esc_html',
					'theme_supports' => 'menus',
					'transport' => 'postMessage',
				),
				'control' => array(
					'label' => __('Header Bar Menu Label',TEXTDOMAIN),
					'description' => __('Resize your browser window to preview the mobile menu label.','make'),
					'type' => 'text',
				),
			),
		),$general_sections['labels']['options']);

		// jframework footer switch
		$general_sections = array_merge($general_sections,array(
			'footer-jframework-footer' => array(
				'panel' => $panel,
				'title' => __('jFramework Footer',TEXTDOMAIN),
				'description' => __('On/off embeded global footer',TEXTDOMAIN),
				'options' => array(
					'footer-jframework-footer' => array(
						'setting' => array(
							'sanitize_callback' => 'absint',
						),
						'control' => array(
							'type' => 'checkbox',
							'label' => sprintf(
								__('Use %1$s',TEXTDOMAIN),
								__('jFramework footer',TEXTDOMAIN)
							),
							'description' => sprintf(
								'<a href="%1$s" target="_blank">%2$s</a>',
								'https://github.com/jinbonetwork/jframework',
								sprintf(
									__('Refer %1$s',TEXTDOMAIN),
									__('Github repository',TEXTDOMAIN)
								)
							)
						),
					),
				),
			),
		));
		return $general_sections;
	}

	public function make_customizer_contentlayout_sections($contentlayout_sections){
		$theme_prefix = 'ml_';
		$panel = 'ttfmake_content-layout';
		$pattern = array('footer','blog','archive','search','post','page');
		foreach($pattern as $scope){
			switch($scope){
			case 'blog':
			case 'archive':
			case 'search':
			case 'post':
			case 'page':
				$section_index = "layout-{$scope}";
				$section_options = $contentlayout_sections[$section_index]['options'];
				$key_position = "layout-{$scope}-post-date-location";
				$insert_key = "layout-{$scope}-post-date-show-modified-date";
				$insert_value	= array(
					'setting' => array(
						'sanitize_callback' => 'absint',
					),
					'control' => array(
						'type' => 'checkbox',
						'label' => __('Show modified date',TEXTDOMAIN),
						'description' => '',
					),
				);
				break;
			case 'footer':
				$section_index = 'footer';
				$section_options = $contentlayout_sections[$section_index]['options'];
				$key_position = 'footer-show-social';
				$insert_key = 'footer-custom-footer-file-path';
				$insert_value = array(
					'setting' => array(
						'sanitize_callback' => 'ttfmake_sanitize_text',
					),
					'control' => array(
						'type' => 'text',
						'label' => __('Custom footer file path',TEXTDOMAIN),
						'description' => __('The file must be located in the <code>custom</code> subdirectory of the theme.',TEXTDOMAIN),
					),
				);
				break;
			}
			$section_options = $this->array_insert($section_options,$key_position,null,$insert_value,$insert_key);
			$contentlayout_sections[$section_index]['options'] = $section_options;
		}

		return $contentlayout_sections;
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

	//-----------------------------------------------------------------------------
	//	Make Plus -- Customizer
	//-----------------------------------------------------------------------------
	public function perpage_allowed_keys($allowed_keys){
		$scopes = array('post','page');
		foreach($scopes as $scope){
			$view_keys = $allowed_keys[$scope];
			$value_position = "layout-{$scope}-post-date-location";
			$insert_value = "layout-{$scope}-post-date-show-modified-date";
			$allowed_keys[$scope] = $this->array_insert($view_keys,null,$value_position,$insert_value);
		}
		return $allowed_keys;
	}

	//-----------------------------------------------------------------------------
	//	Makelab -- Components
	//-----------------------------------------------------------------------------
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
	//	Makelab -- Custom Fields
	//---------------------------------------------------------------------------
	public function add_meta_boxes(){
		/*
		$screens = array('post','page');
		foreach($screen as $id => $data){
		}
		*/
	}

	//---------------------------------------------------------------------------
	//	Makelab -- Template Tools
	//---------------------------------------------------------------------------
	public function get_date_option($view=''){
		$view = $view?$view:ttfmake_get_view();
		$key = "layout-{$view}-post-date";
		$value = ttfmake_sanitize_choice(get_theme_mod($key,ttfmake_get_default($key)),$key);
		return $value;
	}

	public function get_date_string($d,$u,$alt=''){
		if($alt) return $alt;
		$option = $this->get_date_option();
		if($option=='relative'){
			$d = sprintf(_x('%s ago','time period','make'),human_time_diff($u,current_time('timestamp')));
		}
		return $d;
	}

	//---------------------------------------------------------------------------
	//	Makelab -- Template Hooks
	//---------------------------------------------------------------------------
	public function the_time($d=''){
		global $post;
		$d = $this->get_date_string($d,get_the_time('U'),get_post_meta($post->ID,'published_date',true));
		return $d;
	}

	public function the_modified_time($d=''){
		global $post;
		$d = $this->get_date_string($d,get_the_modified_time('U'),get_post_meta($post->ID,'modified_date',true));
		return $d;
	}

	public function the_content($content){
		$pattern = array(
		);
		$content = str_replace(array_keys($pattern),array_values($pattern),$content);
		return $content;
	}

	//---------------------------------------------------------------------------
	//	Makelab -- Template Tags
	//---------------------------------------------------------------------------
	public function get_published_date($d='datetime'){
		$date = str_replace('%date%',get_the_time(get_option('date_format')),$this->date_structure);
		$time = str_replace('%time%',get_the_time(get_option('time_format')),$this->time_structure);
		$datetime = "{$date} {$time}";
		$markup = $$d?$$d:str_replace('%date%',get_the_time($d),$this->date_structure);
		return apply_filters('the_time',$markup);
	}

	public function the_published_date($d='datetime'){
		echo $this->get_published_date($d);
	}

	public function get_modified_date($d='datetime'){
		$date = str_replace('%date%',get_the_modified_time(get_option('date_format')),$this->date_structure);
		$time = str_replace('%time%',get_the_modified_time(get_option('time_format')),$this->time_structure);
		$datetime = "{$date} {$time}";
		$markup = $$d?$$d:str_replace('%date%',get_the_modified_time($d),$this->date_structure);
		return $markup;
	}

	public function the_modified_date($d='datetime'){
		echo $thic->get_modified_date($d);
	}

	//---------------------------------------------------------------------------
	//	Constructor
	//---------------------------------------------------------------------------
	public static $instance;

	public static function instance(){
		if(is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init(){
		if('make'===get_template()){
			$this->passive = false;
		}

		// Resources
		add_action('after_setup_theme',array($this,'register_resources'));
		add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
		add_action('admin_enqueue_scripts',array($this,'admin_enqueue_scripts'));
		add_filter('script_loader_tag',array($this,'filter_resource_tag'),0,2);
		add_filter('style_loader_tag',array($this,'filter_resource_tag'),0,2);

		// Localization
		add_filter('load_textdomain_mofile',array($this,'load_textdomain_mofile'),999999999,2);
		load_child_theme_textdomain(TEXTDOMAIN,$this->root_dir.'/languages');

		// Make
		add_filter('make_setting_defaults',array($this,'make_setting_defaults'));
		add_filter('make_customizer_general_sections',array($this,'make_customizer_general_sections'));
		add_filter('make_customizer_contentlayout_sections',array($this,'make_customizer_contentlayout_sections'));
		add_filter('make_css_add',array($this,'make_css_add'));

		// Make Plus
		add_filter('ttfmp_perpage_allowed_keys',array($this,'perpage_allowed_keys'));

		// Makelab
		add_action('after_setup_theme',array($this,'load_components'));
		add_action('admin_init',array($this,'admin_init'));
		add_filter('add_meta_boxes',array($this,'add_meta_boxes'));

		// Template Tags
		add_filter('the_time',array($this,'the_time'),999999999);
		add_filter('the_modified_time',array($this,'the_modified_time'),999999999);
	}

	public function admin_init(){
		add_editor_style("{$url_base}/css/editor.css");
		add_filter('tiny_mce_before_init',array($this,'tiny_mce_before_init'));
	}
}
endif;

//-----------------------------------------------------------------------------
//	Singleton Loader
//-----------------------------------------------------------------------------
if(!function_exists('ml_get_app')):
function ml_get_app(){
	return ML_App::instance();
}
endif;

//-----------------------------------------------------------------------------
//	Initiation
//-----------------------------------------------------------------------------
$custom_functions = ml_get_app()->root_dir.'/custom/functions.php';
if(file_exists($custom_functions)){
	require_once $custom_functions;
}
ml_get_app()->init();
?>
