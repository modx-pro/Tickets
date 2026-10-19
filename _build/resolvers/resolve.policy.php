<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            // Assign policy to template
            if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'TicketUserPolicy'))) {
                if ($template = $modx->getObject('modAccessPolicyTemplate',
                    array('name' => 'TicketsUserPolicyTemplate'))
                ) {
                    $policy->set('template', $template->get('id'));
                    $policy->save();
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR,
                        '[Tickets] Could not find TicketsUserPolicyTemplate Access Policy Template!');
                }
            } else {
                $modx->log(xPDO::LOG_LEVEL_ERROR, '[Tickets] Could not find TicketUserPolicy Access Policy!');
            }

            if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'TicketVipPolicy'))) {
                if ($template = $modx->getObject('modAccessPolicyTemplate',
                    array('name' => 'TicketsUserPolicyTemplate'))
                ) {
                    $policy->set('template', $template->get('id'));
                    $policy->save();
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR,
                        '[Tickets] Could not find TicketsUserPolicyTemplate Access Policy Template!');
                }
            } else {
                $modx->log(xPDO::LOG_LEVEL_ERROR, '[Tickets] Could not find TicketVipPolicy Access Policy!');
            }

            if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'TicketSectionPolicy'))) {
                if ($template = $modx->getObject('modAccessPolicyTemplate',
                    array('name' => 'TicketsSectionPolicyTemplate'))
                ) {
                    $policy->set('template', $template->get('id'));
                    $policy->save();
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR,
                        '[Tickets] Could not find TicketsSectionPolicyTemplate Access Policy Template!');
                }
            } else {
                $modx->log(xPDO::LOG_LEVEL_ERROR, '[Tickets] Could not find TicketSectionPolicy Access Policy!');
            }

            foreach (array('TicketUserPolicy', 'TicketVipPolicy') as $policyName) {
                /** @var modAccessPolicy $existingPolicy */
                if (!$existingPolicy = $modx->getObject('modAccessPolicy', array('name' => $policyName))) {
                    continue;
                }
                $raw = $existingPolicy->get('data');
                if (is_array($raw)) {
                    $data = $raw;
                } elseif (is_string($raw) && $raw !== '') {
                    $decoded = $modx->fromJSON($raw);
                    if (!is_array($decoded)) {
                        $modx->log(
                            xPDO::LOG_LEVEL_ERROR,
                            '[Tickets] Could not read ' . $policyName . ' data; ticket_file_sort was not added.'
                        );
                        continue;
                    }
                    $data = $decoded;
                } elseif ($raw === null || $raw === '') {
                    $data = array();
                } else {
                    $modx->log(
                        xPDO::LOG_LEVEL_ERROR,
                        '[Tickets] Could not read ' . $policyName . ' data; ticket_file_sort was not added.'
                    );
                    continue;
                }
                if (array_key_exists('ticket_file_sort', $data)) {
                    continue;
                }
                $data['ticket_file_sort'] = true;
                $existingPolicy->set('data', $modx->toJSON($data));
                if (!$existingPolicy->save()) {
                    $modx->log(
                        xPDO::LOG_LEVEL_ERROR,
                        '[Tickets] Could not add ticket_file_sort to ' . $policyName . '!'
                    );
                }
            }
            break;
    }
}
return true;