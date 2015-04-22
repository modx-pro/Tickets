<?php

class TicketCommentGetProcessor extends modObjectGetProcessor {
	public $objectType = 'TicketComment';
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');


	/**
	 * @return array|string
	 */
	public function cleanup() {
		$comment = $this->object->toArray();
		$comment['createdon'] = $this->formatDate($comment['createdon']);
		$comment['editedon'] = $this->formatDate($comment['editedon']);
		$comment['deletedon'] = $this->formatDate($comment['deletedon']);
		$comment['text'] = !empty($comment['raw'])
			? html_entity_decode($comment['raw'])
			: html_entity_decode($comment['text']);

		return $this->success('', $comment);
	}

	/**
	 * @param string $date
	 *
	 * @return null|string
	 */
	public function formatDate($date = '') {
		if (empty($date) || $date == '0000-00-00 00:00:00') {
			return $this->modx->lexicon('no');
		}
		return strftime($this->modx->getOption('tickets.date_format'), strtotime($date));
	}

}

return 'TicketCommentGetProcessor';