<?php
/* @var array $scriptProperties */
/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
$Tickets->initialize($modx->context->key, $scriptProperties);

$scriptProperties['nestedChunkPrefix'] = 'tickets_';
/** @var pdoFetch $pdoFetch */
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
if (!$pdoClass = $modx->loadClass($fqn, '', false, true)) {return false;}
$pdoFetch = new $pdoClass($modx, $scriptProperties);
$pdoFetch->addTime('pdoTools loaded');

/** @var Ticket $ticket */
$ticket = !empty($id) && $id != $modx->resource->id
	? $modx->getObject('Ticket', $id)
	: $modx->resource;

if (!($ticket instanceof Ticket)) {
	return 'This resource is not instance of Ticket class.';
}

$data = $ticket->toArray();
$vote = $pdoFetch->getObject('TicketVote', array('id' => $ticket->id, 'class' => 'Ticket', 'createdby' => $modx->user->id), array('select' => 'value', 'sortby' => 'id'));
if (!empty($vote)) {
	$data['vote'] = $vote['value'];
}

if ($data['rating'] > 0) {
	$data['rating'] = '+'.$data['rating'];
	$data['rating_positive'] = 1;
}
elseif ($data['rating'] < 0) {
	$data['rating_negative'] = 1;
}

if (!$modx->user->id || $modx->user->id == $ticket->createdby) {
	$data['cant_vote'] = 1;
}
elseif (array_key_exists('vote', $data)) {
	if ($data['vote'] == '') {
		$data['can_vote'] = 1;
	}
	elseif ($data['vote'] > 0) {
		$data['voted_plus'] = 1;
		$data['cant_vote'] = 1;
	}
	elseif ($data['vote'] < 0) {
		$data['voted_minus'] = 1;
		$data['cant_vote'] = 1;
	}
	else {
		$data['voted_none'] = 1;
		$data['cant_vote'] = 1;
	}
}
$data['active'] = (integer) !empty($data['can_vote']);
$data['inactive'] = (integer) !empty($data['cant_vote']);

if (!empty($getSection)) {
	$fields = $modx->getFieldMeta('TicketsSection');
	unset($fields['content']);
	$section = $pdoFetch->getObject('TicketsSection', $ticket->parent, array('select' => implode(',', array_keys($fields))));
	foreach ($section as $k => $v) {
		$data['section.'.$k] = $v;
	}
}
if (!empty($getUser)) {
	$fields = $modx->getFieldMeta('modUserProfile');
	$user = $pdoFetch->getObject('modUserProfile', array('internalKey' => $ticket->createdby), array(
		'innerJoin' => array(
			'modUser' => array('class' => 'modUser', 'on' => '`modUserProfile`.`internalKey` = `modUser`.`id`')
		),
		'select' => array(
			'modUserProfile' => implode(',', array_keys($fields)),
			'modUser' => 'username',
		)
	));

	$data = array_merge($data, $user);
}
$data['id'] = $ticket->id;

return !empty($tpl)
	? $pdoFetch->getChunk($tpl, $data)
	: $pdoFetch->getChunk('', $data);
