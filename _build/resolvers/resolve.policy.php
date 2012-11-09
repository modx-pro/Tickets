<?php
if ($object->xpdo) {

	$modx =& $object->xpdo;
	$modelPath = $modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/';

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			$modx->addPackage('tickets',$modelPath);
			$modx->setLogLevel(modX::LOG_LEVEL_ERROR);

			/* assign policy to template */
			if ($policy = $transport->xpdo->getObject('modAccessPolicy',array('name' => 'TicketUserPolicy'))) {
				if ($template = $transport->xpdo->getObject('modAccessPolicyTemplate',array('name' => 'TicketsUserPolicyTemplate'))) {
					$policy->set('template',$template->get('id'));
					$policy->save();
				} else {
					$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not find TicketsUserPolicyTemplate Access Policy Template!');
				}
			} else {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not find TicketUserPolicy Access Policy!');
			}

		if ($policy = $transport->xpdo->getObject('modAccessPolicy',array('name' => 'TicketSectionPolicy'))) {
			if ($template = $transport->xpdo->getObject('modAccessPolicyTemplate',array('name' => 'TicketsSectionPolicyTemplate'))) {
				$policy->set('template',$template->get('id'));
				$policy->save();
			} else {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not find TicketsSectionPolicyTemplate Access Policy Template!');
			}
		} else {
			$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not find TicketSectionPolicy Access Policy!');
		}
			break;
	}
}
return true;