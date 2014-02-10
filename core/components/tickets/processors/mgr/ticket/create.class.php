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


	/** {@inheritDoc} */
	public function beforeSet() {
		$published = $this->getProperty('published');
		$createdby = $this->getProperty('createdby');
		$this->published = empty($published) || $published === 'false' ? 0 : 1;
		if (!$this->publishedon = $this->getProperty('publishedon')) {$this->publishedon = time();}
		if (!$this->publishedby = $this->getProperty('publishedby')) {$this->publishedby = $this->modx->user->id;}

		// Required fields
		$requiredFields = $this->getProperty('requiredFields', array('parent','pagetitle','content'));
		foreach ($requiredFields as $field) {
			$value = trim($this->getProperty($field));
			if (empty($value) && $this->modx->context->key != 'mgr') {
				$this->addFieldError($field, $this->modx->lexicon('field_required'));
			}
			else {
				$this->setProperty($field, $value);
			}
		}
		if (!$this->getProperty('content') && $this->modx->context->key != 'mgr') {
			return $this->modx->lexicon('ticket_err_empty');
		}
		if (!$this->getProperty('template')) {
			$this->setProperty('template', $this->modx->getOption('tickets.default_template', null, $this->modx->getOption('default_template'), true));
		}

		$beforeSet = parent::beforeSet();
		if ($this->hasErrors()) {
			return $this->modx->lexicon('ticket_err_form');
		}
		if ($introtext = $this->getProperty('introtext')) {
			$introtext = $this->object->Jevix($introtext);
		}
		else {
			$introtext = $this->object->getIntroText($this->getProperty('content'));
		}

		if (!$hidemenu = $this->modx->getOption('tickets.ticket_hidemenu_force', null, false)) {
			$hidemenu = array_key_exists('hidemenu', $this->properties)
				? $this->getProperty('hidemenu')
				: $this->modx->getOption('hidemenu_default');
		}
		if (!$isfolder = $this->modx->getOption('tickets.ticket_isfolder_force', null, false)) {
			$isfolder = array_key_exists('isfolder', $this->properties)
				? $this->getProperty('isfolder')
				: false;
		}

		$properties = array(
			'class_key' => 'Ticket',
			'show_in_tree' => $this->modx->getOption('tickets.ticket_show_in_tree_default', null, false),
			'published' => 0,
			'hidemenu' => $hidemenu,
			'syncsite' => 0,
			'isfolder' => $isfolder,
			'introtext' => $introtext,
			'createdby' => !empty($createdby) ? $createdby : $this->modx->user->id,
		);

		$this->setProperties($properties);
		/* Tickets properties */
		if ($this->modx->context->key != 'mgr') {
			$this->unsetProperty('properties');
		}

		return $beforeSet;
	}


	/** {@inheritDoc} */
	public function prepareAlias() {
		parent::prepareAlias();

		$found = false;
		$alias = 'empty';

		foreach ($this->modx->error->errors as $k => $v) {
			if ($v['id'] == 'alias') {
				unset($this->modx->error->errors[$k]);
				$found = true;
				break;
			}
		}

		if ($found || $this->workingContext->getOption('tickets.ticket_id_as_alias')) {
			$this->setProperty('alias', $alias);
		}
		else {
			$alias = parent::prepareAlias();
		}

		return $alias;
	}


	/** {@inheritDoc} */
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


	/** {@inheritDoc} */
	public function afterSave() {
		$this->object->fromArray(array(
			'published' => $this->published
			,'publishedon' => $this->published ? $this->publishedon : 0
			,'publishedby' => $this->published ? $this->publishedby : 0
		));
		if ($this->object->alias == 'empty') {
			$this->object->set('alias', $this->object->id);
		}
		$this->object->save();

		// Updating resourceMap before OnDocSaveForm event
		$results = $this->modx->cacheManager->generateContext($this->object->context_key, array('cache_context_settings' => false));
		$this->modx->context->resourceMap = $results['resourceMap'];
		$this->modx->context->aliasMap = $results['aliasMap'];

		return parent::afterSave();
	}


	/** {@inheritDoc} */
	public function clearCache() {
		$clear = false;
		/* @var TicketsSection $category */
		if ($category = $this->object->getOne('Section')) {
			$category->clearCache();
			$clear = true;
		}

		/** @var xPDOFileCache $cache */
		$cache = $this->modx->cacheManager->getCacheProvider($this->modx->getOption('cache_context_settings_key', null, 'context_settings'));
		$key = $this->modx->context->getCacheKey();
		$cache->delete($key);

		return $clear;
	}


	/** {@inheritDoc} */
	public function addTemplateVariables() {
		$properties = $this->getProperties();
		$fields = array_keys($this->modx->getFieldMeta($this->classKey));
		$tvs = array_diff(array_keys($properties), $fields);

		if (!empty($tvs)) {
			$q = $this->modx->newQuery('modTemplateVar', array('name:IN' => $tvs));
			$q->select('id,name');
			if ($q->prepare() && $q->stmt->execute()) {
				while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
					$this->setProperty('tv'.$row['id'], $properties[$row['name']]);
				}
			}
			return parent::addTemplateVariables();
		}
		return false;
	}

}