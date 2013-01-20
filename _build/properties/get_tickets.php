<?php
	/**
	 * Properties for the getTickets snippet.
	 *
	 * @package tickets
	 * @subpackage build
	 */
$properties = array(
	array(
		'name' => 'tpl'
		,'desc' => 'tickets.tpl'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.list.row'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'limit'
		,'desc' => 'tickets.limit'
		,'type' => 'numberfield'
		,'value' => 10
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'sortBy'
		,'desc' => 'tickets.sortBy'
		,'type' => 'textfield'
		,'value' => 'createdon'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'sortDir',
		'desc' => 'tickets.sortDir',
		'type' => 'list',
		'options' => array(
			array('text' => 'ASC','value' => 'ASC'),
			array('text' => 'DESC','value' => 'DESC'),
		),
		'value' => 'DESC',
		'lexicon' => 'tickets:properties',
	)
	,array(
		'name' => 'toPlaceholder',
		'desc' => 'tickets.toPlaceholder',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'tickets:properties',
	)
	,array(
		'name' => 'showLog',
		'desc' => 'tickets.showLog',
		'type' => 'combo-boolean',
		'value' => 'true',
		'lexicon' => 'tickets:properties',
	)
	,array(
		'name' => 'parents',
		'desc' => 'tickets.parents',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'tickets:properties',
	)
	,array(
		'name' => 'fastMode',
		'desc' => 'tickets.fastMode',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'tickets:properties',
	)
);

return $properties;