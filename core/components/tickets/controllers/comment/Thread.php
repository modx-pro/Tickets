<?php
require MODX_CORE_PATH . 'components/quip/controllers/web/Thread.php';

class CommentThreadController extends QuipThreadController {
	public function process() {
		$this->setPlaceholders(array(
			'comment' => '',
			'error' => '',
		));
		$time = microtime(true);
		if (!$this->getThread()) return '';
		$this->modx->log(modX::LOG_LEVEL_ERROR, 'getThread: '.(microtime(true) - $time));

		$this->setThreadCallParameters();
		$this->sync();
		$this->checkPermissions();
		$this->handleActions();
		$this->loadCss();

		$this->checkForUnsubscription();
		/* set idprefix */
		$this->setPlaceholder('idprefix',$this->thread->get('idprefix'));
		$this->preparePaginationIds();

		$cacheKey = 'tickets/thread/'.$this->thread->get('resource');
		if (!$comments = $this->modx->cacheManager->get($cacheKey)) {
			$this->getComments();
			$comments = $this->prepareComments();
			$this->modx->cacheManager->set($cacheKey, $comments);
		}
		$this->setPlaceholder('total',count($comments));

		$content = $this->render($comments);

		$this->buildPagination();
		$content = $this->wrap($content);

		return $this->output($content);
	}
}

return 'CommentThreadController';