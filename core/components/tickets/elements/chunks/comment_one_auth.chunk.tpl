<li class="ticket-comment[[+comment_new]]" id="comment-[[+id]]" data-parent="[[+parent]]" data-newparent="[[+new_parent]]">
	<div class="ticket-comment-body">
		<div class="ticket-comment-header">
			<div class="ticket-comment-dot-wrapper"><div class="ticket-comment-dot"></div></div>
			<img src="[[+avatar]]" class="ticket-avatar" alt="" />
			<span class="ticket-comment-author">[[+fullname]]</span>
			<span class="ticket-comment-createdon">[[+date_ago]]</span>[[+comment_was_edited]]
			<span class="ticket-comment-link"><a href="[[+url]]#comment-[[+id]]">#</a></span>
		</div>
		<div class="ticket-comment-text">
			[[+text]]
		</div>
	</div>
	<div class="comment-reply">
		<a href="#reply" onclick="Tickets.forms.reply([[+id]]);return false;">[[%ticket_comment_reply]]</a>
		[[+comment_edit_link]]
	</div>
	<ol class="comments-list">[[+children]]</ol>
</li>
<!--tickets_comment_edit_link <a href="#edit" onclick="Tickets.forms.edit([[+id]]);return false;">[[%ticket_comment_edit]]</a>-->
<!--tickets_comment_was_edited <span class="ticket-comment-edited">([[%ticket_comment_was_edited]])</span></a>-->
<!--tickets_comment_new  ticket-comment-new-->
