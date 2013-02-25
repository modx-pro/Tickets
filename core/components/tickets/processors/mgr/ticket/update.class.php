<?php
/**
 * Overrides the modResourceUpdateProcessor to provide custom processor functionality for the Ticket type
 *
 * @package tickets
 */

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class TicketUpdateProcessor extends modResourceUpdateProcessor {
	/** @var Ticket $object */
	public $object;
	public $classKey = 'Ticket';
	public $permission = 'ticket_save';
	public $languageTopics = array('resource','tickets:default');
	private $published = 0;
	private $publishedon = 0;
	private $publishedby = 0;
	private $updatepubdate = 0;

	/**
	 * {@inheritDoc}
	 * @return mixed
	 */
	public function beforeSet() {
		if ($this->object->createdby != $this->modx->user->id && !$this->modx->hasPermission('edit_document')) {
			return $this->modx->lexicon('ticket_err_wrong_user');
		}

		$published = $this->getProperty('published');
		$this->published = empty($published) || $published === 'false' ? 0 : 1;
		if ($this->object->published != $this->published) {
			$this->updatepubdate = 1;
		}
		if (!$this->publishedon = $this->getProperty('publishedon')) {$this->publishedon = time();}
		if (!$this->publishedby = $this->getProperty('publishedby')) {$this->publishedby = $this->modx->user->id;}

		foreach (array('parent','pagetitle','content') as $field) {
			$value = trim($this->getProperty($field));
			if (empty($value) && $this->modx->context->key != 'mgr') {
				$this->addFieldError($field, $this->modx->lexicon('field_required'));
			}
			else {
				$this->setProperty($field, $value);
			}
		}

		$beforeSet = parent::beforeSet();
		if ($this->hasErrors()) {return false;}
		if ($introtext = $this->getProperty('introtext')) {
			$introtext = $this->object->Jevix($introtext);
		}
		else {
			$introtext = $this->object->getIntroText($this->getProperty('content'));
		}

		$this->setProperties(array(
			'class_key' => 'Ticket'
			,'show_in_tree' => 0
			,'published' => 0
			,'hidemenu' => 1
			,'syncsite' => 0
			,'isfolder' => 1
			,'introtext' => $introtext
		));

		/* Tickets properties */
		if ($this->modx->context->key == 'mgr') {
			$prop1 = $this->object->get('properties');
			$prop2 = $this->getProperty('properties');
			if (empty($prop1)) {$prop1 = array();}
			if (empty($prop2)) {$prop2 = array();}
			$properties = array_merge($prop1, $prop2);
			$this->setProperty('properties', $properties);
		}
		else {
			$this->unsetProperty('properties');
		}

		return $beforeSet;
	}

	/**
	 * {@inheritDoc}
	 * @return string
	 */
	public function checkFriendlyAlias() {
		parent::checkFriendlyAlias();
		foreach ($this->modx->error->errors as $k => $v) {
			if ($v['id'] == 'alias') {
				unset($this->modx->error->errors[$k]);
				$this->setProperty('alias', $this->object->id);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 * @return mixed
	 */
	public function afterSave() {
		$this->object->fromArray(array(
			'published' => $this->published
			,'isfolder' => 1
		));
		if ($this->updatepubdate) {
			$this->object->set('publishedon', $this->published ? $this->publishedon : 0);
			$this->object->set('publishedby', $this->published ? $this->publishedby : 0);
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

		$this->object->clearCache();
		/** @var TicketsSection $section */
		if ($section = $this->modx->getObject('TicketsSection', $this->object->parent)) {
			$section->clearCache();
		}
	}
}