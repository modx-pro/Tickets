<?php
/** @var array $scriptProperties */
/** @var Tickets $Tickets */
$Tickets = $modx->getService('tickets', 'Tickets', $modx->getOption('tickets.core_path', null,
        $modx->getOption('core_path') . 'components/tickets/') . 'model/tickets/', $scriptProperties);
$Tickets->initialize($modx->context->key, $scriptProperties);

/** @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdoFetch');
$pdoFetch->setConfig($scriptProperties);
$pdoFetch->addTime('pdoTools loaded');

if (isset($parents) && $parents === '') {
    $scriptProperties['parents'] = $modx->resource->id;
}

$class = 'Ticket';
$where = array('class_key' => $class);

// Filter by user
if (!empty($user)) {
    $user = array_map('trim', explode(',', $user));
    $user_id = $user_username = array();
    foreach ($user as $v) {
        if (is_numeric($v)) {
            $user_id[] = $v;
        } else {
            $user_username[] = $v;
        }
    }
    if (!empty($user_id) && !empty($user_username)) {
        $where[] = '(`User`.`id` IN (' . implode(',', $user_id) . ') OR `User`.`username` IN (\'' . implode('\',\'',
                $user_username) . '\'))';
    } else {
        if (!empty($user_id)) {
            $where['User.id:IN'] = $user_id;
        } else {
            if (!empty($user_username)) {
                $where['User.username:IN'] = $user_username;
            }
        }
    }
}

// Joining tables
$leftJoin = array(
    'Section' => array('class' => 'TicketsSection', 'on' => '`Section`.`id` = `Ticket`.`parent`'),
    'User' => array('class' => 'modUser', 'on' => '`User`.`id` = `Ticket`.`createdby`'),
    'Profile' => array('class' => 'modUserProfile', 'on' => '`Profile`.`internalKey` = `User`.`id`'),
    'Total' => array('class' => 'TicketTotal'),
);
if ($Tickets->authenticated) {
    $leftJoin['Vote'] = array(
        'class' => 'TicketVote',
        'on' => '`Vote`.`id` = `Ticket`.`id` AND `Vote`.`class` = "Ticket" AND `Vote`.`createdby` = ' . $modx->user->id,
    );
    $leftJoin['Star'] = array(
        'class' => 'TicketStar',
        'on' => '`Star`.`id` = `Ticket`.`id` AND `Star`.`class` = "Ticket" AND `Star`.`createdby` = ' . $modx->user->id,
    );
    $leftJoin['Thread'] = array(
        'class' => 'TicketThread',
        'on' => '`Thread`.`resource` = `Ticket`.`id` AND `Thread`.`deleted` = 0',
    );
}

// Fields to select
$select = array(
    'Section' => $modx->getSelectColumns('TicketsSection', 'Section', 'section.', array('content'), true),
    'User' => $modx->getSelectColumns('modUser', 'User', '', array('username')),
    'Profile' => $modx->getSelectColumns('modUserProfile', 'Profile', '', array('id'), true),
    'Ticket' => !empty($includeContent)
        ? $modx->getSelectColumns($class, $class)
        : $modx->getSelectColumns($class, $class, '', array('content'), true),
    'Total' => 'comments, views, stars, rating, rating_plus, rating_minus',
);
if ($Tickets->authenticated) {
    $select['Vote'] = '`Vote`.`value` as `vote`';
    $select['Star'] = 'COUNT(`Star`.`id`) as `star`';
    $select['Thread'] = '`Thread`.`id` as `thread`';
}
$pdoFetch->addTime('Conditions prepared');

// Add custom parameters
foreach (array('where', 'select', 'leftJoin') as $v) {
    if (!empty($scriptProperties[$v])) {
        $tmp = $scriptProperties[$v];
        if (!is_array($tmp)) {
            $tmp = json_decode($tmp, true);
        }
        if (is_array($tmp)) {
            $$v = array_merge($$v, $tmp);
        }
    }
    unset($scriptProperties[$v]);
}

$default = array(
    'class' => $class,
    'where' => json_encode($where),
    'leftJoin' => json_encode($leftJoin),
    'select' => json_encode($select),
    'sortby' => 'createdon',
    'sortdir' => 'DESC',
    'groupby' => $class . '.id',
    'return' => !empty($returnIds)
        ? 'ids'
        : 'data',
    'nestedChunkPrefix' => 'tickets_',
);

// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties));
$pdoFetch->addTime('Query parameters are prepared.');
$rows = $pdoFetch->run();

if (!empty($returnIds)) {
    return $rows;
}
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
// Processing rows
$output = array();
if (!empty($rows) && is_array($rows)) {
    foreach ($rows as $k => $row) {
        // Handle properties
        $properties = is_string($row['properties'])
            ? json_decode($row['properties'], true)
            : $row['properties'];
        if (!empty($properties['tickets'])) {
            $properties = $properties['tickets'];
        }
        if (empty($properties['process_tags'])) {
            foreach ($row as $field => $value) {
                $row[$field] = str_replace(
                    array('[', ']', '`', '{', '}'),
                    array('&#91;', '&#93;', '&#96;', '&#123;', '&#125;'),
                    $value
                );
            }
        }
        if (!is_array($properties)) {
            $properties = array();
        }

        // Handle rating
        if ($row['rating'] > 0) {
            $row['rating'] = '+' . $row['rating'];
            $row['rating_positive'] = 1;
        } elseif ($row['rating'] < 0) {
            $row['rating_negative'] = 1;
        }
        $row['rating_total'] = abs($row['rating_plus']) + abs($row['rating_minus']);
        // Handle rating
        if (isset($row['section.properties']['ratings']['days_ticket_vote'])) {
            if ($row['section.properties']['ratings']['days_ticket_vote'] !== '') {
                $max = $row['createdon'] + ((float)$row['section.properties']['ratings']['days_ticket_vote'] * 86400);
                if (time() > $max) {
                    $row['cant_vote'] = 1;
                }
            }
        }
        if (!isset($row['cant_vote'])) {
            if (!$Tickets->authenticated || $modx->user->id == $row['createdby']) {
                $row['cant_vote'] = 1;
            } elseif (array_key_exists('vote', $row)) {
                if ($row['vote'] == '') {
                    $row['can_vote'] = 1;
                } elseif ($row['vote'] > 0) {
                    $row['voted_plus'] = 1;
                    $row['cant_vote'] = 1;
                } elseif ($row['vote'] < 0) {
                    $row['voted_minus'] = 1;
                    $row['cant_vote'] = 1;
                } else {
                    $row['voted_none'] = 1;
                    $row['cant_vote'] = 1;
                }
            }
        }
        // Special fields for quick placeholders
        $row['active'] = (int)!empty($row['can_vote']);
        $row['inactive'] = (int)!empty($row['cant_vote']);
        $row['can_star'] = $Tickets->authenticated;
        $row['stared'] = !empty($row['star']);
        $row['unstared'] = empty($row['star']);
        $row['isauthor'] = $modx->user->id == $row['createdby'];
        $row['unpublished'] = empty($row['published']);

        $row['date_ago'] = $Tickets->dateFormat($row['createdon']);
        $row['idx'] = $pdoFetch->idx++;
        // Processing new comments
        if ($Tickets->authenticated && !empty($row['thread'])) {
            $last_view = $pdoFetch->getObject('TicketView', array(
                'parent' => $row['id'],
                'uid' => $modx->user->id,
            ), array(
                'sortby' => 'timestamp',
                'sortdir' => 'DESC',
                'limit' => 1,
            ));
            if (!empty($last_view['timestamp'])) {
                $row['new_comments'] = $modx->getCount('TicketComment', array(
                    'published' => 1,
                    'thread' => $row['thread'],
                    'createdon:>' => $last_view['timestamp'],
                    'createdby:!=' => $modx->user->id,
                ));
            } else {
                $row['new_comments'] = $row['comments'];
            }
        } else {
            $row['new_comments'] = '';
        }

        // Processing chunk
        $tpl = $pdoFetch->defineChunk($row);
        $output[] = empty($tpl)
            ? '<pre>' . $pdoFetch->getChunk('', $row) . '</pre>'
            : $pdoFetch->getChunk($tpl, $row, $pdoFetch->config['fastMode']);
    }
}
$pdoFetch->addTime('Returning processed chunks');

$log = '';
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $log .= '<pre class="getTicketsLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

// Return output
if (!empty($toSeparatePlaceholders)) {
    $output['log'] = $log;
    $modx->setPlaceholders($output, $toSeparatePlaceholders);
} else {
    if (empty($outputSeparator)) {
        $outputSeparator = "\n";
    }
    $output = implode($outputSeparator, $output);
    $output .= $log;

    if (!empty($tplWrapper) && (!empty($wrapIfEmpty) || !empty($output))) {
        $array = array('output' => $output);
        if ($Tickets->authenticated && $modx->resource->class_key == 'TicketsSection') {
            /** @var TicketsSection $section */
            $section = &$modx->resource;
            $array['subscribed'] = $section->isSubscribed();
        }
        $output = $pdoFetch->getChunk($tplWrapper, $array, $pdoFetch->config['fastMode']);
    }

    if (!empty($toPlaceholder)) {
        $modx->setPlaceholder($toPlaceholder, $output);
    } else {
        return $output;
    }
}