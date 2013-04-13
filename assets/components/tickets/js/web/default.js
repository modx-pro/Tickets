Tickets = {
	initialize: function() {
		if (typeof window['prettyPrint'] != 'function') {
			document.write('<script src="'+TicketsConfig.jsUrl+'lib/prettify/prettify.js"><\/script>');
			document.write('<link href="'+TicketsConfig.jsUrl+'lib/prettify/prettify.css" rel="stylesheet">');
		}
		if(!jQuery().ajaxForm) {
			document.write('<script src="'+TicketsConfig.jsUrl+'lib/jquery.form.min.js"><\/script>');
		}
		if(!jQuery().jGrowl) {
			document.write('<script src="'+TicketsConfig.jsUrl+'lib/jquery.jgrowl.min.js"><\/script>');
		}
		$(document).ready(function() {
			if (TicketsConfig.enable_editor == true) {
				$('#ticket-editor').markItUp(TicketsConfig.editor.ticket);
			}
		})
		$(document).ready(function() {
			if (TicketsConfig.enable_editor == true) {
				$('#comment-editor').markItUp(TicketsConfig.editor.comment);
				$.jGrowl.defaults.closerTemplate = '<div>[ '+TicketsConfig.close_all_message+' ]</div>';
			}
		});
		$(document).on('click', '#comment-preview-placeholder a', function(e) {
			return false;
		});
	}

	,ticket: {
		preview: function(form,button) {
			$(form).ajaxSubmit({
				data: {action: 'previewTicket'}
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
						Tickets.Message.error(response.message);
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

	,comment: {
		preview: function(form,button) {
			$(form).ajaxSubmit({
				data: {action: 'comment/preview'}
				,url: TicketsConfig.actionUrl
				,form: form
				,button: button
				,beforeSubmit: function() {
					//$(button).addClass('loading');
					var text = $('textarea[name="text"]',form).val();
					var allSpacesRe = /\s+/g;
					text = text.replace(allSpacesRe, "");
					if(text == ''){
						return false;
					}
					$(button).attr('disabled','disabled');
					return true;
				}
				,success: function(data) {
					$('#comment-preview-placeholder').html(data).show();
					$(button).removeAttr('disabled');
					prettyPrint();
				}
			});
			return false;
		}
		,save: function(form, button)  {
			$(form).ajaxSubmit({
				data: {action: 'comment/save'}
				,url: TicketsConfig.actionUrl
				,form: form
				,button: button
				,beforeSubmit: function() {
					//$(button).addClass('loading');
					clearInterval(window.timer);
					var text = $('textarea[name="text"]',form).val();
					text = text.replace(/\s+/g, "");
					if(text == ''){
						return false;
					}
					$(button).attr('disabled','disabled');
					return true;
				}
				,success: function(response) {
					response = $.parseJSON(response);
					if (response.error == 1) {
						$(button).removeAttr('disabled');
						Tickets.Message.error(response.message);
						return;
					}
					else if (!response.data && response.message) {
						Tickets.Message.info(response.message);
					}
					else {
						var parent = $(response.data).attr('data-parent');
						var id = $(response.data).attr('id');
						var comment = $('#' + id);

						Tickets.forms.comment();
						if (comment.length > 0) {
							comment.replaceWith(response.data);
						}
						else if (parent == 0 && TicketsConfig.formBefore) {
							$('#comments').prepend(response.data)
						}
						else if (parent == 0) {
							$('#comments').append(response.data)
						}
						else {
							var pcomm = $('#comment-'+parent);
							if (pcomm.data('parent') != pcomm.data('newparent')) {
								parent = pcomm.data('newparent');
							}
							$('#comment-'+parent+' > .comments-list').append(response.data);
						}
					}

					$('#comment-preview-placeholder').html('').hide();
					$('#comment-editor',form).val('');
					$(form).hide();
					$('.ticket-comment .comment-reply a').show();

					var count = $('.ticket-comment').size();
					$('#comment-total').text(count);

					$(button).removeAttr('disabled');
					prettyPrint();

					Tickets.utils.goto(id);
				}
			});
			return false;
		}
	}

	,forms: {
		reply: function(comment_id) {
			clearInterval(window.timer);
			$('.time', form).text('');
			$('.ticket-comment .comment-reply a').show();

			var form = $('#comment-form');
			$('#comment-preview-placeholder').hide();
			$('input[name="parent"]',form).val(comment_id);
			$('input[name="id"]',form).val(0);

			var reply = $('#comment-'+comment_id+' > .comment-reply');
			$('a',reply).hide();
			reply.append(form);
			form.show();

			$('#comment-editor', form).focus().val('');
			return false;
		}
		,comment: function() {
			clearInterval(window.timer);
			$('.time', form).text('');
			$('.ticket-comment .comment-reply a').show();

			var form = $('#comment-form');
			$('#comment-preview-placeholder').hide();
			$('input[name="parent"]',form).val(0);
			$('input[name="id"]',form).val(0);
			$('#comment-form-placeholder').append(form);
			form.show();

			$('#comment-editor', form).focus().val('');
			return false;
		}
		,edit: function(comment_id) {
			$.post(TicketsConfig.actionUrl, {action: "comment/get", id: comment_id}, function(response) {
				response = $.parseJSON(response);
				if (response.error == 1) {
					Tickets.Message.error(response.message);
				}
				else {
					clearInterval(window.timer);
					$('.ticket-comment .comment-reply a').show();
					var form = $('#comment-form');
					$('#comment-preview-placeholder').hide();
					$('input[name="parent"]',form).val(0);
					$('input[name="id"]',form).val(comment_id);

					var reply = $('#comment-'+comment_id+' > .comment-reply');
					var time_left = $('.time', form);

					time_left.text('');
					$('a',reply).hide();

					reply.append(form);
					form.show();
					$('#comment-editor', form).focus().val(response.data);

					var time = response.time;
					window.timer = setInterval(function(){
						if (time > 0) {
							time -= 1;
							time_left.text(Tickets.utils.timer(time));
						}
						else {
							clearInterval(window.timer);
							time_left.text('');
							//Tickets.forms.comment();
						}
					}, 1000);
				}
			});

			return false;
		}
	}

	,utils: {
		timer: function(diff) {
			days  = Math.floor( diff / (60*60*24) );
			hours = Math.floor( diff / (60*60) );
			mins  = Math.floor( diff / (60) );
			secs  = Math.floor( diff );

			dd = days;
			hh = hours - days  * 24;
			mm = mins  - hours * 60;
			ss = secs  - mins  * 60;

			var result = [];

			if( hh > 0) result.push(hh ? this.addzero(hh) : '00');
			result.push(mm ? this.addzero(mm) : '00');
			result.push(ss ? this.addzero(ss) : '00');

			return result.join(':');
		}
		,addzero: function(n) {
			return (n < 10) ? '0'+n : n;
		}
		,goto: function(id) {
			document.location.hash = id;
		}
	}


	,error: function(message) {
		alert(message);
	}
};


Tickets.Message = {
	success: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'tickets-message-success'});
		}
	}
	,error: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'tickets-message-error', sticky: true});
		}
	}
	,info: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'tickets-message-info'});
		}
	}
	,close: function() {
		$.jGrowl('close');
	}
};


Tickets.initialize();
