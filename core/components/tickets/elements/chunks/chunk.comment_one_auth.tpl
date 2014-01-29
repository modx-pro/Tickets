<li class="ticket-comment[[+comment_new]]" id="comment-[[+id]]" data-parent="[[+parent]]" data-newparent="[[+new_parent]]">
	<div class="ticket-comment-body">
		<div class="ticket-comment-header">
			<div class="ticket-comment-dot-wrapper"><div class="ticket-comment-dot"></div></div>
			<img src="[[+avatar]]" class="ticket-avatar" alt="" />
			<span class="ticket-comment-author">[[+fullname]]</span>
			<span class="ticket-comment-createdon">[[+date_ago]]</span>[[+comment_was_edited]]
			<span class="ticket-comment-link"><a href="[[+url]]#comment-[[+id]]">#</a></span>

			[[+has_parent]]
			<span class="ticket-comment-down"><a href="#" data-child="">&darr;</a></span>

			<span class="ticket-comment-rating[[+can_vote]][[+cant_vote]]">
				<span class="rating[[+rating_positive]][[+rating_negative]]" title="[[%ticket_rating_total]] [[+rating_total]]: ↑[[+rating_plus]] [[%ticket_rating_and]] ↓[[+rating_minus]]">
					[[+rating]]
				</span>
				<span class="vote plus[[+voted_plus]]" title="[[%ticket_like]]" onclick="return Tickets.Vote.comment.vote(this, [[+id]], 1);" ontouchend="return Tickets.Vote.comment.vote(this, [[+id]], 1);">
					<i class="glyphicon glyphicon-arrow-up"></i>
				</span>
				<span class="vote minus[[+voted_minus]]" title="[[%ticket_dislike]]" onclick="return Tickets.Vote.comment.vote(this, [[+id]], -1);"  ontouchend="return Tickets.Vote.comment.vote(this, [[+id]], -1);">
					<i class="glyphicon glyphicon-arrow-down"></i>
				</span>
			</span>
		</div>
		<div class="ticket-comment-text">
			[[+text]]
		</div>
	</div>
	<div class="comment-reply">
		<a href="#reply" onclick="return Tickets.forms.reply([[+id]]);" ontouchend="return Tickets.forms.reply([[+id]]);">[[%ticket_comment_reply]]</a>
		[[+comment_edit_link]]
	</div>
	<ol class="comments-list">[[+children]]</ol>
</li>
<!--tickets_comment_edit_link <a href="#edit" onclick="return Tickets.forms.edit([[+id]]);" ontouchend="return Tickets.forms.edit([[+id]]);">[[%ticket_comment_edit]]</a>-->
<!--tickets_comment_was_edited <span class="ticket-comment-edited">([[%ticket_comment_was_edited]])</span></a>-->
<!--tickets_comment_new  ticket-comment-new-->
<!--tickets_can_vote  active-->
<!--tickets_cant_vote  inactive-->
<!--tickets_rating_positive  positive-->
<!--tickets_rating_negative  negative-->
<!--tickets_voted_plus  voted-->
<!--tickets_voted_minus  voted-->
<!--tickets_has_parent <span class="ticket-comment-up"><a href="[[+url]]#comment-[[+parent]]" data-id="[[+id]]" data-parent="[[+parent]]">&uarr;</a></span>-->
