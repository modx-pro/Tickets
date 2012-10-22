if(!jQuery().ajaxForm) {
	document.write('<script src="https://yandex.st/jquery/form/3.14/jquery.form.min.js"><\/script>')
}

$(document).ready(function() {
	$('#comment-editor').markItUp(Comments.config.editor.comment);
})


function previewComment(form, button) {
	$(form).ajaxSubmit({
		data: {action: 'previewComment' }
		,form: $(form)
		,beforeSubmit: function() {
			//$(button).addClass('loading');
			var text = $('textarea[name="comment"]',form).val();
			var allSpacesRe = /\s+/g;
			text = text.replace(allSpacesRe, "")
			if(text == ''){
				alert('Вы забыли ввести текст комментария');
				return false;
			}
		}
		,success: function(data) {
			data = $.parseJSON(data);
			if (data.errors.length == 0) {
				$('#comment-preview-placeholder').html(data.text).show();
			}
		}
	})
	return false;
}



function sendComment(form, button) {
	$(form).ajaxSubmit({
		data: {action: 'sendComment' }
		,form: $(form)
		,beforeSubmit: function() {
			//$(button).addClass('loading');
			var text = $('textarea[name="comment"]',form).val();
			var allSpacesRe = /\s+/g;
			text = text.replace(allSpacesRe, "")
			if(text == ''){
				alert('Вы забыли ввести текст комментария');
				//$(button).removeClass('loading');
				return false;
			}
		}
		,success: function(data) {
			data = $.parseJSON(data);
			var parent = $(data.text).attr('data-parent');
			if (parent == 0) {
				$('#comments').append(data.text)
			}
			else {
				$('#comment-'+parent+' > .comments-list').append(data.text)
			}
			$('#comment-preview-placeholder').html('').hide();

			$('#comment-editor',form).val('');
			$(form).hide();
			$('.ticket-comment .comment-reply a').show();

			var count = $('.ticket-comment').size();
			$('#comment-total').text(count)

		}
	})
	return false;
}

function showReplyForm(comment_id) {
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

function showCommentForm() {
	$('.ticket-comment .comment-reply a').show();

	var form = $('#comment-form');
	$('#comment-preview-placeholder').hide();
	$('input[name="parent"]',form).val(0);
	$('#comment-form-placeholder').append(form);
	form.show();

	$('#comment-editor', form).focus().val('');
	return false;
}