<?php
$where = array(
	'class_key' => 'TicketsSection'
	,'published' => 1
	,'deleted' => 0
);
if (!isset($parents)) {
	$where['parent'] = $modx->resource->id;
}
else if (!empty($parents)) {
	$where['parent:IN'] = explode(',', $parents);
}

$default = array(
	'class' => 'TicketsSection'
	,'where' => json_encode($where)
	,'leftJoin' => '
			{"Ticket":{"alias":"Ticket","on":"Ticket.parent=TicketsSection.id AND Ticket.published=1 AND Ticket.deleted=0 AND Ticket.class_key=\'Ticket\'"}
			,"TicketView":{"alias":"View","on":"Ticket.id=View.parent"}
			,"TicketVote":{"alias":"Vote","on":"Ticket.id=Vote.parent AND Vote.class=\'Ticket\'"}
			,"TicketThread":{"alias":"Thread","on":"Thread.resource=Ticket.id"}
			,"TicketComment":{"alias":"Comment","on":"Comment.thread=Thread.id"}
			}
		'
	,'select' => '
			{"TicketsSection":"all"
			,"Ticket":"COUNT(DISTINCT Ticket.id) as tickets"
			,"Vote":"SUM(DISTINCT Vote.value) as votes"
			,"View":"COUNT(DISTINCT View.parent, View.uid) as views"
			,"Comment":"COUNT(DISTINCT Comment.id) as comments"
			}
		'
	,'groupby' => 'TicketsSection.id'
	,'sortby' => 'views'
	,'sortdir' => 'desc'
);

$scriptProperties = array_merge($default, $scriptProperties);
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'pdotools/',$scriptProperties);

$output = $pdoFetch->run();

if ($modx->user->hasSessionContext('mgr')) {
	$output .= '<pre>' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

return $output;