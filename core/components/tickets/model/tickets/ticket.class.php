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
	private $_properties = array();


	function __construct(xPDO & $xpdo) {
		parent :: __construct($xpdo);

		$this->set('class_key','Ticket');
		$this->set('comments',0);
		$this->set('views',0);
		$this->set('votes',0);
	}


	/**
	 * Loads ticket properties
	 */
	private function _loadProperties() {
		$properties = array();

		$q = $this->xpdo->newQuery('Ticket', $this->id);
		$q->select('properties');
		$tstart = microtime(true);
		if ($q->prepare() && $q->stmt->execute()) {
			$this->xpdo->startTime += microtime(true) - $tstart;
			$this->xpdo->executedQueries ++;
			$properties = $this->xpdo->fromJSON($q->stmt->fetch(PDO::FETCH_COLUMN));
			if (!is_array($properties)) {
				$properties = array();
			}
		}

		$properties['disable_jevix'] = !empty($properties['disable_jevix']);
		$properties['process_tags'] = !empty($properties['process_tags']) || $this->xpdo->context->key == 'mgr';

		$this->_properties = $properties;
	}


	/** {@inheritDoc} */
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
				case 'date_ago': $value = $this->getDateAgo(); break;
				default: $value = parent::get($k, $format, $formatTemplate);
			}

			if (!$this->_properties) {$this->_loadProperties();}

			if (!$this->_properties['process_tags'] && is_string($k) && !in_array($k, $fields) && @$this->_fieldMeta[$k]['phptype'] == 'string') {
				$value = str_replace(array('[',']','`'),array('&#91;','&#93;','&#96;'), $value);
			}
		}

		return $value;
	}


	/**
	 * {@inheritDoc}
	 */
	public function toArray($keyPrefix= '', $rawValues= false, $excludeLazy= false, $includeRelated= false) {
		$array = array_merge(
			parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated),
			$this->getVirtualFields()
		);

		return $array;
	}


	/**
	 * {@inheritDoc}
	 */
	public function process() {
		if ($this->privateweb && !$this->xpdo->hasPermission('ticket_view_private') && $id = $this->getOption('tickets.private_ticket_page')) {
			$this->xpdo->sendForward($id);
			die;
		}
		else {
			//$this->xpdo->setPlaceholders($this->getVirtualFields(), 'ticket_');

			return parent::process();
		}
	}


	/**
	 * {@inheritDoc}
	 */
	public function getContent(array $options = array()) {
		$content = parent::get('content');

		if (!$this->_properties) {$this->_loadProperties();}
		if (!$this->_properties['disable_jevix']) {
			$content = $this->Jevix($content, false);
		}
		if (!$this->_properties['process_tags']) {
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
	 *
	 * @var mixed $text for processing
	 * @var bool $replaceTags
	 *
	 * @return mixed Filtered text
	 */
	function Jevix($text, $replaceTags = true) {
		/** @var Tickets $Tickets */
		if ($Tickets = $this->xpdo->getService('Tickets')) {
			return $Tickets->Jevix($text, 'Ticket', $replaceTags);
		}
		return 'Error on loading class "Tickets".';
	}


	/**
	 * Generate intro text from content buy cutting text before tag <cut/>
	 * @param string $content Any text for processing, with tag <cut/>
	 *
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


	/** {@inheritDoc} */
	public function & getMany($alias, $criteria= null, $cacheFlag= true) {
		if ($alias == 'Attachments' || $alias == 'Votes') {
			$criteria = array('class' => $this->class_key);
		}
		return parent::getMany($alias, $criteria, $cacheFlag);
	}


	/** {@inheritDoc} */
	public function addMany(& $obj, $alias= '') {
		$added= false;
		if (is_array($obj)) {
			foreach ($obj as $o) {
				/** @var xpdoObject $o */
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


	/**
	 * Shorthand for getting virtual Ticket fields
	 *
	 * @return array $array Array with virtual fields
	 */
	function getVirtualFields() {
		if (!$this->_properties) {$this->_loadProperties();}

		$array = array(
			'comments' => $this->getCommentsCount(),
			'views' => $this->getViewsCount(),
			'date_ago' => $this->getDateAgo(),
		);
		$array = array_merge($array, $this->getRating());

		return $array;
	}


	/**
	 * Returns all information about ticket rating
	 *
	 * @return array
	 */
	public function getRating() {
		if (!$this->_properties) {$this->_loadProperties();}

		$array = array(
			'rating' => isset($this->_properties['rating']) ? $this->_properties['rating'] : 0,
			'rating_total' => isset($this->_properties['rating']) ? $this->_properties['rating'] : 0,
			'rating_plus' => isset($this->_properties['rating_plus']) ? $this->_properties['rating_plus'] : 0,
			'rating_minus' => isset($this->_properties['rating_minus']) ? $this->_properties['rating_minus'] : 0,
		);
		$rating = array_key_exists('rating', $this->_properties)
			? $this->_properties['rating']
			: '';
		//if ($array['rating'] > 0) {$array['rating'] = '+' . $rating;}

		if (!$this->xpdo->user->id || $this->xpdo->user->id == $this->createdby) {
			$array['voted'] = 0;
		}
		else {
			$voted = $this->getVote();
			if ($voted > 0) {$voted = 1;}
			elseif ($voted < 0) {$array['voted'] = -1;}
			$array['voted'] = $voted;
		}

		$array['can_vote'] = $array['voted'] === false && $this->xpdo->user->id && $this->xpdo->user->id != $this->createdby;

		return $array;
	}


	/**
	 * Returns count of views of Ticket by users
	 *
	 * @return integer $count Total count of views
	 */
	public function getViewsCount() {
		$q = $this->xpdo->newQuery('Ticket', $this->id);
		$q->leftJoin('TicketView','TicketView', "`TicketView`.`parent` = `Ticket`.`id`");
		$q->select('COUNT(`TicketView`.`parent`) as `views`');

		$count = 0;
		$tstart = microtime(true);
		if ($q->prepare() && $q->stmt->execute()) {
			$this->xpdo->startTime += microtime(true) - $tstart;
			$this->xpdo->executedQueries ++;
			$count = (integer) $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		return $count;
	}


	/**
	 * Returns count of comments to Ticket
	 *
	 * @return integer $count Total count of comment
	 */
	public function getCommentsCount() {
		$q = $this->xpdo->newQuery('Ticket', $this->id);
		$q->leftJoin('TicketThread','TicketThread', "`TicketThread`.`name` = 'resource-{$this->id}'");
		$q->leftJoin('TicketComment','TicketComment', "`TicketThread`.`id` = `TicketComment`.`thread` AND `TicketComment`.`published` = 1");
		$q->select('COUNT(`TicketComment`.`id`) as `comments`');

		$count = 0;
		$tstart = microtime(true);
		if ($q->prepare() && $q->stmt->execute()) {
			$this->xpdo->startTime += microtime(true) - $tstart;
			$this->xpdo->executedQueries ++;
			$count = (integer) $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		return $count;
	}


	/**
	 * Returns sum of votes to Ticket by users
	 *
	 * @return integer $count Total sum of votes
	 */
	public function getVotesSum() {
		$q = $this->xpdo->newQuery('Ticket', $this->id);
		$q->leftJoin('TicketVote','TicketVote', "`TicketVote`.`parent` = `Ticket`.`id` AND `TicketVote`.`class` = 'Ticket'");
		$q->select('SUM(`TicketVote`.`value`) as `votes`');

		$sum = 0;
		$tstart = microtime(true);
		if ($q->prepare() && $q->stmt->execute()) {
			$this->xpdo->startTime += microtime(true) - $tstart;
			$this->xpdo->executedQueries ++;
			$sum = (integer) $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		return $sum;
	}


	/**
	 * Return formatted date of ticket creation
	 *
	 * @return string
	 */
	public function getDateAgo() {
		$createdon = parent::get('createdon');
		/** @var Tickets $Tickets */
		if ($Tickets = $this->xpdo->getService('Tickets')) {
			$createdon = $Tickets->dateFormat($createdon);
		}
		return $createdon;
	}


	/**
	 * Returns vote of current user for this ticket
	 *
	 * @return int|mixed
	 */
	public function getVote() {
		$q = $this->xpdo->newQuery('TicketVote');
		$q->where(array(
			'id' => $this->id,
			'createdby' => $this->xpdo->user->id,
			'class' => 'Ticket',
		));
		$q->select('`value`');

		$vote = 0;
		$tstart = microtime(true);
		if ($q->prepare() && $q->stmt->execute()) {
			$this->xpdo->startTime += microtime(true) - $tstart;
			$this->xpdo->executedQueries ++;
			$vote = $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		return $vote;
	}


	/**
	 * Update comment rating
	 *
	 * @return array
	 */
	public function updateRating() {
		$votes = array('rating' => 0, 'rating_plus' => 0, 'rating_minus' => 0);

		$q = $this->xpdo->newQuery('TicketVote', array('id' => $this->id, 'class' => 'Ticket'));
		$q->innerJoin('modUser', 'modUser', '`modUser`.`id` = `TicketVote`.`createdby`');
		$q->select('value');
		$tstart = microtime(true);
		if ($q->prepare() && $q->stmt->execute()) {
			$this->xpdo->startTime += microtime(true) - $tstart;
			$this->xpdo->executedQueries ++;
			while ($value = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
				$votes['rating'] += $value;
				if ($value > 0) {
					$votes['rating_plus'] += $value;
				}
				elseif ($value < 0) {
					$votes['rating_minus'] += $value;
				}
			}
			$tmp = $this->get('properties');
			$this->_properties = array_merge($tmp, $votes);
			$this->set('properties', $this->_properties);
			$this->save();
		}

		return $votes;
	}

}