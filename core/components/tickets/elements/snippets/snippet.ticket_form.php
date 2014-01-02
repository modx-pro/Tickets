<?php
/* @var array $scriptProperties */
/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
$Tickets->initialize($modx->context->key, $scriptProperties);

if (!$modx->user->isAuthenticated($modx->context->key)) {
	return $modx->lexicon('ticket_err_no_auth');
}

$tid = !empty($_REQUEST['tid']) ? (integer) $_REQUEST['tid'] : 0;
$parent = !empty($_REQUEST['parent']) ? (integer) $_REQUEST['parent'] : 0;
$data = array();

// Update of ticket
if (!empty($tid)) {
	$tpl = $scriptProperties['tplFormUpdate'];
	/* @var Ticket $ticket */
	if ($ticket = $modx->getObject('Ticket', array('class_key' => 'Ticket', 'id' => $tid))) {
		if ($ticket->get('createdby') != $modx->user->id  && !$modx->hasPermission('edit_document')) {
			return $modx->lexicon('ticket_err_wrong_user');
		}
		$charset = $modx->getOption('modx_charset');
		$allowedFields = array_map('trim', explode(',', $scriptProperties['allowedFields']));
		$allowedFields = array_unique(array_merge($allowedFields, array('parent','pagetitle','content')));

		$fields = array_keys($modx->getFieldMeta('Ticket'));
		foreach ($allowedFields as $field) {
			$value = in_array($field, $fields) ? $ticket->get($field) : $ticket->getTVValue($field);
			if (is_string($value)) {
				$value = html_entity_decode($value, ENT_QUOTES, $charset);
				$value = str_replace(array('[^','^]','[',']'), array('&#91;^','^&#93;','{{{{{','}}}}}'), $value);
				$value = htmlentities($value, ENT_QUOTES, $charset);
			}
			$data[$field] = $value;
		}
		$data['id'] = $ticket->id;
		$parent = $ticket->get('parent');
	}
	else {
		return $modx->lexicon('ticket_err_id', array('id' => $tid));
	}
}
else {
	$tpl = $scriptProperties['tplFormCreate'];
}

// Get available sections for ticket create
$data['sections'] = '';
$response = $Tickets->runProcessor('web/section/getlist');
$response = $modx->fromJSON($response->response);

foreach ($response['results'] as $v) {
	$v['selected'] = ($parent == $v['id']) ? 'selected' : '';
	$data['sections'] .= $Tickets->getChunk($Tickets->config['tplSectionRow'], $v);
}

$_SESSION['TicketForm'] = $Tickets->config;
return $Tickets->getChunk($tpl, $data);