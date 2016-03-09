<?php
/* @var array $scriptProperties */
/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets', 'Tickets', $modx->getOption('tickets.core_path', null, $modx->getOption('core_path') . 'components/tickets/') . 'model/tickets/', $scriptProperties);
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

//Filter by user
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

// Joining tables
$leftJoin = array(
	'Section' => array('class' => 'TicketsSection', 'on' => '`Section`.`id` = `Ticket`.`parent`'),
	'User' => array('class' => 'modUser', 'on' => '`User`.`id` = `Ticket`.`createdby`'),
	'Profile' => array('class' => 'modUserProfile', 'on' => '`Profile`.`internalKey` = `User`.`id`'),
);
if ($Tickets->authenticated) {
	$leftJoin['Vote'] = array(
		'class' => 'TicketVote',
		'on' => '`Vote`.`id` = `Ticket`.`id` AND `Vote`.`class` = "Ticket" AND `Vote`.`createdby` = ' . $modx->user->id
	);
	$leftJoin['Star'] = array(
		'class' => 'TicketStar',
		'on' => '`Star`.`id` = `Ticket`.`id` AND `Star`.`class` = "Ticket" AND `Star`.`createdby` = ' . $modx->user->id
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
);
if ($Tickets->authenticated) {
	$select['Vote'] = '`Vote`.`value` as `vote`';
	$select['Star'] = 'COUNT(`Star`.`id`) as `star`';
}
$pdoFetch->addTime('Conditions prepared');

// Add custom parameters
foreach (array('where', 'select', 'leftJoin') as $v) {
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
	'leftJoin' => $modx->toJSON($leftJoin),
	'select' => $modx->toJSON($select),
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

// Processing rows
$output = array();
if (!empty($rows) && is_array($rows)) {
	foreach ($rows as $k => $row) {
		// Handle properties
		$properties = is_string($row['properties'])
			? $modx->fromJSON($row['properties'])
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
		$row['rating'] = $row['rating_total'] = array_key_exists('rating', $properties)
			? $properties['rating']
			: 0;
		$row['rating_plus'] = array_key_exists('rating_plus', $properties)
			? $properties['rating_plus']
			: 0;
		$row['rating_minus'] = array_key_exists('rating_minus', $properties)
			? $properties['rating_minus']
			: 0;
		if ($row['rating'] > 0) {
			$row['rating'] = '+' . $row['rating'];
			$row['rating_positive'] = 1;
		}
		elseif ($row['rating'] < 0) {
			$row['rating_negative'] = 1;
		}

		if (!$Tickets->authenticated || $modx->user->id == $row['createdby']) {
			$row['cant_vote'] = 1;
		}
		elseif (array_key_exists('vote', $row)) {
			if ($row['vote'] == '') {
				$row['can_vote'] = 1;
			}
			elseif ($row['vote'] > 0) {
				$row['voted_plus'] = 1;
				$row['cant_vote'] = 1;
			}
			elseif ($row['vote'] < 0) {
				$row['voted_minus'] = 1;
				$row['cant_vote'] = 1;
			}
			else {
				$row['voted_none'] = 1;
				$row['cant_vote'] = 1;
			}
		}
		$row['active'] = (int)!empty($row['can_vote']);
		$row['inactive'] = (int)!empty($row['cant_vote']);

		$row['can_star'] = $Tickets->authenticated;
		$row['stared'] = !empty($row['star']);
		$row['unstared'] = empty($row['star']);

		// Adding fields to row
		$additional_fields = $pdoFetch->getObject('Ticket', $row['id'], array(
			'leftJoin' => array(
				'View' => array('class' => 'TicketView', 'on' => '`Ticket`.`id` = `View`.`parent`'),
				'Thread' => array('class' => 'TicketThread', 'on' => '`Thread`.`resource` = `Ticket`.`id`  AND `Thread`.`deleted` = 0'),
			),
			'select' => array(
				'View' => 'COUNT(`View`.`parent`) as `views`',
				'Thread' => '`Thread`.`id` as `thread`',
			),
			'groupby' => $class . '.id'
		));
		$row = array_merge($row, $additional_fields);
		$row['date_ago'] = $Tickets->dateFormat($row['createdon']);
		$row['comments'] = $modx->getCount('TicketComment', array('published' => 1, 'thread' => $row['thread']));
		$row['stars'] = $modx->getCount('TicketStar', array('id' => $row['id'], 'class' => 'Ticket'));
		// Special fields for quick placeholders
		$row['isauthor'] = $modx->user->id == $row['createdby'];
		$row['unpublished'] = empty($row['published']);

		$row['idx'] = $pdoFetch->idx++;
		// Processing new comments
		if ($Tickets->authenticated) {
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
			}
			else {
				$row['new_comments'] = $row['comments'];
			}
		}
		else {
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
}
else {
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
	}
	else {
		return $output;
	}
}