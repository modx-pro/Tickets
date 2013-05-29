<?php
$xpdo_meta_map['TicketVote']= array (
  'package' => 'tickets',
  'version' => '1.1',
  'table' => 'tickets_votes',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'parent' => 0,
    'ip' => '0.0.0.0',
    'value' => NULL,
    'createdon' => NULL,
    'createdby' => 0,
    'class' => NULL,
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
    'ip' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => false,
      'default' => '0.0.0.0',
    ),
    'value' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'createdon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
      'index' => 'index',
    ),
    'createdby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'pk',
    ),
    'class' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'index' => 'pk',
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
        'createdby' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'createdon' => 
    array (
      'alias' => 'createdon',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'createdon' => 
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
