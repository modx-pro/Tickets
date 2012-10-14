$(document).ready(function() {

	if (Tickets.config.enable_editor == true) {
		$('#editor').markItUp(Tickets.config.editor.ticket);

		// Предпросмотр перед отправкой тикета
		$(document).on('click', '#previewTicket', function(e) {
			var data = new Object();
			data.section = $('[name="section"]').val();
			data.pagetitle = $('[name="pagetitle"]').val();
			data.content = $('[name="content"]').val();

			$.post(document.location.href, {action: 'previewTicket', data: data}, function(response) {
				response = $.parseJSON(response);
				if (response.error == 1) {
					$('#preview').html('').hide();
					alert(response.message);
				}
				else {
					$('#preview').html(response.data).show();
					//prettyPrint();
				}

			})
			e.preventDefault();
		})


	}
})