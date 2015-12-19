var Tickets = {
	initialize: function() {
		if (typeof window['prettyPrint'] != 'function') {
			$.getScript(TicketsConfig.jsUrl + 'lib/prettify/prettify.js', function() {
				prettyPrint();
			});
			$('<link/>', {
				rel: 'stylesheet',
				type: 'text/css',
				href: TicketsConfig.jsUrl + 'lib/prettify/prettify.css'
			}).appendTo('head');
		}
		if (!jQuery().ajaxForm) {
			document.write('<script src="' + TicketsConfig.jsUrl + 'lib/jquery.form.min.js"><\/script>');
		}
		if (!jQuery().jGrowl) {
			document.write('<script src="' + TicketsConfig.jsUrl + 'lib/jquery.jgrowl.min.js"><\/script>');
		}
		if (!jQuery().sisyphus) {
			document.write('<script src="' + TicketsConfig.jsUrl + 'lib/jquery.sisyphus.min.js"><\/script>');
		}

		// Forms listeners
		$(document).on('click', '#comment-preview-placeholder a', function() {
			return false;
		});
		$(document).on('change', '#comments-subscribe', function() {
			Tickets.comment.subscribe($('[name="thread"]', $('#comment-form')));
		});
		$(document).on('change', '#tickets-subscribe', function() {
			Tickets.ticket.subscribe($(this).data('id'));
		});
		$(document).on('submit', '#ticketForm', function(e) {
			Tickets.ticket.save(this, $(this).find('[type="submit"]')[0]);
			e.preventDefault();
			return false;
		});
		$(document).on('submit', '#comment-form', function(e) {
			Tickets.comment.save(this, $(this).find('[type="submit"]')[0]);
			e.preventDefault();
			return false;
		});
		// Preview and submit
		$(document).on('click touchend', '#ticketForm .preview, #ticketForm .save, #ticketForm .draft, #ticketForm .publish', function(e) {
			if ($(this).hasClass('preview')) {
				Tickets.ticket.preview(this.form, this);
			}
			else {
				Tickets.ticket.save(this.form, this);
			}
			e.preventDefault();
			return false;
		});
		$(document).on('click touchend', '#comment-form .preview, #comment-form .submit', function(e) {
			if ($(this).hasClass('preview')) {
				Tickets.comment.preview(this.form, this);
			}
			else {
				Tickets.comment.save(this.form, this);
			}
			e.preventDefault();
			return false;
		});
		// Hotkeys
		$(document).on('keydown', '#ticketForm, #comment-form', function(e) {
			if (e.keyCode == 13) {
				if (e.shiftKey && (e.ctrlKey || e.metaKey)) {
					$(this).submit();
				}
				else if ((e.ctrlKey || e.metaKey)) {
					$(this).find('input[type="button"].preview').click();
				}
			}
		});
		// Show and hide forms
		$(document).on('click touchend', '#comment-new-link a', function(e) {
			Tickets.forms.comment();
			e.preventDefault();
			return false;
		});
		$(document).on('click touchend', '.comment-reply a', function(e) {
			var id = $(this).parents('.ticket-comment').data('id');
			if ($(this).hasClass('reply')) {
				Tickets.forms.reply(id);
			}
			else if ($(this).hasClass('edit')) {
				Tickets.forms.edit(id);
			}
			e.preventDefault();
			return false;
		});
		// Votes and rating
		$(document).on('click touchend', '.ticket-comment-rating.active > .vote', function(e) {
			var id = $(this).parents('.ticket-comment').data('id');
			if ($(this).hasClass('plus')) {
				Tickets.Vote.comment.vote(this, id, 1);
			}
			else if ($(this).hasClass('minus')) {
				Tickets.Vote.comment.vote(this, id, -1);
			}
			e.preventDefault();
			return false;
		});
		$(document).on('click touchend', '.ticket-rating.active > .vote', function(e) {
			var id = $(this).parents('.ticket-meta').data('id');
			if ($(this).hasClass('plus')) {
				Tickets.Vote.ticket.vote(this, id, 1);
			}
			else if ($(this).hasClass('minus')) {
				Tickets.Vote.ticket.vote(this, id, -1);
			}
			else {
				Tickets.Vote.ticket.vote(this, id, 0);
			}
			e.preventDefault();
			return false;
		});
		// --
		// Stars
		$(document).on('click touchend', '.ticket-comment-star.active > .star', function(e) {
			var id = $(this).parents('.ticket-comment').data('id');
			Tickets.Star.comment.star(this, id, 0);
			e.preventDefault();
			return false;
		});
		$(document).on('click touchend', '.ticket-star.active > .star', function(e) {
			var id = $(this).parents('.ticket-meta').data('id');
			Tickets.Star.ticket.star(this, id, 0);
			e.preventDefault();
			return false;
		});

		$(document).ready(function() {
			if (TicketsConfig.enable_editor == true) {
				$('#ticket-editor').markItUp(TicketsConfig.editor.ticket);
			}
			if (TicketsConfig.enable_editor == true) {
				$('#comment-editor').markItUp(TicketsConfig.editor.comment);
			}

			$.jGrowl.defaults.closerTemplate = '<div>[ '+TicketsConfig.close_all_message+' ]</div>';

			var count = $('.ticket-comment').size();
			$('#comment-total, .ticket-comments-count').text(count);

			$("#ticketForm.create").sisyphus({
				excludeFields: $('#ticketForm .disable-sisyphus')
			});

			// Auto hide new comment button
			if ($('#comment-form').is(':visible')) {
				$('#comment-new-link').hide();
			}
		});

		// Link to parent comment
		$('#comments').on('click touchend', '.ticket-comment-up a', function() {
			var id = $(this).data('id');
			var parent = $(this).data('parent');
			if (parent && id) {
				Tickets.utils.goto('comment-' + parent);
				$('#comment-' + parent + ' .ticket-comment-down:lt(1)').show().find('a').attr('data-child', id);
			}
			return false;
		});

		// Link to child comment
		$('#comments').on('click touchend', '.ticket-comment-down a', function() {
			var child = $(this).data('child');
			if (child) {
				Tickets.utils.goto('comment-' + child);
			}
			$(this).attr('data-child', '').parent().hide();
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
			var action = 'ticket/';
			switch ($(button).prop('name')) {
				case 'draft': action += 'draft'; break;
				case 'save': action += 'save'; break;
				default: action += 'publish'; break;
			}

			$(form).ajaxSubmit({
				data: {action: action}
				,url: TicketsConfig.actionUrl
				,form: form
				,button: button
				,dataType: 'json'
				,beforeSubmit: function() {
					$(form).find('input[type="submit"], input[type="button"]').attr('disabled','disabled');
					$('.error',form).text('');
					return true;
				}
				,success: function(response) {
					$('#ticketForm.create').sisyphus().manuallyReleaseData();

					if (response.success) {
						if (response.message) {
							Tickets.Message.success(response.message);
						}
						if (action == 'ticket/save') {
							$(form).find('input[type="submit"], input[type="button"]').removeAttr('disabled');
						}
						else if (response.data.redirect) {
							document.location.href = response.data.redirect;
						}
					}
					else {
						$(form).find('input[type="submit"], input[type="button"]').removeAttr('disabled');
						Tickets.Message.error(response.message);
						if (response.data) {
							var i, field;
							for (i in response.data) {
								field = response.data[i];
								$(form).find('[name="' + field.field + '"]').parent().find('.error').text(field.message)
							}
						}
					}
				}
			});
		}

		,subscribe: function(section) {
			if (section) {
				$.post(TicketsConfig.actionUrl, {action: "section/subscribe", section: section}, function(response) {
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

	,comment: {
		preview: function(form,button) {
			$(form).ajaxSubmit({
				data: {action: 'comment/preview'}
				,url: TicketsConfig.actionUrl
				,form: form
				,button: button
				,dataType: 'json'
				,beforeSubmit: function() {
					$(button).attr('disabled','disabled');
					return true;
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
					$('.error',form).text('');
					$(button).attr('disabled','disabled');
					return true;
				}
				,success: function(response) {
					$(button).removeAttr('disabled');
					if (response.success) {
						Tickets.forms.comment(false);
						$('#comment-preview-placeholder').html('').hide();
						$('#comment-editor',form).val('');
						$('.ticket-comment .comment-reply a').show();

						// autoPublish = 0
						if (!response.data.length && response.message) {
							Tickets.Message.info(response.message);
						}
						else {
							Tickets.comment.insert(response.data.comment);
							Tickets.utils.goto($(response.data.comment).attr('id'));
						}

						Tickets.comment.getlist();
						prettyPrint();
					}
					else {
						Tickets.Message.error(response.message);
						if (response.data) {
							var errors = [];
							var i, field;
							for (i in response.data) {
								field = response.data[i];
								var elem = $(form).find('[name="' + field.field + '"]').parent().find('.error');
								if (elem.length > 0) {
									elem.text(field.message)
								}
								else if (field.field && field.message) {
									errors.push(field.field + ': ' + field.message);
								}
							}
							if (errors.length > 0) {
								Tickets.Message.error(errors.join('<br/>'));
							}
						}
					}
					if (response.data.captcha) {
						$('input[name="captcha"]', form).val('').focus();
						$('#comment-captcha', form).text(response.data.captcha);
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
				$('#comment-total, .ticket-comments-count').text(count);

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

		,subscribe: function(thread) {
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
			$('#comment-new-link').show();

			clearInterval(window.timer);
			var form = $('#comment-form');
			$('.time', form).text('');
			$('.ticket-comment .comment-reply a').show();

			$('#comment-preview-placeholder').hide();
			$('input[name="parent"]',form).val(comment_id);
			$('input[name="id"]',form).val(0);

			var reply = $('#comment-'+comment_id+' > .comment-reply');
			form.insertAfter(reply).show();
			$('a', reply).hide();
			reply.parents('.ticket-comment').removeClass('ticket-comment-new');

			$('#comment-editor', form).val('').focus();
			return false;
		}

		,comment: function(focus) {
			if (focus !== false) {focus = true;}
			clearInterval(window.timer);

			$('#comment-new-link').hide();

			var form = $('#comment-form');
			$('.time', form).text('');
			$('.ticket-comment .comment-reply a:hidden').show();

			$('#comment-preview-placeholder').hide();
			$('input[name="parent"]',form).val(0);
			$('input[name="id"]',form).val(0);
			$(form).insertAfter('#comment-form-placeholder').show();

			$('#comment-editor', form).val('');
			if (focus) {
				$('#comment-editor', form).focus();
			}
			return false;
		}

		,edit: function(comment_id) {
			$('#comment-new-link').show();

			var thread = $('#comment-form [name="thread"]').val();
			$.post(TicketsConfig.actionUrl, {action: "comment/get", id: comment_id, thread: thread}, function(response) {
				if (!response.success) {
					Tickets.Message.error(response.message);
				}
				else {
					clearInterval(window.timer);
					$('.ticket-comment .comment-reply a:hidden').show();
					var form = $('#comment-form');
					$('#comment-preview-placeholder').hide();
					$('input[name="parent"]',form).val(0);
					$('input[name="id"]',form).val(comment_id);

					var reply = $('#comment-'+comment_id+' > .comment-reply');
					var time_left = $('.time', form);

					time_left.text('');
					form.insertAfter(reply).show();
					$('a', reply).hide();

					$('#comment-editor', form).val(response.data.raw).focus();
					if (response.data.name) {
						$('[name="name"]', form).val(response.data.name);
					}
					if (response.data.email) {
						$('[name="email"]', form).val(response.data.email);
					}

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


Tickets.Vote = {

	comment: {
		options: {
			active: 'active'
			,inactive: 'inactive'
			,voted: 'voted'
			,vote: 'vote'
			,rating: 'rating'
			,positive: 'positive'
			,negative: 'negative'
		}
		,vote: function(link, id, value) {
			link = $(link);
			var parent = link.parent();
			var options = this.options;
			var rating = parent.find('.' + options.rating);
			if (parent.hasClass(options.inactive)) {
				return false;
			}

			$.post(TicketsConfig.actionUrl, {action: 'comment/vote', id: id, value: value}, function(response) {
				if (response.success) {
					link.addClass(options.voted);
					parent.removeClass(options.active).addClass(options.inactive);
					parent.find('.' + options.vote);
					rating.text(response.data.rating).attr('title', response.data.title);

					rating.removeClass(options.positive + ' ' + options.negative);
					if (response.data.status == 1) {
						rating.addClass(options.positive);
					}
					else if (response.data.status == -1) {
						rating.addClass(options.negative);
					}
				}
				else {
					Tickets.Message.error(response.message);
				}
			}, 'json');

			return true;
		}
	}
	,ticket: {
		options: {
			active: 'active'
			,inactive: 'inactive'
			,voted: 'voted'
			,vote: 'vote'
			,rating: 'rating'
			,positive: 'positive'
			,negative: 'negative'
		}
		,vote: function(link, id, value) {
			link = $(link);
			var parent = link.parent();
			var options = this.options;
			var rating = parent.find('.' + options.rating);
			if (parent.hasClass(options.inactive)) {
				return false;
			}

			$.post(TicketsConfig.actionUrl, {action: 'ticket/vote', id: id, value: value}, function(response) {
				if (response.success) {
					link.addClass(options.voted);
					parent.removeClass(options.active).addClass(options.inactive);
					parent.find('.' + options.vote);
					rating.text(response.data.rating).attr('title', response.data.title).removeClass(options.vote);

					rating.removeClass(options.positive + ' ' + options.negative);
					if (response.data.status == 1) {
						rating.addClass(options.positive);
					}
					else if (response.data.status == -1) {
						rating.addClass(options.negative);
					}
				}
				else {
					Tickets.Message.error(response.message);
				}
			}, 'json');

			return true;
		}
	}
};


Tickets.Star = {
	comment: {
		options: {
			stared: 'stared'
			,unstared: 'unstared'
			//,count: 'ticket-comment-star-count'
		}
		,star: function(link, id, value) {
			link = $(link);
			var options = this.options;
			var parent = link.parent();

			$.post(TicketsConfig.actionUrl, {action: 'comment/star', id: id}, function(response) {
				if (response.success) {
					link.toggleClass(options.stared).toggleClass(options.unstared);
				}
				else {
					Tickets.Message.error(response.message);
				}
			}, 'json');

			return true;
		}
	}
	,ticket: {
		options: {
			stared: 'stared'
			,unstared: 'unstared'
			,count: 'ticket-star-count'
		}
		,star: function(link, id, value) {
			link = $(link);
			var options = this.options;
			var count = link.parent().find('.' + this.options.count);

			$.post(TicketsConfig.actionUrl, {action: 'ticket/star', id: id}, function(response) {
				if (response.success) {
					link.toggleClass(options.stared).toggleClass(options.unstared);
					count.text(response.data.stars);
				}
				else {
					Tickets.Message.error(response.message);
				}
			}, 'json');

			return true;
		}
	}
};


Tickets.tpanel = {
	wrapper: $('#comments-tpanel')
	,refresh: $('#tpanel-refresh')
	,new_comments: $('#tpanel-new')
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

		this.new_comments.on('click', function() {
			var elem = $('.' + Tickets.tpanel.class_new + ':first');
			$('html, body').animate({
				scrollTop: elem.offset().top
			}, 1000, 'linear', function() {
				elem.removeClass(Tickets.tpanel.class_new);
			});

			var count = parseInt(Tickets.tpanel.new_comments.text());
			if (count > 1) {
				Tickets.tpanel.new_comments.text(count - 1);
			}
			else {
				Tickets.tpanel.new_comments.text('').hide();
			}
		});
	}

	,start: function() {
		this.refresh.addClass('loading');
	}

	,stop: function() {
		var count = $('.' + this.class_new).size();
		if (count > 0) {
			this.new_comments.text(count).show();
		}
		else {
			this.new_comments.hide();
		}
		this.refresh.removeClass('loading');
	}

};
if (typeof TicketsConfig != 'undefined'){
	Tickets.initialize();
	Tickets.tpanel.initialize();
}
