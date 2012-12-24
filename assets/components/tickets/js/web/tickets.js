Tickets = {
	initialize: function() {
		if (typeof window['prettyPrint'] != 'function') {
			document.write('<script src="'+TicketsConfig.jsUrl+'lib/prettify/prettify.js"><\/script>');
			document.write('<link href="'+TicketsConfig.jsUrl+'lib/prettify/prettify.css" rel="stylesheet">');
		}
		if(!jQuery().ajaxForm) {
			document.write('<script src="'+TicketsConfig.jsUrl+'lib/jquery.form.min.js"><\/script>');
		}

		$(document).ready(function() {
			if (TicketsConfig.enable_editor == true) {
				$('#ticket-editor').markItUp(TicketsConfig.editor.ticket);
			}
		})
	}
	,ticket: {
		preview: function(form,button) {
			$(form).ajaxSubmit({
				data: {action: 'previewTicket' }
				,form: form
				,button: button
				,beforeSubmit: function() {
					var content = $('textarea[name="content"]',form).val();
					content = content.replace(/\s+/g, "");
					if(content == ''){
						return false;
					}
					$(button).attr('disabled','disabled');
					return true;
				}
				,success: function(response) {
					$(button).removeClass('loading');
					response = $.parseJSON(response);
					var element = $('#ticket-preview-placeholder');
					if (response.error == 1) {
						element.html('').hide();
						Tickets.error(response.message);
						$(button).removeAttr('disabled');
					}
					else {
						element.html(response.data).show();
						$(button).removeAttr('disabled');
						prettyPrint();
					}
				}
			});
		}
	}
	,error: function(message) {
		alert(message);
	}
};


Tickets.initialize();

/* For compatibility with old chunks */
function previewTicket(form, button) {
	return Tickets.comment.preview(form, button);
}
