<?php
/**
 * Properties for the Tickets snippet.
 *
 * @package tickets
 * @subpackage build
 */
$properties = array(
	array(
		'name' => 'tpl',
		'desc' => 'prop_tickets.tpl_desc',
		'type' => 'textfield',
		'options' => '',
		'value' => 'tpl.Tickets.item',
		'lexicon' => 'tickets:properties',
	),
	array(
		'name' => 'sortBy',
		'desc' => 'prop_tickets.sortby_desc',
		'type' => 'textfield',
		'options' => '',
		'value' => 'name',
		'lexicon' => 'tickets:properties',
	),
	array(
		'name' => 'sortDir',
		'desc' => 'prop_tickets.sortdir_desc',
		'type' => 'list',
		'options' => array(
			array('text' => 'ASC','value' => 'ASC'),
			array('text' => 'DESC','value' => 'DESC'),
		),
		'value' => 'ASC',
		'lexicon' => 'tickets:properties',
	),
	array(
		'name' => 'limit',
		'desc' => 'prop_tickets.limit_desc',
		'type' => 'numberfield',
		'options' => '',
		'value' => 5,
		'lexicon' => 'tickets:properties',
	),
	array(
		'name' => 'outputSeparator',
		'desc' => 'prop_tickets.outputseparator_desc',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
		'lexicon' => 'tickets:properties',
	),
	array(
		'name' => 'toPlaceholder',
		'desc' => 'prop_tickets.toplaceholder_desc',
		'type' => 'combo-boolean',
		'options' => '',
		'value' => false,
		'lexicon' => 'tickets:properties',
	),
/*
	array(
		'name' => '',
		'desc' => 'prop_tickets.',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
		'lexicon' => 'tickets:properties',
	),
	*/
);

return $properties;