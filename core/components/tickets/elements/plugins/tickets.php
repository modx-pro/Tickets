<?php

switch($modx->event->name) {
	case 'OnManagerPageInit':
		$cssFile = $modx->getOption('tickets.assets_url',null,$modx->getOption('assets_url').'components/tickets/').'css/mgr/tickets.css';
		$modx->regClientCSS($cssFile);
	break;

	case 'OnSiteRefresh':
		if ($modx->cacheManager->refresh(array('default/tickets' => array()))) {
			$modx->log(modX::LOG_LEVEL_INFO, $modx->lexicon('refresh_default').': Tickets');
		}
	break;

	case 'OnDocFormRender':
		if ($resource->class_key == "TicketsSection") {
			/* @var TicketsSection $resource */
			$resource->set('syncsite', 0);
		}
	break;

	case 'OnDocFormSave':
		/* @var Ticket $resource */
		if ($mode == 'new' && $resource->class_key == "Ticket") {
			$modx->cacheManager->delete('tickets/latest.tickets');
		}
		/* @var TicketsSection $resource */
		if ($mode == 'upd' && $resource->class_key == 'TicketsSection') {
			if (method_exists($resource, 'clearCache')) {
				$resource->clearCache();
			}
		}
	break;

}