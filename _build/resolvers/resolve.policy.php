<?php

if ($object->xpdo) {
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:

			/* assign policy to template */
			if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'TicketUserPolicy'))) {
				if ($template = $modx->getObject('modAccessPolicyTemplate', array('name' => 'TicketsUserPolicyTemplate'))) {
					$policy->set('template', $template->get('id'));
					$policy->save();
				}
				else {
					$modx->log(xPDO::LOG_LEVEL_ERROR, '[Tickets] Could not find TicketsUserPolicyTemplate Access Policy Template!');
				}
			}
			else {
				$modx->log(xPDO::LOG_LEVEL_ERROR, '[Tickets] Could not find TicketUserPolicy Access Policy!');
			}

			if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'TicketVipPolicy'))) {
				if ($template = $modx->getObject('modAccessPolicyTemplate', array('name' => 'TicketsUserPolicyTemplate'))) {
					$policy->set('template', $template->get('id'));
					$policy->save();
				}
				else {
					$modx->log(xPDO::LOG_LEVEL_ERROR, '[Tickets] Could not find TicketsUserPolicyTemplate Access Policy Template!');
				}
			}
			else {
				$modx->log(xPDO::LOG_LEVEL_ERROR, '[Tickets] Could not find TicketVipPolicy Access Policy!');
			}

			if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'TicketSectionPolicy'))) {
				if ($template = $modx->getObject('modAccessPolicyTemplate', array('name' => 'TicketsSectionPolicyTemplate'))) {
					$policy->set('template', $template->get('id'));
					$policy->save();
				}
				else {
					$modx->log(xPDO::LOG_LEVEL_ERROR, '[Tickets] Could not find TicketsSectionPolicyTemplate Access Policy Template!');
				}
			}
			else {
				$modx->log(xPDO::LOG_LEVEL_ERROR, '[Tickets] Could not find TicketSectionPolicy Access Policy!');
			}

			break;
	}
}
return true;