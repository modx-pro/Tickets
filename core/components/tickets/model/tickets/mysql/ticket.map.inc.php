<?php
$xpdo_meta_map['Ticket']= array (
  'package' => 'tickets',
  'version' => NULL,
  'extends' => 'modResource',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'aggregates' => 
  array (
    'Section' => 
    array (
      'class' => 'TicketsSection',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
