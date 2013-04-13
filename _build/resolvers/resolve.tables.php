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
			//$manager->createObjectContainer('TicketAttachment');
			$manager->createObjectContainer('TicketVote');
			$manager->createObjectContainer('TicketView');

			if ($modx instanceof modX) {
				$modx->addExtensionPackage('tickets', '[[++core_path]]components/tickets/model/');
			}

			$TicketThread = $modx->getTableName('TicketThread');
			$modx->query("ALTER TABLE  {$TicketThread} ADD `comment_last` INT(10) UNSIGNED NOT NULL DEFAULT  '0', ADD `comment_time` DATETIME NULL , ADD INDEX (`comment_last`, `comment_time`)");
			$modx->query("ALTER TABLE {$TicketThread} ADD `closed` TINYINT(1) NOT NULL AFTER `createdby`, ADD `properties` TEXT NULL DEFAULT NULL");

			$TicketComment = $modx->getTableName('TicketComment');
			$modx->query("ALTER TABLE {$TicketComment} ADD `raw` TEXT NOT NULL AFTER `text`");
			$modx->query("ALTER TABLE {$TicketComment} ADD `published` TINYINT(1) NOT NULL AFTER `editedby`");
		break;

		case xPDOTransport::ACTION_UNINSTALL:
			if ($modx instanceof modX) {
				$modx->removeExtensionPackage('tickets');
			}
		break;
	}
}
return true;