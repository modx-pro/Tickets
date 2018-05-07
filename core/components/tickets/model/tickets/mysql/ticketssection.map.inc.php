<?php
$xpdo_meta_map['TicketsSection']= array (
  'package' => 'tickets',
  'version' => '1.1',
  'extends' => 'modResource',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
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
    'Total' => 
    array (
      'class' => 'TicketTotal',
      'local' => 'id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
      'criteria' => 
      array (
        'foreign' => 
        array (
          'class' => 'TicketsSection',
        ),
      ),
    ),
  ),
);
