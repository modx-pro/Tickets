<div class="tickets-row">
	<h3 class="title"><a href="[[~[[+id]]]]">[[+pagetitle]]</a></h3>
	<div class="content">
		[[+introtext:notempty=`[[+introtext]]<br/><a href="[[~[[+id]]]]#cut" class="btn read-more">[[%ticket_read_more?namespace=`tickets`]]</a>`]]
	</div>
	<div class="ticket-meta row">
		<span class="col-md-3"><i class="glyphicon glyphicon-calendar"></i> [[+date_ago]]</span>
		<span class="col-md-3"><i class="glyphicon glyphicon-user"></i> [[+fullname]]</span>
		<span class="col-md-2"><a href="[[~[[+section.id]]]]"><i class="glyphicon glyphicon-folder-open"></i> [[+section.pagetitle]]</a></span>
		<span class="col-md-1"><i class="glyphicon glyphicon-eye-open"></i> [[+views]]</span>
		<span class="col-md-1"><i class="glyphicon glyphicon-comment"></i> [[+comments]]  [[+new_comments]]</span>
		<span class="col-md-2 pull-right ticket-rating[[+active]][[+inactive]]">
			<span class="vote plus[[+voted_plus]]" title="[[%ticket_like]]" onclick="return Tickets.Vote.ticket.vote(this, [[+id]], 1);" ontouchend="return Tickets.Vote.ticket.vote(this, [[+id]], 1);">
				<i class="glyphicon glyphicon-arrow-up"></i>
			</span>
			[[+can_vote]][[+cant_vote]]
			<span class="vote minus[[+voted_minus]]" title="[[%ticket_dislike]]" onclick="return Tickets.Vote.ticket.vote(this, [[+id]], -1);"  ontouchend="return Tickets.Vote.ticket.vote(this, [[+id]], -1);">
				<i class="glyphicon glyphicon-arrow-down"></i>
			</span>
		</span>
	</div>
</div>
<!--tickets_can_vote <span class="vote rating" title="[[%ticket_refrain]]" onclick="return Tickets.Vote.ticket.vote(this, [[+id]], 0);" ontouchend="return Tickets.Vote.ticket.vote(this, [[+id]], 0);"><i class="glyphicon glyphicon-minus"></i></span>-->
<!--tickets_cant_vote <span class="rating[[+rating_positive]][[+rating_negative]]" title="[[%ticket_rating_total]] [[+rating_total]]: ↑[[+rating_plus]] [[%ticket_rating_and]] ↓[[+rating_minus]]">[[+rating]]</span>-->
<!--tickets_new_comments <span class="green">+[[+new_comments]]</span>-->
<!--tickets_active  active-->
<!--tickets_inactive  inactive-->
<!--tickets_voted_plus  voted-->
<!--tickets_voted_minus  voted-->
<!--tickets_rating_positive  positive-->
<!--tickets_rating_negative  negative-->