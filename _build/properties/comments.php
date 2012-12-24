<?php
/**
 * Properties for the TicketComments snippet.
 *
 * @package tickets
 * @subpackage build
 */
$properties = array(
	array(
		'name' => 'thread'
		,'desc' => 'tickets.thread'
		,'type' => 'textfield'
		,'value' => ''
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'fastMode'
		,'desc' => 'tickets.fastMode'
		,'type' => 'combo-boolean'
		,'value' => true
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'dateFormat'
		,'desc' => 'tickets.dateFormat'
		,'type' => 'textfield'
		,'value' => '%d %b %Y %H:%M'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'gravatarIcon'
		,'desc' => 'tickets.gravatarIcon'
		,'type' => 'textfield'
		,'value' => 'mm'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'gravatarSize'
		,'desc' => 'tickets.gravatarSize'
		,'type' => 'numberfield'
		,'value' => '24'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'gravatarUrl'
		,'desc' => 'tickets.gravatarUrl'
		,'type' => 'textfield'
		,'value' => 'http://www.gravatar.com/avatar/'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplCommentForm'
		,'desc' => 'tickets.tplCommentForm'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.form'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplCommentAuth'
		,'desc' => 'tickets.tplCommentAuth'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.one.auth'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplCommentGuest'
		,'desc' => 'tickets.tplCommentGuest'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.one.guest'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplComments'
		,'desc' => 'tickets.tplComments'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.wrapper'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplLoginToComment'
		,'desc' => 'tickets.tplLoginToComment'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.login'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplCommentEmailOwner'
		,'desc' => 'tickets.tplCommentEmailOwner'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.email.owner'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplCommentEmailReply'
		,'desc' => 'tickets.tplCommentEmailReply'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.email.reply'
		,'lexicon' => 'tickets:properties'
	)

);

return $properties;