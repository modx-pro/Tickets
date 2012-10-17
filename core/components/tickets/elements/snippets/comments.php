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
	,'postAction' => 'addComment'
	,'previewAction' => 'previewComment'
	,'replyResourceId' => 1
);
$scriptProperties = array_merge($scriptProperties, $defaultOptions);
//$processorsPath = $modx->getOption('quip.core_path',null,$modx->getOption('core_path').'components/quip/').'processors/';

if (!empty($_POST)) {

	$quip = $modx->getService('quip','Comments',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
	if (!($quip instanceof Comments)) return '';
	$controller = $quip->loadController('ThreadReply');
	$output = $controller->run($scriptProperties);


	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
		var_dump($output);die;
		die;
	}
	else {
		var_dump($output);die;
		$modx->sendRedirect($modx->makeUrl($modx->resource->id,'','','full'));
	}
}

if ($scriptProperties['useCss']) {
	$scriptProperties['useCss'] = 0;
	$modx->regClientCSS($modx->getOption('tickets.assets_url',null,$modx->getOption('assets_url').'components/tickets/').'css/web/comments.css');
}
if ($scriptProperties['useJs']) {
	$modx->regClientScript($modx->getOption('tickets.assets_url',null,$modx->getOption('assets_url').'components/tickets/').'js/web/comments.js');
}

$output = $modx->runSnippet('Quip', $scriptProperties);

$output .= $modx->runSnippet('QuipReply', $scriptProperties);

return $output;