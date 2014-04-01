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
		$q = trim(@$_REQUEST[$modx->context->getOption('request_param_alias','q')]);
		$matches = explode('/', rtrim($q, '/'));
		$count = count($matches);
		if ($count < 3) {return;}

		$ticket_uri = array_pop($matches);
		$section_uri = implode('/', $matches) . '/';

		if ($section_id = $modx->findResource($section_uri)) {
			/** @var TicketsSection $section */
			if ($section = $modx->getObject('TicketsSection', $section_id)) {
				if (is_numeric($ticket_uri)) {
					$ticket_id = $ticket_uri;
				}
				else {
					$properties = $section->getProperties('tickets');
					if (!empty($properties['uri']) && strpos($properties['uri'], '%id') !== false) {
						$pcre = str_replace('%id', '([0-9]+)', $properties['uri']);
						$pcre = preg_replace('/(\%[a-z]+)/', '(?:.*?)', $pcre);
						if (preg_match('/'.$pcre.'/', $ticket_uri, $matches)) {
							$ticket_id = $matches[1];
						}
					}
				}
				if (!empty($ticket_id)) {
					$modx->sendRedirect($modx->makeUrl($ticket_id, '', '', 'full'));
				}
			}
		}
		break;


	case 'OnWebPageComplete':
		$Tickets = $modx->getService('tickets');
		$Tickets->logView($modx->resource->id);
		break;


	case 'OnEmptyTrash':
		if (!empty($ids)) {
			$collection = $modx->getIterator('TicketThread', array('resource:IN' => $ids));
			/** @var TicketThread $item */
			foreach ($collection as $item) {
				$item->remove();
			}

			$collection = $modx->getIterator('TicketVote', array('id:IN' => $ids, 'class' => 'Ticket'));
			/** @var TicketVote $item */
			foreach ($collection as $item) {
				$item->remove();
			}

			$collection = $modx->getIterator('TicketStar', array('id:IN' => $ids, 'class' => 'Ticket'));
			/** @var TicketStar $item */
			foreach ($collection as $item) {
				$item->remove();
			}
		}
		break;

}