var socialLikesButtons = {
	livejournal: {
		click: function(e) {
			var form = this._livejournalForm;
			if (!form) {
				var html = this.widget.data('html')
					.replace(/{url}/g, this.options.url)
					.replace(/{title}/g, this.options.title)
					.replace(/&/g, '&amp;')
					.replace(/"/g, '&quot;');
				form = jQuery('#sociallikes-livejournal-form');
				form.attr({
					action: 'http://www.livejournal.com/update.bml',
					method: 'post',
					target: '_blank',
					'accept-charset': 'UTF-8'
				});
				form.empty();
				appendHiddenInput(form, 'mode', 'full');
				appendHiddenInput(form, 'subject', this.options.title);
				appendHiddenInput(form, 'event', html);
			}
			form.submit();
		}
	}
};

var appendHiddenInput = function(form, name, value) {
	form.append(jQuery('<input/>', {
		type: 'hidden',
		name: name,
		value: value
	}));
};
