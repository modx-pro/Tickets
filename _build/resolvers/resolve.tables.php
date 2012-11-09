<?php
/**
 * Resolve creating db tables
 * @var xPDOObject $object
 * @var array $options
 * @package tickets
 * @subpackage build
 */
if ($object->xpdo) {
	$modx =& $object->xpdo;
	$modelPath = $modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/';

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			$modx->addPackage('tickets',$modelPath);
			$modx->setLogLevel(modX::LOG_LEVEL_ERROR);

			$manager = $modx->getManager();
			$manager->createObjectContainer('TicketComment');
			$manager->createObjectContainer('TicketThread');

			if ($modx instanceof modX) {
				$modx->addExtensionPackage('tickets', '[[++core_path]]components/tickets/model/');
			}
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			if ($modx instanceof modX) {
				$modx->removeExtensionPackage('tickets');
			}
			break;
	}
}
return true;