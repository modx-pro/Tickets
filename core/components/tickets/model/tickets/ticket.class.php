<?php
/**
 * The Ticket CRC for Tickets.
 *
 * @package tickets
 */

require_once MODX_CORE_PATH.'components/tickets/processors/mgr/ticket/create.class.php';
require_once MODX_CORE_PATH.'components/tickets/processors/mgr/ticket/update.class.php';

class Ticket extends modResource {
	public $showInContextMenu = false;
	private $paramsLoaded = 0;
	private $disableJevix = 0;
	private $processTags = 0;

	function __construct(xPDO & $xpdo) {
		parent :: __construct($xpdo);

		$this->set('class_key','Ticket');
		$this->set('comments',0);
		$this->set('views',0);
		$this->set('votes',0);
	}


	/* Loads ticket params
	 * @return void
	 * */
	private function loadParams() {
		$properties = parent::get('properties');
		$this->disableJevix = !empty($properties['disable_jevix']) ? 1 : 0;
		$this->processTags = !empty($properties['process_tags']) || $this->xpdo->context->key == 'mgr' ? 1 : 0;
		$this->paramsLoaded = true;
	}

	/**
	 * {@inheritDoc}
	 * @return mixed
	 */
	public static function getControllerPath(xPDO &$modx) {
		return $modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'controllers/ticket/';
	}

	/**
	 * {@inheritDoc}
	 * @return mixed
	 */
	public function getContextMenuText() {
		$this->xpdo->lexicon->load('tickets:default');
		return array(
			'text_create' => $this->xpdo->lexicon('tickets'),
			'text_create_here' => $this->xpdo->lexicon('ticket_create_here'),
		);
	}

	/**
	 * {@inheritDoc}
	 * @return mixed
	 */
	public function getResourceTypeName() {
		$this->xpdo->lexicon->load('tickets:default');
		return $this->xpdo->lexicon('ticket');
	}

	/**
	 * {@inheritDoc}
	 */
	public function get($k, $format = null, $formatTemplate= null) {
		$fields = array('comments','views','votes');

		if (is_array($k)) {
			$value = parent::get($k, $format, $formatTemplate);
		}
		else {
			switch ($k) {
				case 'comments': $value = $this->getCommentsCount(); break;
				case 'views': $value = $this->getViewsCount(); break;
				case 'votes': $value = $this->getVotesSum(); break;
				default: $value = parent::get($k, $format, $formatTemplate);
			}

			if (!$this->paramsLoaded) {$this->loadParams();}

			if (!$this->processTags && is_string($k) && !in_array($k, $fields) && @$this->_fieldMeta[$k]['phptype'] == 'string') {
				$value = str_replace(array('[',']','`'),array('&#91;','&#93;','&#96;'), $value);
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
		if ($this->privateweb && !$this->xpdo->hasPermission('ticket_view_private') && $id = $this->getOption('tickets.private_ticket_page')) {
			return $this->xpdo->sendForward($id);
		}
		else {
			$this->logView();
			$this->xpdo->setPlaceholders($this->getVirtualFields());
			return parent::process();
		}
	}


	/**
	 * {@inheritDoc}
	 */
	public function getContent(array $options = array()) {
		$content = parent::get('content');

		if (!$this->paramsLoaded) {$this->loadParams();}

		if (!$this->disableJevix) {
			$content = $this->Jevix($content, false);
		}
		if (!$this->processTags) {
			$content = str_replace(array('[',']','`'),array('&#91;','&#93;','&#96;'), $content);
		}
		$content = preg_replace('/<cut(.*?)>/i', '<a name="cut"></a>', $content);

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


	/**
	 * Html filter and typograf
	 * @var mixed Text for processing
	 * @returns mixed Filtered text
	 * */
	function Jevix($text, $replaceTags = true) {
		if (!in_array('Tickets', get_declared_classes())) {
			require 'tickets.class.php';
		}
		if (!isset($this->xpdo->Tickets) || !is_object($this->xpdo->Tickets) || !($this->xpdo->Tickets instanceof Tickets)) {
			$this->xpdo->Tickets = new Tickets($this->xpdo, array());
		}
		return $this->xpdo->Tickets->Jevix($text, 'Ticket', $replaceTags);
	}


	/**
	 * Generate intro text from content buy cutting text before tag <cut/>
	 * @param string $content Any text for processing, with tag <cut/>
	 * @return mixed $introtext
	 */
	function getIntroText($content = null) {
		if (empty($content)) {
			$content = parent::get('content');
		}
		$content = preg_replace('/<cut(.*?)>/i', '<cut/>', $content);

		if (!preg_match('/<cut\/>/', $content)) {
			$introtext = '';
		}
		else {
			$tmp = explode("<cut/>", $content);
			$introtext = reset($tmp);
			$introtext = $this->Jevix($introtext);
		}
		return $introtext;
	}


	/**
	 * {@inheritDoc}
	 */
	public function & getMany($alias, $criteria= null, $cacheFlag= true) {
		if ($alias == 'Attachments' || $alias == 'Votes') {
			$criteria = array('class' => $this->class_key);
		}
		return parent::getMany($alias, $criteria, $cacheFlag);
	}


	/**
	 * {@inheritDoc}
	 */
	public function addMany(& $obj, $alias= '') {
		$added= false;
		if (is_array($obj)) {
			foreach ($obj as $o) {
				if (is_object($o)) {
					$o->set('class', $this->class_key);
					$added = parent::addMany($obj, $alias);
				}
			}
			return $added;
		}
		else {
			return parent::addMany($obj, $alias);
		}
	}


	/*
	 * Logs user views of a Ticket
	 *
	 * @return void
	 * */
	public function logView() {
		$id = $this->id;

		/* @var PDOStatement $stmt */
		if ($this->xpdo->user->isAuthenticated() && $uid = $this->xpdo->user->id) {
			/*
			if (!$res = $this->xpdo->getObject('TicketView', array('parent' => $this->id, 'uid' => $uid))) {
				$res = $this->xpdo->newObject('TicketView');
				$res->set('parent', $this->id);
				$res->set('uid', $uid);
			}
			$res->set('timestamp', time());
			$res->save();
			*/
			$table = $this->xpdo->getTableName('TicketView');
			$timestamp = date('Y-m-d H:i:s');
			$sql = "INSERT INTO {$table} (`uid`,`parent`,`timestamp`) VALUES ({$uid},{$id},'{$timestamp}') ON DUPLICATE KEY UPDATE `timestamp` = '{$timestamp}'";
			if ($stmt = $this->xpdo->prepare($sql)) {$stmt->execute();}
		}
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
		);

		return $array;
	}


	/*
	 * Returns count of views of Ticket by users
	 *
	 * @return integer $count Total count of views
	 * */
	public function getViewsCount() {
		$q = $this->xpdo->newQuery('Ticket', $this->id);
		$q->leftJoin('TicketView','TicketView', "`TicketView`.`parent` = `Ticket`.`id`");
		$q->select('COUNT(`TicketView`.`parent`) as `views`');

		$count = 0;
		if ($q->prepare() && $q->stmt->execute()) {
			$count = (integer) $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		return $count;
	}


	/*
	 * Returns count of comments to Ticket
	 *
	 * @return integer $count Total count of comment
	 * */
	public function getCommentsCount() {
		$q = $this->xpdo->newQuery('Ticket', $this->id);
		$q->leftJoin('TicketThread','TicketThread', "`TicketThread`.`name` = 'resource-{$this->id}'");
		$q->leftJoin('TicketComment','TicketComment', "`TicketThread`.`id` = `TicketComment`.`thread`");
		$q->select('COUNT(`TicketComment`.`id`) as `comments`');

		$count = 0;
		if ($q->prepare() && $q->stmt->execute()) {
			$count = (integer) $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		return $count;
	}


	/*
	 * Returns sum of votes to Ticket by users
	 *
	 * @return integer $count Total sum of votes
	 * */
	public function getVotesSum() {
		$q = $this->xpdo->newQuery('Ticket', $this->id);
		$q->leftJoin('TicketVote','TicketVote', "`TicketVote`.`parent` = `Ticket`.`id` AND `TicketVote`.`class` = 'Ticket'");
		$q->select('SUM(`TicketVote`.`value`) as `votes`');

		$sum = 0;
		if ($q->prepare() && $q->stmt->execute()) {
			$sum = (integer) $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		return $sum;
	}
}