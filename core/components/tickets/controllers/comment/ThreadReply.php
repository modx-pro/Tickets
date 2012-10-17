<?php

//require MODX_CORE_PATH . 'components/quip/model/quip/request/quipcontroller.class.php';
require MODX_CORE_PATH . 'components/quip/controllers/web/ThreadReply.php';

class CommentThreadReplyController extends QuipThreadReplyController {

	/**
	 * {@inheritDoc}
	 * @return mixed
	 */
	public function process() {
		if (!$this->getThread()) return '';
		$this->checkPermissions();

		/* handle POST */
		$this->hasPreview = false;
		if (!empty($_POST)) {
			return $this->handlePost();
		}
	}

	/**
	 * {@inheritDoc}
	 * @return mixed
	 */
	public function handlePost() {
		$fields = array();
		$errors = array();
		foreach ($_POST as $k => $v) {
			$fields[$k] = str_replace(array('[',']'),array('&#91;','&#93;'),$v);
		}

		$fields['name'] = $this->modx->user->Profile->fullname;
		$fields['email'] = $this->modx->user->Profile->email;
		$fields['website'] = $this->modx->user->Profile->website;

		/* verify a message was posted */
		if (empty($fields['comment'])) {
			$errors['comment'] = $this->modx->lexicon('quip.message_err_ns');
		}

		if (!empty($_POST['addComment']) && empty($errors)) {
			$comment = $this->runProcessor('web/comment/create',$fields);

			if (is_object($comment) && $comment instanceof quipComment) {
				if ($this->hasAuth) {$comment->hasAuth = true;}
				return $this->modx->getChunk($this->config['tplComment'], $comment->prepare($this->getProperties(),1));
			} else if (is_array($comment)) {
				$errors = array_merge($errors,$comment);
				return $errors;
			}
		}
		/* handle preview */
		else if (!empty($_POST['previewComment']) && empty($errors)) {
			$errors = $this->runProcessor('web/comment/preview',$fields);
		}
		if (!empty($errors)) {
			return $errors;
		}
		else {
			return $this->getPlaceholder('preview');

		}

	}


}

return 'CommentThreadReplyController';