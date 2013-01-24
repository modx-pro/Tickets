<?php
/* @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('pdotools.core_path',null,$modx->getOption('core_path').'components/pdotools/').'model/pdotools/',$scriptProperties);

$where = array('class_key' => 'TicketsSection');
if (empty($showUnpublished)) {$where['published'] = 1;}
if (empty($showDeleted)) {$where['deleted'] = 0;}
if (!isset($parents)) {$where['parent'] = $modx->resource->id;}
else if (!empty($parents)){
	$pids = explode(',', $parents);
	$parents = $pids;
	foreach ($pids as $v) {
		$parents = array_merge($parents, $modx->getChildIds($v));
	}
	$where['parent:IN'] = $parents;
}

$default = array(
	'class' => 'TicketsSection'
	,'where' => json_encode($where)
	,'leftJoin' => '[
		{"class":"Ticket","alias":"Ticket","on":"Ticket.parent=TicketsSection.id AND Ticket.published=1 AND Ticket.deleted=0 AND Ticket.class_key=\'Ticket\'"}
		,{"class":"TicketView","alias":"View","on":"Ticket.id=View.parent"}
		,{"class":"TicketVote","alias":"Vote","on":"Ticket.id=Vote.parent AND Vote.class=\'Ticket\'"}
		,{"class":"TicketThread","alias":"Thread","on":"Thread.resource=Ticket.id"}
		,{"class":"TicketComment","alias":"Comment","on":"Comment.thread=Thread.id"}
	]'
	,'select' => '{
		"TicketsSection":"all"
		,"Ticket":"COUNT(DISTINCT Ticket.id) as tickets"
		,"Vote":"SUM(DISTINCT Vote.value) as votes"
		,"View":"COUNT(DISTINCT View.parent, View.uid) as views"
		,"Comment":"COUNT(DISTINCT Comment.id) as comments"
	}'
	,'groupby' => 'TicketsSection.id'
	,'sortby' => 'views'
	,'sortdir' => 'desc'
	,'fastMode' => false
);

$pdoFetch->config = array_merge($pdoFetch->config, $default, $scriptProperties);
$output = $pdoFetch->run();

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$output .= '<pre>' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

if (!empty($toPlaceholder)) {
	$modx->setPlaceholder($toPlaceholder, $output);
}
else {
	return $output;
}