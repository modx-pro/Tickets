<?php

class TicketCommentDeleteProcessor extends modObjectUpdateProcessor
{
    /** @var TicketComment $object */
    public $object;
    public $objectType = 'TicketComment';
    public $classKey = 'TicketComment';
    public $languageTopics = array('tickets:default');
    public $beforeSaveEvent = 'OnBeforeCommentDelete';
    public $afterSaveEvent = 'OnCommentDelete';
    public $permission = 'comment_delete';


    /**
     *
     */
    public function beforeSet()
    {
        $this->properties = array();

        return true;
    }


    /**
     * @return bool|null|string
     */
    public function beforeSave()
    {
        $this->object->fromArray(array(
            'deleted' => 1,
            'deletedon' => time(),
            'deletedby' => $this->modx->user->get('id'),
        ));

        return parent::beforeSave();
    }


    /**
     * @return bool
     */
    public function afterSave()
    {
        $this->object->clearTicketCache();
        /** @var TicketThread $thread */
        if ($thread = $this->object->getOne('Thread')) {
            $thread->updateLastComment();
        }
        $this->modx->cacheManager->delete('tickets/latest.comments');
        $this->modx->cacheManager->delete('tickets/latest.tickets');

        return parent::afterSave();
    }


    /**
     *
     */
    public function logManagerAction()
    {
        $this->modx->logManagerAction($this->objectType . '_delete', $this->classKey,
            $this->object->get($this->primaryKeyField));
    }

}

return 'TicketCommentDeleteProcessor';