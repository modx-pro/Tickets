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

$class = 'TicketsSection';
$where = array('class_key' => $class);

// Add custom parameters
foreach (array('where') as $v) {
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
$pdoFetch->addTime('Conditions prepared');

// Joining tables
$leftJoin = array(
    'Total' => array('class' => 'TicketTotal'),
);

// Fields to select
$select = array(
    'TicketsSection' => !empty($includeContent)
        ? $modx->getSelectColumns($class, $class)
        : $modx->getSelectColumns($class, $class, '', array('content'), true),
    'Total' => 'tickets, comments, views, stars, rating, rating_plus, rating_minus',
);

$default = array(
    'class' => $class,
    'where' => json_encode($where),
    'leftJoin' => json_encode($leftJoin),
    'select' => json_encode($select),
    'groupby' => $class . '.id',
    'sortby' => 'views',
    'sortdir' => 'DESC',
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

// Processing rows
$output = array();
if (!empty($rows) && is_array($rows)) {
    foreach ($rows as $k => $row) {
        $row['date_ago'] = $Tickets->dateFormat($row['createdon']);
        $row['idx'] = $pdoFetch->idx++;

        $tpl = $pdoFetch->defineChunk($row);
        $output[] = empty($tpl)
            ? '<pre>' . $pdoFetch->getChunk('', $row) . '</pre>'
            : $pdoFetch->getChunk($tpl, $row, $pdoFetch->config['fastMode']);
    }
}
$pdoFetch->addTime('Returning processed chunks');
if (empty($outputSeparator)) {
    $outputSeparator = "\n";
}
$output = implode($outputSeparator, $output);

$log = '';
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $log .= '<pre class="getSectionsLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

// Return output
if (!empty($toSeparatePlaceholders)) {
    $output['log'] = $log;
    $modx->setPlaceholders($output, $toSeparatePlaceholders);
} else {
    $output .= $log;

    if (!empty($tplWrapper) && (!empty($wrapIfEmpty) || !empty($output))) {
        $output = $pdoFetch->getChunk($tplWrapper, array('output' => $output), $pdoFetch->config['fastMode']);
    }

    if (!empty($toPlaceholder)) {
        $modx->setPlaceholder($toPlaceholder, $output);
    } else {
        return $output;
    }
}