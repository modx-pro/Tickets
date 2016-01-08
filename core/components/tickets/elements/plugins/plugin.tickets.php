<?php
switch ($modx->event->name) {

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
		$output = str_replace(
			array('*(*(*(*(*(*', '*)*)*)*)*)*', '~(~(~(~(~(~', '~)~)~)~)~)~'),
			array('[', ']', '{', '}'),
			$output
		);
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
				elseif (preg_match('#^\d+#', $ticket_uri, $tmp)) {
					$ticket_id = $tmp[0];
				}
				else {
					$properties = $section->getProperties('tickets');
					if (!empty($properties['uri']) && strpos($properties['uri'], '%id') !== false) {
						$pcre = str_replace('%id', '([0-9]+)', $properties['uri']);
						$pcre = preg_replace('#(\%[a-z]+)#', '(?:.*?)', $pcre);
						if (preg_match($pcre, $ticket_uri, $matches)) {
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
		$authenticated = $modx->user->isAuthenticated($modx->context->get('key'));
		$key = 'Tickets_User';

		if (!$authenticated && !$modx->getOption('tickets.count_guests')) {
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

		if ($authenticated) {
			/** @var TicketAuthor $profile */
			if (!$profile = $modx->user->getOne('AuthorProfile')) {
				$profile = $modx->newObject('TicketAuthor');
				$modx->user->addOne($profile);
			}
			$profile->set('visitedon', time());
			$profile->save();
		}
		break;


	case 'OnWebPageComplete':
		/** @var Tickets $Tickets */
		$Tickets = $modx->getService('tickets');
		$Tickets->logView($modx->resource->get('id'));
		break;


	case 'OnUserSave':
		/** @var modUser $user */
		if ($mode == 'new' && $user && !$user->getOne('AuthorProfile')) {
			$profile = $modx->newObject('TicketAuthor');
			$user->addOne($profile);
			$profile->save();
		}
		break;

}