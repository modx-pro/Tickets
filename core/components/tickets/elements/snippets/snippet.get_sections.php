<?php
/* @var array $scriptProperties */
/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
/* @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH.'components/pdotools/model/pdotools/',$scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

$class = 'TicketsSection';
$where = array('class_key' => $class);
if (empty($showUnpublished)) {$where['published'] = 1;}
if (empty($showHidden)) {$where['hidemenu'] = 0;}
if (empty($showDeleted)) {$where['deleted'] = 0;}

// Filter by ids
if (!empty($resources)) {
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
			$where[$class.'.parent:IN'] = $parents;
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
		//,'{"class":"TicketVote","alias":"Vote","on":"Ticket.id=Vote.parent AND Vote.class=\'Ticket\'"}'
);

// Fields to select
$resourceColumns = !empty($includeContent) ?  $modx->getSelectColumns($class, $class) : $modx->getSelectColumns($class, $class, '', array('content'), true);
$select = array(
	'"TicketsSection":"'.$resourceColumns.'"'
	,'"Ticket":"COUNT(DISTINCT `Ticket`.`id`) as `tickets`"'
	//,'"Vote":"SUM(DISTINCT `Vote`.`value`) as `votes`"'
	,'"View":"COUNT(DISTINCT `View`.`parent`, `View`.`uid`) as `views`"'
);

$default = array(
	'class' => $class
	,'where' => $modx->toJSON($where)
	,'leftJoin' => '['.implode(',',$leftJoin).']'
	,'select' => '{'.implode(',',$select).'}'
	,'groupby' => '`'.$class.'`.`id`'
	,'sortby' => 'views'
	,'sortdir' => 'DESC'
	,'return' => 'data'
	,'nestedChunkPrefix' => 'tickets_'
);

if (!empty($in) && (empty($scriptProperties['sortby']) || $scriptProperties['sortby'] == 'id')) {
	$scriptProperties['sortby'] = "find_in_set(`$class`.`id`,'".implode(',', $in)."')";
	$scriptProperties['sortdir'] = '';
}

// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties));
$pdoFetch->addTime('Query parameters are prepared.');
$rows = $pdoFetch->run();

// Processing rows
$output = array();
if (!empty($rows) && is_array($rows)) {
	foreach ($rows as $k => $row) {
		// Processing main fields
		$q = $modx->newQuery('TicketThread', array('closed' => 0, 'deleted' => 0));
		$q->innerJoin('Ticket', 'Ticket', 'Ticket.id = TicketThread.resource AND Ticket.published=1 AND Ticket.deleted=0 AND Ticket.class_key="Ticket" AND Ticket.parent='.$row['id']);
		$q->select('SUM(`comments`)');
		$tstart = microtime(true);
		if ($q->prepare() && $q->stmt->execute()) {
			$modx->executedQueries++;
			$modx->queryTime += microtime(true) - $tstart;
			$row['comments'] = $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		$row['date_ago'] = $Tickets->dateFormat($row['createdon']);

		$row['idx'] = $pdoFetch->idx++;
		// Processing chunk
		$tpl = $pdoFetch->defineChunk($row);
		$output[] = empty($tpl)
			? '<pre>'.$pdoFetch->getChunk('', $row).'</pre>'
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