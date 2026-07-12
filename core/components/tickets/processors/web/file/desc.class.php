<?php

class TicketFileDescProcessor extends modObjectProcessor
{
    public $classKey = 'TicketFile';
    public $permission = 'ticket_file_upload';


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }


    /**
     * @return array|string
     */
    public function process()
    {
        $id = (int)$this->getProperty('id');
        $description = trim(strip_tags((string)$this->getProperty('description', '')));
        $charset = $this->modx->getOption('modx_charset', null, 'UTF-8', true);
        if (mb_strlen($description, $charset) > 500) {
            $description = mb_substr($description, 0, 500, $charset);
        }
        /** @var TicketFile $file */
        if (!$file = $this->modx->getObject($this->classKey, $id)) {
            return $this->failure($this->modx->lexicon('ticket_err_file_ns'));
        } elseif ($file->createdby != $this->modx->user->id && !$this->modx->user->isMember('Administrator')) {
            return $this->failure($this->modx->lexicon('ticket_err_file_owner'));
        }

        $file->set('description', $description);
        $file->save();

        return $this->success('', array('description' => $description));
    }

}

return 'TicketFileDescProcessor';
