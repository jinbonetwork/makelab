var oneApp = oneApp || {};
var overlay, overlayOptions;
var form;
var editor_attributes; 
var editor_data;

(function($) {
	'use strict';
	var disable = false,
	classifierSection = {
		init: function(){
			if(false===disable){
				jQuery('.ttfmake-section').on('click','.ml-classify-section',function(e){
					e.preventDefault();
					var $this = jQuery(this);
					var $section = $this.parents('.ttfmake-section');
					var metadata;
					var view;

					$this.addClass('ml-spinner');
					disable = true;
					metadata = ml.classifier.metadata[$section.attr('id')] || {"classes":"","attributes":"","data":""};

					overlay = jQuery(jQuery('#ml-overlay-template-classifier').text()
						.replace(/{{ml_overlay_id}}/g,$section.attr('id'))
						.replace(/{{ml_classifier_classes}}/g,metadata.classes)
						.replace(/{{ml_classifier_attributes}}/g,metadata.attributes)
						.replace(/{{ml_classifier_data}}/g,metadata.data)
					);
					form = overlay.find('form');
					var editor_attributes_selector = '#ml-overlay-input-control-classifier-attributes';
					var editor_data_selector = '#ml-overlay-input-control-classifier-data';

					if(typeof JSONEditor!='undefined'){
						editor_attributes = new JSONEditor(overlay.find(editor_attributes_selector)[0],{mode:"text"},{});
						editor_data = new JSONEditor(overlay.find(editor_data_selector)[0],{mode:"text"},{});
					}else if(typeof ace!='undefined'){
						editor_attributes = ace.edit(overlay.find(editor_attributes_selector)[0]);
						//editor_attributes.setTheme("ace/theme/github");
						editor_attributes.getSession().setMode("ace/mode/txt");
						editor_data = ace.edit(overlay.find(editor_data_selector)[0]);
						//editor_data.setTheme("ace/theme/github");
						editor_data.getSession().setMode("ace/mode/txt");
					}else{
						editor_attributes = overlay.find(editor_attributes_selector);
						editor_data = overlay.find(editor_data_selector);
					}

					overlayOptions = jQuery.extend({},fancyboxOptions,{
						content:overlay,
						afterLoad:function(){
							overlay.find('.ttfmake-overlay-close').on('click',function(e){
								jQuery.fancybox.close();
							});
							var attributes = metadata.attributes.replace(/&quot;/g,"\"");
							var data = metadata.data.replace(/&quot;/g,"\"");
							if(typeof JSONEditor!='undefined'){
								editor_attributes.set(attributes);
								editor_attributes.focus();
								editor_data.set(data);
								editor_data.focus();
							}else if(typeof ace!='undefined'){
								editor_attributes.setValue(attributes);
								editor_data.setValue(data);
							}else{
								editor_attributes.val(attributes);
								editor_data.val(data);
							}
							$this.removeClass('ml-spinner');
						},
						beforeClose:function(){
							metadata.classes = form.find('#ml-overlay-input-control-classifier-classes').val();
							if(typeof JSONEditor!='undefined'){
								try{
									metadata.attributes = editor_attributes.get();
								}catch(e){
									alert('Invalid metadata format: attributes');
								}
								try{
									metadata.data = editor_data.get();
								}catch(e){
									alert('Invalid metadata format: data');
								}
							}else if(typeof ace!='undefined'){
								metadata.attributes = editor_attributes.getValue();
								metadata.data = editor_data.getValue();
							}else{
								metadata.attributes = editor_attributes.val();
								metadata.data = editor_data.val();
							}
							metadata.attributes = metadata.attributes.replace(/\"/g,"&quot;");
							metadata.data = metadata.data.replace(/\"/g,"&quot;");
							ml.classifier.metadata[$section.attr('id')] = metadata;
							$this.addClass('ml-spinner');
						},
						afterClose:function(){
							/* garbage collection */
							for(var sid in ml.classifier.metadata){
								if(jQuery('#'+sid).length){
									console.log(sid+' is live.');
									console.log(ml.classifier.metadata[sid]);
								}else{
									console.log(sid+' is expired.');
									delete ml.classifier.metadata[sid];
								}
							}
							wp.ajax.send('ml_update_post_meta_json',{
								data:jQuery.extend({},ml.classifier,{"nonce":ml_classify_section.nonce}),
								type:"POST",
								success:function(sent){
									console.log(sent);
									$this.removeClass('ml-spinner');
									disable = false;
								},
								error:function(sent){
									console.log(sent);
									$this.removeClass('ml-spinner');
									disable = false;
								},
							});
						}
					});

					jQuery.fancybox.open(overlayOptions);
				});
			}
		}
	};

	classifierSection.init();
})(jQuery);
