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
		add_action('admin_enqueue_scripts',array($this,'print_overlay_template'));
		add_action('admin_enqueue_scripts',array($this,'admin_enqueue_scripts'));
		add_action('admin_footer',array($this,'print_metadata_admin'));
		add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
		add_action('wp_footer',array($this,'print_metadata'));
		add_action('ttfmake_builder_section_footer_links',array($this,'builder_section_footer_links'));
		add_action('wp_ajax_ml_update_post_meta_json',array($this,'save_metadata'));
		add_action('wp_ajax_no_priv_ml_update_post_meta_json',array($this,'save_metadata'));
		add_action('the_content',array($this,'classify_section'));
	}

	public function print_overlay_template($hook_suffix){
		if(!in_array($hook_suffix,array('post.php','post-new.php'))||'page'!==get_post_type()){
			return;
		}
		$structure = file_get_contents(dirname(__FILE__).'/overlay.html');
		$pattern = array(
			'%overlay_title%' => __('Classify Section',TEXTDOMAIN),
			'%label_input_classes%' => __('CSS Classes',TEXTDOMAIN),
			'%label_input_attributes%' => __('HTML Attributes',TEXTDOMAIN),
			'%label_input_data%' => __('Selector-Attribute Data (JSON format)',TEXTDOMAIN),
		);
		echo str_replace(array_keys($pattern),array_values($pattern),$structure);
	}

	public function admin_enqueue_scripts($hook_suffix){
		if(!in_array($hook_suffix,array('post.php','post-new.php'))||'page'!==get_post_type()){
			return;
		}

		$deps_style = array(
			'fancybox',
			'makelab-admin'
		);
		wp_enqueue_style('ml-classify-section',ml_get_classifier()->url_base.'/css/section.css',$deps_style,ml_get_app()->version);
		$deps_script = array(
			'jquery',
			'fancybox',
			'ace',
			'ttfmake-builder',
			'makelab-admin'
		);
		wp_enqueue_script('ml-classify-section-utilities',ml_get_classifier()->url_base.'/js/utilities.js',$deps_script,ml_get_app()->version,true);
		wp_enqueue_script('ml-classify-section',ml_get_classifier()->url_base.'/js/section.js',array('ml-classify-section-utilities',),ml_get_app()->version,true);

		wp_localize_script('ml-classify-section','ml_classify_section',array(
			'nonce' => wp_create_nonce('ml-classify-section'),
			'defaultError' => __('An unexpected error occurred.',TEXTDOMAIN),
		));

		//makelab_test_resources();
	}

	public function enqueue_scripts(){
		wp_enqueue_script('ml-classify-section-frontend',ml_get_classifier()->url_base.'/js/section-frontend.js',array('jquery','makelab'),ml_get_app()->version,true);
	}

	public function builder_section_footer_links($links){
		$links[0] = array(
			'class' => 'ml-classify-section',
			'href'  => '#',
			'label' => __('Classify',TEXTDOMAIN),
			'title' => __('Classify section',TEXTDOMAIN),
		);
		return $links;
	}

	public function save_metadata(){
  	$nonce = isset($_POST["nonce"])?$_POST["nonce"]:"";
	  $post_ID = isset($_POST["post_ID"])?$_POST["post_ID"]:null;
		$metadata = isset($_POST["metadata"])?$_POST["metadata"]:null;

		if(wp_verify_nonce($nonce,'ml-classify-section')&&$post_ID&&!empty($metadata)){
			ml_get_app()->update_post_meta_json($post_ID,'ml_classifier_metadata',$metadata);
			wp_send_json_success(__('Data saving success.',TEXTDOMAIN));
		}else{
			wp_send_json_error(__('Invalid data submitted.',TEXTDOMAIN));
		}
	}

	public function print_metadata(){
		global $post;
		$metadata = ml_get_app()->get_post_meta_json($post->ID,'ml_classifier_metadata',true);
		if($metadata){
			$metadata = json_encode($metadata);
		}else{
			$metadata = '{}';
		}
		echo <<<DATA
			<script>
				ml.classifier = {
					post_ID: {$post->ID},
					metadata: {$metadata}
				};
			</script>
DATA;
	}

	public function print_metadata_admin($hook_suffix){
		$this->print_metadata();
	}

	public function classify_section($content){
		global $post;
		$meta = ml_get_app()->get_post_meta_json($post->ID,'ml_classifier_metadata',true);
		if(!empty($meta)){
			foreach($meta as $key => $value){
				$section_id = str_replace('ttfmake','builder',$key);
				$attributes = html_entity_decode($value->attributes);
				$data = html_entity_decode($value->data);
				$classes = $value->classes;
				$content = str_replace("id=\"{$section_id}\" class=\"","id=\"{$section_id}\" {$attributes} class=\"{$classes} ",$content); 
			}
		}
		return $content;
	}
}
endif;

if (!function_exists('ml_get_section_classifier')):
function ml_get_section_classifier(){
	return ML_Section_Classifier::instance();
}
endif;

ml_get_section_classifier();
?>
