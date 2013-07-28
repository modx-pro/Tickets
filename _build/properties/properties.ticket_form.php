<?php

$properties = array();

$tmp = array(
	'tplFormCreate' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.form.create'
	)
	,'tplFormUpdate' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.form.update'
	)
	,'tplPreview' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.form.preview'
	)
	,'tplSectionRow' => array(
		'type' => 'textfield'
		,'value' => 'tpl.Tickets.form.section.row'
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