<?php

$properties = array();

$tmp = array(
	'action' => array(
		'type' => 'list'
		,'options' => array(
			array('text' => 'getTicketForm','value' => 'getTicketForm')
			,array('text' => 'saveTicket','value' => 'saveTicket')
			,array('text' => 'previewTicket','value' => 'previewTicket')
		)
		,'value' => 'getTicketForm'
	)
	,'tplFormCreate' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.form.create'
	)
	,'tplFormUpdate' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.form.update'
	)
	,'tplSectionRow' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.form.section.row'
	)
	,'tplPreview' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.form.preview'
	)
	,'tplTicketEmailBcc' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.ticket.email.bcc'
	)
	,'allowedFields' => array(
		'type' => 'textfield'
		,'value' => 'parent,pagetitle,content,published'
	)
	,'requiredFields' => array(
		'type' => 'textfield'
		,'value' => 'parent,pagetitle,content'
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