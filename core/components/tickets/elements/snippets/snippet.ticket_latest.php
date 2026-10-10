<?php

/** @var array $scriptProperties */
$ticketsCorePath = $modx->getOption(
    'tickets.core_path',
    null,
    $modx->getOption('core_path') . 'components/tickets/'
);
require_once $ticketsCorePath . 'model/tickets/ticketlatestqueries.inc.php';

if (!empty($cacheKey) && $output = $modx->cacheManager->get('tickets/latest.' . $cacheKey)) {
    return $output;
}

/** @var Tickets $Tickets */
$Tickets = $modx->getService(
    'tickets',
    'Tickets',
    $ticketsCorePath . 'model/tickets/',
    $scriptProperties
);
$Tickets->initialize($modx->context->key, $scriptProperties);

/** @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdoFetch');
$pdoFetch->setConfig($scriptProperties);
$pdoFetch->addTime('pdoTools loaded');

if (empty($action)) {
    $action = 'comments';
}
$action = strtolower($action);
if ($action == 'tickets' && $scriptProperties['tpl'] == 'tpl.Tickets.comment.latest') {
    $scriptProperties['tpl'] = 'tpl.Tickets.ticket.latest';
}
$where = $action == 'tickets'
    ? array('class_key' => 'Ticket')
    : array();

if (empty($showUnpublished)) {
    $where['Ticket.published'] = 1;
}
if (empty($showHidden)) {
    $where['Ticket.hidemenu'] = 0;
}
if (empty($showDeleted)) {
    $where['Ticket.deleted'] = 0;
}
if (!isset($cacheTime)) {
    $cacheTime = 1800;
}
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
        $where[] = '(`User`.`id` IN (' . implode(',', $user_id)
            . ') OR `User`.`username` IN (\'' . implode('\',\'', $user_username) . '\'))';
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

// Filter by ids
if (!empty($resources)) {
    $resources = array_map('trim', explode(',', $resources));
    $in = $out = array();
    foreach ($resources as $v) {
        if (!is_numeric($v)) {
            continue;
        }
        if ($v < 0) {
            $out[] = abs($v);
        } else {
            $in[] = $v;
        }
    }
    if (!empty($in)) {
        $where[$action == 'comments' ? 'Ticket.id:IN' : 'id:IN'] = $in;
    }
    if (!empty($out)) {
        $where[$action == 'comments' ? 'Ticket.id:NOT IN' : 'id:NOT IN'] = $out;
    }
} else {
    // Filter by parents
    if (!empty($parents) && $parents > 0) {
        $pids = array_map('trim', explode(',', $parents));
        $parents = $pids;
        if (!empty($depth) && $depth > 0) {
            foreach ($pids as $v) {
                if (!is_numeric($v)) {
                    continue;
                }
                $parents = array_merge($parents, $modx->getChildIds($v, $depth));
            }
        }
        if (!empty($parents)) {
            $where['Ticket.parent:IN'] = $parents;
        }
    }
}

$innerJoin = array();
$leftJoin = array();
$select = array();
$groupby = '';
$defaultSortby = 'createdon';
$threadFirst = ($action == 'comments' && empty($user));

if ($action == 'comments') {
    $ticketSelect = $modx->getSelectColumns(
        'Ticket',
        'Ticket',
        'ticket.',
        array('id', 'pagetitle', 'uri', 'alias', 'parent', 'context_key'),
        false
    );
    $commentSelect = !empty($includeContent)
        ? $modx->getSelectColumns('TicketComment', 'TicketComment', '', array('raw'), true)
        : $modx->getSelectColumns('TicketComment', 'TicketComment', '', array('text', 'raw'), true);
    $actorSelect = array(
        'Section' => $modx->getSelectColumns(
            'TicketsSection',
            'Section',
            'section.',
            array('id', 'pagetitle'),
            false
        ),
        'User' => $modx->getSelectColumns('modUser', 'User', '', array('username')),
        'Profile' => $modx->getSelectColumns(
            'modUserProfile',
            'Profile',
            '',
            array('email', 'fullname', 'photo'),
            false
        ),
    );
    $leftJoin = array(
        'Section' => array('class' => 'TicketsSection', 'on' => '`Section`.`id` = `Ticket`.`parent`'),
        'User' => array('class' => 'modUser', 'on' => '`User`.`id` = `TicketComment`.`createdby`'),
        'Profile' => array(
            'class' => 'modUserProfile',
            'on' => '`Profile`.`internalKey` = `TicketComment`.`createdby`',
        ),
    );

    $query = $threadFirst
        ? tickets_ticket_latest_thread_first($ticketSelect, $commentSelect)
        : tickets_ticket_latest_user_comments($ticketSelect, $commentSelect);
    if ($threadFirst) {
        $defaultSortby = 'comment_time';
    }

    $class = $query['class'];
    $where = array_merge($where, $query['where']);
    $innerJoin = $query['innerJoin'];
    $select = array_merge($query['select'], $actorSelect);
    $groupby = $query['groupby'];
} elseif ($action == 'tickets') {
    $class = 'Ticket';
    $leftJoin = array(
        'Thread' => array(
            'class' => 'TicketThread',
            'on' => '`Thread`.`resource` = `Ticket`.`id` AND `Thread`.`deleted` = 0',
        ),
        'Section' => array('class' => 'TicketsSection', 'on' => '`Section`.`id` = `Ticket`.`parent`'),
        'User' => array('class' => 'modUser', 'on' => '`User`.`id` = `Ticket`.`createdby`'),
        'Profile' => array('class' => 'modUserProfile', 'on' => '`Profile`.`internalKey` = `Ticket`.`createdby`'),
    );
    $select = array(
        'Ticket' => !empty($includeContent)
            ? $modx->getSelectColumns('Ticket', 'Ticket')
            : $modx->getSelectColumns('Ticket', 'Ticket', '', array('content'), true),
        'Thread' => '`Thread`.`id` as `thread`, `Thread`.`comments`',
        'Section' => $modx->getSelectColumns('TicketsSection', 'Section', 'section.', array('content'), true),
        'User' => $modx->getSelectColumns('modUser', 'User', '', array('username')),
        'Profile' => $modx->getSelectColumns('modUserProfile', 'Profile', '', array('id'), true),
    );
    $groupby = '`Ticket`.`id`';
} else {
    return 'Wrong action. You must use "ticket" or "comment".';
}

// Empty or legacy createdon → defaultSortby; thread-first maps createdon to comment_time.
$requestedSortby = $scriptProperties['sortby'] ?? null;
unset($scriptProperties['sortby']);
if ($requestedSortby === null || $requestedSortby === '') {
    $sortby = $defaultSortby;
} elseif ($threadFirst && $requestedSortby === 'createdon') {
    $sortby = 'comment_time';
} else {
    $sortby = $requestedSortby;
}

// Add custom parameters
foreach (array('where', 'select', 'leftJoin', 'innerJoin') as $v) {
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
    'innerJoin' => json_encode($innerJoin),
    'leftJoin' => json_encode($leftJoin),
    'select' => json_encode($select),
    'sortby' => $sortby,
    'sortdir' => 'DESC',
    'groupby' => $groupby,
    'return' => 'data',
    'nestedChunkPrefix' => 'tickets_',
);

// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties));
$pdoFetch->addTime('Query parameters are prepared.');
$rows = $pdoFetch->run();

// Processing rows
$output = array();
if (!empty($rows) && is_array($rows)) {
    foreach ($rows as $k => $row) {
        // Prepare row
        if ($action == 'tickets') {
            $row['date_ago'] = $Tickets->dateFormat($row['createdon']);
            $properties = is_string($row['properties'])
                ? json_decode($row['properties'], true)
                : $row['properties'];
            if (empty($properties['process_tags'])) {
                foreach ($row as $field => $value) {
                    $row[$field] = str_replace(
                        array('[', ']', '`', '{', '}'),
                        array('&#91;', '&#93;', '&#96;', '&#123;', '&#125;'),
                        $value
                    );
                }
            }
        } else {
            if (empty($row['createdby'])) {
                $row['fullname'] = $row['name'];
                $row['guest'] = 1;
            }
            $row['resource'] = $row['ticket.id'];
            $row = $Tickets->prepareComment($row);
        }

        // Processing chunk
        $row['idx'] = $pdoFetch->idx++;
        $tpl = $pdoFetch->defineChunk($row);
        $output[] = !empty($tpl)
            ? $pdoFetch->getChunk($tpl, $row, $pdoFetch->config['fastMode'])
            : $pdoFetch->getChunk('', $row);
    }
    $pdoFetch->addTime('Returning processed chunks');
}
if (empty($outputSeparator)) {
    $outputSeparator = "\n";
}
$output = implode($outputSeparator, $output);

if (!empty($cacheKey)) {
    $modx->cacheManager->set('tickets/latest.' . $cacheKey, $output, $cacheTime);
}

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="TicketLatestLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}
