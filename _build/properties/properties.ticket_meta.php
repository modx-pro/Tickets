<?php

$properties = array();

$tmp = array(
	'tpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.Tickets.meta',
		'desc' => 'tickets_prop_meta_tpl'
	),
	'getSection' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'getUser' => array(
		'type' => 'combo-boolean',
		'value' => true,
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