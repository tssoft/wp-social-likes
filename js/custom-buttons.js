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
	}
};
