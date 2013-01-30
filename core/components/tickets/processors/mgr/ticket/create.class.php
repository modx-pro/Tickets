<?php
/**
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the Ticket type
 *
 * @package tickets
 */

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';

class TicketCreateProcessor extends modResourceCreateProcessor {
	/** @var Ticket $object */
	public $object;
	public $classKey = 'Ticket';
	public $permission = 'ticket_save';
	public $languageTopics = array('access','resource','tickets:default');
	private $published = 0;
	private $publishedon = 0;
	private $publishedby = 0;

	/**
	 * {@inheritDoc}
	 * @return mixed
	 */
	public function beforeSet() {
		$published = $this->getProperty('published');
		$createdby = $this->getProperty('createdby');
		$this->published = empty($published) || $published === 'false' ? 0 : 1;
		if (!$this->publishedon = $this->getProperty('publishedon')) {$this->publishedon = time();}
		if (!$this->publishedby = $this->getProperty('publishedby')) {$this->publishedby = $this->modx->user->id;}

		$beforeSet = parent::beforeSet();

		if (!$this->getProperty('parent')) {
			$this->addFieldError('parent', $this->modx->lexicon('field_required'));
		}
		if (!$this->getProperty('pagetitle')) {
			$this->addFieldError('pagetitle', $this->modx->lexicon('field_required'));
		}
		if (!$this->getProperty('content') && $this->modx->context->key != 'mgr') {
			$this->addFieldError('content', $this->modx->lexicon('field_required'));
		}
		if ($this->hasErrors()) {return false;}

		if ($introtext = $this->getProperty('introtext')) {
			$introtext = $this->object->Jevix($introtext);
		}
		else {
			$introtext = $this->object->getIntroText($this->getProperty('content'));
		}

		$properties = array(
			'class_key' => 'Ticket'
			,'show_in_tree' => 0
			,'published' => 0
			,'hidemenu' => 1
			,'syncsite' => 0
			,'isfolder' => 1
			,'introtext' => $introtext
			,'createdby' => !empty($createdby) ? $createdby : $this->modx->user->id
		);

		$this->setProperties($properties);
		/* Tickets properties */
		if ($this->modx->context->key != 'mgr') {
			$this->unsetProperty('properties');
		}

		return $beforeSet;
	}

	/**
	 * {@inheritDoc}
	 * @return string
	 */
	public function prepareAlias() {
		parent::prepareAlias();

		foreach ($this->modx->error->errors as $k => $v) {
			if ($v['id'] == 'alias') {
				unset($this->modx->error->errors[$k]);
				$this->setProperty('alias', 'empty-resource-alias');
			}
		}
	}

	/**
	 * Make sure parent exists and user can add_children to the parent
	 * @return boolean|string
	 */
	public function checkParentPermissions() {
		$parent = null;
		$parentId = intval($this->getProperty('parent'));
		if ($parentId > 0) {
			$this->parentResource = $this->modx->getObject('TicketsSection',$parentId);
			if ($this->parentResource->get('class_key') != 'TicketsSection') {
				return $this->modx->lexicon('ticket_err_wrong_parent');
			}
			if ($this->parentResource) {
				if (!$this->parentResource->checkPolicy('section_add_children')) {
					return $this->modx->lexicon('ticket_err_wrong_parent') . $this->modx->lexicon('ticket_err_access_denied');
				}
			} else {
				return $this->modx->lexicon('resource_err_nfs', array('id' => $parentId));
			}
		}
		else {
			return $this->modx->lexicon('ticket_err_access_denied');
		}
		return true;
	}

	/**
	 * {@inheritDoc}
	 * @return mixed
	 */
	public function afterSave() {
		$this->object->fromArray(array(
			'published' => $this->published
			,'publishedon' => $this->published ? $this->publishedon : 0
			,'publishedby' => $this->published ? $this->publishedby : 0
			,'isfolder' => 1
			,'template' => $this->modx->getOption('tickets.default_template', null, $this->modx->getOption('default_template'), true)
		));
		if ($this->object->alias == 'empty-resource-alias') {
			$this->object->set('alias', $this->object->id);
		}
		$this->object->save();
		return parent::afterSave();
	}

	/**
	 * {@inheritDoc}
	 * @return void
	 */
	public function clearCache() {
		$results = $this->modx->cacheManager->generateContext($this->object->context_key);
		$this->modx->context->resourceMap = $results['resourceMap'];
		$this->modx->context->aliasMap = $results['aliasMap'];

		/** @var TicketsSection $section */
		if ($section = $this->modx->getObject('TicketsSection', $this->object->parent)) {
			$section->clearCache();
		}
	}

}