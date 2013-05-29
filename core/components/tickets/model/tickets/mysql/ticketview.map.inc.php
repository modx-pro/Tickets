<?php
$xpdo_meta_map['TicketView']= array (
  'package' => 'tickets',
  'version' => '1.1',
  'table' => 'tickets_views',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'parent' => 0,
    'uid' => 0,
    'timestamp' => NULL,
  ),
  'fieldMeta' => 
  array (
    'parent' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'pk',
    ),
    'uid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'pk',
    ),
    'timestamp' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'unique_key' => 
    array (
      'alias' => 'unique_key',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'parent' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'uid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Ticket' => 
    array (
      'class' => 'Ticket',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
