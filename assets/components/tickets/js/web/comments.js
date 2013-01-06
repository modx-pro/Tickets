Comments = {
	initialize: function() {
		if (typeof window['prettyPrint'] != 'function') {
			document.write('<script src="'+CommentsConfig.jsUrl+'lib/prettify/prettify.js"><\/script>');
			document.write('<link href="'+CommentsConfig.jsUrl+'lib/prettify/prettify.css" rel="stylesheet">');
		}
		if(!jQuery().ajaxForm) {
			document.write('<script src="'+CommentsConfig.jsUrl+'lib/jquery.form.min.js"><\/script>');
		}

		$(document).ready(function() {
			if (CommentsConfig.enable_editor == true) {
				$('#comment-editor').markItUp(CommentsConfig.editor.comment);
			}
		});

		$(document).on('click', '#comment-preview-placeholder a', function(e) {
			e.preventDefault();
		});
	}
	,comment: {
		preview: function(form,button) {
			$(form).ajaxSubmit({
				data: {action: 'previewComment' }
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
				data: {action: 'saveComment' }
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
						Comments.error(response.message);
						return;
					}
					var parent = $(response.data).attr('data-parent');
					var id = $(response.data).attr('id');
					var comment = $('#' + id);

					Comments.forms.comment();
					if (comment.length > 0) {
						comment.replaceWith(response.data);
					}
					else if (parent == 0) {
						$('#comments').append(response.data)
					}
					else {
						$('#comment-'+parent+' > .comments-list').append(response.data)
					}
					$('#comment-preview-placeholder').html('').hide();

					$('#comment-editor',form).val('');
					$(form).hide();
					$('.ticket-comment .comment-reply a').show();

					var count = $('.ticket-comment').size();
					$('#comment-total').text(count);

					$(button).removeAttr('disabled');
					prettyPrint();
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
			$.post(CommentsConfig.connector, {"action":"getComment","id":comment_id}, function(response) {
				response = $.parseJSON(response);
				if (response.error == 1) {
					Comments.error(response.message);
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
							time_left.text(Comments.utils.timer(time));
						}
						else {
							clearInterval(window.timer);
							time_left.text('');
							//Comments.forms.comment();
						}
					}, 1000)
				}
			});

			return false;
		}
	}
	,error: function(message) {
		alert(message);
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
	}
};

Comments.initialize();

/* For compatibility with old chunks */
function previewComment(form, button) {
	return Comments.comment.preview(form, button);
}
function saveComment(form, button) {
	return Comments.comment.save(form, button);
}
function showReplyForm(comment_id) {
	return Comments.forms.reply(comment_id);
}
function showCommentForm() {
	return Comments.forms.comment();
}