if(!jQuery().ajaxForm) {
	document.write('<script src="https://yandex.st/jquery/form/3.14/jquery.form.min.js"><\/script>')
}

$(document).ready(function() {

	if (Tickets.config.enable_editor == true) {
		$('#ticket-editor').markItUp(Tickets.config.editor.ticket);
	}

/*
	$(document).on('click', '#previewTicket', function(e) {
		var data = new Object();
		data.parent = $('[name="parent"]').val();
		data.pagetitle = $('[name="pagetitle"]').val();
		data.content = $('[name="content"]').val();
		if (data.content == '' && data.pagetitle == '') {return false;}

		var button = this;
		$(button).attr('disabled','disabled');
		$.post(document.location.href, {action: 'previewTicket', data: data}, function(response) {
			response = $.parseJSON(response);
			if (response.error == 1) {
				$('#ticket-preview-placeholder').html('').hide();
				alert(response.message);
				$(button).removeAttr('disabled');
			}
			else {
				$('#ticket-preview-placeholder').html(response.data).show();
				$(button).removeAttr('disabled');
				//prettyPrint();
			}

		})
		e.preventDefault();
	})
*/
})
// Предпросмотр перед отправкой тикета
function previewTicket(form, button) {
	$(form).ajaxSubmit({
		data: {action: 'previewTicket' }
		,form: form
		,button: button
		,beforeSubmit: function() {
			//$(button).addClass('loading');
			var content = $('textarea[name="content"]',form).val();
			content = content.replace(/\s+/g, "")
			if(content == ''){
				return false;
			}
			$(button).attr('disabled','disabled');
		}
		,success: function(response) {
			response = $.parseJSON(response);
			if (response.error == 1) {
				$('#ticket-preview-placeholder').html('').hide();
				alert(response.message);
				$(button).removeAttr('disabled');
			}
			else {
				$('#ticket-preview-placeholder').html(response.data).show();
				$(button).removeAttr('disabled');
				//prettyPrint();
			}
		}
	})
}
