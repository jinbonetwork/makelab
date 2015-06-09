var mp = new Maniprop();
var ld = new Lazydo();
var _wow = new WOW();
var _skrollr;
jQuery(document).on('lazydo',function(e){
	_wow.init();
	//_skrollr = skrollr.init();
});

/*
var $wow = false, $containers;

jQuery(document).ready(function(e){
	$containers = jQuery('[data-wow-item-delay-step]');
	if($containers.length){
		$containers.each(function(index){
			var $container = jQuery(this);
			var $items = $container.find('.builder-gallery-item,.builder-text-column');
			var classes = $container.attr('class').replace(/ builder\-[^ ]+/g,"");
			var delay = $container.attr('data-wow-delay');
			var item_delay_step = parseInt($container.attr('data-wow-item-delay-step').replace("ms","00").replace("s","000"));
			var data;

			delay = delay || 0;
			delay = parseInt(delay) || 0;

			for(var name in $container.data()){
				if(name.search(/^wow/)){
					newName = 'data-'+name.replace(/([A-Z])/g,"-$1").toLowerCase();
					data[newName] = $container.attr(name);
				}	
			}

			$items.each(function(index){
				var $item = jQuery(this);
				$item.addClass(classes);
				for(var name in data){
					$item.attr(name,data[name]);
				}
				$item.attr('data-wow-delay',((delay+(item_delay_step*index))/1000)+'s')
				if(index==$items.length-1){
					jQuery.removeData($container.removeClass(classes));
					jQuery(document).trigger('ml-check-conditions');
				}
			});
		});
	}else{
		jQuery(document).trigger('ml-check-conditions');
	}
});

jQuery(document).on('ml-check-conditions',function(e){
	if(!$wow){
		$containers = jQuery('.wow');
		if($containers.length){
			jQuery('#site-wrapper').css({
				"overflow":"hidden"
			});
			new WOW().init();
		}
	}
});
*/
