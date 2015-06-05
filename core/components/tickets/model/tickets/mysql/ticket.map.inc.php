<?php
$xpdo_meta_map['Ticket']= array (
  'package' => 'tickets',
  'version' => '1.1',
  'extends' => 'modResource',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'composites' => 
  array (
    'Views' => 
    array (
      'class' => 'TicketView',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Votes' => 
    array (
      'class' => 'TicketVote',
      'local' => 'id',
      'foreign' => 'id',
      'cardinality' => 'many',
      'owner' => 'local',
      'criteria' => 
      array (
        'local' => 
        array (
          'class' => 'Ticket',
        ),
      ),
    ),
    'Stars' => 
    array (
      'class' => 'TicketStar',
      'local' => 'id',
      'foreign' => 'id',
      'cardinality' => 'many',
      'owner' => 'local',
      'criteria' => 
      array (
        'local' => 
        array (
          'class' => 'Ticket',
        ),
      ),
    ),
    'Files' => 
    array (
      'class' => 'TicketFile',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
      'criteria' => 
      array (
        'local' => 
        array (
          'class' => 'Ticket',
        ),
      ),
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
    'Threads' => 
    array (
      'class' => 'TicketThread',
      'local' => 'id',
      'foreign' => 'resource',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
