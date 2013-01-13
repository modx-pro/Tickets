<li class="ticket-comment" id="comment-[[+id]]" data-parent="[[+parent]]">
	<div class="ticket-comment-body">
		<div class="ticket-header">
			<img src="[[+avatar]]" class="ticket-avatar" alt="" />
			<span class="ticket-comment-author">[[+name]]</span>
			<span class="ticket-comment-createdon">[[+date_ago]]</span>[[+ticket_comment_was_edited]]
			<span class="ticket-comment-link"><a href="[[+url]]#comment-[[+id]]">#</a></span>
		</div>
		<div class="ticket-comment-text">
			[[+text]]
		</div>
	</div>
	<div class="comment-reply">
		<a href="#reply" onclick="Comments.forms.reply([[+id]]);return false;">[[%ticket_comment_reply]]</a>
		[[+ticket_comment_edit_link]]
	</div>
	<ol class="comments-list">[[+children]]</ol>
</li>
<!--ticket_comment_edit_link <a href="#edit" onclick="Comments.forms.edit([[+id]]);return false;">[[%ticket_comment_edit]]</a>-->
<!--ticket_comment_was_edited <span class="ticket-comment-edited">([[%ticket_comment_was_edited]])</span></a>-->
