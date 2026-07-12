<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;
    $modelPath = MODX_CORE_PATH . 'components/tickets/model/';

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modx->addPackage('tickets', $modelPath);
            $manager = $modx->getManager();

            // Remove old tables
            $c = $modx->prepare("SHOW COLUMNS IN {$modx->getTableName('TicketAuthor')}");
            $c->execute();
            while ($tmp = $c->fetch(PDO::FETCH_ASSOC)) {
                if ($tmp['Field'] == 'votes' || $tmp['Field'] == 'stars') {
                    $manager->removeObjectContainer('TicketAuthor');
                    $manager->removeObjectContainer('TicketAuthorAction');
                    break;
                }
            }

            // Create or update new
            $tables = array(
                'TicketComment',
                'TicketThread',
                'TicketView',
                'TicketStar',
                'TicketQueue',
                'TicketFile',
                'TicketVote',
                'TicketAuthor',
                'TicketAuthorAction',
                'TicketTotal',
            );

            foreach ($tables as $table) {
                $manager->createObjectContainer($table);
                $table_name = $modx->getTableName($table);

                // FIELDS
                $fields = array();
                $sql = $modx->query("SHOW FIELDS FROM {$table_name}");
                while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                    if (strpos($row['Type'], 'int') === 0) {
                        $type = 'integer';
                    } else {
                        $type = preg_replace('#\(.*#', '', $row['Type']);
                    }
                    $fields[$row['Field']] = strtolower($type);
                }

                // Add or alter existing fields
                $map = $modx->getFieldMeta($table);
                foreach ($map as $key => $field) {
                    // Add new fields
                    if (!isset($fields[$key])) {
                        if ($manager->addField($table, $key)) {
                            $modx->log(modX::LOG_LEVEL_INFO, "Added field \"{$key}\" in the table \"{$table}\"");
                        }
                    } else {
                        $type = strtolower($field['dbtype']);
                        if (strpos($type, 'int') === 0) {
                            $type = 'integer';
                        }
                        // Modify existing fields
                        if ($type != $fields[$key]) {
                            if ($manager->alterField($table, $key)) {
                                $modx->log(modX::LOG_LEVEL_INFO, "Updated field \"{$key}\" of the table \"{$table}\"");
                            }
                        }
                    }
                }
                // Remove old fields
                foreach ($fields as $key => $field) {
                    if (!isset($map[$key])) {
                        if ($manager->removeField($table, $key)) {
                            $modx->log(modX::LOG_LEVEL_INFO, "Removed field \"{$key}\" of the table \"{$table}\"");
                        }
                    }
                }

                // INDEXES
                $indexes = array();
                $sql = $modx->query("SHOW INDEXES FROM {$table_name}");

                while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                    $name = $row['Key_name'];
                    if (!isset($indexes[$name])) {
                        $indexes[$name] = array($row['Column_name']);
                    } else {
                        $indexes[$name][] = $row['Column_name'];
                    }
                }
                foreach ($indexes as $name => $values) {
                    sort($values);
                    $indexes[$name] = implode(':', $values);
                }
                $map = $modx->getIndexMeta($table);

                // Remove old indexes
                foreach ($indexes as $key => $index) {
                    if (!isset($map[$key])) {
                        if ($manager->removeIndex($table, $key)) {
                            $modx->log(modX::LOG_LEVEL_INFO, "Removed index \"{$key}\" of the table \"{$table}\"");
                        }
                    }
                }
                // Add or alter existing
                foreach ($map as $key => $index) {
                    ksort($index['columns']);
                    $index = implode(':', array_keys($index['columns']));
                    if (!isset($indexes[$key])) {
                        if ($manager->addIndex($table, $key)) {
                            $modx->log(modX::LOG_LEVEL_INFO, "Added index \"{$key}\" in the table \"{$table}\"");
                        }
                    } else {
                        if ($index != $indexes[$key]) {
                            if ($manager->removeIndex($table, $key) && $manager->addIndex($table, $key)) {
                                $modx->log(modX::LOG_LEVEL_INFO, "Updated index \"{$key}\" of the table \"{$table}\"");
                            }
                        }
                    }
                }
            }

            if ($modx instanceof modX) {
                $modx->addExtensionPackage('tickets', '[[++core_path]]components/tickets/model/');
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            if ($modx instanceof modX) {
                $modx->removeExtensionPackage('tickets');

                // Keep mgr usable: custom CRC classes are gone with the package
                $resourceTable = $modx->getTableName('modResource');
                $modx->exec(
                    "UPDATE {$resourceTable} SET class_key = 'modDocument' WHERE class_key IN ('Ticket', 'TicketsSection')"
                );

                $modx->addPackage('tickets', $modelPath);
                $manager = $modx->getManager();
                foreach (array(
                    'TicketComment',
                    'TicketThread',
                    'TicketView',
                    'TicketStar',
                    'TicketQueue',
                    'TicketFile',
                    'TicketVote',
                    'TicketAuthor',
                    'TicketAuthorAction',
                    'TicketTotal',
                ) as $table) {
                    $manager->removeObjectContainer($table);
                }

                // Menu/chunks may survive when UPDATE_OBJECT was false at install time
                if ($menu = $modx->getObject('modMenu', array('text' => 'tickets', 'namespace' => 'tickets'))) {
                    $menu->remove();
                }

                if ($category = $modx->getObject('modCategory', array('category' => 'Tickets'))) {
                    $categoryId = $category->get('id');
                    foreach ($modx->getCollection('modPlugin', array('category' => $categoryId, 'name' => 'Tickets')) as $object) {
                        $object->remove();
                    }
                    foreach ($modx->getCollection('modSnippet', array('category' => $categoryId)) as $object) {
                        if (strpos($object->get('name'), 'Ticket') === 0) {
                            $object->remove();
                        }
                    }
                    foreach ($modx->getCollection('modChunk', array('category' => $categoryId)) as $object) {
                        if (strpos($object->get('name'), 'tpl.Tickets.') === 0) {
                            $object->remove();
                        }
                    }
                    $category->remove();
                }
            }
            break;
    }
}
return true;