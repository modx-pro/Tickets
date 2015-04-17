<?php

class GetThreadProcessor extends modProcessor {
	public $classKey = 'TicketThread';
	public $languageTopics = array('tickets:default');
	/* @var TicketThread $object */
	private $object;
	private $comments;
	private $total = 0;


	/**
	 * @return bool
	 */
	public function initialize() {
		$thread = $this->getProperty('thread');
		if (!$this->object = $this->modx->getObject($this->classKey, array('name' => $thread))) {
			$this->object = $this->modx->newObject($this->classKey);
			$this->object->fromArray(array(
				'name' => $thread,
				'createdby' => $this->modx->user->get('id'),
				'createdon' => date('Y-m-d H:i:s'),
				'resource' => $this->modx->resource->get('id'),
			));
			$this->object->save();
		}
		else {
			if ($this->object->deleted == 1) {
				$this->modx->error->message = $this->modx->lexicon('ticket_thread_err_deleted');
				return false;
			}
		}
		return true;
	}


	/**
	 * @return string
	 */
	public function process() {
		$this->getComments();
		$this->checkCommentLast();
		$this->buildTree();

		return $this->cleanup();
	}


	/**
	 *
	 */
	public function getComments() {
		$res = array();
		$result = null;
		$q = $this->modx->newQuery('TicketComment');
		$q->select($this->modx->getSelectColumns('TicketComment', 'TicketComment'));
		$q->select($this->modx->getSelectColumns('modUserProfile', 'modUserProfile', '', array('id'), true));
		$q->select('`TicketThread`.`resource`');
		$q->select('`modUser`.`username`');
		$q->leftJoin('modUser', 'modUser', '`TicketComment`.`createdby` = `modUser`.`id`');
		$q->leftJoin('modUserProfile', 'modUserProfile', '`TicketComment`.`createdby` = `modUserProfile`.`internalKey`');
		$q->leftJoin('TicketThread', 'TicketThread', '`TicketThread`.`id` = `TicketComment`.`thread`');
		$q->where(array('thread' => $this->object->id));
		$q->sortby('id', 'ASC');
		if ($q->prepare() && $q->stmt->execute()) {
			while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
				$res[$row['id']] = $row;
			}
			$this->total = count($res);
			$this->comments = $res;
		}
	}


	/**
	 *
	 */
	public function checkCommentLast() {
		if (!$this->object->get('comment_last') && $key = key(array_slice($this->comments, -1, 1, true))) {
			$comment = $this->comments[$key];
			$this->object->fromArray(array(
				'comment_last' => $key,
				'comment_time' => $comment['createdon'],
			));
			$this->object->save();
		}
	}


	/**
	 *
	 */
	public function buildTree() {
		$data = $this->comments;
		$this->comments = array();
		foreach ($data as $id => &$row) {
			if (empty($row['parent'])) {
				$this->comments[$id] = &$row;
			}
			else {
				$data[$row['parent']]['children'][$id] = &$row;
			}
		}
	}


	/**
	 * @return string
	 */
	public function cleanup() {
		return $this->outputArray($this->comments, $this->total);
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return $this->languageTopics;
	}

}

return 'GetThreadProcessor';