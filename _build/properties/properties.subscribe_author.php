<?php

$properties = array();

$tmp = array(
    'tpl' => array(
        'type' => 'textfield',
        'value' => 'tpl.Tickets.author.subscribe',
    ),
    'TicketsInit' => array(
        'type' => 'textfield',
        'value' => '0',
    ),
    'createdby' => array(
        'type' => 'textfield',
        'value' => '0',
    ),
);

foreach ($tmp as $k => $v) {
    $properties[$k] = array_merge(array(
        'name' => $k,
        'desc' => 'tickets_prop_' . $k,
        'lexicon' => 'tickets:properties',
    ), $v);
}

return $properties;