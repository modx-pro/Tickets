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
			//$modx->addPackage('tickets',$modelPath);
			//$manager = $modx->getManager();
			//$manager->createObjectContainer('');
			if ($modx instanceof modX) {
				$modx->addExtensionPackage('tickets',$modelPath);
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