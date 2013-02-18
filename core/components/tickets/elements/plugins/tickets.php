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

	case 'OnWebPagePrerender':
		$output = & $modx->resource->_output;
		$output = str_replace(array('{{{{{','}}}}}'), array('[',']'), $output);
	break;

	case 'OnPageNotFound':
		// It is working only with friendly urls enabled
		$q = trim($_REQUEST['q']);
		$matches = explode('/', $q);
		$count = count($matches);
		if ($count < 3) {return;}

		$section = $matches[$count - 3];
		$ticket = $matches[$count - 2];

		// Redirect to requested page, when you moved ticket from one section to another
		if ($modx->getCount('TicketsSection',array('class_key' => 'TicketsSection', 'alias' => $section, 'deleted' => 0, 'published' => 1))) {
			if (preg_match('/^\d+$/', $ticket)) {
				if ($modx->getCount('Ticket',  array('id' => $ticket, 'published' => 1, 'deleted' => 0))) {
					$url = $modx->makeUrl($ticket, '', '', 'full');
					$modx->sendRedirect($url);
				}
			}
		}
	break;

}