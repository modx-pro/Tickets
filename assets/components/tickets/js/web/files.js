
var form = $('#comment-form');
if (!form.length) {
	form = $('#ticketForm');
}

Tickets.Uploader = new plupload.Uploader({
	runtimes: 'html5,flash,silverlight,html4',

	browse_button: 'ticket-files-select',
	//upload_button: document.getElementById('ticket-files-upload'),
	container: 'ticket-files-container',
	filelist: 'ticket-files-list',
	progress: 'ticket-files-progress',
	progress_bar: 'ticket-files-progress-bar',
	progress_count: 'ticket-files-progress-count',
	progress_percent: 'ticket-files-progress-percent',
	form: form,

	multipart_params: {
		action: $('#' + this.container).data('action') || 'ticket/file/upload',
		tid: this.form.find('[name="tid"]').val(),
		form_key: this.form.find('[name="form_key"]').val(),
		ctx: TicketsConfig.ctx || 'web'
	},
	drop_element: 'ticket-files-list',

	url: TicketsConfig.actionUrl,

	filters: {
		max_file_size: TicketsConfig.source.size,
		mime_types: [{
			title: 'Files',
			extensions: TicketsConfig.source.extensions
		}]
	},

	resize: {
		width: TicketsConfig.source.width,
		height: TicketsConfig.source.height
	},

	flash_swf_url: TicketsConfig.jsUrl + 'web/lib/plupload/js/Moxie.swf',
	silverlight_xap_url: TicketsConfig.jsUrl + 'web/lib/plupload/js/Moxie.xap',

	init: {
		Init: function(up) {
			if (this.runtime == 'html5') {
				var element = $(this.settings.drop_element);
				element.addClass('droppable');
				element.on('dragover', function() {
					if (!element.hasClass('dragover')) {
						element.addClass('dragover');
					}
				});
				element.on('dragleave drop', function() {
					element.removeClass('dragover');
				});
			}
		},

		PostInit: function(up) {},

		FilesAdded: function(up, files) {
			this.settings.form.find('[type="submit"]').attr('disabled',true);
			up.start();
		},

		UploadProgress: function(up, file) {
			$(up.settings.browse_button).hide();
			$('#' + up.settings.progress).show();
			$('#' + up.settings.progress_count).text((up.total.uploaded + 1) + ' / ' + up.files.length);
			$('#' + up.settings.progress_percent).text(up.total.percent + '%');
			$('#' + up.settings.progress_bar).css('width', up.total.percent + '%');
		},

		FileUploaded: function(up, file, response) {
			response = $.parseJSON(response.response);
			if (response.success) {
				// Successfull action
				var files = $('#' + up.settings.filelist);
				var clearfix = files.find('.clearfix');
				if (clearfix.length != 0) {
					$(response.data).insertBefore(clearfix);
				}
				else {
					files.append(response.data);
				}

			}
			else {
				Tickets.Message.error(response.message);
			}
		},

		UploadComplete: function(up, file, response) {
			$(up.settings.browse_button).show();
			$('#' + up.settings.progress).hide();
			up.total.reset();
			up.splice();
			this.settings.form.find('[type="submit"]').attr('disabled',false);
		},

		Error: function(up, err) {
			Tickets.Message.error(err.message);
		}
	}
});

Tickets.Uploader.init();

$(document).on('click', '.ticket-file-delete, .ticket-file-restore', function(e) {
	var deleted = 'deleted';
	var $this = $(this);
	var $form = $this.parents('form');
	var $parent = $this.parents('.ticket-file');
	var id = $parent.data('id');
	var form_key = $form.find('[name="form_key"]').val();

	$.post(TicketsConfig.actionUrl, {action: 'ticket/file/delete', id: id, form_key: form_key}, function(response) {
		if (response.success) {
			if ($parent.hasClass(deleted)) {
				$parent.removeClass(deleted)
			}
			else {
				$parent.addClass(deleted)
			}
		}
		else {
			Tickets.Message.error(response.message);
		}
	}, 'json');
	return false;
});

$(document).on('click', '.ticket-file-insert', function(e) {
	var $this = $(this);
	var $parent = $this.parents('.ticket-file');
	var $text = $('[name="content"]');
	var template = $parent.find('.ticket-file-template').html();
	template = template.replace(/^\n/g, '').replace(/\t{2}/g, '').replace(/\t$/g, '');

	$text.focus();
	$.markItUp({replaceWith: template});
	return false;
});