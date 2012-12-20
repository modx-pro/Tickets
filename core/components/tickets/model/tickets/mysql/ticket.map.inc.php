<?php
$xpdo_meta_map['Ticket']= array (
  'package' => 'tickets',
  'version' => '0.1.0',
  'extends' => 'modResource',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'composites' => 
  array (
    'Votes' => 
    array (
      'class' => 'TicketVote',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Views' => 
    array (
      'class' => 'TicketView',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
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
