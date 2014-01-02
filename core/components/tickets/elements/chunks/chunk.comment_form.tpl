<h4 id="comment-new-link">
	<a href="#reply" onclick="return Tickets.forms.comment();" class="btn btn-default">[[%ticket_comment_create]]</a>
</h4>

<div id="comment-form-placeholder">
	<form id="comment-form" action="" method="post" class="well">
		<div id="comment-preview-placeholder"></div>
		<input type="hidden" name="thread" value="[[+thread]]" />
		<input type="hidden" name="parent" value="0" />
		<input type="hidden" name="id" value="0" />

		<textarea name="text" id="comment-editor" cols="30" rows="10" class="form-control">[[+comment]]</textarea>

		<div class="form-actions">
			<input type="button" class="btn btn-default" value="[[%ticket_comment_preview]]" onclick="return Tickets.comment.preview(this.form, this);" />
			<input type="button" class="btn btn-primary" value="[[%ticket_comment_save]]" onclick="return Tickets.comment.save(this.form, this);" />
			<span class="time"></span>
		</div>
	</form>
</div>