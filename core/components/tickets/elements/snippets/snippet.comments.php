<?php
/* @var array $scriptProperties */
if (empty($scriptProperties['thread']) && !empty($modx->resource)) {$scriptProperties['thread'] = 'resource-'.$modx->resource->id;}
$scriptProperties = array_merge(array(
	'resource' => $modx->resource->id
	,'snippetPrepareComment' => $modx->getOption('tickets.snippet_prepare_comment')
	,'commentEditTime' => $modx->getOption('tickets.comment_edit_time', null, 180)
), $scriptProperties);

if (!isset($depth)) {$depth = 0;}
if (!isset($tplCommentAuth)) {$tplCommentAuth = 'tpl.Tickets.comment.one.auth';}
if (!isset($tplCommentGuest)) {$tplCommentGuest = 'tpl.Tickets.comment.one.guest';}

/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets ','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
$Tickets->initialize($modx->context->key, $scriptProperties);

/* @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH.'components/pdotools/model/pdotools/',$scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

/* @var TicketThread $thread */
if (!$thread = $modx->getObject('TicketThread', array('name' => $scriptProperties['thread']))) {
	$thread = $modx->newObject('TicketThread');
	$thread->fromArray(array(
		'name' => $scriptProperties['thread']
		,'resource' => $modx->resource->id
		,'createdby' => $modx->user->id
		,'createdon' => date('Y-m-d H:i:s')
		,'subscribers' => array($modx->resource->get('createdby'))
	));
}
else if ($thread->get('deleted')) {
	return $modx->lexicon('ticket_thread_err_deleted');
}
// Migrate authors to subscription system
if (!is_array($thread->get('subscribers'))) {
	$thread->set('subscribers', array($modx->resource->get('createdby')));
}

$scriptProperties['resource'] = $modx->resource->id;
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
	,'limit' => 0
	,'fastMode' => true
	,'return' => 'sql'
	,'nestedChunkPrefix' => 'tickets_'
);

// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties));
$pdoFetch->addTime('Query parameters are prepared.');
$sql = $pdoFetch->run();

/* @var PDOStatement $q*/
$q = $modx->prepare($sql);
$pdoFetch->addTime('SQL prepared <small>"'.$q->queryString.'"</small>');
if (!$q->execute()) {
	$modx->log(modX::LOG_LEVEL_INFO, '[pdoTools] '.$sql);
	$errors = $q->errorInfo();
	$modx->log(modX::LOG_LEVEL_ERROR, '[pdoTools] Error '.$errors[0].': '.$errors[2]);
}
$pdoFetch->addTime('SQL executed.');

$q2 = $modx->prepare("SELECT FOUND_ROWS();");
$q2->execute();
$total = $q2->fetch(PDO::FETCH_COLUMN);
$pdoFetch->addTime('Total rows: <b>'.$total.'</b>');

$rows = array();
while ($row = $q->fetch(PDO::FETCH_ASSOC))  {
	$row['level'] = 0;
	$row['new_parent'] = $row['parent'];
	$rows[$row['id']] = $row;
}
$pdoFetch->addTime('Rows fetched');

// Processing rows
$output = $commentsThread = null;
if (!empty($rows) && is_array($rows)) {
	$tmp = array();

	$rows = $thread->buildTree($rows, $depth);
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

$commentsThread = $pdoFetch->getChunk($Tickets->config['tplComments'], array(
	'total' => $total
	,'comments' => $output
	,'subscribed' => $thread->isSubscribed()
));

$form = !$modx->user->isAuthenticated()
	? $Tickets->getChunk($Tickets->config['tplLoginToComment'])
	: $Tickets->getChunk($Tickets->config['tplCommentForm'], array('thread' => $scriptProperties['thread']));

$commentForm = $thread->get('closed') ? $modx->lexicon('ticket_thread_err_closed') : $form;
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