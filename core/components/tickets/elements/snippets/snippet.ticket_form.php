<?php
/* @var array $scriptProperties */
/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets', 'Tickets', $modx->getOption('tickets.core_path', null, $modx->getOption('core_path') . 'components/tickets/') . 'model/tickets/', $scriptProperties);
$Tickets->initialize($modx->context->key, $scriptProperties);

if (!$Tickets->authenticated) {
	return $modx->lexicon('ticket_err_no_auth');
}

$tplSectionRow = $modx->getOption('tplSectionRow', $scriptProperties, 'tpl.Tickets.sections.row');
$tplFormCreate = $modx->getOption('tplFormCreate', $scriptProperties, 'tpl.Tickets.form.create');
$tplFormUpdate = $modx->getOption('tplFormUpdate', $scriptProperties, 'tpl.Tickets.form.update');
$tplFiles = $modx->getOption('tplFiles', $scriptProperties, 'tpl.Tickets.form.files');
$tplFile = $Tickets->config['tplFile'] = $modx->getOption('tplFile', $scriptProperties, 'tpl.Tickets.form.file', true);
$tplImage = $Tickets->config['tplImage'] = $modx->getOption('tplImage', $scriptProperties, 'tpl.Tickets.form.image', true);
if (empty($source)) {
	$source = $Tickets->config['source'] = $modx->getOption('tickets.source_default', null, $modx->getOption('default_media_source'));
}
$tid = !empty($_REQUEST['tid'])
	? (int)$_REQUEST['tid']
	: 0;
$parent = !empty($_REQUEST['parent'])
	? $_REQUEST['parent']
	: '';
$data = array();

// Update of ticket
if (!empty($tid)) {
	$tplWrapper = $tplFormUpdate;
	/* @var Ticket $ticket */
	if ($ticket = $modx->getObject('Ticket', array('class_key' => 'Ticket', 'id' => $tid))) {
		if ($ticket->get('createdby') != $modx->user->id && !$modx->hasPermission('edit_document')) {
			return $modx->lexicon('ticket_err_wrong_user');
		}
		$charset = $modx->getOption('modx_charset');
		$allowedFields = array_map('trim', explode(',', $scriptProperties['allowedFields']));
		$allowedFields = array_unique(array_merge($allowedFields, array('parent', 'pagetitle', 'content')));

		$fields = array_keys($modx->getFieldMeta('Ticket'));
		foreach ($allowedFields as $field) {
			$value = in_array($field, $fields)
				? $ticket->get($field)
				: $ticket->getTVValue($field);
			if (is_string($value)) {
				$value = html_entity_decode($value, ENT_QUOTES, $charset);
				$value = str_replace(
					array('[^', '^]', '[', ']', '{', '}'),
					array('&#91;^', '^&#93;', '*(*(*(*(*(*', '*)*)*)*)*)*', '~(~(~(~(~(~', '~)~)~)~)~)~'),
					$value
				);
				$value = htmlentities($value, ENT_QUOTES, $charset);
			}
			$data[$field] = $value;
		}
		$data['id'] = $ticket->id;
		$data['published'] = $ticket->published;
		if (empty($parent)) {
			$parent = $ticket->get('parent');
		}
	}
	else {
		return $modx->lexicon('ticket_err_id', array('id' => $tid));
	}
}
else {
	$tplWrapper = $tplFormCreate;
}

// Get available sections for ticket create
$data['sections'] = '';
/** @var modProcessorResponse $response */
$response = $Tickets->runProcessor('web/section/getlist', array(
	'parents' => $scriptProperties['parents'],
	'resources' => $scriptProperties['resources'],
	'permissions' => $scriptProperties['permissions'],
	'sortby' => !empty($scriptProperties['sortby'])
		? $scriptProperties['sortby']
		: 'pagetitle',
	'sortdir' => !empty($scriptProperties['sortdir'])
		? $scriptProperties['sortdir']
		: 'asc',
	'depth' => isset($scriptProperties['depth'])
		? $scriptProperties['depth']
		: 0,
	'context' => !empty($scriptProperties['context'])
		? $scriptProperties['context']
		: $modx->context->key,
	'limit' => 0,
));
$response = $modx->fromJSON($response->getResponse());

if (!empty($response['results'])) {
	$Tickets->config['sections'] = array();
	foreach ($response['results'] as $v) {
		$v['selected'] = $parent == $v['id'] || $parent == $v['alias']
			? 'selected'
			: '';
		$data['sections'] .= $Tickets->getChunk($tplSectionRow, $v);
		$Tickets->config['sections'][] = $v['id'];
	}
}

if (!empty($allowFiles)) {
	$q = $modx->newQuery('TicketFile');
	$q->where(array('class' => 'Ticket'));
	$q->andCondition(array('parent' => 0, 'createdby' => $modx->user->id), null, 1);
	if (!empty($tid)) {
		$q->orCondition(array('parent' => $tid), null, 1);
	}
	$q->sortby('createdon', 'ASC');
	$collection = $modx->getIterator('TicketFile', $q);
	$files = '';
	/** @var TicketFile $item */
	foreach ($collection as $item) {
		if ($item->get('deleted') && !$item->get('parent')) {
			$item->remove();
		}
		else {
			$item = $item->toArray();
			$item['size'] = round($item['size'] / 1024, 2);
			$item['new'] = empty($item['parent']);
			$tpl = $item['type'] == 'image'
				? $tplImage
				: $tplFile;
			$files .= $Tickets->getChunk($tpl, $item);
		}
	}
	$data['files'] = $Tickets->getChunk($tplFiles, array(
		'files' => $files,
	));
	/** @var modMediaSource $source */
	if ($source = $modx->getObject('sources.modMediaSource', $source)) {
		$properties = $source->getPropertyList();
		$config = array(
			'size' => !empty($properties['maxUploadSize'])
				? $properties['maxUploadSize']
				: 3145728,
			'height' => !empty($properties['maxUploadHeight'])
				? $properties['maxUploadHeight']
				: 1080,
			'width' => !empty($properties['maxUploadWidth'])
				? $properties['maxUploadWidth']
				: 1920,
			'extensions' => !empty($properties['allowedFileTypes'])
				? $properties['allowedFileTypes']
				: 'jpg,jpeg,png,gif'
		);
		$modx->regClientStartupScript('<script type="text/javascript">TicketsConfig.source=' . $modx->toJSON($config) . ';</script>', true);
	}
	$modx->regClientScript($Tickets->config['jsUrl'] . 'web/lib/plupload/plupload.full.min.js');
	$modx->regClientScript($Tickets->config['jsUrl'] . 'web/files.js');

	$lang = $modx->getOption('cultureKey');
	if ($lang != 'en' && file_exists($Tickets->config['jsPath'] . 'web/lib/plupload/i18n/' . $lang . '.js')) {
		$modx->regClientScript($Tickets->config['jsUrl'] . 'web/lib/plupload/i18n/' . $lang . '.js');
	}
}

$output = $Tickets->getChunk($tplWrapper, $data);
$key = md5($modx->toJSON($Tickets->config));
$_SESSION['TicketForm'][$key] = $Tickets->config;
$output = str_ireplace('</form>', "\n<input type=\"hidden\" name=\"form_key\" value=\"{$key}\" class=\"disable-sisyphus\" />\n</form>", $output);

return $output;