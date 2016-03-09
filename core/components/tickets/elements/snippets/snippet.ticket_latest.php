<?php
/* @var array $scriptProperties */
if (!empty($cacheKey) && $output = $modx->cacheManager->get('tickets/latest.' . $cacheKey)) {
	return $output;
}

/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets', 'Tickets', $modx->getOption('tickets.core_path', null, $modx->getOption('core_path') . 'components/tickets/') . 'model/tickets/', $scriptProperties);
$Tickets->initialize($modx->context->key, $scriptProperties);

/** @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdoFetch');
$pdoFetch->setConfig($scriptProperties);
$pdoFetch->addTime('pdoTools loaded');

if (empty($action)) {
	$action = 'comments';
}
if ($action == 'tickets' && $scriptProperties['tpl'] == 'tpl.Tickets.comment.latest') {
	$scriptProperties['tpl'] = 'tpl.Tickets.ticket.latest';
}
$action = strtolower($action);
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
		}
		else {
			$user_username[] = $v;
		}
	}
	if (!empty($user_id) && !empty($user_username)) {
		$where[] = '(`User`.`id` IN (' . implode(',', $user_id) . ') OR `User`.`username` IN (\'' . implode('\',\'', $user_username) . '\'))';
	}
	else {
		if (!empty($user_id)) {
			$where['User.id:IN'] = $user_id;
		}
		else {
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
		}
		else {
			$in[] = $v;
		}
	}
	if (!empty($in)) {
		$where['id:IN'] = $in;
	}
	if (!empty($out)) {
		$where['id:NOT IN'] = $out;
	}
}
// Filter by parents
else {
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

// Joining tables
if ($action == 'comments') {
	$class = 'TicketComment';

	$innerJoin = array();
	$innerJoin['Thread'] = empty($user)
		? array('class' => 'TicketThread', 'on' => '`TicketComment`.`id` = `Thread`.`comment_last` AND `Thread`.`deleted` = 0')
		: array('class' => 'TicketThread', 'on' => '`TicketComment`.`thread` = `Thread`.`id` AND `Thread`.`deleted` = 0');
	$innerJoin['Ticket'] = array('class' => 'Ticket', 'on' => '`Ticket`.`id` = `Thread`.`resource`');

	$leftJoin = array(
		'Section' => array('class' => 'TicketsSection', 'on' => '`Section`.`id` = `Ticket`.`parent`'),
		'User' => array('class' => 'modUser', 'on' => '`User`.`id` = `TicketComment`.`createdby`'),
		'Profile' => array('class' => 'modUserProfile', 'on' => '`Profile`.`internalKey` = `TicketComment`.`createdby`'),
	);

	$select = array(
		'TicketComment' => !empty($includeContent)
			? $modx->getSelectColumns('TicketComment', 'TicketComment', '', array('raw'), true)
			: $modx->getSelectColumns('TicketComment', 'TicketComment', '', array('text', 'raw'), true),
		'Ticket' => !empty($includeContent)
			? $modx->getSelectColumns('Ticket', 'Ticket', 'ticket.')
			: $modx->getSelectColumns('Ticket', 'Ticket', 'ticket.', array('content'), true)
	);
	$groupby = empty($user)
		? '`Ticket`.`id`'
		: '`TicketComment`.`id`';
	$where['TicketComment.deleted'] = 0;
}
elseif ($action == 'tickets') {
	$class = 'Ticket';

	$innerJoin = array();
	$leftJoin = array(
		'Thread' => array('class' => 'TicketThread', 'on' => '`Thread`.`resource` = `Ticket`.`id` AND `Thread`.`deleted` = 0'),
		'Section' => array('class' => 'TicketsSection', 'on' => '`Section`.`id` = `Ticket`.`parent`'),
		'User' => array('class' => 'modUser', 'on' => '`User`.`id` = `Ticket`.`createdby`'),
		'Profile' => array('class' => 'modUserProfile', 'on' => '`Profile`.`internalKey` = `Ticket`.`createdby`'),
	);

	$select = array(
		'Ticket' => !empty($includeContent)
			? $modx->getSelectColumns('Ticket', 'Ticket')
			: $modx->getSelectColumns('Ticket', 'Ticket', '', array('content'), true),
		'Thread' => '`Thread`.`id` as `thread`'
	);
	$groupby = '`Ticket`.`id`';
}
else {
	return 'Wrong action. You must use "ticket" or "comment".';
}

// Fields to select
$select = array_merge($select, array(
	'Section' => $modx->getSelectColumns('TicketsSection', 'Section', 'section.', array('content'), true),
	'User' => $modx->getSelectColumns('modUser', 'User', '', array('username')),
	'Profile' => $modx->getSelectColumns('modUserProfile', 'Profile', '', array('id'), true),
));

// Add custom parameters
foreach (array('where', 'select', 'leftJoin', 'innerJoin') as $v) {
	if (!empty($scriptProperties[$v])) {
		$tmp = $modx->fromJSON($scriptProperties[$v]);
		if (is_array($tmp)) {
			$$v = array_merge($$v, $tmp);
		}
	}
	unset($scriptProperties[$v]);
}

$default = array(
	'class' => $class,
	'where' => $modx->toJSON($where),
	'innerJoin' => $modx->toJSON($innerJoin),
	'leftJoin' => $modx->toJSON($leftJoin),
	'select' => $modx->toJSON($select),
	'sortby' => 'createdon',
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
		// Processing main fields
		$row['comments'] = $modx->getCount('TicketComment', array('thread' => $row['thread'], 'published' => 1));

		// Prepare row
		if ($class == 'Ticket') {
			$row['date_ago'] = $Tickets->dateFormat($row['createdon']);
			$properties = is_string($row['properties'])
				? $modx->fromJSON($row['properties'])
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
		}
		else {

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
}
else {
	return $output;
}