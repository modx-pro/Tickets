<?php
class TicketCommentsGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $defaultSortField = 'createdon';
	public $defaultSortDirection = 'DESC';
	public $objectType = 'comment';
	private $resources = array();

	public function prepareQueryBeforeCount(xPDOQuery $c) {

		/* Get all comments by section */
		if ($section = (integer) $this->getProperty('section')) {
			if ($section = $this->modx->getObject('modResource', $section)) {
				$parents = $this->modx->getChildIds($section->get('id'),1,array('context' => $section->get('context_key')));
				$c->where(array('TicketThread.resource:IN' => $parents));
			}
		}
		/* OR get all comments by threads list */
		else if ($threads = $this->getProperty('threads')) {
			if (!is_array($threads)) {$threads = explode(',',$threads);}
			$c->where(array('TicketComment.thread:IN' => $threads));
		}
		/* OR get all comments by tickets list */
		else if ($parents = $this->getProperty('parents')) {
			if (!is_array($parents)) {$parents = explode(',',$parents);}
			$c->where(array('TicketThread.resource:IN' => $parents));
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


		$c->select($this->modx->getSelectColumns('TicketComment','TicketComment'));
		//$c->select('`TicketThread`.`resource`, `modResource`.`pagetitle`,`modResource`.`parent` AS `section`');
		$c->select('`TicketThread`.`resource`');
		$c->leftJoin('TicketThread','TicketThread','`TicketThread`.`id` = `TicketComment`.`thread`');
		//$c->leftJoin('modResource','modResource', '`TicketThread`.`resource` = `modResource`.`id`');

		return $c;
	}

	public function prepareRow(xPDOObject $object) {
		$comment = $object->toArray();
		/*
		if ($comment['deleted'] == 1) {
			$comment['text'] = $this->modx->lexicon('ticket_comment_deleted_text');
		}
		*/
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