<?php
$xpdo_meta_map['TicketVote']= array (
  'package' => 'tickets',
  'version' => '0.1.0',
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
    ),
    'createdby' => 
    array (
      'dbtype' => 'integer',
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
);
