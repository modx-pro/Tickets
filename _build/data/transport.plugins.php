<?php
/**
 * Package in plugins
 *
 * @package miniShop
 * @subpackage build
 */
$plugins = array();

/* create the plugin object */
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->set('id',1);
$plugins[0]->set('name','Tickets');
$plugins[0]->set('description','Main plugin for Tickets');
$plugins[0]->set('plugincode', getSnippetContent($sources['plugins'] . 'tickets.php'));
$plugins[0]->set('category',0);


$events = array();

$events[0]= $modx->newObject('modPluginEvent');
$events[0]->fromArray(array(
	'event' => 'OnDocFormSave',
	'priority' => 0,
	'propertyset' => 0,
),'',true,true);

$events[1]= $modx->newObject('modPluginEvent');
$events[1]->fromArray(array(
	'event' => 'OnSiteRefresh',
	'priority' => 0,
	'propertyset' => 0,
),'',true,true);

$events[2]= $modx->newObject('modPluginEvent');
$events[2]->fromArray(array(
	'event' => 'OnManagerPageInit',
	'priority' => 0,
	'propertyset' => 0,
),'',true,true);

if (is_array($events) && !empty($events)) {
	$plugins[0]->addMany($events);
	$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' plugin events.'); flush();
} else {
	$modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events!');
}
unset($events);

/*
$properties = array();
if (is_array($properties)) {
	$modx->log(xPDO::LOG_LEVEL_INFO,'Set '.count($properties).' plugin properties.'); flush();
	$plugins[0]->setProperties($properties);
} else {
	$modx->log(xPDO::LOG_LEVEL_ERROR,'Could not set plugin properties.');
}
unset($properties);
*/

return $plugins;