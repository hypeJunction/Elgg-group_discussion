define(function(require) {
	var elgg = require('elgg');
	var $ = require('jquery');
	require('jquery.form');
	var spinner = require('elgg/spinner');

	$(document).on('submit', '.group-discussion-widget-form .elgg-form-discussion-save', function(e) {
		e.preventDefault();
		var $form = $(this);
		elgg.action($form.attr('action'), {
			data: $form.serialize(),
			beforeSend: function() {
				spinner.start();
				$form.find('[type="submit"]').prop('disabled', true);
			},
			complete: function() {
				$form.find('[type="submit"]').prop('disabled', false);
				spinner.stop();
			},
			success: function(response) {
				if (response.status >= 0) {
					$form.resetForm();
					$form.find('textarea').val(''); // reset CKEditor
					$form.find('select').trigger('init'); // reset group picker
					$form.closest('.elgg-widget-content').find('.elgg-list').trigger('refresh');
				}
			}
		});
	});

});