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

	var wpsl_ul = $("<ul/>", { class: "social-likes social-likes_visible social-likes_ready" });
	var parent = $("<div/>", { class: "social-likes_single-w" });
	var single = false;
	
	var li = {};
	
	var title_vkontakte = $("#title_vkontakte").attr("value");
	var title_facebook = $("#title_facebook ").attr("value");
	var title_twitter = $("#title_twitter").attr("value");
	var title_plusone = $("#title_plusone").attr("value");
	var title_pinterest = $("#title_pinterest").attr("value");
	//var title_livejournal = $("#title_livejournal").attr("value");
	var title_odnoklassniki = $("#title_odnoklassniki").attr("value");
	var title_mailru = $("#title_mailru").attr("value");
	var label_vkontakte = $("<span/>", { class: "labelToDisappear", text: $("#label_vkontakte").attr("value") });
	var label_facebook = $("<span/>", { class: "labelToDisappear", text: $("#label_facebook ").attr("value") });
	var label_twitter = $("<span/>", { class: "labelToDisappear", text: $("#label_twitter").attr("value") });
	var label_plusone = $("<span/>", { class: "labelToDisappear", text: $("#label_plusone").attr("value") });
	var label_pinterest = $("<span/>", { class: "labelToDisappear", text: $("#label_pinterest").attr("value") });
	//var label_livejournal = $("<span/>", { class: "labelToDisappear", text: $("#label_livejournal").attr("value") });
	var label_odnoklassniki = $("<span/>", { class: "labelToDisappear", text: $("#label_odnoklassniki").attr("value") });
	var label_mailru = $("<span/>", { class: "labelToDisappear", text: $("#label_mailru").attr("value") });
	
	li['vk_btn'] = $('<li/>', {
		class: 'social-likes__widget social-likes__widget_vkontakte',
		title: title_vkontakte
	}).append($('<span/>', {
		class: 'social-likes__button social-likes__button_vkontakte'
	}).append($('<span/>', {
		class: 'social-likes__icon social-likes__icon_vkontakte'
	})).append(label_vkontakte));
	
	li['facebook_btn'] = $('<li/>', {
		class: 'social-likes__widget social-likes__widget_facebook',
		title: title_facebook
	}).append($('<span/>', {
		class: 'social-likes__button social-likes__button_facebook'
	}).append($('<span/>', {
		class: 'social-likes__icon social-likes__icon_facebook'
	})).append(label_facebook));
	
	li['twitter_btn'] = $('<li/>', {
		class: 'social-likes__widget social-likes__widget_twitter',
		title: title_twitter
	}).append($('<span/>', {
		class: 'social-likes__button social-likes__button_twitter'
	}).append($('<span/>', {
		class: 'social-likes__icon social-likes__icon_twitter'
	})).append(label_twitter));
	
	li['google_btn'] = $('<li/>', {
		class: 'social-likes__widget social-likes__widget_plusone',
		title: title_plusone
	}).append($('<span/>', {
		class: 'social-likes__button social-likes__button_plusone'
	}).append($('<span/>', {
		class: 'social-likes__icon social-likes__icon_plusone'
	})).append(label_plusone));
	
	li['pinterest_btn'] = $('<li/>', {
		class: 'social-likes__widget social-likes__widget_pinterest',
		title: title_pinterest
	}).append($('<span/>', {
		class: 'social-likes__button social-likes__button_pinterest'
	}).append($('<span/>', {
		class: 'social-likes__icon social-likes__icon_pinterest'
	})).append(label_pinterest));
	
	/*li['lj_btn'] = $('<li/>', {
		class: 'social-likes__widget social-likes__widget_livejournal',
		title: title_livejournal
	}).append($('<span/>', {
		class: 'social-likes__button social-likes__button_livejournal'
	}).append($('<span/>', {
		class: 'social-likes__icon social-likes__icon_livejournal'
	})).append(label_livejournal));*/
	
	li['odn_btn'] = $('<li/>', {
		class: 'social-likes__widget social-likes__widget_odnoklassniki',
		title: title_odnoklassniki
	}).append($('<span/>', {
		class: 'social-likes__button social-likes__button_odnoklassniki'
	}).append($('<span/>', {
		class: 'social-likes__icon social-likes__icon_odnoklassniki'
	})).append(label_odnoklassniki));
	
	li['mm_btn'] = $('<li/>', {
		class: 'social-likes__widget social-likes__widget_mailru',
		title: title_mailru
	}).append($('<span/>', {
		class: 'social-likes__button social-likes__button_mailru'
	}).append($('<span/>', {
		class: 'social-likes__icon social-likes__icon_mailru'
	})).append(label_mailru));
	
	$('#preview').html(wpsl_ul);
	
	function sort_buttons() {
		wpsl_ul.empty();
		if (single) {
			addCloseButton();
		}
		$('input[type="checkbox"]:checked').each(function () {
			if ($(this).attr("id").indexOf("_btn") != -1)
			{
				wpsl_ul.append(li[$(this).attr('id')]);
			}
		});
		var preview = $('#preview');
		if ($("#icons").is(":checked")) {
			wpsl_ul.addClass('social-likes_notext');
			$(".labelToDisappear").hide();
		}
		else
		{
			wpsl_ul.removeClass('social-likes_notext');
			$(".labelToDisappear").show();
		}
	}
	
	function rebuild() {
		if (single) {
			wpsl_ul.unwrap();
			$('div.social-likes__button_single').remove();
			$('div.social-likes__close').remove();
			single = false;
		}
		var radio = $('input[name=look]:checked').val();
		wpsl_ul.removeClass('social-likes_single');
        wpsl_ul.removeClass('social-likes_vertical');
		wpsl_ul.removeAttr('data-single-title');
		wpsl_ul.css('display', 'inline-block');
		if (radio == 'v') {
			wpsl_ul.addClass('social-likes_vertical');
		} else if (radio == 's') {
			single = true;
			wpsl_ul.addClass('social-likes_single');
			wpsl_ul.addClass('social-likes_vertical');
			wpsl_ul.attr('data-single-title', $("#label_share").attr("value"));
			addCloseButton();
			wpsl_ul.wrap(parent);
			var shareText = $("#label_share").attr("value");
			var buttonDiv = $("<div/>", {
				class: "social-likes__button social-likes__button_single"
			});
			buttonDiv.append($("<span/>", {
				class: "social-likes__icon social-likes__icon_single"
			})).append(shareText);
			$("ul.social-likes").after($("<div/>", {
				class: "social-likes__widget social-likes__widget_single",
				html: buttonDiv
			}));
		}
	}
	
	function addCloseButton() {
		var closeButton = $("<div/>", {
			class: "social-likes__close",
			text: "×"
		});
		closeButton.on("click", function (event) {
			event.stopPropagation();
			wpsl_ul.removeClass('social-likes_opened');
		});
		wpsl_ul.append(closeButton);
	}
	
	sort_buttons();
	rebuild();
	
	$('.view-state').on('change', sortableContainer.changeView);
	
	$('form').on('change', '#counters', function () {
		if ($(this).is(":checked")) {
			wpsl_ul.removeAttr('data-counters');
			$("#withZeroes").show();
		} else {
			wpsl_ul.attr('data-counters', 'no');
			$("#withZeroes").hide();
		}
	});
	if (!$("#counters").is(":checked")) {
		$("#withZeroes").hide();
	}
	
	$('form').on('change', '#zeroes', function () {
		if ($("#zeroes").is(":checked")) {
			wpsl_ul.attr('data-zeroes', 'yes');
		} else {
			wpsl_ul.removeAttr('data-zeroes');
		}
	});
	
	if ($('input[name=look]:checked').val() == 's') {
		single = true;
	}
	
	$('form').on('change', '#h_look, #v_look, #s_look', rebuild);
	
	$('form').on('click', 'div.social-likes_single-w', function (event) {
		event.stopPropagation();
		wpsl_ul.addClass('social-likes_opened');
	});
	
	$('form').on('change', 'input:checkbox', function () {
		sort_buttons();
	});

	$('body').on('click', 'form', function () {
		if (single) {
			wpsl_ul.removeClass('social-likes_opened');
		}
	});
	
	$(document).on('click', '.more-websites', function () {
		$('li.sortable-item.hidden').show();
		$(this).hide();
	});

	$(document).on('click', 'input[type="radio"]', resetRadioButtons);

	resetRadioButtons();
	
	function resetRadioButtons() {
		$('input[type="radio"]').removeClass('checked');
		$('input[type="radio"]:checked').addClass('checked');
	};
	
	$('form').on('change', '[id *= "skin"], #light, #icons', changeStyle);
	
	function changeStyle()
	{
		var styleSheet = "#styleClassic";
		$("#lightStyle").hide();
		if ($("#skin_flat").is(":checked"))
		{
			styleSheet = "#styleFlat";
			$("#lightStyle").show();
			if ($("#light").is(":checked") || $("#icons").is(":checked")) {
				wpsl_ul.addClass('social-likes_light');
			}
			else
			{
				wpsl_ul.removeClass('social-likes_light');
			}
		}
		else if ($("#skin_birman").is(":checked"))
		{
			styleSheet = "#styleBirman";
		}
		$("#styleClassic").attr("disabled", true);
		$("#styleFlat").attr("disabled", true);
		$("#styleBirman").attr("disabled", true);
		$(styleSheet).removeAttr("disabled");
	}
	
	changeStyle();
});