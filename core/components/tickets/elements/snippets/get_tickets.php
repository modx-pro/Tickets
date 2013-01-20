<?php
/* @var pdoFetch $pdoFetch */
/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
$pdoFetch = $modx->getService('pdofetch','pdoFetch',$modx->getOption('pdotools.core_path',null,$modx->getOption('core_path').'components/pdotools/').'model/pdotools/',$scriptProperties);

$where = array('class_key' => 'Ticket');
if (empty($showUnpublished)) {$where['published'] = 1;}
if (empty($showDeleted)) {$where['deleted'] = 0;}
if (empty($parents) && $parents != '0') {$where['parent'] = $modx->resource->id;}
else if (!empty($parents)){
	$pids = explode(',', $parents);
	$parents = $pids;
	foreach ($pids as $v) {
		$parents = array_merge($parents, $modx->getChildIds($v));
	}
	$where['parent:IN'] = $parents;
}

$default = array(
	'class' => 'Ticket'
	,'where' => json_encode($where)
	,'leftJoin' => '[
		{"class":"TicketView","alias":"View","on":"Ticket.id=View.parent"}
		,{"class":"TicketView","alias":"LastView","on":"Ticket.id=LastView.parent AND LastView.uid = '.$modx->user->id.'"}
		,{"class":"TicketVote","alias":"Vote","on":"Ticket.id=Vote.parent AND Vote.class=\'Ticket\'"}
		,{"class":"TicketThread","alias":"Thread","on":"Thread.resource=Ticket.id"}
		,{"class":"TicketComment","alias":"Comment","on":"Comment.thread=Thread.id"}
		,{"class":"TicketsSection","alias":"Section","on":"Section.id=Ticket.parent"}
		,{"class":"modUser","alias":"User","on":"User.id=Ticket.createdby"}
		,{"class":"modUserProfile","alias":"Profile","on":"Profile.internalKey=User.id"}
	]'
	,'select' => '{
		"Ticket":"all"
		,"Vote":"SUM(Vote.value) AS votes"
		,"View":"COUNT(DISTINCT View.uid) as views"
		,"LastView":"LastView.timestamp as new_comments"
		,"Comment":"COUNT(DISTINCT Comment.id) as comments"
		,"Section":"Section.pagetitle as section_name, Section.uri as section_uri"
		,"User":"User.username"
		,"Profile":"Profile.fullname"
	}'
	,'groupby' => 'Ticket.id'
	,'sortby' => 'createdon'
	,'sortdir' => 'desc'
	,'fastMode' => false
	,'return' => 'data'
);

$pdoFetch->config = array_merge($pdoFetch->config, $default, $scriptProperties);
$rows = $pdoFetch->run();

$output = null;
foreach ($rows as $k => $v) {
	$properties = $modx->fromJSON(@$v['properties']);
	if (empty($properties['process_tags'])) {
		foreach ($v as $field => $value) {
			$v[$field] = str_replace(array('[',']'), array('&#91;','&#93;'), $value);
		}
	}

	$v['date_ago'] = $Tickets->dateFormat($v['createdon']);

	// Processing new comments
	if ($modx->user->isAuthenticated() && empty($v['new_comments'])) {
		$v['new_comments'] = $v['comments'];
	}
	else if (!empty($v['new_comments'])) {
		$thread_name = 'resource-'.$v['id'];
		$q = $modx->newQuery('TicketComment');
		$q->leftJoin('TicketThread', 'Thread', 'Thread.name = "'.$thread_name.'" AND Thread.id=TicketComment.thread');
		$q->where('Thread.name = "'.$thread_name.'" AND TicketComment.createdon > "'.$v['new_comments'].'" AND TicketComment.createdby != '.$modx->user->id);
		$q->select('TicketComment.id');
		$q->prepare();

		$v['new_comments'] = $modx->getCount('TicketComment', $q);
	}

	if (!empty($tpl) && !empty($v['new_comments'])) {
		if (!array_key_exists($tpl, $Tickets->elements)) {
			$Tickets->getChunk($tpl);
		}
		$v['ticket_new_comments'] = @$Tickets->elements[$tpl]['placeholders']['ticket_new_comments'];
	}

	// Processing chunk
	if (empty($tpl)) {
		$output[] = '<pre>'.str_replace(array('[',']','`'), array('&#91;','&#93;','&#96;'), htmlentities(print_r($v, true), ENT_QUOTES, 'UTF-8')).'</pre>';
	}
	else {
		$output[] = $Tickets->getChunk($tpl, $v, $pdoFetch->config['fastMode']);
	}
}
$pdoFetch->addTime('Returning processed chunks');

if (!empty($output)) {
	$output = implode($pdoFetch->config['outputSeparator'], $output);
}

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$output .= '<pre>' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

if (!empty($toPlaceholder)) {
	$modx->setPlaceholder($toPlaceholder, $output);
}
else {
	return $output;
}