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
					if (parent == 0) {
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
			$('.ticket-comment .comment-reply a').show();

			var form = $('#comment-form');
			$('#comment-preview-placeholder').hide();
			$('input[name="parent"]',form).val(comment_id);

			var reply = $('#comment-'+comment_id+' > .comment-reply');
			$('a',reply).hide();
			reply.append(form);
			form.show();

			$('#comment-editor', form).focus().val('');
			return false;
		}
		,comment: function() {
			$('.ticket-comment .comment-reply a').show();

			var form = $('#comment-form');
			$('#comment-preview-placeholder').hide();
			$('input[name="parent"]',form).val(0);
			$('#comment-form-placeholder').append(form);
			form.show();

			$('#comment-editor', form).focus().val('');
			return false;
		}
	}
	,error: function(message) {
		alert(message);
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