jQuery(document).ready(function(e){
	var $containers = jQuery('[data-wow-item-delay-step]');
	if($containers.length){
		$containers.each(function(index){
			var $container = jQuery(this);
			var $items = $container.find('.builder-gallery-item,.builder-text-column');
			var classes = $container.attr('class').replace(/ builder\-[^ ]+/g,"");
			var delay = $container.attr('data-wow-delay');
			var item_delay_step = parseInt($container.attr('data-wow-item-delay-step').replace("s","000"));
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
				$item.attr('data-wow-delay',delay+(item_delay_step*index))
				if(index==$items.length-1){
					jQuery.removeData($container.removeClass(classes));
					new WOW().init();
				}
			});
		});
	}else{
		new WOW().init();
	}
});


/*
var makelab = {
	window: jQuery(window),
	document: jQuery(document),
	body: jQuery('body'),

	animation: {
		threshold: 300,
		start: {
			position: 0.85 
		},
		init: {
			classes: "fadeup"
		},
		off: {
			classes: "out"
		},
		on: {
			classes: "in"
		}
	},

	makelab: true
};

makelab.resetInterval = function(callback,timeout){
	handler = callback+'_handler';
	timeout = timeout || makelab.animation.threshold;
	if(typeof makelab[handler]!='undefined'){
		clearInterval(makelab[handler]);
	}
	makelab[handler] = setInterval(makelab[callback],timeout);
};

makelab.resetTimeout = function(callback,timeout){
	handler = callback+'_handler';
	timeout = timeout || makelab.animation.threshold;
	if(typeof makelab[handler]!='undefined'){
		clearTimeout(makelab[handler]);
	}
	makelab[handler] = setTimeout(makelab[callback],timeout);
};

makelab.resize = function(){
};

makelab.scroll = function(){
	var $scrollTop = makelab.window.scrollTop();
	var $startPosition = makelab.window.height() * makelab.animation.start.position;
	jQuery('.fadeup.out').each(function(index){
		var $this = jQuery(this);
		var $offsetTop = $this.offset().top - $scrollTop;
		if($offsetTop < $startPosition) {
			$this.removeClass(makelab.animation.off.classes).addClass(makelab.animation.on.classes);
		}
	});
};

jQuery(document).ready(function(e){
	jQuery(window).on('resize',function(e){makelab.resetTimeout('resize');});
	jQuery(window).on('scroll',function(e){makelab.scroll();});
	jQuery('.builder-section').each(function(index){
		var $this = jQuery(this);
		var $actor;
		if(index > 1){
			$actor = $this;
		}
		if(typeof $actor=='object'){
			$actor.addClass(makelab.animation.init.classes).addClass(makelab.animation.off.classes);
		}
	});
});
*/
