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

	case 'OnDocFormSave':
		if ($modx->event->params['mode'] == 'new') {
			$resource = $modx->event->params['resource'];
			if ($resource->get('class_key') == 'Ticket') {
				$modx->cacheManager->delete('tickets/latest.tickets');
			}
		}
}