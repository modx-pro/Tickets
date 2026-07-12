<?php

class TicketCommentsGetUnpublishedCountProcessor extends modProcessor
{
    public $languageTopics = array('tickets:default');

    public function process()
    {
        $c = $this->modx->newQuery('TicketComment');
        $c->where(array(
            'published' => 0,
            'deleted' => 0,
        ));
        if ($parents = $this->getProperty('parents')) {
            if (!is_array($parents)) {
                $parents = explode(',', $parents);
            }
            $parents = array_filter(array_map('intval', $parents));
            if (!empty($parents)) {
                $c->leftJoin('TicketThread', 'Thread');
                $c->where(array('Thread.resource:IN' => $parents));
            }
        }
        $count = $this->modx->getCount('TicketComment', $c);

        return $this->success('', array('count' => (int)$count));
    }
}

return 'TicketCommentsGetUnpublishedCountProcessor';
