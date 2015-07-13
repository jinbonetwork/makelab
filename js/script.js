var mp = new Maniprop();
var ld = new Lazydo();
var _wow = new WOW();
var _skrollr;
jQuery(document).on('lazydo',function(e){
	_wow.init();
	//_skrollr = skrollr.init();
});

jQuery(document).ready(function(e){
	jQuery('.header-bar .menu-toggle').on('click',function(e){
		var $trigger = jQuery(this);
		var $container = $trigger.closest('.header-bar');
		$container.toggleClass('toggled');
	});
});

jQuery(document).ready(function(e){
	jQuery('.turning-sentences').turningSentences();
});
