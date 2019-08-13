<?php

$properties = array();

$tmp = array(
    'tpl' => array(
        'type' => 'textfield',
        'value' => 'tpl.Tickets.author.subscribe',
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