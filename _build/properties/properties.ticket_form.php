<?php

$properties = array();

$tmp = array(
	'tplFormCreate' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.form.create',
	),
	'tplFormUpdate' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.form.update',
	),
	'tplPreview' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.form.preview',
	),
	'tplSectionRow' => array(
		'type' => 'textfield',
		'value' => '@INLINE <option value="[[+id]]" [[+selected]]>[[+pagetitle]]</option>',
	),
	'tplTicketEmailBcc' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.ticket.email.bcc',
	),
	'allowedFields' => array(
		'type' => 'textfield',
		'value' => 'parent,pagetitle,content,published',
	),
	'requiredFields' => array(
		'type' => 'textfield',
		'value' => 'parent,pagetitle,content',
	),
	'redirectUnpublished' => array(
		'type' => 'numberfield',
		'value' => 0,
	),

	'parents' => array(
		'type' => 'textfield',
		'value' => '',
		'desc' => 'tickets_prop_sections_parents'
	),
	'permissions' => array(
		'type' => 'textfield',
		'value' => 'section_add_children',
		'desc' => 'tickets_prop_sections_permissions'
	),
	'sortby' => array(
		'type' => 'textfield',
		'value' => 'pagetitle',
		'desc' => 'tickets_prop_sections_sortby'
	),
	'sortdir' => array(
		'type' => 'list',
		'options' => array(
			array('text' => 'ASC','value' => 'ASC'),
			array('text' => 'DESC','value' => 'DESC'),
		),
		'value' => 'ASC',
		'desc' => 'tickets_prop_sections_sortdir'
	),
	'context' => array(
		'type' => 'textfield',
		'value' => '',
		'desc' => 'tickets_prop_sections_context',
	),

	'allowFiles' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'source' => array(
		'type' => 'numberfield',
		'value' => 0,
	),
	'tplFiles' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.form.files',
	),
	'tplFile' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.form.file',
	),
	'tplImage' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.form.image',
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