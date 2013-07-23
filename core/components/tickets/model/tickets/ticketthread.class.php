<?php
class TicketThread extends xPDOSimpleObject {

	function __construct(xPDO & $xpdo) {
		parent :: __construct($xpdo);

		$this->set('comments',0);
	}


	public function getCommentsCount() {
		$q = $this->xpdo->newQuery('TicketComment', array('thread' => $this->get('id')));

		return $this->xpdo->getCount('TicketComment', $q);
	}


	public function updateLastComment() {
		$q = $this->xpdo->newQuery('TicketComment', array('thread' => $this->get('id')));
		$q->sortby('createdon','DESC');
		$q->limit(1);
		$q->select('id as comment_last,createdon as comment_time');
		if ($q->prepare() && $q->stmt->execute()) {
			$comment = $q->stmt->fetch(PDO::FETCH_ASSOC);
			if (empty($comment)) {
				$comment = array('comment_last' => 0, 'comment_time' => 0);
			}
			$this->fromArray($comment);
			$this->save();
		}
	}


	public function get($k, $format = null, $formatTemplate= null) {
		if ($k == 'comments') {
			return $this->getCommentsCount();
		}
		else {
			return parent::get($k, $format, $formatTemplate);
		}
	}


	public function toArray($keyPrefix= '', $rawValues= false, $excludeLazy= false, $includeRelated= false) {
		$array = parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated);
		$array['comments'] = $this->getCommentsCount();

		return $array;
	}


	public function buildTree($comments = array(), $depth = 0) {
		if (!$this->get('comment_last') && $key = key(array_slice($comments, -1, 1, true))) {
			$comment = $comments[$key];
			$this->fromArray(array(
				'comment_last' => $key
				,'comment_time' => $comment['createdon']
			));
			$this->save();
		}
		// Thank to Agel_Nash for the idea about how to limit comments by depth
		$tree = array();
		foreach ($comments as $id => &$row) {
			$row['has_children'] = $row['level'] = 0;

			if (empty($row['parent'])) {
				$tree[$id] = &$row;
			}
			else {
				$parent = $row['parent'];
				$level = $comments[$parent]['level'];
				$comments[$parent]['has_children'] = 1;

				if (!empty($depth) && $level >= $depth) {
					$parent = $comments[$parent]['new_parent'];
					$row['new_parent'] = $parent;
					$row['level'] = $level;
				}
				else {
					$row['level'] = $level + 1;
				}

				$comments[$parent]['children'][$id] = &$row;
			}
		}
		return $tree;
	}


	public function Subscribe($uid = 0) {
		if (!$uid) {$uid = $this->xpdo->user->id;}

		$subscribers = $this->get('subscribers');
		if (empty($subscribers) || !is_array($subscribers)) {
			$subscribers = array();
		}

		$found = array_search($uid, $subscribers);
		if ($found !== false) {
			unset($subscribers[$found]);
		}
		else {
			$subscribers[] = $uid;
		}
		$this->set('subscribers', $subscribers);
		$this->save();

		return !$found;
	}


	public function isSubscribed($uid = 0) {
		if (!$uid) {$uid = $this->xpdo->user->id;}

		$subscribers = $this->get('subscribers');
		if (empty($subscribers) || !is_array($subscribers)) {
			$subscribers = array();
		}

		return in_array($uid, $subscribers);
	}
}