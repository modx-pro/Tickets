<h4 id="comment-new-link"><a href="#reply" onclick="return showCommentForm()">Написать комментарий</a></h4>
<div id="comment-form-placeholder">
	<form id="comment-form" action="[[+assetsUrl]]comment.php" method="post">
		<div id="comment-preview-placeholder"></div>
		<input type="hidden" name="thread" value="[[+thread]]" />
		<input type="hidden" name="parent" value="0" />

		<textarea name="text" id="comment-editor" cols="30" rows="10">[[+comment]]</textarea>

		<div class="form-actions">
			<input type="button" class="btn" value="Предпросмотр" onclick="previewComment(this.form, this)" />
			<input type="button" class="btn btn-primary" value="Написать" onclick="saveComment(this.form, this)" />
		</div>
	</form>
</div>