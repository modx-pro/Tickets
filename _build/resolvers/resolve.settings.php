<?php

if ($object->xpdo) {
	/* @var modX $modx */
	$modx =& $object->xpdo;

	$modx->getVersionData();
	if (!empty($this->modx->version) && version_compare($this->modx->version['full_version'], '2.3.0', '<')) {
		return true;
	}

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			if (!$setting = $modx->getObject('modSystemSetting', array('key' => 'mgr_tree_icon_TicketsSection'))) {
				$setting = $modx->newObject('modSystemSetting');
				$setting->fromArray(array(
					'key' => 'mgr_tree_icon_ticketssection',
					'area' => 'tickets.main',
					'namespace' => 'tickets',
					'value' => 'icon icon-comments-o',
				), '', true, true);
				$setting->save();
			}

			if (!$setting = $modx->getObject('modSystemSetting', array('key' => 'mgr_tree_icon_Ticket'))) {
				$setting = $modx->newObject('modSystemSetting');
				$setting->fromArray(array(
					'key' => 'mgr_tree_icon_ticket',
					'area' => 'tickets.main',
					'namespace' => 'tickets',
					'value' => 'icon icon-comment-o',
				), '', true, true);
				$setting->save();
			}
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			$modx->removeCollection('modSystemSetting', array(
				'key:IN' => array(
					'mgr_tree_icon_ticketssection',
					'mgr_tree_icon_ticket'
				)
			));
			break;
	}
}
return true;