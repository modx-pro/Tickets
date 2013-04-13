<?php
/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
/* @var pdoFetch $pdoFetch */
if (!empty($modx->services['pdofetch'])) {unset($modx->services['pdofetch']);}
$pdoFetch = $modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH.'components/pdotools/model/pdotools/',$scriptProperties);
$pdoFetch->config['nestedChunkPrefix'] = 'tickets_';
$pdoFetch->addTime('pdoTools loaded.');

$class = 'Ticket';
$where = array('class_key' => $class);
if (empty($showUnpublished)) {$where['published'] = 1;}
if (empty($showHidden)) {$where['hidemenu'] = 0;}
if (empty($showDeleted)) {$where['deleted'] = 0;}
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
	'{"class":"TicketView","alias":"View","on":"Ticket.id=View.parent"}'
	,'{"class":"TicketView","alias":"LastView","on":"Ticket.id=LastView.parent AND LastView.uid = '.$modx->user->id.'"}'
	,'{"class":"TicketVote","alias":"Vote","on":"Ticket.id=Vote.parent AND Vote.class=\'Ticket\'"}'
	,'{"class":"TicketThread","alias":"Thread","on":"Thread.resource=Ticket.id"}'
	,'{"class":"TicketComment","alias":"Comment","on":"Comment.thread=Thread.id"}'
	,'{"class":"TicketsSection","alias":"Section","on":"Section.id=Ticket.parent"}'
	,'{"class":"modUser","alias":"User","on":"User.id=Ticket.createdby"}'
	,'{"class":"modUserProfile","alias":"Profile","on":"Profile.internalKey=User.id"}'
);

// Include TVs
$tvsLeftJoin = $tvsSelect = array();
if (!empty($includeTVs)) {
	$tvs = array_map('trim',explode(',',$includeTVs));
	if(!empty($tvs[0])){
		$q = $modx->newQuery('modTemplateVar', array('name:IN' => $tvs));
		$q->select('id,name');
		if ($q->prepare() && $q->stmt->execute()) {
			$tv_ids = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
			if (!empty($tv_ids)) {
				foreach ($tv_ids as $tv) {
					$leftJoin[] = '{"class":"modTemplateVarResource","alias":"TV'.$tv['name'].'","on":"TV'.$tv['name'].'.contentid = '.$class.'.id AND TV'.$tv['name'].'.tmplvarid = '.$tv['id'].'"}';
					$tvsSelect[] = ' "TV'.$tv['name'].'":"`TV'.$tv['name'].'`.`value` as `'.$tvPrefix.$tv['name'].'`" ';
				}
			}
		}
		$pdoFetch->addTime('Included list of tvs: <b>'.implode(', ',$tvs).'</b>.');
	}
}
// End of including TVs

// Fields to select
$resourceColumns = !empty($includeContent) ?  $modx->getSelectColumns($class, $class) : $modx->getSelectColumns($class, $class, '', array('content'), true);
$sectionColumns = $modx->getSelectColumns('TicketsSection', 'Section', 'section.', array('content'), true);
$userColumns = $modx->getSelectColumns('modUser', 'User', '', array('username'));
$profileColumns = $modx->getSelectColumns('modUserProfile', 'Profile', '', array('id'), true);
$select = array(
	'"Ticket":"'.$resourceColumns.'"'
	,'"Section":"'.$sectionColumns.'"'
	,'"User":"'.$userColumns.'"'
	,'"Profile":"'.$profileColumns.'"'
	,'"Vote":"SUM(`Vote`.`value`) AS `votes`"'
	,'"View":"COUNT(DISTINCT `View`.`uid`) as `views`"'
	,'"LastView":"`LastView`.`timestamp` as `new_comments`"'
	,'"Comment":"COUNT(DISTINCT `Comment`.`id`) as `comments`"'
);
if (!empty($tvsSelect)) {$select = array_merge($select, $tvsSelect);}

$default = array(
	'class' => $class
	,'where' => $modx->toJSON($where)
	,'leftJoin' => '['.implode(',',$leftJoin).']'
	,'select' => '{'.implode(',',$select).'}'
	,'sortby' => 'createdon'
	,'sortdir' => 'DESC'
	,'groupby' => '`'.$class.'`.`id`'
	,'fastMode' => false
	,'return' => 'data'
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

		// Processing new comments
		if ($modx->user->isAuthenticated() && empty($row['new_comments'])) {
			$row['new_comments'] = $row['comments'];
		}
		else if (!empty($row['new_comments'])) {
			$thread_name = 'resource-'.$row['id'];
			$q = $modx->newQuery('TicketComment');
			$q->leftJoin('TicketThread', 'Thread', 'Thread.name = "'.$thread_name.'" AND `Thread`.`id`=`TicketComment`.`thread`');
			$q->where('`Thread`.`name` = "'.$thread_name.'" AND `TicketComment`.`createdon` > "'.$row['new_comments'].'" AND `TicketComment`.`createdby` != '.$modx->user->id);
			$q->select('COUNT(`TicketComment`.`id`)');
			if ($q->prepare() && $q->stmt->execute()) {
				$row['new_comments'] = $q->stmt->fetch(PDO::FETCH_COLUMN);
			}
		}
		// Processing main fields
		$row['date_ago'] = $Tickets->dateFormat($row['createdon']);

		// Processing quick fields
		if (!empty($tpl)) {
			$pl = $pdoFetch->makePlaceholders($row);
			$qfields = array_keys($pdoFetch->elements[$tpl]['placeholders']);
			foreach ($qfields as $field) {
				if (!empty($row[$field])) {
					$row[$field] = str_replace($pl['pl'], $pl['vl'], $pdoFetch->elements[$tpl]['placeholders'][$field]);
				}
				else {
					$row[$field] = '';
				}
			}
		}

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
	$output .= '<pre class="getTicketsLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

// Return output
if (!empty($toPlaceholder)) {
	$modx->setPlaceholder($toPlaceholder, $output);
}
else {
	return $output;
}