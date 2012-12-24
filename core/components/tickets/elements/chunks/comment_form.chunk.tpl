<h4 id="comment-new-link"><a href="#reply" onclick="return Comments.forms.comment()">[[%ticket_comment_create]]</a></h4>
<div id="comment-form-placeholder">
	<form id="comment-form" action="[[+assetsUrl]]comment.php" method="post">
		<div id="comment-preview-placeholder"></div>
		<input type="hidden" name="thread" value="[[+thread]]" />
		<input type="hidden" name="parent" value="0" />

		<textarea name="text" id="comment-editor" cols="30" rows="10">[[+comment]]</textarea>

		<div class="form-actions">
			<input type="button" class="btn" value="[[%ticket_comment_preview]]" onclick="Comments.comment.preview(this.form, this)" />
			<input type="button" class="btn btn-primary" value="[[%ticket_comment_save]]" onclick="Comments.comment.save(this.form, this)" />
		</div>
	</form>
</div>