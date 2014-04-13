<?php
class TicketCommentsGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'TicketComment';
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $defaultSortField = 'createdon';
	public $defaultSortDirection = 'DESC';
	private $resources = array();

	
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		/* Get all comments by section */
		if ($section = (integer) $this->getProperty('section')) {
			if ($section = $this->modx->getObject('modResource', $section)) {
				$parents = $this->modx->getChildIds($section->get('id'), 1, array('context' => $section->get('context_key')));
				if (empty($parents)) {$parents = array('0');}
				$c->where(array('TicketThread.resource:IN' => $parents));
			}
		}
		/* OR get all comments by threads list */
		elseif ($threads = $this->getProperty('threads')) {
			if (!is_array($threads)) {$threads = explode(',', $threads);}
			if (!empty($threads)) {
				$c->where(array('TicketComment.thread:IN' => $threads));
			}
		}
		/* OR get all comments by tickets list */
		elseif ($parents = $this->getProperty('parents')) {
			if (!is_array($parents)) {$parents = explode(',',$parents);}
			if (!empty($parents)) {
				$c->where(array('TicketThread.resource:IN' => $parents));
			}
		}
		/* OR get all comments */
		else {
			$c->where(array('TicketThread.resource:!=' => 0));
		}

		if ($query = $this->getProperty('query',null)) {
			$query = trim($query);
			if (is_numeric($query)) {
				$c->where(array(
					'TicketComment.id:=' => $query
					,'OR:TicketComment.parent:=' => $query
				));

			}
			else {
				$c->where(array(
					'TicketComment.text:LIKE' => '%'.$query.'%'
					,'OR:TicketComment.name:LIKE' => '%'.$query.'%'
					,'OR:TicketComment.email:LIKE' => '%'.$query.'%'
				));
			}
		}

		$c->limit($this->getProperty('limit',10),$this->getProperty('start',0));
		if (!$this->getProperty('sort')) {
			$c->sortby('TicketComment.createdon', 'DESC');
		}


		$c->leftJoin('TicketThread','TicketThread','`TicketThread`.`id` = `TicketComment`.`thread`');
		$c->leftJoin('modUserProfile', 'modUserProfile', '`TicketComment`.`createdby` = `modUserProfile`.`internalKey`');
		//$c->select('`TicketThread`.`resource`, `modResource`.`pagetitle`,`modResource`.`parent` AS `section`');
		$c->select($this->modx->getSelectColumns('TicketComment','TicketComment'));
		$c->select('`TicketThread`.`resource`');
		$c->select('`modUserProfile`.`fullname`');
		//$c->leftJoin('modResource','modResource', '`TicketThread`.`resource` = `modResource`.`id`');

		return $c;
	}


	public function prepareRow(xPDOObject $object) {
		$comment = $object->toArray();
		$resources = & $this->resources;
		if (!array_key_exists($comment['resource'], $resources)) {
			if ($resource = $this->modx->getObject('modResource', $comment['resource'])) {
				$resources[$comment['resource']] = array(
					'resource_url' => $this->modx->makeUrl($comment['resource'],'','','full')
					,'pagetitle' => $resource->get('pagetitle')
				);
			}
		}

		if (!empty($resources[$comment['resource']])) {
			$comment = array_merge($comment, $resources[$comment['resource']]);
			$comment['comment_url'] = $comment['resource_url'].'#comment-'.$comment['id'];
		}

		$comment['text'] = strip_tags(html_entity_decode($comment['text']));
		$comment['createdon'] = $this->formatDate($comment['createdon']);
		$comment['editedon'] = $this->formatDate($comment['editedon']);
		$comment['deletedon'] = $this->formatDate($comment['deletedon']);
		if (!empty($comment['fullname'])) {
			$comment['name'] = $comment['fullname'];
		}

		return $comment;
	}


	public function formatDate($date = '') {
		if (empty($date) || $date == '0000-00-00 00:00:00') {
			return $this->modx->lexicon('no');
		}
		return strftime('%d %b %Y %H:%M', strtotime($date));
	}

}
return 'TicketCommentsGetListProcessor';