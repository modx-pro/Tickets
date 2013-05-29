<?php
/**
 * Package in plugins
 *
 * @package miniShop
 * @subpackage build
 */
$plugins = array();
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->set('name','Tickets');
$plugins[0]->fromArray(array(
	'id' => 0
	,'category' => 0
	,'description' =>'Main plugin for Tickets'
	,'plugincode' => getSnippetContent($sources['plugins'] . 'tickets.php')
	//,'static' => 1
	//,'static_file' => 'core/components/tickets/elements/plugins/tickets.php'
));

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

$events[3]= $modx->newObject('modPluginEvent');
$events[3]->fromArray(array(
	'event' => 'OnDocFormRender',
	'priority' => 0,
	'propertyset' => 0,
),'',true,true);

$events[4]= $modx->newObject('modPluginEvent');
$events[4]->fromArray(array(
	'event' => 'OnWebPagePrerender',
	'priority' => 10,
	'propertyset' => 0,
),'',true,true);

$events[5]= $modx->newObject('modPluginEvent');
$events[5]->fromArray(array(
	'event' => 'OnPageNotFound',
	'priority' => 0,
	'propertyset' => 0,
),'',true,true);

$events[6]= $modx->newObject('modPluginEvent');
$events[6]->fromArray(array(
	'event' => 'OnWebPageComplete',
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
return $plugins;