<?php
class TicketFileSortProcessor extends modObjectProcessor
{
    public $classKey = 'TicketFile';

    public function process() {
        $rank = $this->getProperty('rank');
        foreach($rank as $idx => $id){
            if (!$file = $this->modx->getObject($this->classKey, (int)$id)) {
                return $this->failure($this->modx->lexicon('ticket_err_file_ns'));
            }
            elseif ($file->createdby != $this->modx->user->id && !$this->modx->user->isMember('Administrator')) {
                return $this->failure($this->modx->lexicon('ticket_err_file_owner'));
            }
            $file->set('rank', (int)$idx);
            $file->save();
        }
        return $this->success();
    }
}

return 'TicketFileSortProcessor';
