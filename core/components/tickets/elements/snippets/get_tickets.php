<?php
$where = array(
	'class_key' => 'Ticket'
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
	'class' => 'Ticket'
	,'where' => json_encode($where)
	,'leftJoin' => '
			{"TicketView":{"alias":"View","on":"Ticket.id=View.parent"}
			,"TicketVote":{"alias":"Vote","on":"Ticket.id=Vote.parent AND Vote.class=\'Ticket\'"}
			,"TicketThread":{"alias":"Thread","on":"Thread.resource=Ticket.id"}
			,"TicketComment":{"alias":"Comment","on":"Comment.thread=Thread.id"}
			,"TicketsSection":{"alias":"Section","on":"Section.id=Ticket.parent"}
			,"modUser":{"alias":"User","on":"User.id=Ticket.createdby"}
			,"modUserProfile":{"alias":"Profile","on":"Profile.internalKey=User.id"}}
		'
	,'select' => '
			{"Ticket":"all"
			,"Vote":"SUM(Vote.value) AS votes"
			,"View":"COUNT(DISTINCT View.uid) as views"
			,"Comment":"COUNT(DISTINCT Comment.id) as comments"
			,"Section":"Section.pagetitle as section_name, Section.uri as section_uri"
			,"User":"User.username"
			,"Profile":"Profile.fullname"}
		'
	,'groupby' => 'Ticket.id'
	,'sortby' => 'createdon'
	,'sortdir' => 'desc'
);

$scriptProperties = array_merge($default, $scriptProperties);
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'pdotools/',$scriptProperties);

$output = $pdoFetch->run();

if ($modx->user->hasSessionContext('mgr')) {
	$output .= '<pre>' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

return $output;