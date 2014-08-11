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
			$manager->createObjectContainer('TicketView');
			$manager->createObjectContainer('TicketStar');
			$manager->createObjectContainer('TicketQueue');
			$manager->createObjectContainer('TicketFile');

			$tmp = $modx->getFieldMeta('TicketVote');
			if (isset($tmp['parent']) && !$modx->getCount('TicketVote')) {
				$manager->removeObjectContainer('TicketVote');
			}
			$manager->createObjectContainer('TicketVote');

			if ($modx instanceof modX) {
				$modx->addExtensionPackage('tickets', '[[++core_path]]components/tickets/model/');
			}

			$level = $modx->getLogLevel();
			$modx->setLogLevel(xPDO::LOG_LEVEL_FATAL);

			$manager->addField('TicketThread', 'comment_last');
			$manager->addIndex('TicketThread', 'comment_last');
			$manager->addField('TicketThread', 'comment_time');
			$manager->addIndex('TicketThread', 'comment_time');
			$manager->addField('TicketThread', 'comments');
			$manager->addIndex('TicketThread', 'comments');
			$manager->addField('TicketThread', 'closed');
			$manager->addIndex('TicketThread', 'closed');

			$manager->addField('TicketComment', 'raw');
			$manager->addField('TicketComment', 'properties');
			$manager->addField('TicketComment', 'published');
			$manager->addIndex('TicketComment', 'published');
			$manager->addField('TicketComment', 'rating');
			$manager->addIndex('TicketComment', 'rating');
			$manager->addField('TicketComment', 'rating_plus');
			$manager->addField('TicketComment', 'rating_minus');

			$manager->addField('TicketVote', 'owner');
			$manager->addIndex('TicketVote', 'owner');

			$manager->addField('TicketQueue', 'email');
			$manager->addIndex('TicketQueue', 'email');

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