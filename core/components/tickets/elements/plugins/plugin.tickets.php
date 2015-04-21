<?php
switch ($modx->event->name) {

	case 'OnManagerPageInit':
		/** @var Tickets $Tickets */
		$Tickets = $modx->getService('Tickets');
		$modx->regClientStartupHTMLBlock('<script type="text/javascript">
			MODx.modx23 = ' . (int)$Tickets->systemVersion() . ';
		</script>');
		break;


	case 'OnSiteRefresh':
		if ($modx->cacheManager->refresh(array('default/tickets' => array()))) {
			$modx->log(modX::LOG_LEVEL_INFO, $modx->lexicon('refresh_default') . ': Tickets');
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
		$output = &$modx->resource->_output;
		$output = str_replace(array('*{*{*{*{*{*', '*}*}*}*}*}*'), array('[', ']'), $output);
		break;


	case 'OnPageNotFound':
		// It is working only with friendly urls enabled
		$q = trim(@$_REQUEST[$modx->context->getOption('request_param_alias', 'q')]);
		$matches = explode('/', rtrim($q, '/'));
		if (count($matches) < 2) {
			return;
		}

		$ticket_uri = array_pop($matches);
		$section_uri = implode('/', $matches) . '/';

		if ($section_id = $modx->findResource($section_uri)) {
			/** @var TicketsSection $section */
			if ($section = $modx->getObject('TicketsSection', $section_id)) {
				if (is_numeric($ticket_uri)) {
					$ticket_id = $ticket_uri;
				}
				elseif (preg_match('/^\d+/', $ticket_uri, $tmp)) {
					$ticket_id = $tmp[0];
				}
				else {
					$properties = $section->getProperties('tickets');
					if (!empty($properties['uri']) && strpos($properties['uri'], '%id') !== false) {
						$pcre = str_replace('%id', '([0-9]+)', $properties['uri']);
						$pcre = preg_replace('/(\%[a-z]+)/', '(?:.*?)', $pcre);
						if (preg_match('/' . $pcre . '/', $ticket_uri, $matches)) {
							$ticket_id = $matches[1];
						}
					}
				}
				if (!empty($ticket_id)) {
					if ($ticket = $modx->getObject('Ticket', array('id' => $ticket_id, 'deleted' => 0))) {
						if ($ticket->published) {
							$modx->sendRedirect($modx->makeUrl($ticket_id), array('responseCode' => 'HTTP/1.1 301 Moved Permanently'));
						}
						elseif ($unp_id = $modx->getOption('tickets.unpublished_ticket_page')) {
							$modx->sendForward($unp_id);
						}
					}
				}
			}
		}
		break;


	case 'OnLoadWebDocument':
		$key = 'Tickets_User';
		if (!$modx->user->isAuthenticated($modx->context->get('key')) && !$modx->getOption('tickets.count_guests', false)) {
			return;
		}

		if (empty($_COOKIE[$key])) {
			if (!empty($_SESSION[$key])) {
				$guest_key = $_SESSION[$key];
			}
			else {
				$guest_key = $_SESSION[$key] = md5(rand() . time() . rand());
			}
			setcookie($key, $guest_key, time() + (86400 * 365), '/');
		}
		else {
			$guest_key = $_COOKIE[$key];
		}

		if (empty($_SESSION[$key])) {
			$_SESSION[$key] = $guest_key;
		}
		break;


	case 'OnWebPageComplete':
		/** @var Tickets $Tickets */
		$Tickets = $modx->getService('tickets');
		$Tickets->logView($modx->resource->id);
		break;


	// @TODO Move this to Ticket::remove() and add TicketFile
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