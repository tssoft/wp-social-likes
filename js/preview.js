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

	var wpsl_ul = $("<div/>", { class: "social-likes social-likes_visible social-likes_ready" });
	var parent = $("<div/>", { class: "social-likes_single-w" });
	var single = false;
	
	var li = {};
	
	function newButton(social_network_name)
	{
		var title = [];
		var label = [];
		title['vkontakte'] = $("#title_vkontakte").attr("value");
		title['facebook'] = $("#title_facebook ").attr("value");
		title['twitter'] = $("#title_twitter").attr("value");
		title['plusone'] = $("#title_plusone").attr("value");
		title['pinterest'] = $("#title_pinterest").attr("value");
		//title['livejournal'] = $("#title_livejournal").attr("value");
		title['odnoklassniki'] = $("#title_odnoklassniki").attr("value");
		title['mailru'] = $("#title_mailru").attr("value");
		label['vkontakte'] = $("<span/>", { class: "labelToDisappear", text: $("#label_vkontakte").attr("value") });
		label['facebook'] = $("<span/>", { class: "labelToDisappear", text: $("#label_facebook ").attr("value") });
		label['twitter'] = $("<span/>", { class: "labelToDisappear", text: $("#label_twitter").attr("value") });
		label['plusone'] = $("<span/>", { class: "labelToDisappear", text: $("#label_plusone").attr("value") });
		label['pinterest'] = $("<span/>", { class: "labelToDisappear", text: $("#label_pinterest").attr("value") });
		//label['livejournal'] = $("<span/>", { class: "labelToDisappear", text: $("#label_livejournal").attr("value") });
		label['odnoklassniki'] = $("<span/>", { class: "labelToDisappear", text: $("#label_odnoklassniki").attr("value") });
		label['mailru'] = $("<span/>", { class: "labelToDisappear", text: $("#label_mailru").attr("value") });
		
		var button = $('<div/>', {
			class: 'social-likes__widget social-likes__widget_'.concat(social_network_name),
			title: title[social_network_name]
		}).append($('<span/>', {
			class: 'social-likes__button social-likes__button_'.concat(social_network_name)
		}).append($('<span/>', {
			class: 'social-likes__icon social-likes__icon_'.concat(social_network_name)
		})).append(label[social_network_name]));
		return button;
	}
	
	li['vk_btn'] = newButton('vkontakte');
	li['facebook_btn'] = newButton('facebook');
	li['twitter_btn'] = newButton('twitter');
	li['google_btn'] = newButton('plusone');
	li['pinterest_btn'] = newButton('pinterest');
	//li['lj_btn'] = newButton('likes__widget_livejournal');
	li['odn_btn'] = newButton('odnoklassniki');
	li['mm_btn'] = newButton('mailru');

	$('#preview').html(wpsl_ul);
	
	function sort_buttons() {
		wpsl_ul.empty();
		/*if (single) {
			addCloseButton();
		}*/
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
			//$('div.social-likes__close').remove();
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
			//addCloseButton();
			wpsl_ul.wrap(parent);
			var shareText = $("#label_share").attr("value");
			var buttonDiv = $("<div/>", {
				class: "social-likes__button social-likes__button_single"
			});
			buttonDiv.append($("<span/>", {
				class: "social-likes__icon social-likes__icon_single"
			})).append(shareText);
			$("div.social-likes").after($("<div/>", {
				class: "social-likes__widget social-likes__widget_single",
				html: buttonDiv
			}));
		}
	}
	
	/*function addCloseButton() {
		var closeButton = $("<div/>", {
			class: "social-likes__close",
			text: "×"
		});
		closeButton.on("click", function (event) {
			event.stopPropagation();
			wpsl_ul.removeClass('social-likes_opened');
		});
		wpsl_ul.append(closeButton);
	}*/
	
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
	
	$('form').on('change', '[id *= "skin"], #icons', changeStyle);
	
	function changeStyle()
	{
		wpsl_ul.removeClass('social-likes_light');
		var styleSheet = "#styleClassic";
		if ($("#skin_flat").is(":checked") || $("#skin_flatlight").is(":checked"))
		{
			styleSheet = "#styleFlat";
			if ($("#skin_flatlight").is(":checked"))
			{
				wpsl_ul.addClass('social-likes_light');
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