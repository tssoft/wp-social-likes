jQuery(document).ready(function ($) {
	$('.sortable-container').sortable({
		update: function(e,ui){
			sort_buttons();
		}
	});

	var sortableContainer = {
		element: $('.sortable-container'),
		defaultState: 'horizontal',
		changeView: function() {
			if($('.view-state:checked').val() === sortableContainer['defaultState']) {
				sortableContainer.element.addClass(sortableContainer['defaultState'])
			}
			else {
				sortableContainer.element.removeClass(sortableContainer['defaultState']);
			}			
		}
	}

	sortableContainer.changeView();
	
	$('.view-state').on('change', sortableContainer.changeView);

	var wpsl_ul = $('<ul class="social-likes"></ul>');
	var parent = '<div class="social-likes_single-w"></div>';	
	var single = false;
	
	var li = {};

	if ($('#preview').attr('language') == 'ru') {
		li['vk_btn'] = '<li class="social-likes__widget social-likes__widget_vkontakte" title="Поделиться ссылкой во Вконтакте"><span class="social-likes__button social-likes__button_vkontakte"><span class="social-likes__icon social-likes__icon_vkontakte"></span>Вконтакте</span></li>';
		li['facebook_btn'] = '<li class="social-likes__widget social-likes__widget_facebook" title="Поделиться ссылкой на Фейсбуке"><span class="social-likes__button social-likes__button_facebook"><span class="social-likes__icon social-likes__icon_facebook"></span>Facebook</span></li>';
		li['twitter_btn'] = '<li class="social-likes__widget social-likes__widget_twitter" title="Поделиться ссылкой в Твиттере"><span class="social-likes__button social-likes__button_twitter"><span class="social-likes__icon social-likes__icon_twitter"></span>Twitter</span></li>';
		li['google_btn'] = '<li class="social-likes__widget social-likes__widget_plusone" title="Поделиться ссылкой в Гугл-плюсе"><span class="social-likes__button social-likes__button_plusone"><span class="social-likes__icon social-likes__icon_plusone"></span>Google+</span></li>';
		li['pinterest_btn'] = '<li class="social-likes__widget social-likes__widget_pinterest" title="Поделиться картинкой на Пинтересте" data-media=""><span class="social-likes__button social-likes__button_pinterest"><span class="social-likes__icon social-likes__icon_pinterest"></span>Pinterest</span></li>';
		li['lj_btn'] = '<li class="social-likes__widget social-likes__widget_livejournal" title="Поделиться ссылкой в ЖЖ"><span class="social-likes__button social-likes__button_livejournal"><span class="social-likes__icon social-likes__icon_livejournal"></span>LiveJournal</span></li>';
		li['odn_btn'] = '<li class="social-likes__widget social-likes__widget_odnoklassniki" title="Поделиться ссылкой в Одноклассниках"><span class="social-likes__button social-likes__button_odnoklassniki"><span class="social-likes__icon social-likes__icon_odnoklassniki"></span>Одноклассники</span></li>';
		li['mm_btn'] = '<li class="social-likes__widget social-likes__widget_mailru" title="Поделиться ссылкой в Моём мире"><span class="social-likes__button social-likes__button_mailru"><span class="social-likes__icon social-likes__icon_mailru"></span>Мой мир</span></li>';
	} else {
		li['vk_btn'] = '<li class="social-likes__widget social-likes__widget_vkontakte" title="Share link on VK"><span class="social-likes__button social-likes__button_vkontakte"><span class="social-likes__icon social-likes__icon_vkontakte"></span>Вконтакте</span></li>';
		li['facebook_btn'] = '<li class="social-likes__widget social-likes__widget_facebook" title="Share link on Facebook"><span class="social-likes__button social-likes__button_facebook"><span class="social-likes__icon social-likes__icon_facebook"></span>Facebook</span></li>';
		li['twitter_btn'] = '<li class="social-likes__widget social-likes__widget_twitter" title="Share link on Twitter"><span class="social-likes__button social-likes__button_twitter"><span class="social-likes__icon social-likes__icon_twitter"></span>Twitter</span></li>';
		li['google_btn'] = '<li class="social-likes__widget social-likes__widget_plusone" title="Share link on Google+"><span class="social-likes__button social-likes__button_plusone"><span class="social-likes__icon social-likes__icon_plusone"></span>Google+</span></li>';
		li['pinterest_btn'] = '<li class="social-likes__widget social-likes__widget_pinterest" title="Share image on Pinterest" data-media=""><span class="social-likes__button social-likes__button_pinterest"><span class="social-likes__icon social-likes__icon_pinterest"></span>Pinterest</span></li>';
		li['lj_btn'] = '<li class="social-likes__widget social-likes__widget_livejournal" title="Share link on LiveJournal"><span class="social-likes__button social-likes__button_livejournal"><span class="social-likes__icon social-likes__icon_livejournal"></span>LiveJournal</span></li>';
	}
	
	function sort_buttons() {
		wpsl_ul.empty();
		$('input[type="checkbox"]:checked').each(function () {
			wpsl_ul.append(li[$(this).attr('id')]);		
		});
		if (!single) {
			$('#preview').append(wpsl_ul);
		} else {
			$('#preview').append(wpsl_ul.parent());
		}
	}
	
	function rebuild() {
		if (single) {
			wpsl_ul.unwrap();
			$('div.social-likes__button_single').remove();
			single = false;
		}
		wpsl_ul.css('display', 'block');
		
		if ($('input[name=look]:checked').val() == 'v') {
			wpsl_ul.addClass('social-likes_vertical');
			wpsl_ul.removeClass('social-likes_single');
			wpsl_ul.removeAttr('data-single-title');
			
		} else if ($('input[name=look]:checked').val() == 's') {
			single = true;
			wpsl_ul.addClass('social-likes_single');
			wpsl_ul.addClass('social-likes_vertical');
			wpsl_ul.attr('data-single-title', 'Share');

			wpsl_ul.wrap(parent);
			$('div.social-likes_single-w').append('<div class="social-likes__button social-likes__button_single"><span class="social-likes__icon social-likes__icon_single"></span>Share</div>');
		} else {
			wpsl_ul.removeClass('social-likes_single');
			wpsl_ul.removeClass('social-likes_vertical');
			wpsl_ul.removeAttr('data-single-title');
		}
	}
	
	rebuild();
	sort_buttons();
	
	$('form').on('change', '#counters', function () {
		if (wpsl_ul.attr('data-counters')) {
			wpsl_ul.removeAttr('data-counters');
		} else {
			wpsl_ul.attr('data-counters', 'no');
		}
	});	
	
	if ($('input[name=look]:checked').val() == 's') {
		single = true;
	}
	
	$('form').on('change', '#h_look, #v_look, #s_look', rebuild);
	
	$('form').on('click', 'div.social-likes_single-w', function (event) {
		event.stopPropagation();
		wpsl_ul.css('display', 'block');
	});
	
	$('body').on('click', 'form', function () {
		if (single) {
			wpsl_ul.css('display', 'none');	
		}
	});
	
	$('form').on('change', 'input:checkbox', function () {
		sort_buttons();
	});
});

