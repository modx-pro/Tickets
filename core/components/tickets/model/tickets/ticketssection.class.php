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
