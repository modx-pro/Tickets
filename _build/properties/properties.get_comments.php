<?php

$properties = array();

$tmp = array(
    'tpl' => array(
        'type' => 'textfield',
        'value' => 'tpl.Tickets.comment.list.row',
    ),
    'tplCommentDeleted' => array(
        'type' => 'textfield',
        'value' => 'tpl.Tickets.comment.one.deleted',
    ),
    'depth' => array(
        'type' => 'numberfield',
        'value' => 10,
    ),
    'threads' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'parents' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'resources' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'sortby' => array(
        'type' => 'textfield',
        'value' => 'createdon',
    ),
    'sortdir' => array(
        'type' => 'list',
        'options' => array(
            array('text' => 'ASC', 'value' => 'ASC'),
            array('text' => 'DESC', 'value' => 'DESC'),
        ),
        'value' => 'DESC',
    ),
    'includeContent' => array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'toPlaceholder' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'where' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'outputSeparator' => array(
        'type' => 'textfield',
        'value' => "\n",
    ),
    'showLog' => array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'showUnpublished' => array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'showDeleted' => array(
        'type' => 'combo-boolean',
        'value' => false,
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
