<?php
/* @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH.'components/pdotools/model/pdotools/',$scriptProperties);
$pdoFetch->setConfig($scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

$class = 'TicketsSection';
$where = array('class_key' => $class);
if (empty($showUnpublished)) {$where['published'] = 1;}
if (empty($showHidden)) {$where['hidemenu'] = 0;}
if (empty($showDeleted)) {$where['deleted'] = 0;}

// Filter by ids
if (!empty($resources)){
	$resources = array_map('trim', explode(',', $resources));
	$in = $out = array();
	foreach ($resources as $v) {
		if (!is_numeric($v)) {continue;}
		if ($v < 0) {$out[] = abs($v);}
		else {$in[] = $v;}
	}
	if (!empty($in)) {$where['id:IN'] = $in;}
	if (!empty($out)) {$where['id:NOT IN'] = $out;}
}
// Filter by parents
else {
	if (empty($parents) && $parents != '0') {$parents = $modx->resource->id;}
	if (!empty($parents) && $parents > 0) {
		$pids = array_map('trim', explode(',', $parents));
		$parents = $pids;
		if (!empty($depth) && $depth > 0) {
			foreach ($pids as $v) {
				if (!is_numeric($v)) {continue;}
				$parents = array_merge($parents, $modx->getChildIds($v, $depth));
			}
		}
		if (!empty($parents)) {
			$where['parent:IN'] = $parents;
		}
	}
}

// Adding custom where parameters
if (!empty($scriptProperties['where'])) {
	$tmp = $modx->fromJSON($scriptProperties['where']);
	if (is_array($tmp)) {
		$where = array_merge($where, $tmp);
	}
}
unset($scriptProperties['where']);
$pdoFetch->addTime('"Where" expression built.');

// Joining tables
$leftJoin = array(
		'{"class":"Ticket","alias":"Ticket","on":"Ticket.parent=TicketsSection.id AND Ticket.published=1 AND Ticket.deleted=0 AND Ticket.class_key=\'Ticket\'"}'
		,'{"class":"TicketView","alias":"View","on":"Ticket.id=View.parent"}'
		,'{"class":"TicketVote","alias":"Vote","on":"Ticket.id=Vote.parent AND Vote.class=\'Ticket\'"}'
		,'{"class":"TicketThread","alias":"Thread","on":"Thread.resource=Ticket.id AND Thread.closed=0 AND Thread.deleted=0"}'
		,'{"class":"TicketComment","alias":"Comment","on":"Comment.thread=Thread.id AND Comment.published=1"}'
);

// Fields to select
$resourceColumns = !empty($includeContent) ?  $modx->getSelectColumns($class, $class) : $modx->getSelectColumns($class, $class, '', array('content'), true);
$select = array(
	'"TicketsSection":"'.$resourceColumns.'"'
	,'"Ticket":"COUNT(DISTINCT `Ticket`.`id`) as `tickets`"'
	,'"Vote":"SUM(DISTINCT `Vote`.`value`) as `votes`"'
	,'"View":"COUNT(DISTINCT `View`.`parent`, `View`.`uid`) as `views`"'
	,'"Comment":"COUNT(DISTINCT `Comment`.`id`) as `comments`"'
);
if (!empty($tvsSelect)) {$select = array_merge($select, $tvsSelect);}

$default = array(
	'class' => 'TicketsSection'
	,'where' => $modx->toJSON($where)
	,'leftJoin' => '['.implode(',',$leftJoin).']'
	,'select' => '{'.implode(',',$select).'}'
	,'groupby' => '`'.$class.'`.`id`'
	,'sortby' => 'views'
	,'sortdir' => 'DESC'
	,'fastMode' => false
	,'return' => 'data'
	,'nestedChunkPrefix' => 'tickets_'
);

// Merge all properties and run!
if (!empty($scriptProperties['sortBy'])) {$scriptProperties['sortby'] = $scriptProperties['sortBy'];}
if (!empty($scriptProperties['sortDir'])) {$scriptProperties['sortdir'] = $scriptProperties['sortDir'];}
$pdoFetch->config = array_merge($pdoFetch->config, $default, $scriptProperties);
$pdoFetch->addTime('Query parameters are prepared.');
$rows = $pdoFetch->run();

// Initializing chunk for template rows
if (!empty($tpl)) {
	$pdoFetch->getChunk($tpl);
}

$output = null;
// Processing rows
$output = null;
if (!empty($rows) && is_array($rows)) {
	foreach ($rows as $k => $row) {
		// Processing chunk
		$output[] = empty($tpl)
			? '<pre>'.str_replace(array('[',']','`'), array('&#91;','&#93;','&#96;'), htmlentities(print_r($row, true), ENT_QUOTES, 'UTF-8')).'</pre>'
			: $pdoFetch->getChunk($tpl, $row, $pdoFetch->config['fastMode']);
	}
	$pdoFetch->addTime('Returning processed chunks');
	if (empty($outputSeparator)) {$outputSeparator = "\n";}
	if (!empty($output)) {
		$output = implode($outputSeparator, $output);
	}
}

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$output .= '<pre class="getSectionsLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

// Return output
if (!empty($toPlaceholder)) {
	$modx->setPlaceholder($toPlaceholder, $output);
}
else {
	return $output;
}