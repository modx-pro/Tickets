<?php

class TicketCommentGetProcessor extends modObjectGetProcessor {
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $objectType = 'comment';

	public function cleanup() {
		$comment = $this->object->toArray();
		$comment['createdon'] = $this->formatDate($comment['createdon']);
		$comment['editedon'] = $this->formatDate($comment['editedon']);
		$comment['deletedon'] = $this->formatDate($comment['deletedon']);
		$comment['text'] = !empty($comment['raw']) ? html_entity_decode($comment['raw']) : html_entity_decode($comment['text']);

		return $this->success('',$comment);
	}

	public function formatDate($date = '') {
		if (empty($date)) {
			return $this->modx->lexicon('no');
		}
		return strftime('%d %b %Y %H:%M', strtotime($date));
	}
}

return 'TicketCommentGetProcessor';