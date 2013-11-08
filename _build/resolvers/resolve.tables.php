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

			$manager = $modx->getManager();
			$manager->createObjectContainer('TicketComment');
			$manager->createObjectContainer('TicketThread');
			$manager->createObjectContainer('TicketVote');
			$manager->createObjectContainer('TicketView');
			$manager->createObjectContainer('TicketQueue');

			if ($modx instanceof modX) {
				$modx->addExtensionPackage('tickets', '[[++core_path]]components/tickets/model/');
			}

			$level = $modx->getLogLevel();
			$modx->setLogLevel(xPDO::LOG_LEVEL_FATAL);

			$manager->addField('TicketThread', 'comment_last');
			$manager->addField('TicketThread', 'comment_time');
			$manager->addField('TicketThread', 'comments');
			$manager->addIndex('TicketThread', 'comment_last');
			$manager->addIndex('TicketThread', 'comment_time');
			$manager->addIndex('TicketThread', 'comments');

			$manager->addField('TicketComment', 'raw');
			$manager->addField('TicketComment', 'published');
			$manager->addField('TicketComment', 'properties');
			$manager->addIndex('TicketComment', 'published');

			$modx->setLogLevel($level);
		break;

		case xPDOTransport::ACTION_UNINSTALL:
			if ($modx instanceof modX) {
				$modx->removeExtensionPackage('tickets');
			}
		break;
	}
}
return true;