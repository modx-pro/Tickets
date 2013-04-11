<?php

$properties = array();

$tmp = array(
	'thread' => array(
		'name' => 'thread'
		,'type' => 'textfield'
		,'value' => ''
	)
	,'fastMode' => array(
		'type' => 'combo-boolean'
		,'value' => true
	)/*
	,'dateFormat' => array(
		'type' => 'textfield'
		,'value' => 'd F Y, H:i'
	)*/
	,'gravatarIcon' => array(
		'type' => 'textfield'
		,'value' => 'mm'
	)
	,'gravatarSize' => array(
		'type' => 'numberfield'
		,'value' => '24'
	)
	,'gravatarUrl' => array(
		'type' => 'textfield'
		,'value' => 'http://www.gravatar.com/avatar/'
	)
	,'tplCommentForm' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.form'
	)
	,'tplCommentAuth' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.one.auth'
	)
	,'tplCommentGuest' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.one.guest'
	)
	,'tplComments' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.wrapper'
	)
	,'tplLoginToComment' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.login'
	)
	,'tplCommentEmailOwner' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.email.owner'
	)
	,'tplCommentEmailReply' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.email.reply'
	)
);

foreach ($tmp as $k => $v) {
	$properties[$k] = array_merge(array(
		'name' => $k
		,'desc' => 'tickets_prop_'.$k
		,'lexicon' => 'tickets:properties'
	), $v);
}

return $properties;
