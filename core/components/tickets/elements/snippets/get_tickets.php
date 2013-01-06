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
	,'fastMode' => false
);

$scriptProperties = array_merge($default, $scriptProperties, array('return' => 'data'));
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'pdotools/',$scriptProperties);

$rows = $pdoFetch->run();
foreach ($rows as $k => $v) {
	$properties = $modx->fromJSON(@$v['properties']);
	if (empty($properties['process_tags'])) {
		foreach ($v as $field => $value) {
			$v[$field] = str_replace(array('[',']'), array('&#91;','&#93;'), $value);
		}
	}

	if (empty($pdoFetch->config['tpl'])) {
		$output[] = '<pre>'.print_r($v, true).'</pre>';
	}
	else {
		$output[] = $pdoFetch->getChunk($pdoFetch->config['tpl'], $v, $pdoFetch->config['fastMode']);
	}
}
$pdoFetch->addTime('Returning processed chunks');

if (!empty($output)) {
	$output = implode($pdoFetch->config['outputSeparator'], $output);
}


if ($modx->user->hasSessionContext('mgr')) {
	$output .= '<pre>' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

return $output;