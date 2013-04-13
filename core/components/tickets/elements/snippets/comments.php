<?php
if (empty($scriptProperties['thread']) && !empty($modx->resource)) {$scriptProperties['thread'] = 'resource-'.$modx->resource->id;}
$scriptProperties = array_merge(array(
	'resource' => $modx->resource->id
	,'snippetPrepareComment' => $modx->getOption('tickets.snippet_prepare_comment')
	,'commentEditTime' => $modx->getOption('tickets.comment_edit_time', null, 180)
), $scriptProperties);

/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
$Tickets->initialize($modx->context->key);
/* @var pdoFetch $pdoFetch */
if (!empty($modx->services['pdofetch'])) {unset($modx->services['pdofetch']);}
$pdoFetch = $modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH.'components/pdotools/model/pdotools/',$scriptProperties);
$pdoFetch->config['nestedChunkPrefix'] = 'tickets_';
$pdoFetch->addTime('pdoTools loaded.');

/* @var TicketThread $thread */
if (!$thread = $modx->getObject('TicketThread', array('name' => $scriptProperties['thread']))) {
	$thread = $modx->newObject('TicketThread');
	$thread->fromArray(array(
		'name' => $scriptProperties['thread']
		,'createdby' => $modx->user->id
		,'createdon' => date('Y-m-d H:i:s')
		,'resource' => $modx->resource->id
	));
}
else if ($thread->get('deleted')) {
	return $modx->lexicon('ticket_thread_err_deleted');
}

$thread->set('properties', $scriptProperties);
$thread->save();

$class = 'TicketComment';
$where = array();
if (empty($showUnpublished)) {$where['published'] = 1;}

// Adding custom where parameters
if (!empty($scriptProperties['where'])) {
	$tmp = $modx->fromJSON($scriptProperties['where']);
	if (is_array($tmp)) {
		$where = array_merge($where, $tmp);
	}
}
unset($scriptProperties['where']);
$pdoFetch->addTime('"Where" expression built.');

// Joining tables
$innerJoin = array(
	'{"class":"TicketThread","alias":"Thread","on":"Thread.id=TicketComment.thread AND Thread.name=\''.$thread->get('name').'\'"}'
);
$leftJoin = array(
	'{"class":"modUser","alias":"User","on":"User.id=TicketComment.createdby"}'
	,'{"class":"modUserProfile","alias":"Profile","on":"Profile.internalKey=User.id"}'
);

// Fields to select
$select = array(
	'"Comment":"'.$modx->getSelectColumns('TicketComment', 'TicketComment', '', array('raw'), true).'"'
	,'"Thread":"'.$modx->getSelectColumns('TicketThread', 'Thread', '', array('resource')).'"'
	,'"User":"'.$modx->getSelectColumns('modUser', 'User', '', array('username')).'"'
	,'"Profile":"'.$modx->getSelectColumns('modUserProfile', 'Profile', '', array('id'), true).'"'
);

$default = array(
	'class' => $class
	,'where' => $modx->toJSON($where)
	,'innerJoin' => '['.implode(',',$innerJoin).']'
	,'leftJoin' => '['.implode(',',$leftJoin).']'
	,'select' => '{'.implode(',',$select).'}'
	,'sortby' => 'id'
	,'sortdir' => 'ASC'
	,'fastMode' => true
	,'return' => 'data'
);

// Merge all properties and run!
$pdoFetch->config = array_merge($pdoFetch->config, $default, $scriptProperties);
$pdoFetch->addTime('Query parameters are prepared.');
$rows = $pdoFetch->run();

// Processing rows
$output = $commentsThread = null; $total = 0;
if (!empty($rows) && is_array($rows)) {
	$tmp = array();
	foreach ($rows as $row)  {
		$row['level'] = 0;
		$row['new_parent'] = $row['parent'];
		$tmp[$row['id']] = $row;
		$total++;
	}
	$rows = $thread->buildTree($tmp, $depth);
	unset($tmp, $row);

	if (!empty($formBefore)) {
		$rows = array_reverse($rows);
	}

	$tpl = ($modx->user->isAuthenticated() && !$thread->get('closed')) ? $tplCommentAuth : $tplCommentGuest;
	foreach ($rows as $row) {
		$output[] = $Tickets->templateNode($row, $tpl);
	}

	$pdoFetch->addTime('Returning processed chunks');
	if (empty($outputSeparator)) {$outputSeparator = "\n";}
	if (!empty($output)) {
		$output = implode($outputSeparator, $output);
	}
}

$commentsThread = $pdoFetch->getChunk($Tickets->config['tplComments'], array('total' => $total, 'comments' => $output));
$commentForm = $thread->get('closed') ? $modx->lexicon('ticket_thread_err_closed') : $Tickets->getCommentForm();
$output = (!empty($formBefore)) ? $commentForm . $commentsThread : $commentsThread . $commentForm;

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$output .= '<pre class="CommentsLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

// Return output
if (!empty($toPlaceholder)) {
	$modx->setPlaceholder($toPlaceholder, $output);
}
else {
	return $output;
}