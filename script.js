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
	alert('resize');
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

