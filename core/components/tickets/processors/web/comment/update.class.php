<?php

class TicketCommentUpdateProcessor extends modObjectUpdateProcessor
{
    /** @var TicketComment $object */
    public $object;
    public $objectType = 'TicketComment';
    public $classKey = 'TicketComment';
    public $languageTopics = array('tickets:default');
    public $permission = 'comment_save';
    public $beforeSaveEvent = 'OnBeforeCommentSave';
    public $afterSaveEvent = 'OnCommentSave';
    private $guest = false;


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        $this->guest = (bool)$this->getProperty('allowGuest', false);

        return !empty($this->permission) && !$this->guest
            ? $this->modx->hasPermission($this->permission)
            : true;
    }


    /**
     * @return bool|null|string
     */
    public function beforeSet()
    {
        $time = time() - strtotime($this->object->get('createdon'));
        $ip = $this->modx->request->getClientIp();

        if (!$this->modx->getCount('TicketThread',
            array('name' => $this->getProperty('thread'), 'deleted' => 0, 'closed' => 0))
        ) {
            return $this->modx->lexicon('ticket_err_wrong_thread');
        } elseif ($this->modx->user->isAuthenticated($this->modx->context->key) && $this->object->get('createdby') != $this->modx->user->id) {
            return $this->modx->lexicon('ticket_comment_err_wrong_user');
        } elseif (!$this->modx->user->isAuthenticated($this->modx->context->key)) {
            if (!$this->getProperty('allowGuest') || !$this->getProperty('allowGuestEdit')) {
                return $this->modx->lexicon('ticket_comment_err_guest_edit');
            } elseif ($this->object->get('ip') != $ip['ip']) {
                return $this->modx->lexicon('ticket_comment_err_wrong_guest_ip');
            }
        } elseif ($this->modx->getCount('TicketComment', array('parent' => $this->object->get('id')))) {
            return $this->modx->lexicon('ticket_comment_err_has_replies');
        } elseif ($time >= $this->modx->getOption('tickets.comment_edit_time', null, 600)) {
            return $this->modx->lexicon('ticket_comment_err_no_time');
        } elseif ($this->object->get('deleted')) {
            return $this->modx->lexicon('ticket_err_deleted_comment');
        } elseif (!$this->object->get('published')) {
            return $this->modx->lexicon('ticket_err_unpublished_comment');
        }

        // Required fields
        $requiredFields = array_map('trim', explode(',', $this->getProperty('requiredFields', 'name,email')));
        foreach ($requiredFields as $field) {
            $value = $this->modx->stripTags(trim($this->getProperty($field)));
            if (empty($value)) {
                $value = $this->object->get($field);
            }
            if ($field == 'email' && !preg_match('/.+@.+\..+/i', $value)) {
                $this->setProperty('email', '');
                $this->addFieldError($field, $this->modx->lexicon('ticket_comment_err_email'));
            } else {
                if ($field == 'email') {
                    $value = strtolower($value);
                }
                $this->setProperty($field, $value);
            }
        }

        if (!$text = trim($this->getProperty('text'))) {
            return $this->modx->lexicon('ticket_comment_err_empty');
        }
        if (!$this->getProperty('email') && $this->modx->user->isAuthenticated($this->modx->context->key)) {
            return $this->modx->lexicon('ticket_comment_err_no_email');
        }

        // Additional properties
        $properties = $this->getProperties();
        $add = array();
        $meta = $this->modx->getFieldMeta('TicketComment');
        foreach ($properties as $k => $v) {
            if (!isset($meta[$k])) {
                $add[$k] = $this->modx->stripTags($v);
            }
        }

        $this->properties = array(
            'text' => $text,
            'raw' => $this->getProperty('raw'),
            'name' => $this->getProperty('name'),
            'email' => $this->getProperty('email'),
            'properties' => !empty($add)
                ? $add
                : $this->object->get('properties'),
        );
        $this->unsetProperty('action');

        return parent::beforeSet();
    }


    /**
     * @return bool
     */
    public function beforeSave()
    {
        $this->object->fromArray(array(
            'editedon' => time(),
            'editedby' => $this->modx->user->isAuthenticated($this->modx->context->key)
                ? $this->modx->user->id
                : 0,
        ));

        if ($this->guest) {
            $_SESSION['TicketComments']['name'] = $this->object->get('name');
            $_SESSION['TicketComments']['email'] = $this->object->get('email');
        }

        return parent::beforeSave();
    }


    /**
     * @return bool
     */
    public function afterSave()
    {
        $this->object->clearTicketCache();

        return parent::afterSave();
    }

}

return 'TicketCommentUpdateProcessor';