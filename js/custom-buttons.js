var socialLikesButtons = {
	livejournal: {
		click: function(e) {
			var html = this.widget.data('html')
				.replace(/{url}/g, this.options.url)
				.replace(/{title}/g, this.options.title)
				.replace(/&/g, '&amp;')
				.replace(/"/g, '&quot;');

			var form = jQuery('.sociallikes-livejournal-form').first();

			form.attr({
				action: 'http://www.livejournal.com/update.bml',
				method: 'post',
				target: '_blank',
				'accept-charset': 'UTF-8'
			}).html(jQuery('<input/>', {
				type: 'hidden',
				name: 'mode',
				value: 'full'
			})).append(jQuery('<input/>', {
				type: 'hidden',
				name: 'subject',
				value: this.options.title
			})).append(jQuery('<input/>', {
				type: 'hidden',
				name: 'event',
				value: html
			})).submit();
		}
	},
	linkedin: {
		counterUrl: 'http://www.linkedin.com/countserv/count/share?url={url}',
		counter: function(jsonUrl, deferred) {
			var options = socialLikesButtons.linkedin;
			if (!options._) {
				options._ = {};
				if (!window.IN) window.IN = {Tags: {}};
				window.IN.Tags.Share = {
					handleCount: function(params) {
						var jsonUrl = options.counterUrl.replace(/{url}/g, encodeURIComponent(params.url));
						options._[jsonUrl].resolve(params.count);
					}
				};
			}
			options._[jsonUrl] = deferred;
			$.getScript(jsonUrl).fail(deferred.reject);
		},
		popupUrl: 'http://www.linkedin.com/shareArticle?mini=false&url={url}&title={title}',
		popupWidth: 650,
		popupHeight: 500
	},
	email: {
		click: function(e) {
			var mailtoUrl = 'mailto:?subject={subject}&body={body}'
				.replace('{subject}', this.options.title)
				.replace('{body}', this.options.url);
			window.location.href = mailtoUrl;
		}
	}
};
