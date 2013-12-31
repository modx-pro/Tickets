<?php
/* @var array $scriptProperties */
/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
/* @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH.'components/pdotools/model/pdotools/',$scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

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
		if (is_numeric($v)) {$user_id[] = $v;}
		else {$user_username[] = $v;}
	}
	if (!empty($user_id) && !empty($user_username)) {
		$where[] = '(`User`.`id` IN ('.implode(',',$user_id).') OR `User`.`username` IN (\''.implode('\',\'',$user_username).'\'))';
	}
	else if (!empty($user_id)) {$where['User.id:IN'] = $user_id;}
	else if (!empty($user_username)) {$where['User.username:IN'] = $user_username;}
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
	'Section' => array('class' => 'TicketsSection', 'on' => 'Section.id=Ticket.parent'),
	'User' => array('class' => 'modUser', 'on' => 'User.id=Ticket.createdby'),
	'Profile' => array('class' => 'modUserProfile', 'on' => 'Profile.internalKey=User.id'),
);

// Fields to select
$select = array(
	'Section' => $modx->getSelectColumns('TicketsSection', 'Section', 'section.', array('content'), true),
	'User' => $modx->getSelectColumns('modUser', 'User', '', array('username')),
	'Profile' => $modx->getSelectColumns('modUserProfile', 'Profile', '', array('id'), true),
	'Ticket' => !empty($includeContent) ?  $modx->getSelectColumns($class, $class) : $modx->getSelectColumns($class, $class, '', array('content'), true),
);

$default = array(
	'class' => $class
	,'where' => $modx->toJSON($where)
	,'leftJoin' => $modx->toJSON($leftJoin)
	,'select' => $modx->toJSON($select)
	,'sortby' => 'createdon'
	,'sortdir' => 'DESC'
	,'groupby' => $class.'.id'
	,'return' => 'data'
	,'nestedChunkPrefix' => 'tickets_'
);

// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties));
$pdoFetch->addTime('Query parameters are prepared.');
$rows = $pdoFetch->run();

// Processing rows
$output = array();
if (!empty($rows) && is_array($rows)) {
	foreach ($rows as $k => $row) {
		$properties = is_string($row['properties'])
			? $modx->fromJSON($row['properties'])
			: $row['properties'];
		if (empty($properties['process_tags'])) {
			foreach ($row as $field => $value) {
				$row[$field] = str_replace(array('[',']'), array('&#91;','&#93;'), $value);
			}
		}

		// Adding fields to row
		$additional_fields = $pdoFetch->getObject('Ticket', $row['id'], array(
			'leftJoin' => array(
				'View' => array('class' => 'TicketView', 'on' => 'Ticket.id=View.parent'),
				'LastView' => array('class' => 'TicketView', 'on' => 'Ticket.id=LastView.parent AND LastView.uid = '.$modx->user->id),
				//'Vote' => array('class' => 'TicketVote', 'on' => 'Ticket.id=Vote.parent AND Vote.class="Ticket"'),
				'Thread' => array('class' => 'TicketThread', 'on' => 'Thread.resource=Ticket.id  AND Thread.closed=0 AND Thread.deleted=0'),
			),
			'select' => array(
				'View' => 'COUNT(DISTINCT `View`.`uid`) as `views`',
				'LastView' => '`LastView`.`timestamp` as `new_comments`',
				//'Vote' => 'SUM(`Vote`.`value`) AS `votes`',
				'Thread' => '`Thread`.`id` as `thread`',
			),
			'groupby' => $class.'.id'
		));

		$row = array_merge($row, $additional_fields);
		$row['date_ago'] = $Tickets->dateFormat($row['createdon']);
		$row['comments'] = $modx->getCount('TicketComment', array('published' => 1, 'thread' => $row['thread']));

		$row['idx'] = $pdoFetch->idx++;
		// Processing new comments
		if ($modx->user->id && empty($row['new_comments'])) {
			$row['new_comments'] = $row['comments'];
		}
		elseif (!empty($row['new_comments'])) {
			$row['new_comments'] = $modx->getCount('TicketComment', array(
				'published' => 1
				,'thread' => $row['thread']
				,'createdon:>' => $row['new_comments']
				,'createdby:!=' => $modx->user->id
			));
		}

		// Processing chunk
		$tpl = $pdoFetch->defineChunk($row);
		$output[] = empty($tpl)
			? '<pre>'.$pdoFetch->getChunk('', $row).'</pre>'
			: $pdoFetch->getChunk($tpl, $row, $pdoFetch->config['fastMode']);
	}
	$pdoFetch->addTime('Returning processed chunks');
	if (empty($outputSeparator)) {$outputSeparator = "\n";}
	$output = implode($outputSeparator, $output);
}

$log = '';
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$log .= '<pre class="getTicketsLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

// Return output
if (!empty($returnIds)) {
	$modx->setPlaceholder('pdoResources.log', $log);
	if (!empty($toPlaceholder)) {
		$modx->setPlaceholder($toPlaceholder, $output);
	}
	else {
		return $output;
	}
}
elseif (!empty($toSeparatePlaceholders)) {
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