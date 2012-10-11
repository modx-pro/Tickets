<?php
/**
 * Resolve creating db tables
 *
 * @package tickets
 * @subpackage build
 */
if ($object->xpdo) {
	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		$modx =& $object->xpdo;
		$modelPath = $modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/';

		case xPDOTransport::ACTION_INSTALL:
			$modx->addPackage('tickets',$modelPath);

			$manager = $modx->getManager();

			//$manager->createObjectContainer('');
			$modx->addExtensionPackage('tickets',$modelPath);

			break;
		case xPDOTransport::ACTION_UPGRADE:
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			$modx->removeExtensionPackage('tickets',$modelPath);
			break;
	}
}
return true;