<?php
if(!class_exists('ML_Section_Classifier')):
class ML_Section_Classifier {
	private static $instance;
	private static $editor = 'ace'; // ace, jsoneditor
	public static function instance(){
		if(is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function __construct() {
		add_action('admin_enqueue_scripts',array($this,'print_data'));
		add_action('admin_enqueue_scripts',array($this,'admin_enqueue_scripts'));
		add_action('admin_enqueue_scripts',array($this,'print_overlay_template'));
		add_action('ttfmake_builder_section_footer_links',array($this,'builder_section_footer_links'));
		add_action('wp_ajax_ml_update_post_meta_json',array($this,'save_data'));
		add_action('wp_ajax_no_priv_ml_update_post_meta_json',array($this,'save_data'));
		add_action('the_content',array($this,'classify_section'));
	}
	public function admin_enqueue_scripts($hook_suffix){
		if(!in_array($hook_suffix,array('post.php','post-new.php'))||'page'!==get_post_type()){
			return;
		}
		global $jf_resources;
		$jf_resources = array_merge($jf_resources,array(
			'fancybox',
		));

		switch(self::$editor){
			case 'ace':
				$editorDependancy = array(
					'css' => array(),
					'js' => array('ace'),
				);
			break;
			case 'jsoneditor':
				$editorDependancy = array(
					'css' => array('jsoneditor'),
					'js' => array('jsoneditor'),
				);
			break;
			default:
				$editorDependancy = array(
					'css' => array(),
					'js' => array(),
				);
			break;
		}

		$scriptDependancy = array_merge(array('jquery','ttfmake-builder','makelab-admin'),$editorDependancy['js']);
		$styleDependancy = array_merge(array(),$editorDependancy['css']);
		wp_enqueue_script('ml-classify-section-utilities',ml_get_classifier()->url_base.'/js/utilities.js',$scriptDependancy,ml_get_app()->version,true);
		wp_enqueue_script('ml-classify-section',ml_get_classifier()->url_base.'/js/section.js',array('ml-classify-section-utilities',),ml_get_app()->version,true);
		wp_localize_script('ml-classify-section','mlClassifySection',array(
			'nonce' => wp_create_nonce('ml-classify-section'),
			'defaultError' => __('An unexpected error occurred.','makelab'),
		));
		wp_enqueue_style('ml-classify-section',ml_get_classifier()->url_base.'/css/section.css',$styleDependancy,ml_get_app()->version,false);
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
	public function save_data(){
  	$nonce = isset($_POST["nonce"])?$_POST["nonce"]:"";
	  $post_ID = isset($_POST["post_ID"])?$_POST["post_ID"]:null;
		$data = isset($_POST["data"])?$_POST["data"]:null;

		if(wp_verify_nonce($nonce,'ml-classify-section')&&$post_ID&&!empty($data)){
			ml_update_post_meta_json($post_ID,'ml_classifier_data',$data);
			wp_send_json_success(__('Data saving success.','makelab'));
		}else{
			wp_send_json_error(__('Invalid data submitted.','makelab'));
		}
	}
	public function print_data($hook_suffix){
		if(!in_array($hook_suffix,array('post.php','post-new.php'))||'page'!==get_post_type()){
			return;
		}
		global $post;
		$meta = ml_get_post_meta_json($post->ID,'ml_classifier_data',true);
		if($meta){
			$meta = json_encode($meta);
		}else{
			$meta = '{}';
		}
		echo <<<DATA
			<script>
				ml.classifier = {
					post_ID: {$post->ID},
					data: {$meta}
				};
			</script>
DATA;
	}
	public function print_overlay_template($hook_suffix){
		if(!in_array($hook_suffix,array('post.php','post-new.php'))||'page'!==get_post_type()){
			return;
		}
		$structure = file_get_contents(dirname(__FILE__).'/overlay.html');
		$pattern = array(
			'%overlay_title%' => __('Classify section','makelab'),
			'%label_input_classes%' => __('Classes','makelab'),
			'%label_input_attributes%' => __('Attributes','makelab'),
		);
		echo str_replace(array_keys($pattern),array_values($pattern),$structure);
	}

	public function classify_section($content){
		global $post;
		$meta = ml_get_post_meta_json($post->ID,'ml_classifier_data',true);
		if(!empty($meta)){
			foreach($meta as $key => $value){
				$section_id = str_replace('ttfmake','builder',$key);
				$attributes = html_entity_decode($value->attributes);
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

function ml_recursive_stripslashes(&$item,$key){
	$item = stripslashes($item);
}
?>
