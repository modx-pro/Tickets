<?php

$events = array();

$tmp = array(
	'OnBeforeCommentSave' => array(),
	'OnCommentSave' => array(),

	'OnBeforeCommentPublish' => array(),
	'OnCommentPublish' => array(),
	'OnBeforeCommentUnpublish' => array(),
	'OnCommentUnpublish' => array(),
	'OnBeforeCommentDelete' => array(),
	'OnCommentDelete' => array(),
	'OnBeforeCommentUndelete' => array(),
	'OnCommentUndelete' => array(),

	'OnBeforeCommentRemove' => array(),
	'OnCommentRemove' => array(),

	'OnBeforeTicketThreadClose' => array(),
	'OnTicketThreadClose' => array(),
	'OnBeforeTicketThreadOpen' => array(),
	'OnTicketThreadOpen' => array(),
	'OnBeforeTicketThreadDelete' => array(),
	'OnTicketThreadDelete' => array(),
	'OnBeforeTicketThreadUndelete' => array(),
	'OnTicketThreadUndelete' => array(),

	'OnBeforeTicketThreadRemove' => array(),
	'OnTicketThreadRemove' => array(),

	'OnTicketVote' => array(),
	'OnCommentVote' => array(),

	'OnTicketStar' => array(),
	'OnTicketUnStar' => array(),
	'OnCommentStar' => array(),
	'OnCommentUnStar' => array(),
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