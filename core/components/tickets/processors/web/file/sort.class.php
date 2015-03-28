<?php

class TicketFileDeleteProcessor extends modObjectProcessor {
    public $classKey = 'TicketFile';
    public $permission = 'ticket_file_upload';


    /** {@inheritDoc} */
    public function initialize() {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }
        return true;
    }


    /** {@inheritDoc} */
    public function process() {
        $rank = $this->getProperty('rank');
        /** @var TicketFile $files */
        foreach($rank as $idx => $id){
            if (!$file = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('ticket_err_file_ns'));
            }
            elseif ($file->createdby != $this->modx->user->id && !$this->modx->user->isMember('Administrator')) {
                return $this->failure($this->modx->lexicon('ticket_err_file_owner'));
            }

            $file->set('rank', $idx);
            $file->save();
        }

        return $this->success();
    }

}
return 'TicketFileDeleteProcessor';