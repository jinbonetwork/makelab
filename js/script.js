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

/* 첫 페이지 ****/
(function($){
	$(document).ready(function(){
		var covers = [];
		$('#site #covers figure').each(function(){
			var url = $(this).css('background-image').replace('url(', '').replace(')', '');
			covers.push(url);
		});

		var slideIndex = 0;
		$('#site #introduction .builder-banner-slide').each(function(){
			if($(this).css('visibility') == 'hidden') return;

			var $content = $(this).find('.builder-banner-inner-content');
			var imgUrl = $(this).css('background-image').replace('url(', '').replace(')', '');
			$(this).css('background-image', '');
			var subtitle = '<p class="subtitle">'+$content.find('p:nth-child(1)').html()+'</p>';
			var title = '<h1 class="title">'+$content.find('p:nth-child(2)').html()+'</h1>';
			var href = $content.find('p:nth-child(2)').find('a').attr('href');
			var questions = '<p class="questions">'+$content.find('p:nth-child(3)').html()+'<br>'+$content.find('p:nth-child(4)').html()+'</p>';
			var description = '<p class="description">'+$content.find('p:nth-child(5)').html()+'</p>';
			$content.children().remove();
			$content.html(
				'<div class="header">'+subtitle+title+'</div>'+
				'<div class="image"><a href="'+href+'"><img src='+imgUrl+'></a></div>'+
				'<div class="content">'+questions+description+'</div>'
			);

			/*책 표지 ****/
			$content.append('<div class="cover-wrapper"><div class="cover-list"></div></div>');
			$content.find('.cover-list').append('<div class="bracket left"><div class="margin"></div><div class="border"></div></div>');
			for(var i = 0; i < 5; i++){
				$content.find('.cover-list').append(
					'<div class="cover"><div class="margin"><i class="fa fa-circle"></i></div><div class="cover-image"></div></div>'
				);
			}
			$content.find('.cover-list').append('<div class="bracket right"><div class="margin"></div><div class="border"></div></div>');
			for(var i = 0; i < covers.length; i++)
				$content.find('.cover:eq('+i+') .cover-image').html('<img src='+covers[i]+'>');
			$content.find('.cover:eq('+(slideIndex++)+') .margin').addClass('marked');
		});
	});
})(jQuery);
