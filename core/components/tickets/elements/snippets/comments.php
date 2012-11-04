<?php
$defaultOptions = array(
	'thread' => 'ticket_'.$modx->resource->id
	,'threaded' => 1
	,'useMargins' => 0
	,'allowRemove' => 0
	,'idPrefix' => 'comment-'
	,'limit' => 0
	,'start' => 0
	,'requireAuth' => 1
	,'disableRecaptchaWhenLoggedIn' => 1
	,'postAction' => 'sendComment'
	,'previewAction' => 'previewComment'
	,'replyResourceId' => 1
	,'autoConvertLinks' => 0
	,'allowReportAsSpam' => 0
);
$scriptProperties = array_merge($scriptProperties, $defaultOptions);

if ((empty($action) || $action == 'getComments') && !empty($_REQUEST['action'])) {$action = $_REQUEST['action'];}
if (empty($action)) {$action = 'getComments';}

if ($action == 'previewComment' || $action == 'sendComment') {
	$quip = $modx->getService('quip','Comments',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
	if (!($quip instanceof Comments)) return '';

	$controller = $quip->loadController('ThreadReply');
	$output = $controller->run($scriptProperties);

	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
		if (is_array($output)) {$output = json_encode($output);}
		echo $output;
		die;
	}
	else {
		$modx->sendRedirect($modx->makeUrl($modx->resource->id,'','','full'));
	}
}
else if ($action == 'getComments') {
	$quip = $modx->getService('quip','Comments',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
	if (!($quip instanceof Comments)) return '';

	$assets_url = $modx->getOption('tickets.assets_url',null,$modx->getOption('assets_url').'components/tickets/');

	if ($scriptProperties['useCss']) {
		$scriptProperties['useCss'] = 0;
		$modx->regClientCSS($assets_url .'css/web/comments.css');
	}
	if ($scriptProperties['useJs']) {
		$modx->regClientScript($assets_url.'js/web/comments.js');
	}

	$enable_editor = $modx->getOption('tickets.enable_editor');
	$htmlBlock = 'enable_editor:'.$enable_editor.'';
	if ($enable_editor) {
		$modx->regClientStartupScript($assets_url.'js/web/editor/jquery.markitup.js');
		$modx->regClientCSS($assets_url.'js/web/editor/editor.css');
		$htmlBlock .= ',editor:{comment:'.$modx->getOption('tickets.editor_config.comment').'}';
	}
	$modx->regClientStartupHTMLBlock('<script type="text/javascript">
		Comments = new Object();
		Comments.config = {'.$htmlBlock.'};
	</script>');

	$quip->initialize($modx->context->get('key'));

	$controller = $quip->loadController('Thread');
	$output = $controller->run($scriptProperties);

	$controller = $quip->loadController('ThreadReply');
	$output .= $controller->run($scriptProperties);

	return $output;
}

