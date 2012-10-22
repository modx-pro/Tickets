<?php
/**
 * Properties for the Tickets snippet.
 *
 * @package tickets
 * @subpackage build
 */
$properties = array(
	array(
		'name' => 'action'
		,'desc' => 'tickets.action'
		,'type' => 'list'
		,'value' => 'getTicketForm'
		,'options' => array(
			array('text' => 'getTicketForm','value' => 'getTicketForm')
			,array('text' => 'saveTicket','value' => 'saveTicket')
			,array('text' => 'previewTicket','value' => 'previewTicket')
		)
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplFormCreate'
		,'desc' => 'tickets.tplFormCreate'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.form.create'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplFormUpdate'
		,'desc' => 'tickets.tplFormUpdate'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.form.update'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplSectionRow'
		,'desc' => 'tickets.tplSectionRow'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.form.section.row'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tplPreview'
		,'desc' => 'tickets.tplPreview'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.form.preview'
		,'lexicon' => 'tickets:properties'
	)
);

return $properties;