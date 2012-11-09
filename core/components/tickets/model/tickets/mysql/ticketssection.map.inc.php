<?php
$xpdo_meta_map['TicketsSection']= array (
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
    'Tickets' => 
    array (
      'class' => 'Ticket',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
