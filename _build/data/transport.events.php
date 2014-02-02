<?php

$events = array();

$tmp = array(
	'OnBeforeCommentSave' => array(),
	'OnCommentSave' => array(),
	'OnBeforeCommentDelete' => array(),
	'OnCommentDelete' => array(),
	'OnBeforeCommentRemove' => array(),
	'OnCommentRemove' => array(),
	'OnBeforeTicketThreadDelete' => array(),
	'OnTicketThreadDelete' => array(),
	'OnBeforeTicketThreadRemove' => array(),
	'OnTicketThreadRemove' => array(),
);

foreach ($tmp as $k => $v) {
	/* @var modEvent $event */
	$event = $modx->newObject('modEvent');
	$event->fromArray(array_merge(array(
		'name' => $k,
		'service' => 6,
		'groupname' => PKG_NAME,
	), $v)
	,'', true, true);

	$events[] = $event;
}

return $events;