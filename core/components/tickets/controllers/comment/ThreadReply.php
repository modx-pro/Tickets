<?php
require MODX_CORE_PATH . 'components/quip/controllers/web/ThreadReply.php';

class CommentThreadReplyController extends QuipThreadReplyController {

	/**
	 * {@inheritDoc}
	 * @return mixed
	 */
	public function process() {
		if (!$this->getThread()) return '';
		$this->checkPermissions();

		/* setup default placeholders */
		$p = $this->modx->request->getParameters();
		unset($p['reported'],$p['quip_approved']);
		$this->setPlaceholder('url',$this->modx->makeUrl($this->modx->resource->get('id'),'',$p));

		$this->setPlaceholder('parent',$this->parentThread);
		$this->setPlaceholder('thread',$this->thread->get('name'));
		$this->setPlaceholder('idprefix',$this->thread->get('idprefix'));

		/* handle POST */
		$this->hasPreview = false;
		if (!empty($_POST)) {
			return $this->handlePost();
		}

		/* display moderated success message */
		//$this->checkForModeration();
		//$this->checkForUnSubscribe();

		/* if using recaptcha, load recaptcha html if user is not logged in */
		//$this->loadReCaptcha();

		/* build reply form */
		$isOpen = $this->isOpen();
		if ($this->hasAuth && $isOpen) {
			$replyForm = $this->getReplyForm();
		} else if (!$isOpen) {
			$replyForm = $this->modx->lexicon('quip.thread_autoclosed');
		} else {
			$replyForm = $this->quip->getChunk($this->getProperty('tplLoginToComment','quipLoginToComment'),$this->getPlaceholders());
		}

		/* output or set to placeholder */
		$toPlaceholder = $this->getProperty('toPlaceholder',false);
		if ($toPlaceholder) {
			$this->modx->setPlaceholder($toPlaceholder,$replyForm);
			return '';
		}
		return $replyForm;
	}

	/**
	 * {@inheritDoc}
	 * @return array
	 */
	public function handlePost() {
		$fields = array();
		$errors = array();
		$text = '';
		foreach ($_POST as $k => $v) {
			//$fields[$k] = str_replace(array('[',']','`'),array('&#91;','&#93;','&#96;'),$v);
			$fields[$k] = $v;
		}

		$fields['name'] = $this->modx->user->Profile->fullname;
		$fields['email'] = $this->modx->user->Profile->email;
		$fields['website'] = $this->modx->user->Profile->website;
		$fields['website'] = '';

		/* verify a message was posted */
		if (empty($fields['comment'])) {
			$errors = $this->modx->lexicon('quip.message_err_ns');
		}
		/* handle submit */
		if (!empty($_POST['action']) && $_POST['action'] == $this->config['postAction'] && empty($errors)) {
			ini_set('display_errors', 0);
			$comment = $this->runProcessor('web/comment/create',$fields);
			ini_set('display_errors', 1);

			if (is_object($comment) && $comment instanceof quipComment) {
				if ($this->hasAuth) {$comment->hasAuth = true;}
				$text = $this->modx->getChunk($this->config['tplComment'], $comment->prepare($this->getProperties(),1));
				// Delete cache for latest comments
				$this->modx->cacheManager->delete('tickets/latest.comments');
			} else if (is_array($comment)) {
				$errors = array_merge($errors,$comment);
			}
		}
		/* handle preview */
		if (!empty($_POST['action']) && $_POST['action'] == $this->config['previewAction'] && empty($errors)) {
			$text = $this->quip->cleanse($fields['comment']);
		}

		return array(
			'errors' => $errors
			,'text' => $text
		);

	}


}

return 'CommentThreadReplyController';