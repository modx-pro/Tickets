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


	/** {inheritDoc} */
	public function initialize() {
		$primaryKey = $this->getProperty($this->primaryKeyField,false);
		if (empty($primaryKey)) return $this->modx->lexicon($this->objectType.'_err_ns');

		if (!$this->modx->getCount($this->classKey, array('id' => $primaryKey, 'class_key' => $this->classKey)) && $res = $this->modx->getObject('modResource', $primaryKey)) {
			$res->set('class_key', $this->classKey);
			$res->save();
		}

		return parent::initialize();
	}


	/** {@inheritDoc} */
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
		if ($category = $this->modx->getObject('TicketsSection', array('id' => $this->getProperty('parent'), 'class_key' => 'TicketsSection'))) {
			if (!$category->checkPolicy('section_add_children') && $this->object->parent != $category->id) {
				return $this->modx->lexicon('ticket_err_wrong_parent') . $this->modx->lexicon('ticket_err_access_denied');
			}
		}
		elseif ($this->modx->context->key != 'mgr') {
			return $this->modx->lexicon('resource_err_nfs', array('id' => $this->getProperty('parent')));
		}

		$this->setProperties(array(
			'class_key' => 'Ticket'
			//,'show_in_tree' => 0
		,'published' => 0
		,'hidemenu' => $hidemenu
		,'syncsite' => 0
		,'isfolder' => $isfolder
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


	/** {@inheritDoc} */
	public function checkFriendlyAlias() {
		parent::checkFriendlyAlias();

		$found = false;
		foreach ($this->modx->error->errors as $k => $v) {
			if ($v['id'] == 'alias') {
				unset($this->modx->error->errors[$k]);
				$found = true;
			}
		}

		if ($found || $this->workingContext->getOption('tickets.ticket_id_as_alias')) {
			$alias = $this->object->id;
			$this->setProperty('alias', $alias);
		}
		else {
			$alias = parent::checkFriendlyAlias();
		}

		return $alias;
	}


	/** {@inheritDoc} */
	public function afterSave() {
		$this->object->fromArray(array(
			'published' => $this->published
		));
		if ($this->updatepubdate) {
			$this->object->set('publishedon', $this->published ? $this->publishedon : 0);
			$this->object->set('publishedby', $this->published ? $this->publishedby : 0);
		}
		$this->object->save();
		return parent::afterSave();
	}


	/** {@inheritDoc} */
	public function clearCache() {
		$this->object->clearCache();
		/** @var TicketsSection $section */
		if ($section = $this->modx->getObject('TicketsSection', $this->object->parent)) {
			$section->clearCache();
		}
	}


	/** {@inheritDoc} */
	public function saveTemplateVariables() {
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

		return parent::saveTemplateVariables();
	}

}