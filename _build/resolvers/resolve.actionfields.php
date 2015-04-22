<?php

$actionFields = array(
	array(
		'name' => 'tickets-box-publishing-information',
		'tab' => 'modx-resource-main-right',
		'fields' => array(
			'publishedon', 'pub_date', 'unpub_date', 'template', 'modx-resource-createdby',
			'tickets-combo-section', 'alias'
		),
	),
	array(
		'name' => 'tickets-box-options',
		'tab' => 'modx-resource-main-right',
		'fields' => array(
			'searchable', 'properties[disable_jevix]', 'cacheable', 'properties[process_tags]',
			'published', 'private', 'richtext', 'hidemenu', 'isfolder'
		),
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
			if ($modx->getCount('modActionField', array('name' => 'publishedon', 'other' => 'tickets')) > 1) {
				$modx->removeCollection('modActionField', array('other' => 'tickets'));
			}

			$modx->getVersionData();
			$modx23 = !empty($modx->version) && version_compare($modx->version['full_version'], '2.3.0', '>=');
			if (!$modx23) {
				$actions = array();
				foreach ($resourceActions as $controller) {
					$actionObj = $modx->getObject('modAction', array(
						'controller' => $controller,
						'namespace' => 'core',
					));
					$actions[] = $actionObj->get('id');
				}
			}
			else {
				$actions = $resourceActions;
			}
			foreach ($actions as $actionId) {
				$c = $modx->newQuery('modActionField', array('type' => 'tab', 'action' => $actionId));
				$c->select('id, max(`rank`) as tabrank');
				$obj = $modx->getObject('modActionField', $c);
				$tabIdx = $obj->tabrank + 1;

				foreach ($actionFields as $tab) {
					/** @var modActionField $tabObj */
					if (!$tabObj = $modx->getObject('modActionField', array('action' => $actionId, 'name' => $tab['name'], 'other' => 'tickets'))) {
						$tabObj = $modx->newObject('modActionField');
					}
					$tabObj->fromArray(array_merge($tab, array(
						'action' => $actionId,
						'form' => 'modx-panel-resource',
						'type' => 'tab',
						'other' => 'tickets',
						'rank' => $tabIdx,
					)), '', true, true);
					$success = $tabObj->save();
					/*if ($success) {
						$modx->log(xPDO::LOG_LEVEL_INFO,'[Tickets] Tab ' . $tab['name'] . ' added!');
					} else {
						$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not add Tab ' . $tab['name'] . '!');
					}*/

					$tabIdx++;
					$idx = 0;
					foreach ($tab['fields'] as $field) {
						if (!$fieldObj = $modx->getObject('modActionField', array('action' => $actionId, 'name' => $field, 'tab' => $tab['name'], 'other' => 'tickets'))) {
							$fieldObj = $modx->newObject('modActionField');
						}
						$fieldObj->fromArray(array(
							'action' => $actionId,
							'name' => $field,
							'tab' => $tab['name'],
							'form' => 'modx-panel-resource',
							'type' => 'field',
							'other' => 'tickets',
							'rank' => $idx,
						), '', true, true);
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
			$modx->removeCollection('modActionField', array('other' => 'tickets'));
			break;
	}
}

return true;
