<?php
$xpdo_meta_map['TicketAttachment']= array (
  'package' => 'tickets',
  'version' => '0.1.0',
  'table' => 'tickets_attachments',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'parent' => 0,
    'file' => NULL,
    'name' => NULL,
    'description' => NULL,
    'type' => NULL,
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
      'index' => 'index',
    ),
    'file' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'index' => 'index',
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'index' => 'index',
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'null' => true,
      'phptype' => 'text',
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'null' => true,
      'precision' => '100',
      'phptype' => 'string',
      'index' => 'index',
    ),
    'class' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'index' => 'index',
    ),
  ),
);
