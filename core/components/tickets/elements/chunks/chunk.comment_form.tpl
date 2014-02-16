<h4 id="comment-new-link">
	<a href="#" class="btn btn-default">[[%ticket_comment_create]]</a>
</h4>

<div id="comment-form-placeholder">
	<form id="comment-form" action="" method="post" class="well">
		<div id="comment-preview-placeholder"></div>
		<input type="hidden" name="thread" value="[[+thread]]" />
		<input type="hidden" name="parent" value="0" />
		<input type="hidden" name="id" value="0" />

		<div class="form-group">
			<label for="comment-editor"></label>
			<textarea name="text" id="comment-editor" cols="30" rows="10" class="form-control"></textarea>
		</div>

		<div class="form-actions">
			<input type="button" class="btn btn-default preview" value="[[%ticket_comment_preview]]" title="Ctrl + Enter" />
			<input type="submit" class="btn btn-primary submit" value="[[%ticket_comment_save]]" title="Ctrl + Shift + Enter" />
			<span class="time"></span>
		</div>
	</form>
</div>