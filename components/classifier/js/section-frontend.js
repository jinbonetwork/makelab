jQuery(document).ready(function(e){
	if(typeof ml.classifier.attributes != 'object'){
		ml.classifier.attributes = {};
	}
	if(typeof ml.classifier.metadata == 'object'){
		var sindex, metadata,data;
		for(sindex in ml.classifier.metadata){
			metadata = ml.classifier.metadata[sindex];
			data = metadata.data;
			if(typeof data == 'string' && data.length > 8){
				data = data.replace(/&quot;/g,'"');
				data = data.replace(/\$this/g,'#'+sindex.replace(/ttfmake/g,'builder'));
				data = data.trim();
				data = JSON.parse(data);
				ml.classifier.attributes = jQuery.extend({},ml.classifier.attributes,data);
			}
		}
		mp.init(ml.classifier.attributes,function(){ld.set_condition('classifier',true);});
	}
});
