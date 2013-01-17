<div class="question">
	<h3 class="title"><a href="[[~[[+id]]]]">[[+pagetitle]]</a></h3>
	<div class="content">
		[[+introtext:notempty=`[[+introtext]]<br/><a href="[[~[[+id]]]]#cut" class="btn read-more">[[%ticket_read_more?namespace=`tickets`]]</a>`]]
	</div>
	<div class="row">
		<div class="span2 gray">[[+createdon:strtotime:date=`%d %b %Y, %H:%M`]]</div>
		<div class="span3"><i class="icon-user"></i> <b>[[+createdby:userinfo=`fullname`]]</b></div>
		<div class="span1"><a href="[[~[[+id]]]]#comments"><i class="icon black icon-comment"></i> [[+comments]]</a></div>
		<div class="span1"><i class="icon black icon-eye-open"></i> [[+views]]</div>
	</div>
</div>