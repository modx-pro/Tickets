var Tickets = {
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
		if(!jQuery().sisyphus) {
			document.write('<script src="'+TicketsConfig.jsUrl+'lib/jquery.sisyphus.min.js"><\/script>');
		}
		$(document).ready(function() {
			if (TicketsConfig.enable_editor == true) {
				$('#ticket-editor').markItUp(TicketsConfig.editor.ticket);
			}
		});
		$(document).ready(function() {
			if (TicketsConfig.enable_editor == true) {
				$('#comment-editor').markItUp(TicketsConfig.editor.comment);
				$.jGrowl.defaults.closerTemplate = '<div>[ '+TicketsConfig.close_all_message+' ]</div>';
			}
			var count = $('.ticket-comment').size();
			$('#comment-total, .comments-count').text(count);

			$("#ticketForm.create").sisyphus();
		});
		$(document).on('click', '#comment-preview-placeholder a', function() {
			return false;
		});

		$(document).on('change', '#comments-subscribe', function() {
			Tickets.comment.subscribe();
		});

		$(document).on('submit', '#ticketForm', function() {
			Tickets.ticket.save(this, $(this).find('[type="submit"]')[0]);
			return false;
		});
	}

	,ticket: {
		preview: function(form,button) {
			$(form).ajaxSubmit({
				data: {action: 'ticket/preview'}
				,url: TicketsConfig.actionUrl
				,form: form
				,button: button
				,dataType: 'json'
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
					var element = $('#ticket-preview-placeholder');
					if (response.success) {
						element.html(response.data.preview).show();
						prettyPrint();
					}
					else {
						element.html('').hide();
						Tickets.Message.error(response.message);
					}
					$(button).removeAttr('disabled');
				}
			});
		}

		,save: function(form,button) {
			$(form).ajaxSubmit({
				data: {action: 'ticket/save'}
				,url: TicketsConfig.actionUrl
				,form: form
				,button: button
				,dataType: 'json'
				,beforeSubmit: function() {
					var content = $('textarea[name="content"]',form).val().replace(/\s+/g, '');
					if (content == '') {return false;}
					else {
						$(button).attr('disabled','disabled');
						$('.error',form).text('');
						return true;
					}
				}
				,success: function(response) {
					if (response.success && response.data.redirect) {
						document.location.href = response.data.redirect;
					}
					else if (response.success && response.message) {
						$(button).removeAttr('disabled');
						Tickets.Message.success(response.message);
					}
					else {
						$(button).removeAttr('disabled');
						Tickets.Message.error(response.message);
						if (response.data) {
							for (var i in response.data) {
								if (response.data.hasOwnProperty(i)) {
									var input = $(form).find('[name="'+ i + '"]');
									input.parents('label').find('.error').text(response.data[i]);
								}
							}
						}
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
				,dataType: 'json'
				,beforeSubmit: function() {
					var text = $('textarea[name="text"]',form).val().replace(/\s+/g, '');
					if (text == '') {return false;}
					else {
						$(button).attr('disabled','disabled');
						return true;
					}
				}
				,success: function(response) {
					$(button).removeAttr('disabled');
					if (response.success) {
						$('#comment-preview-placeholder').html(response.data.preview).show();
						prettyPrint();
					}
					else {
						Tickets.Message.error(response.message);
					}
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
				,dataType: 'json'
				,beforeSubmit: function() {
					clearInterval(window.timer);
					var text = $('textarea[name="text"]',form).val().replace(/\s+/g, '');
					if (text == '') {return false;}
					else {
						$(button).attr('disabled','disabled');
						return true;
					}
				}
				,success: function(response) {
					$(button).removeAttr('disabled');
					if (response.success) {
						Tickets.forms.comment(false);
						$('#comment-preview-placeholder').html('').hide();
						$('#comment-editor',form).val('');
						$(form).hide();
						$('.ticket-comment .comment-reply a').show();

						// autoPublish = 0
						if (!response.data.length && response.message) {
							Tickets.Message.info(response.message);
						}
						else {
							Tickets.comment.insert(response.data);
							Tickets.utils.goto($(response.data).attr('id'));
						}

						Tickets.comment.getlist();
						prettyPrint();
					}
					else {
						Tickets.Message.error(response.message);
					}
				}
			});
			return false;
		}

		,getlist: function() {
			var form = $('#comment-form');
			var thread = $('[name="thread"]', form);
			if (!thread) {return false;}
			Tickets.tpanel.start();
			$.post(TicketsConfig.actionUrl, {action: 'comment/getlist', thread: thread.val()}, function(response) {
				for (var k in response.data.comments) {
					if (response.data.comments.hasOwnProperty(k)) {
						Tickets.comment.insert(response.data.comments[k], true);
					}
				}
				var count = $('.ticket-comment').size();
				$('#comment-total').text(count);

				Tickets.tpanel.stop();
			}, 'json');
			return true;
		}

		,insert: function(data, remove) {
			var comment = $(data);
			var parent = $(comment).attr('data-parent');
			var id = $(comment).attr('id');
			var exists = $('#' + id);
			var children = '';

			if (exists.length > 0) {
				var np = exists.data('newparent');
				comment.attr('data-newparent', np);
				data = comment[0].outerHTML;
				if (remove) {
					children = exists.find('.comments-list').html();
					exists.remove();
				}
				else {
					exists.replaceWith(data);
					return;
				}
			}

			if (parent == 0 && TicketsConfig.formBefore) {
				$('#comments').prepend(data)
			}
			else if (parent == 0) {
				$('#comments').append(data)
			}
			else {
				var pcomm = $('#comment-'+parent);
				if (pcomm.data('parent') != pcomm.data('newparent')) {
					parent = pcomm.data('newparent');
					comment.attr('data-newparent', parent);
					data = comment[0].outerHTML;
				}
				else if (TicketsConfig.thread_depth) {
					var level = pcomm.parents('.ticket-comment').length;
					if (level > 0 && level >= (TicketsConfig.thread_depth - 1)) {
						parent = pcomm.data('parent');
						comment.attr('data-newparent', parent);
						data = comment[0].outerHTML;
					}
				}
				$('#comment-'+parent+' > .comments-list').append(data);
			}

			if (children.length > 0) {
				$('#' + id).find('.comments-list').html(children);
			}
		}

		,subscribe: function() {
			var form = $('#comment-form');
			var thread = $('[name="thread"]', form);
			if (thread.length) {
				$.post(TicketsConfig.actionUrl, {action: "comment/subscribe", thread: thread.val()}, function(response) {
					if (response.success) {
						Tickets.Message.success(response.message);
					}
					else {
						Tickets.Message.error(response.message);
					}
				}, 'json');
			}
		}
	}

	,forms: {
		reply: function(comment_id) {
			clearInterval(window.timer);
			var form = $('#comment-form');

			$('.time', form).text('');
			$('.ticket-comment .comment-reply a').show();

			$('#comment-preview-placeholder').hide();
			$('input[name="parent"]',form).val(comment_id);
			$('input[name="id"]',form).val(0);

			var reply = $('#comment-'+comment_id+' > .comment-reply');
			$('a',reply).hide();
			reply.append(form);
			reply.parents('.ticket-comment').removeClass('ticket-comment-new');
			form.show();

			$('#comment-editor', form).focus().val('');
			return false;
		}

		,comment: function(focus) {
			var form = $('#comment-form');
			if (focus !== false) {focus = true;}
			clearInterval(window.timer);
			$('.time', form).text('');
			$('.ticket-comment .comment-reply a').show();

			$('#comment-preview-placeholder').hide();
			$('input[name="parent"]',form).val(0);
			$('input[name="id"]',form).val(0);
			$('#comment-form-placeholder').append(form);
			form.show();

			$('#comment-editor', form).val('');
			if (focus) {
				$('#comment-editor', form).focus();
			}
			return false;
		}

		,edit: function(comment_id) {
			$.post(TicketsConfig.actionUrl, {action: "comment/get", id: comment_id}, function(response) {
				if (!response.success) {
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
					$('#comment-editor', form).focus().val(response.data.raw);

					var time = response.data.time;
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
			}, 'json');

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
			$('html, body').animate({
				scrollTop: $('#' + id).offset().top
			}, 1000);
		}
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
			$.jGrowl(message, {theme: 'tickets-message-error'/*, sticky: true*/});
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


Tickets.tpanel = {
	wrapper: $('#comments-tpanel')
	,refresh: $('#tpanel-refresh')
	,new: $('#tpanel-new')
	,class_new: 'ticket-comment-new'

	,initialize: function() {
		if (TicketsConfig.tpanel) {
			this.wrapper.show();
			this.stop();
		}

		this.refresh.on('click', function() {
			$('.' + Tickets.tpanel.class_new).removeClass(Tickets.tpanel.class_new);
			Tickets.comment.getlist();
		});

		this.new.on('click', function() {
			var elem = $('.' + Tickets.tpanel.class_new + ':first');
			$('html, body').animate({
				scrollTop: elem.offset().top
			}, 1000, 'linear', function() {
				elem.removeClass(Tickets.tpanel.class_new);
			});

			var count = parseInt(Tickets.tpanel.new.text());
			if (count > 1) {
				Tickets.tpanel.new.text(count - 1);
			}
			else {
				Tickets.tpanel.new.text('').hide();
			}
		});
	}

	,start: function() {
		this.refresh.addClass('loading');
	}

	,stop: function() {
		var count = $('.' + this.class_new).size();
		if (count > 0) {
			this.new.text(count).show();
		}
		else {
			this.new.hide();
		}
		this.refresh.removeClass('loading');
	}

};

Tickets.initialize();
Tickets.tpanel.initialize();