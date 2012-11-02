if(!jQuery().ajaxForm) {
	document.write('<script src="https://yandex.st/jquery/form/3.14/jquery.form.min.js"><\/script>')
}

$(document).ready(function() {

	if (Tickets.config.enable_editor == true) {
		$('#ticket-editor').markItUp(Tickets.config.editor.ticket);
	}

	// Предпросмотр перед отправкой тикета
	$(document).on('click', '#previewTicket', function(e) {
		var data = new Object();
		data.parent = $('[name="parent"]').val();
		data.pagetitle = $('[name="pagetitle"]').val();
		data.content = $('[name="content"]').val();
		if (data.content == '' && data.pagetitle == '') {return false;}

		$.post(document.location.href, {action: 'previewTicket', data: data}, function(response) {
			response = $.parseJSON(response);
			if (response.error == 1) {
				$('#ticket-preview-placeholder').html('').hide();
				alert(response.message);
			}
			else {
				$('#ticket-preview-placeholder').html(response.data).show();
				//prettyPrint();
			}

		})
		e.preventDefault();
	})
})
/*
function previewTicket(form, button) {
	$(form).ajaxSubmit({
		data: {action: 'previewTicket' }
		//,form: $(form)
		,beforeSubmit: function() {
			//$(button).addClass('loading');
			var text = $('textarea[name="content"]',form).val();
			var allSpacesRe = /\s+/g;
			text = text.replace(allSpacesRe, "")
			if(text == ''){
				alert('Вы забыли ввести текст тикета');
				return false;
			}
		}
		,success: function(data) {
			data = $.parseJSON(data);
			if (data.error == 1) {
				$('#ticket-preview-placeholder').html('').addClass('hidden');
				alert(data.message);
			}
			else {
				$('#ticket-preview-placeholder').html(data.data).removeClass('hidden');
			}
			return false;
		}
	})
}
	*/