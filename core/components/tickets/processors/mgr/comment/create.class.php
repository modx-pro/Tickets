<?php

class TicketCommentCreateProcessor extends modObjectCreateProcessor
{
    /** @var TicketComment $object */
    public $object;
    public $objectType = 'TicketComment';
    public $classKey = 'TicketComment';
    public $languageTopics = array('tickets:default');
    public $permission = 'comment_save';
    public $beforeSaveEvent = 'OnBeforeCommentSave';
    public $afterSaveEvent = 'OnCommentSave';
    /** @var TicketThread $thread */
    private $thread;


    /**
     * @return bool|string
     */
    public function initialize()
    {
        $this->thread = $this->modx->getObject('TicketThread', (int)$this->getProperty('thread'));
        if (!$this->thread) {
            return $this->modx->lexicon('ticket_err_wrong_thread');
        } elseif ($this->thread->closed) {
            return $this->modx->lexicon('ticket_thread_err_closed');
        } elseif ($this->thread->deleted) {
            return $this->modx->lexicon('ticket_thread_err_deleted');
        }

        return parent::initialize();
    }


    /**
     * @return bool|null|string
     */
    public function beforeSet()
    {
        if (!trim($this->getProperty('text'))) {
            return $this->modx->lexicon('ticket_err_empty');
        }

        // Comment values
        $ip = $this->modx->request->getClientIp();
        $this->setProperties(array(
            'parent' => (int)$this->getProperty('parent'),
            'thread' => $this->thread->id,
            'ip' => $ip['ip'],
            'email' => $this->modx->user->Profile->fullname,
            'name' => $this->modx->user->Profile->email,
            'createdon' => date('Y-m-d H:i:s'),
            'createdby' => $this->modx->user->id,
            'published' => true,
        ));
        $this->unsetProperty('action');

        return parent::beforeSet();
    }


    /**
     * @return bool
     */
    public function beforeSave()
    {
        $text = $this->getProperty('text');

        /** @var Tickets $Tickets */
        if ($Tickets = $this->modx->getService('Tickets')) {
            $this->object->fromArray(array(
                'text' => $Tickets->Jevix($text, 'Comment'),
                'raw' => $text,
            ));
        }

        return parent::beforeSave();
    }


    /**
     * @return bool
     */
    public function afterSave()
    {
        $this->thread->fromArray(array(
            'comment_last' => $this->object->get('id'),
            'comment_time' => $this->object->get('createdon'),
        ));
        $this->thread->save();

        $this->thread->updateCommentsCount();
        $this->object->clearTicketCache();

        return parent::afterSave();
    }

}

return 'TicketCommentCreateProcessor';
