<?php

if ($object->xpdo) {
	$modx =& $object->xpdo;
	$modelPath = $modx->getOption('tickets.core_path', null, $modx->getOption('core_path') . 'components/tickets/') . 'model/';

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			$modx->addPackage('tickets', $modelPath);

			// Update comments count
			$threads = $modx->getCollection('TicketThread', array('comments' => 0));
			/** @var TicketThread $thread */
			foreach ($threads as $thread) {
				$thread->updateCommentsCount();
			}

			// Update owners of votes entries
			$tmp = array();
			$q = $modx->newQuery('TicketVote', array('owner' => 0));
			$q->select('class,id');
			if ($q->prepare() && $q->stmt->execute()) {
				while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
					if (empty($tmp[$row['class']])) {
						$tmp[$row['class']] = array($row['id']);
					}
					else {
						$tmp[$row['class']][] = $row['id'];
					}
				}
				if (!empty($tmp)) {
					foreach ($tmp as $k => $v) {
						$q = $modx->newQuery($k, array('id:IN' => $v));
						$q->select('id,createdby');

						$table = $modx->getTableName('TicketVote');
						$sql = "";
						if ($q->prepare() && $q->stmt->execute()) {
							while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
								$sql .= "UPDATE {$table} SET `owner` = {$row['createdby']} WHERE `id` = {$row['id']} AND `class` = '{$k}';\n";
							}
						}
						$modx->exec($sql);
					}

				}
			}
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;