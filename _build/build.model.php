<?php

if (!defined('MODX_BASE_PATH')) {
	require 'build.config.php';
}

/* define sources */
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
	'root' => $root,
	'build' => $root . '_build/',
	'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
	'model' => $root.'core/components/'.PKG_NAME_LOWER.'/model/',
	'schema' => $root.'core/components/'.PKG_NAME_LOWER.'/model/schema/',
	'xml' => $root.'core/components/'.PKG_NAME_LOWER.'/model/schema/'.PKG_NAME_LOWER.'.mysql.schema.xml',
);
unset($root);

require MODX_CORE_PATH . 'model/modx/modx.class.php';
require $sources['build'] . '/includes/functions.php';

$modx= new modX();
$modx->initialize('mgr');
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->loadClass('transport.modPackageBuilder', '', false, true);

/** @var xPDOManager $manager */
$manager = $modx->getManager();
/** @var xPDOGenerator $generator */
$generator = $manager->getGenerator();

// Remove old model
rrmdir($sources['model'] . PKG_NAME_LOWER . '/mysql');

// Generate a new one
$generator->parseSchema($sources['xml'], $sources['model']);

// Add connection to modUser
$data = file_get_contents($sources['model'] . 'tickets/metadata.mysql.php');
$data .= '
$this->map[\'modUser\'][\'composites\'][\'AuthorProfile\'] = array(
  \'class\' => \'TicketAuthor\',
  \'local\' => \'id\',
  \'foreign\' => \'id\',
  \'cardinality\' => \'one\',
  \'owner\' => \'local\',
);';
file_put_contents($sources['model'] . 'tickets/metadata.mysql.php', $data);

$modx->log(modX::LOG_LEVEL_INFO, 'Model generated.');