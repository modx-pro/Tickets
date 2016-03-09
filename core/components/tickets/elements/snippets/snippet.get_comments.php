<?php
/* @var array $scriptProperties */
/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets', 'Tickets', $modx->getOption('tickets.core_path', null, $modx->getOption('core_path') . 'components/tickets/') . 'model/tickets/', $scriptProperties);
$Tickets->initialize($modx->context->key, $scriptProperties);

/** @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdoFetch');
$pdoFetch->setConfig($scriptProperties);
$pdoFetch->addTime('pdoTools loaded');

$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.Tickets.comment.list.row');
$outputSeparator = $modx->getOption('outputSeparator', $scriptProperties, "\n");

// Define threads of comments
if (!empty($parents) || !empty($resources) || !empty($threads)) {
	$where = array();
	$options = array(
		'innerJoin' => array(
			'Thread' => array(
				'class' => 'TicketThread',
				'on' => '`Ticket`.`id` = `Thread`.`resource`',
			)
		),
		'groupby' => '`Ticket`.`id`',
		'select' => array('Thread' => '`Thread`.`id`'),
		'showUnpublished' => !empty($showUnpublished),
		'showDeleted' => !empty($showDeleted),
		'depth' => isset($depth)
			? (int)$depth
			: 10,
	);
	if (!empty($parents)) {
		$options['parents'] = $parents;
	}
	if (!empty($resources)) {
		$options['resources'] = $resources;
	}
	if (!empty($threads)) {
		$threads = array_map('trim', explode(',', $threads));
		$threads_in = $threads_out = array();
		foreach ($threads as $v) {
			if (!is_numeric($v)) {
				continue;
			}
			if ($v[0] == '-') {
				$threads_out[] = abs($v);
			}
			else {
				$threads_in[] = abs($v);
			}
		}
		if (!empty($threads_in)) {
			$where['Thread.id:IN'] = $threads_in;
		}
		if (!empty($threads_out)) {
			$where['Thread.id:NOT IN'] = $threads_out;
		}
	}

	$rows = $pdoFetch->getCollection('Ticket', $where, $options);
	$threads = array();
	foreach ($rows as $item) {
		$threads[] = $item['id'];
	}
}

// Prepare query to db
$class = 'TicketComment';
$where = array();
if (empty($showUnpublished)) {
	$where['published'] = 1;
}
if (empty($showDeleted)) {
	$where['deleted'] = 0;
}

// Filter by user
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
	elseif (!empty($user_id)) {
		$where['User.id:IN'] = $user_id;
	}
	elseif (!empty($user_username)) {
		$where['User.username:IN'] = $user_username;
	}
}
// Filter by threads
if (!empty($threads)) {
	$where['thread:IN'] = $threads;
}
// Filter by comments
if (!empty($comments)) {
	$comments = array_map('trim', explode(',', $comments));
	$comments_in = $comments_out = array();
	foreach ($comments as $v) {
		if (!is_numeric($v)) {
			continue;
		}
		if ($v[0] == '-') {
			$comments_out[] = abs($v);
		}
		else {
			$comments_in[] = abs($v);
		}
	}
	if (!empty($comments_in)) {
		$where['id:IN'] = $comments_in;
	}
	if (!empty($comments_out)) {
		$where['id:NOT IN'] = $comments_out;
	}
}

// Joining tables
$innerJoin = array(
	'Thread' => array(
		'class' => 'TicketThread',
		'on' => '`Thread`.`id` = `TicketComment`.`thread`'
	)
);
$leftJoin = array(
	'User' => array('class' => 'modUser', 'on' => '`User`.`id` = `TicketComment`.`createdby`'),
	'Profile' => array('class' => 'modUserProfile', 'on' => '`Profile`.`internalKey` = `TicketComment`.`createdby`'),
	'Ticket' => array('class' => 'Ticket', 'on' => '`Ticket`.`id` = `Thread`.`resource`'),
	'Section' => array('class' => 'TicketsSection', 'on' => '`Section`.`id` = `Ticket`.`parent`'),
);
if ($Tickets->authenticated) {
	$leftJoin['Vote'] = array(
		'class' => 'TicketVote',
		'on' => '`Vote`.`id` = `TicketComment`.`id` AND `Vote`.`class` = "TicketComment" AND `Vote`.`createdby` = ' . $modx->user->id
	);
	$leftJoin['Star'] = array(
		'class' => 'TicketStar',
		'on' => '`Star`.`id` = `TicketComment`.`id` AND `Star`.`class` = "TicketComment" AND `Star`.`createdby` = ' . $modx->user->id
	);
}
// Fields to select
$select = array(
	'TicketComment' => $modx->getSelectColumns('TicketComment', 'TicketComment', '', array('raw'), true) . ', `rating` as `rating_total`',
	'Thread' => '`Thread`.`resource`',
	'User' => '`User`.`username`',
	'Profile' => $modx->getSelectColumns('modUserProfile', 'Profile', '', array('id', 'email'), true) . ',`Profile`.`email` as `user_email`',
	'Ticket' => !empty($includeContent)
		? $modx->getSelectColumns('Ticket', 'Ticket', 'ticket.')
		: $modx->getSelectColumns('Ticket', 'Ticket', 'ticket.', array('content'), true),
	'Section' => !empty($includeContent)
		? $modx->getSelectColumns('TicketsSection', 'Section', 'section.')
		: $modx->getSelectColumns('TicketsSection', 'Section', 'section.', array('content'), true),
);
if ($Tickets->authenticated) {
	$select['Vote'] = '`Vote`.`value` as `vote`';
	$select['Star'] = 'COUNT(`Star`.`id`) as `star`';
}

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
$pdoFetch->addTime('Conditions prepared');

$default = array(
	'class' => $class,
	'where' => $modx->toJSON($where),
	'innerJoin' => $modx->toJSON($innerJoin),
	'leftJoin' => $modx->toJSON($leftJoin),
	'select' => $modx->toJSON($select),
	'sortby' => $class . '.createdon',
	'sortdir' => 'DESC',
	'groupby' => $class . '.id',
	'fastMode' => true,
	'return' => 'data',
	'nestedChunkPrefix' => 'tickets_',
);

// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties), false);
$pdoFetch->addTime('Query parameters prepared.');
$rows = $pdoFetch->run();

$output = array();
if (!empty($rows)) {
	foreach ($rows as $row) {
		$row['comments'] = $modx->getCount('TicketComment', array('published' => 1, 'thread' => $row['thread']));;
		$output[] = $Tickets->templateNode($row, $tpl);
	}
}
$pdoFetch->addTime('Returning processed chunks');
$output = implode($outputSeparator, $output);

$log = '';
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$log .= '<pre class="getCommentsLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

// Return output
if (!empty($toSeparatePlaceholders)) {
	$output['log'] = $log;
	$modx->setPlaceholders($output, $toSeparatePlaceholders);
}
else {
	$output .= $log;

	if (!empty($tplWrapper) && (!empty($wrapIfEmpty) || !empty($output))) {
		$output = $pdoFetch->getChunk($tplWrapper, array('output' => $output), $pdoFetch->config['fastMode']);
	}

	if (!empty($toPlaceholder)) {
		$modx->setPlaceholder($toPlaceholder, $output);
	}
	else {
		return $output;
	}
}
