<div class="tickets-row">
	<h3 class="title"><a href="[[~[[+id]]]]">[[+pagetitle]]</a></h3>
	<div class="content">
		[[+introtext:notempty=`[[+introtext]]<br/><a href="[[~[[+id]]]]#cut" class="btn read-more">[[%ticket_read_more?namespace=`tickets`]]</a>`]]
	</div>
	<div class="row">
		<div class="col-md-2">[[+date_ago]]</div>
		<div class="col-md-2"><a href="[[~[[+section.id]]]]"><i class="glyphicon glyphicon-folder-open"></i> [[+section.pagetitle]]</a></div>
		<div class="col-md-2"><i class="glyphicon glyphicon-user"></i> <b>[[+fullname]]</b></div>
		<div class="col-md-1"><a href="[[~[[+id]]]]#comments"><i class="glyphicon glyphicon-comment"></i> [[+comments]] [[+new_comments]]</a></div>
		<div class="col-md-1"><i class="glyphicon glyphicon-eye-open"></i> [[+views]]</div>
	</div>
</div>
<!--tickets_new_comments <span class="green">+[[+new_comments]]</span>-->