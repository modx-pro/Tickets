<?php
/**
 * Properties for the TicketComments snippet.
 *
 * @package tickets
 * @subpackage build
 */
$properties = array(
	array(
		'name' => 'useCss'
		,'desc' => 'tickets.useCss'
		,'type' => 'combo-boolean'
		,'value' => true
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'useJs'
		,'desc' => 'tickets.useJs'
		,'type' => 'combo-boolean'
		,'value' => true
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'useGravatar'
		,'desc' => 'tickets.useGravatar'
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
		,'value' => 'identicon'
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
		'name' => 'nameField'
		,'desc' => 'tickets.useGravatar'
		,'type' => 'textfield'
		,'value' => 'fullname'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplAddComment'
		,'desc' => 'tickets.tplAddComment'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.form'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplComment'
		,'desc' => 'tickets.tplComment'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.one'
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
		'name' => 'maxDepth'
		,'desc' => 'tickets.maxDepth'
		,'type' => 'numberfield'
		,'value' => 10
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'closeAfter'
		,'desc' => 'tickets.closeAfter'
		,'type' => 'numberfield'
		,'value' => 0
		,'lexicon' => 'tickets:properties'
	)

);

return $properties;