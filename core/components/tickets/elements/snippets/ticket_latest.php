<?php
$output = '';
if (!empty($cacheKey) && $output = $modx->cacheManager->get('tickets/latest.'.$cacheKey)) {
	return $output;
}

/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
/* @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH.'components/pdotools/model/pdotools/',$scriptProperties);
$pdoFetch->setConfig($scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

if (empty($action)) {$action = 'comments';}
$class = 'Ticket';
$where = ($action == 'tickets') ? array('class_key' => 'Ticket') : array();

if (empty($showUnpublished)) {$where[$class.'.published'] = 1;}
if (empty($showHidden)) {$where[$class.'.hidemenu'] = 0;}
if (empty($showDeleted)) {$where[$class.'.deleted'] = 0;}
if (!empty($user)) {
	$user = array_map('trim', explode(',', $user));
	$user_id = $user_username = array();
	foreach ($user as $v) {
		if (is_numeric($v)) {$user_id[] = $v;}
		else {$user_username[] = $v;}
	}
	if (!empty($user_id) && !empty($user_username)) {
		$where[] = '(`User`.`id` IN ('.implode(',',$user_id).') OR `User`.`username` IN (\''.implode('\',\'',$user_username).'\'))';
	}
	else if (!empty($user_id)) {$where['User.id:IN'] = $user_id;}
	else if (!empty($user_username)) {$where['User.username:IN'] = $user_username;}
}

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
	//if (empty($parents) && $parents != '0') {$parents = $modx->resource->id;}
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

// Fields to select
$sectionColumns = $modx->getSelectColumns('TicketsSection', 'Section', 'section.', array('content'), true);
$userColumns = $modx->getSelectColumns('modUser', 'User', '', array('username'));
$profileColumns = $modx->getSelectColumns('modUserProfile', 'Profile', '', array('id'), true);

// Joining tables
if ($action == 'comments') {
	$resourceColumns = !empty($includeContent) ?  $modx->getSelectColumns('Ticket', 'Ticket', 'ticket.') : $modx->getSelectColumns('Ticket', 'Ticket', 'ticket.', array('content'), true);
	$commentColumns = !empty($includeContent) ?  $modx->getSelectColumns('TicketComment', 'TicketComment') : $modx->getSelectColumns('TicketComment', 'TicketComment', '', array('text','raw'), true);
	$mainClass = 'TicketComment';
	$innerJoin = array(
		'{"class":"TicketThread","alias":"Thread","on":"TicketComment.id=Thread.comment_last"}'
		,'{"class":"modResource","alias":"Ticket","on":"Ticket.id=Thread.resource"}'
	);
	$leftJoin = array(
		'{"class":"modResource","alias":"Section","on":"Section.id=Ticket.parent"}'
		,'{"class":"TicketComment","alias":"Comments","on":"Comments.thread=Thread.id"}'
		,'{"class":"modUser","alias":"User","on":"User.id=TicketComment.createdby"}'
		,'{"class":"modUserProfile","alias":"Profile","on":"Profile.internalKey=User.id"}'
	);
	$select = array(
		'"TicketComment":"'.$commentColumns.'"'
		,'"Comments":"COUNT(DISTINCT `Comments`.`id`) as `comments`"'
		,'"Ticket":"'.$resourceColumns.'"'
	);

}
else if ($action == 'tickets') {
	$resourceColumns = !empty($includeContent) ?  $modx->getSelectColumns('Ticket', 'Ticket') : $modx->getSelectColumns('Ticket', 'Ticket', '', array('content'), true);
	$mainClass = 'Ticket';
	$innerJoin = array();
	$leftJoin = array(
		'{"class":"TicketThread","alias":"Thread","on":"Thread.resource=Ticket.id"}'
		,'{"class":"TicketComment","alias":"TicketComment","on":"TicketComment.thread=Thread.id"}'
		,'{"class":"TicketsSection","alias":"Section","on":"Section.id=Ticket.parent"}'
		,'{"class":"modUser","alias":"User","on":"User.id=Ticket.createdby"}'
		,'{"class":"modUserProfile","alias":"Profile","on":"Profile.internalKey=User.id"}'
	);
	$select = array(
		'"Ticket":"'.$resourceColumns.'"'
		,'"TicketComment":"COUNT(DISTINCT `TicketComment`.`id`) as `comments`"'
	);
}
else {return 'wrong action.';}

$select = array_merge($select, array(
	'"Section":"'.$sectionColumns.'"'
	,'"User":"'.$userColumns.'"'
	,'"Profile":"'.$profileColumns.'"'
));

if (!empty($tvsSelect)) {$select = array_merge($select, $tvsSelect);}

$default = array(
	'class' => $mainClass
	,'where' => $modx->toJSON($where)
	,'leftJoin' => '['.implode(',',$leftJoin).']'
	,'select' => '{'.implode(',',$select).'}'
	,'sortby' => 'createdon'
	,'sortdir' => 'DESC'
	,'groupby' => '`'.$class.'`.`id`'
	,'fastMode' => false
	,'return' => 'data'
	,'nestedChunkPrefix' => 'tickets_'
);
if (!empty($innerJoin)) {
	$default['innerJoin'] = '['.implode(',',$innerJoin).']';
}

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
		$properties = $modx->fromJSON(@$row['properties']);
		if (empty($properties['process_tags'])) {
			foreach ($row as $field => $value) {
				$row[$field] = str_replace(array('[',']'), array('&#91;','&#93;'), $value);
			}
		}

		// Processing main fields
		$row['date_ago'] = $Tickets->dateFormat($row['createdon']);

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

if (!empty($cacheKey)) {
	$modx->cacheManager->set('tickets/latest.'.$cacheKey, $output, 1800);
}

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$output .= '<pre class="TicketLatestLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

if (!empty($toPlaceholder)) {
	$modx->setPlaceholder($toPlaceholder, $output);
}
else {
	return $output;
}