<?php

$properties = array();

$tmp = array(
	'thread' => array(
		'name' => 'thread',
		'type' => 'textfield',
		'value' => '',
	),
	'fastMode' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),/*
	'dateFormat' => array(
		'type' => 'textfield'
		'value' => 'd F Y H:i'
	),*/
	'gravatarIcon' => array(
		'type' => 'textfield',
		'value' => 'mm',
	),
	'gravatarSize' => array(
		'type' => 'numberfield',
		'value' => '24',
	),
	'gravatarUrl' => array(
		'type' => 'textfield',
		'value' => 'http://www.gravatar.com/avatar/',
	),
	'tplCommentForm' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.comment.form',
	),
	'tplCommentFormGuest' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.comment.form.guest',
	),
	'tplCommentAuth' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.comment.one.auth',
	),
	'tplCommentGuest' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.comment.one.guest',
	),
	'tplCommentDeleted' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.comment.one.deleted',
	),
	'tplComments' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.comment.wrapper',
	),
	'tplLoginToComment' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.comment.login',
	),
	'tplCommentEmailOwner' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.comment.email.owner',
	),
	'tplCommentEmailReply' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.comment.email.reply',
	),
	'tplCommentEmailSubscription' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.comment.email.subscription',
	),
	'tplCommentEmailBcc' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.comment.email.bcc',
	),
	'autoPublish' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'formBefore' => array(
		'type' => 'combo-boolean',
		'value' => false,
	),
	'depth' => array(
		'type' => 'numberfield',
		'desc' => 'tickets_prop_commentsDepth',
		'value' => 0,
	),
	'allowGuest' => array(
		'type' => 'combo-boolean',
		'value' => false,
	),
	'allowGuestEdit' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'allowGuestEmails' => array(
		'type' => 'combo-boolean',
		'value' => false,
	),
	'enableCaptcha' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'minCaptcha' => array(
		'type' => 'numberfield',
		'value' => 1,
	),
	'maxCaptcha' => array(
		'type' => 'numberfield',
		'value' => 10,
	),
);

foreach ($tmp as $k => $v) {
	$properties[$k] = array_merge(array(
		'name' => $k,
		'desc' => 'tickets_prop_'.$k,
		'lexicon' => 'tickets:properties',
	), $v);
}

return $properties;
