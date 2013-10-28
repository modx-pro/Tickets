<?php

$actionFields = array(
	array(
		'name' => 'tickets-box-publishing-information',
		'tab' => 'modx-resource-main-right',
		'fields' => array('publishedon', 'pub_date', 'unpub_date', 'template', 'modx-resource-createdby',
			'tickets-combo-section', 'alias'),
	),
	array(
		'name' => 'tickets-box-options',
		'tab' => 'modx-resource-main-right',
		'fields' => array('searchable', 'properties[disable_jevix]','cacheable', 'properties[process_tags]',
			'published', 'private', 'richtext', 'hidemenu', 'isfolder'),
	),
	array(
		'name' => 'modx-tickets-comments',
		'tab' => '',
		'fields' => array(),
	)
);

$resourceActions = array('resource/create', 'resource/update');

if ($object->xpdo) {
	/** @var modX $modx */
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			/** @var modActionField $action */
			/*
			$oldActions = $modx->getIterator('modActionField', array('other' => 'tickets'));
			foreach ($oldActions as $action) {
				$action->remove();
			}
			*/

			$actions = array();
			foreach ($resourceActions as $controller) {
				$actionObj = $modx->getObject('modAction',array(
					'controller' => $controller,
					'namespace' => 'core',
				));
				$actions[] = $actionObj->get('id');
			}

			foreach ($actions as $actionId) {
				$c = $modx->newQuery('modActionField', array('type' => 'tab', 'action' => $actionId));
				$c->select('id, max(`rank`) as tabrank');
				$obj = $modx->getObject('modActionField', $c);
				$tabIdx = $obj->tabrank + 1;

				foreach ($actionFields as $tab) {
					/** @var modActionField $tabObj */
					$tabObj = $modx->newObject('modActionField');
					$tabObj->fromArray(array_merge($tab, array(
						'action' => $actionId,
						'form' => 'modx-panel-resource',
						'type' => 'tab',
						'other' => 'tickets',
						'rank' => $tabIdx,
					)), '', true, true);
					$success = $tabObj->save();
				   /* if ($success) {
						$modx->log(xPDO::LOG_LEVEL_INFO,'[Tickets] Tab ' . $tab['name'] . ' added!');
					} else {
						$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not add Tab ' . $tab['name'] . '!');
					}*/

					$tabIdx++;
					$idx = 0;
					foreach ($tab['fields'] as $field) {
						$fieldObj = $modx->newObject('modActionField');
						$fieldObj->fromArray(array(
							'action' => $actionId,
							'name' => $field,
							'tab' => $tab['name'],
							'form' => 'modx-panel-resource',
							'type' => 'field',
							'other' => 'tickets',
							'rank' => $idx,
						));
						$success = $fieldObj->save();
						/*if ($success) {
							$modx->log(xPDO::LOG_LEVEL_INFO,'[Tickets] Action field ' . $field . ' added!');
						} else {
							$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not add Action Field ' . $field . '!');
						}*/
						$idx++;

					}
				}
			}
		break;
	case xPDOTransport::ACTION_UNINSTALL:
		$actions = $modx->getIterator('modActionField', array('other' => 'tickets'));
		/** @var modActionField $action */
		foreach ($actions as $action) {
			$action->remove();
		}
		break;
	}
}

return true;
