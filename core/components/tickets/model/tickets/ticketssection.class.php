<?php
/**
 * The TicketsSection CRC for Tickets.
 *
 * @package tickets
 */

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class TicketsSection extends modResource {
	public $showInContextMenu = true;
	public $allowChildrenResources = false;

	function __construct(xPDO & $xpdo) {
		parent :: __construct($xpdo);

		$this->set('class_key','TicketsSection');
		$this->set('comments',0);
		$this->set('views',0);
		$this->set('votes',0);
		$this->set('tickets',0);
	}


	public static function getControllerPath(xPDO &$modx) {
		return $modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'controllers/section/';
	}


	public function getContextMenuText() {
		$this->xpdo->lexicon->load('tickets:default');
		return array(
			'text_create' => $this->xpdo->lexicon('tickets_section'),
			'text_create_here' => $this->xpdo->lexicon('tickets_section_create_here'),
		);
	}


	public function getResourceTypeName() {
		$this->xpdo->lexicon->load('tickets:default');
		return $this->xpdo->lexicon('tickets_section');
	}


	/**
	 * {@inheritDoc}
	 */
	public function get($k, $format = null, $formatTemplate= null) {
		$fields = array('comments','views','votes','tickets');

		if (is_array($k)) {
			$k = array_merge($k, $fields);
			$value = parent::get($k, $format, $formatTemplate);
		}
		else {
			switch ($k) {
				case 'comments': $value = $this->getCommentsCount(); break;
				case 'views': $value = $this->getViewsCount(); break;
				case 'votes': $value = $this->getVotesSum(); break;
				case 'tickets': $value = $this->getTicketsCount(); break;
				default: $value = parent::get($k, $format, $formatTemplate);
			}
		}

		return $value;
	}


	/**
	 * {@inheritDoc}
	 */
	public function toArray($keyPrefix= '', $rawValues= false, $excludeLazy= false, $includeRelated= false) {
		$array = array_merge(parent::toArray(), $this->getVirtualFields());

		return $array;
	}


	/**
	 * {@inheritDoc}
	 */
	public function process() {
		$this->xpdo->setPlaceholders($this->getVirtualFields());
		return parent::process();
	}


	public function getContent(array $options = array()) {
		$content = parent::getContent($options);

		return $content;
	}


	/**
	 * Clearing cache of this resource
	 * @param string $context Key of context for clearing
	 * @return void
	 */
	public function clearCache($context = null) {
		if (empty($context)) {
			$context = $this->context_key;
		}
		$this->_contextKey = $context;

		/** @var xPDOFileCache $cache */
		$cache = $this->xpdo->cacheManager->getCacheProvider($this->xpdo->getOption('cache_resource_key', null, 'resource'));
		$key = $this->getCacheKey();
		$cache->delete($key, array('deleteTop' => true));
		$cache->delete($key);
	}


	/*
	 * Shorthand for getting virtual Ticket fields
	 *
	 * @return array $array Array with virtual fields
	 * */
	function getVirtualFields() {
		$array = array(
			'comments' => $this->getCommentsCount()
			,'views' => $this->getViewsCount()
			,'votes' => $this->getVotesSum()
			,'tickets' => $this->getTicketsCount()
		);

		return $array;
	}


	/*
	 * Returns count of views of Tickets by users in this Section
	 *
	 * @return integer $count Total count of views
	 * */
	public function getViewsCount() {
		$q = $this->xpdo->newQuery('Ticket', array('parent' => $this->id, 'published' => 1, 'deleted' => 0));
		$q->leftJoin('TicketView','TicketView', "`TicketView`.`parent` = `Ticket`.`id`");
		$q->select('COUNT(`TicketView`.`parent`) as `views`');

		$count = 0;
		if ($q->prepare() && $q->stmt->execute()) {
			$count = (integer) $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		return $count;
	}


	/*
	 * Returns count of comments to Tickets in this Section
	 *
	 * @return integer $count Total count of comment
	 * */
	public function getCommentsCount() {
		$q = $this->xpdo->newQuery('Ticket', array('parent' => $this->id, 'published' => 1, 'deleted' => 0));
		$q->leftJoin('TicketThread','TicketThread', "`TicketThread`.`resource` = `Ticket`.`id`");
		$q->leftJoin('TicketComment','TicketComment', "`TicketThread`.`id` = `TicketComment`.`thread`");
		$q->select('COUNT(`TicketComment`.`id`) as `comments`');

		$count = 0;
		if ($q->prepare() && $q->stmt->execute()) {
			$count = (integer) $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		return $count;
	}


	/*
	 * Returns sum of votes to Tickets by users in this Section
	 *
	 * @return integer $count Total sum of votes
	 * */
	public function getVotesSum() {
		$q = $this->xpdo->newQuery('Ticket', array('parent' => $this->id, 'published' => 1, 'deleted' => 0));
		$q->leftJoin('TicketVote','TicketVote', "`TicketVote`.`parent` = `Ticket`.`id` AND `TicketVote`.`class` = 'Ticket'");
		$q->select('SUM(`TicketVote`.`value`) as `votes`');

		$sum = 0;
		if ($q->prepare() && $q->stmt->execute()) {
			$sum = (integer) $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		return $sum;
	}


	/*
	 * Returns count of tickets in this Section
	 *
	 * @return integer $count Total sum of votes
	 * */
	public function getTicketsCount() {
		return $this->xpdo->getCount('Ticket', array('parent' => $this->id, 'published' => 1, 'deleted' => 0));
	}
}



/**
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the TicketsSection type
 *
 * @package tickets
 */
class TicketsSectionCreateProcessor extends modResourceCreateProcessor {
	/** @var TicketsSection $object */
	public $object;
	public $classKey = 'TicketsSection';

	public function beforeSet() {
		$this->setProperties(array(
			'hide_children_in_tree' => 1
			,'isfolder' => 1
		));
		return parent::beforeSet();
	}

}



/**
 * Overrides the modResourceUpdateProcessor to provide custom processor functionality for the TicketsSection type
 *
 * @package tickets
 */
class TicketsSectionUpdateProcessor extends modResourceUpdateProcessor {
	/** @var TicketsSection $object */
	public $object;
	public $classKey = 'TicketsSection';

	public function beforeSet() {
		$this->setProperties(array(
			'hide_children_in_tree' => 1
			,'isfolder' => 1
		));
		return parent::beforeSet();
	}

}
