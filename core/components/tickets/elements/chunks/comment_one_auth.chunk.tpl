<li class="ticket-comment" id="comment-[[+id]]" data-parent="[[+parent]]">
	<div class="ticket-comment-body">
		<div class="ticket-header">
			<img src="[[+avatar]]" class="ticket-avatar" alt="" />
			<span class="ticket-comment-author">[[+name]]</span>
			<span class="ticket-comment-createdon">[[+createdon]]</span>
			<span class="ticket-comment-link"><a href="[[+url]]#comment-[[+id]]">#</a></span>
		</div>
		<div class="ticket-comment-text">
			[[+text]]
		</div>
	</div>
	<div class="comment-reply"><a href="#reply" onclick="return Comments.forms.reply([[+id]])">[[%ticket_comment_reply]]</a></div>
	<ol class="comments-list">[[+children]]</ol>
</li>
