<?php
/**
 * Get a list of Tickets
 *
 * @package tickets
 * @subpackage processors
 */
class TicketGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'Ticket';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'DESC';
	public $renderers = '';
	/** @var modAction $editAction */
	public $editAction;

	public function initialize() {
		$this->editAction = $this->modx->getObject('modAction',array(
			'namespace' => 'core',
			'controller' => 'resource/update',
		));
		return parent::initialize();
	}

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		if ($query = $this->getProperty('query',null)) {
			$queryWhere = array(
				'pagetitle:LIKE' => '%'.$query.'%'
				,'OR:description:LIKE' => '%'.$query.'%'
				,'OR:introtext:LIKE' => '%'.$query.'%'
			);
			$c->where($queryWhere);
		}
		$c->leftJoin('modUser','CreatedBy');
		$c->where(array(
			'class_key' => 'Ticket'
			,'parent' => $this->getProperty('parent')
		));
		return $c;
	}

	public function prepareQueryAfterCount(xPDOQuery $c) {
		$c->select($this->modx->getSelectColumns('Ticket','Ticket'));
		$c->select(array(
			'createdby_username' => 'CreatedBy.username',
		));

		$commentsQuery = $this->modx->newQuery('TicketComment');
		$commentsQuery->innerJoin('TicketThread','Thread');
		$commentsQuery->where(array(
			'Thread.resource = Ticket.id',
		));
		$commentsQuery->select(array(
			'COUNT('.$this->modx->getSelectColumns('TicketComment','TicketComment','',array('id')).')',
		));
		$commentsQuery->construct();
		$c->select(array(
			'('.$commentsQuery->toSQL().') AS '.$this->modx->escape('comments'),
		));

		return $c;
	}

	public function prepareRow(xPDOObject $object) {
		$resourceArray = parent::prepareRow($object);

		if (!empty($resourceArray['publishedon'])) {
			$resourceArray['publishedon_date'] = strftime('%b %d',strtotime($resourceArray['publishedon']));
			$resourceArray['publishedon_time'] = strftime('%H:%M %p',strtotime($resourceArray['publishedon']));
			$resourceArray['publishedon'] = strftime('%b %d, %Y %H:%I %p',strtotime($resourceArray['publishedon']));
		}

		$resourceArray['action_edit'] = '?a='.$this->editAction->get('id').'&action=post/update&id='.$resourceArray['id'];
		if (!array_key_exists('comments',$resourceArray)) $resourceArray['comments'] = 0;

		$this->modx->getContext($resourceArray['context_key']);
		$resourceArray['preview_url'] = $this->modx->makeUrl($resourceArray['id'],$resourceArray['context_key']);

		$resourceArray['content'] = '<br/>'.strip_tags($this->ellipsis($object->get('content'),500));

		$resourceArray['actions'] = array();
		$resourceArray['actions'][] = array(
			'className' => 'edit',
			'text' => $this->modx->lexicon('edit'),
		);
		$resourceArray['actions'][] = array(
			'className' => 'view',
			'text' => $this->modx->lexicon('view'),
		);
		$resourceArray['actions'][] = array(
			'className' => 'duplicate',
			'text' => $this->modx->lexicon('duplicate'),
		);
		if (!empty($resourceArray['deleted'])) {
			$resourceArray['actions'][] = array(
				'className' => 'undelete green',
				'text' => $this->modx->lexicon('undelete'),
			);
		} else {
			$resourceArray['actions'][] = array(
				'className' => 'delete',
				'text' => $this->modx->lexicon('delete'),
			);
		}
		if (!empty($resourceArray['published'])) {
			$resourceArray['actions'][] = array(
				'className' => 'unpublish',
				'text' => $this->modx->lexicon('unpublish'),
			);
		} else {
			$resourceArray['actions'][] = array(
				'className' => 'publish orange',
				'text' => $this->modx->lexicon('publish'),
			);
		}
		return $resourceArray;
	}

	public function ellipsis($string,$length = 500) {
		if (strlen($string) > $length) {
			$string = substr($string,0,$length).'...';
		}
		return $string;
	}
}

return 'TicketGetListProcessor';