<?php
$plugins = array();

$tmp = array(
	'Tickets' => array(
		'file' => 'tickets'
		,'description' => ''
		,'events' => array(
			'OnDocFormSave',
			'OnSiteRefresh',
			'OnManagerPageInit',
			'OnDocFormRender',
			'OnWebPagePrerender',
			'OnPageNotFound',
			'OnWebPageComplete',
			'OnEmptyTrash',
		)
	)
);

foreach ($tmp as $k => $v) {
	/* @var modplugin $plugin */
	$plugin = $modx->newObject('modPlugin');
	$plugin->fromArray(array(
		'name' => $k
		,'description' => @$v['description']
		,'plugincode' => getSnippetContent($sources['source_core'].'/elements/plugins/plugin.'.$v['file'].'.php')
		,'static' => BUILD_PLUGIN_STATIC
		,'source' => 1
		,'static_file' => 'core/components/'.PKG_NAME_LOWER.'/elements/plugins/plugin.'.$v['file'].'.php'
	),'',true,true);

	$events = array();
	if (!empty($v['events']) && is_array($v['events'])) {
		foreach ($v['events'] as $k2 => $v2) {
			/* @var $event modPluginEvent */
			$event = $modx->newObject('modPluginEvent');
			$event->fromArray(array(
				'event' => $v2,
				'priority' => 0,
				'propertyset' => 0,
			),'',true,true);
			$events[] = $event;
		}
		unset($v['events']);
	}

	if (!empty($events)) {
		$plugin->addMany($events);
	}

	$plugins[] = $plugin;
}

return $plugins;