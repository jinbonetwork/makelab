var oneApp = oneApp || {};
var overlay;
var form;
var editor; 

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
					var data;
					var view;

					$this.addClass('ml-spinner');
					disable = true;
					data = ml.classifier.data[$section.attr('id')] || {"classes":"","attributes":""};

					overlay = jQuery(jQuery('#ml-overlay-template-classifier').text()
						.replace(/{{ml_overlay_id}}/g,$section.attr('id'))
						.replace(/{{ml_classifier_classes}}/g,data.classes)
						.replace(/{{ml_classifier_attributes}}/g,data.attributes)
					);
					form = overlay.find('form');
					var editorSelector = '#ml-overlay-input-control-editor-placeholder-classifier';

					if(typeof JSONEditor!='undefined'){
						editor = new JSONEditor(overlay.find(editorSelector)[0],{mode:"text"},{});
					}else if(typeof ace!='undefined'){
						editor = ace.edit(overlay.find(editorSelector)[0]);
						//editor.setTheme("ace/theme/github");
						editor.getSession().setMode("ace/mode/txt");
					}else{
						editor = overlay.find(editorSelector);
					}

					jQuery.fancybox.open(jQuery.extend({},fancyboxOptions,{
						content:overlay,
						afterLoad:function(){
							overlay.find('.ttfmake-overlay-close').on('click',function(e){
								jQuery.fancybox.close();
							});
							var attributes = data.attributes.replace(/&quot;/g,"\"");
							if(typeof JSONEditor!='undefined'){
								editor.set(attributes);
								editor.focus();
							}else if(typeof ace!='undefined'){
								editor.setValue(attributes);
							}else{
								editor.val(attributes);
							}
							$this.removeClass('ml-spinner');
						},
						beforeClose:function(){
							data.classes = form.find('#classes').val();
							if(typeof JSONEditor!='undefined'){
								try{
									data.attributes = editor.get();
								}catch(e){
									alert('Invalid data format');
								}
							}else if(typeof ace!='undefined'){
								data.attributes = editor.getValue();
							}else{
								data.attributes = editor.val();
							}
							data.attributes = data.attributes.replace(/\"/g,"&quot;");
							ml.classifier.data[$section.attr('id')] = data;
							$this.addClass('ml-spinner');
						},
						afterClose:function(){
							/* garbage collection */
							for(var sid in ml.classifier.data){
								if(jQuery('#'+sid).length){
									console.log(sid+' is live.');
									console.log(ml.classifier.data[sid]);
								}else{
									console.log(sid+' is expired.');
									delete ml.classifier.data[sid];
								}
							}
							wp.ajax.send('ml_update_post_meta_json',{
								data:jQuery.extend({},ml.classifier,{"nonce":mlClassifySection.nonce}),
								type:"POST",
								success:function(data){
									console.log(data);
									$this.removeClass('ml-spinner');
									disable = false;
								},
								error:function(data){
									console.log(data);
									$this.removeClass('ml-spinner');
									disable = false;
								},
							});
						}
					}));

					/*
					// Only proceed if duplication is not currently disabled
					if (false === disable) {
						// Activate the spinner
						$this.addClass('ml-spinner');
						disable = true;

						wp.ajax.send( 'ttf_duplicate_section', {
							success: function(data) {
								if (data.result && 'success' === data.result && data.section) {
									$appendedSection = $(data.section);
									$appendedSection.appendTo($stage);

									// Init the views
									view = oneApp.initAllViews($appendedSection);

									// Scroll to the content
									oneApp.scrollToAddedView(view);

									// Register the section with the sortable order field
									oneApp.addOrderValue(view.model.get('id'), oneApp.cache.$sectionOrder);

									// Initiate sortables
									if ('text' === sectionType) {
										oneApp.initializeTextColumnSortables(view);
										duplicatorSection.initFrames(view);
									}
								} else {
									duplicatorSection.handleError(data, $this);
								}

								// Remove the spinner
								$this.removeClass('ml-spinner');
								disable = false;
							},
							error: function(data) {
								duplicatorSection.handleError(data, $this);

								// Remove the spinner
								$this.removeClass('ml-spinner');
								disable = false;
							},
							data: {
								nonce: ttfmpDuplicateSection.nonce,
								data: $('#post').serialize(),
								sectionType: sectionType,
								id: $el.attr('data-id')
							}
						});
					}
					*/
				});
			}
		}
	};

	classifierSection.init();
})(jQuery);
