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
	private $_properties;

	/** @var TicketsSection $parentResource */
	public $parentResource;


	/** {@inheritDoc} */
	public function beforeSet() {
		$published = $this->getProperty('published');
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

		// Run main verifications
		parent::beforeSet();
		if ($this->hasErrors()) {
			return $this->modx->lexicon('ticket_err_form');
		}

		// Ticket properties
		$properties = $this->modx->context->key == 'mgr'
			? $this->getProperty('properties')
			: $this->parentResource->getProperties();
		$this->_properties = $properties;
		$this->unsetProperty('properties');

		// Define introtext
		$introtext = $this->getProperty('introtext');
		if (empty($introtext)) {
			$introtext = $this->object->getIntroText($this->getProperty('content'), false);
		}
		if (empty($properties['disable_jevix'])) {
			$introtext = $this->object->Jevix($introtext);
		}

		// Redefine main parameters if we are not in the manager
		if ($this->modx->context->key == 'mgr') {
			$template = $this->getProperty('template');
			$hidemenu = $this->getProperty('hidemenu');
			$show_in_tree = $this->getProperty('show_in_tree');
			$createdby = $this->getProperty('createdby');
		}
		else {
			$template = $properties['template'];
			$hidemenu = $properties['hidemenu'];
			$show_in_tree = $properties['show_in_tree'];
			$createdby = $this->modx->user->id;
		}

		// Set properties
		$this->setProperties(array(
			'class_key' => 'Ticket',
			'published' => 0,
			'syncsite' => 0,
			'template' => $template,
			'introtext' => $introtext,
			'hidemenu' => $hidemenu,
			'show_in_tree' => $show_in_tree,
			'createdby' => $createdby,
			'properties' => array(
				'disable_jevix' => !empty($properties['disable_jevix']),
				'process_tags' => !empty($properties['process_tags']),
			)
		));

		return true;
	}


	/** {@inheritDoc} */
	public function prepareAlias() {
		$alias = parent::prepareAlias();

		if ($this->modx->context->key != 'mgr') {
			foreach ($this->modx->error->errors as $k => $v) {
				if ($v['id'] == 'alias' || $v['id'] == 'uri') {
					unset($this->modx->error->errors[$k]);
				}
			}
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
			}
			else {
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

		$uri = $this->object->get('uri');
		$new_uri = str_replace('%id', $this->object->get('id'), $uri);
		if ($uri != $new_uri) {
			$this->object->set('uri', $new_uri);
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
		if ($this->modx->context->key != 'mgr') {
			$values = array();
			$tvs = $this->object->getMany('TemplateVars');

			/** @var modTemplateVarResource $tv */
			foreach ($tvs as $tv) {
				$values['tv' . $tv->id] = $this->getProperty($tv->name, $tv->get('value'));
			}

			if (!empty($values)) {
				$this->setProperties($values);
				$this->setProperty('tvs', 1);
			}
		}

		return parent::addTemplateVariables();
	}

}