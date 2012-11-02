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
		,'value' => 'comments'
		,'options' => array(
			array('text' => 'Comments','value' => 'comments')
			,array('text' => 'Tickets','value' => 'tickets')
		)
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'tpl'
		,'desc' => 'tickets.tpl'
		,'type' => 'textfield'
		,'value' => 'tpl.Tickets.comment.latest'
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'start',
		'desc' => 'tickets.start',
		'type' => 'textfield',
		'options' => '',
		'value' => 0,
		'lexicon' => 'tickets:properties',
	)
	,array(
		'name' => 'limit'
		,'desc' => 'tickets.limit'
		,'type' => 'numberfield'
		,'value' => 20
		,'lexicon' => 'tickets:properties'
	)
	,array(
		'name' => 'sortBy',
		'desc' => 'tickets.sortBy',
		'type' => 'textfield',
		'value' => 'createdon',
		'lexicon' => 'tickets:properties',
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
		'name' => 'cacheKey',
		'desc' => 'tickets.cacheKey',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'tickets:properties',
	)
);

return $properties;